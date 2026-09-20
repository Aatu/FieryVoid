# Walkers of Sigma-957 — Implementation Plan

New Ancient faction. Nine new systems, three of which need machinery FV does not have today, plus a
second wave (Stages 11–19, added 2026-09-08) covering Mapmaker electronic warfare, the Traveler's
Docking Bay and the two Walker jump drives. This document is the long-form record; update it as
stages land.

**Status: Stages 0–9 COMPLETE (Stages 8 and 9 on 2026-09-06), Stage 10 COMPLETE
(10A the EW Detector's allowance 2026-09-09, 10B late EW allocation 2026-09-10) - so the FIRST
WAVE IS FINISHED - Stage 11 (housekeeping) COMPLETE, STAGE 12 (Mapmaker Electronic Warfare) and
STAGE 13 (Mapmaker Jump Engine) COMPLETE 2026-09-10, and STAGE 14 (the Mapmakers' Medium Lightning
Array + the Mapmaker hangar rule) COMPLETE 2026-09-11 after two play-test passes (§3.13b, and the
EW rules corrected from the rulebook text in §3.13c), and STAGE 15 (the Walker jump drive - promoted from
Stage 18 on 2026-09-11, D32) COMPLETE 2026-09-11 and then REWORKED the same day (§3.17b - Ancient drives
are legacy drives; the first build's deferred departure is gone), and STAGE 16 (the Traveler's Docking Bay) COMPLETE
2026-09-11 with the Waymarker's two-turn procedure (§3.14a) deferred by the user (D35) - §3.14b, and
STAGE 17 (the Traveler repairs what it carries) COMPLETE 2026-09-12 with three additions from the
user's notes the same day (D42/D45 one list sorted by priority alone, docked rows marked by their ship name, D43 a docked unit's own Self Repair keeps running,
D44 cobalt reinforcement rows) - §3.15a, and STAGE 18 (docked power sharing) COMPLETE 2026-09-12,
built to both halves of §3.16 with the grant recorded as CLIENT-COMPUTED AND ADVISORY (D46) -
§3.16a - and a play-test follow-up the same day that gave the OPPONENT the same figure by disclosing
the bay's ship ids and nothing else (D47) - §3.16b, and STAGE 19 (the Waymarker's two-turn procedure,
the aft-hit redirect, the hangar-manoeuvre label and what a stowed unit still projects) COMPLETE
2026-09-12, finishing the one piece D35 had deferred out of Stage 16 and adding three rules from the
user's notes the same day (D48 least damaged = most structure boxes remaining, D49 a rider can be shot
but cannot shoot, D50 the banner goes on the unit and a NEW flight gets none, D51 left-click scrolls on
ALL docked units, which withdraws a Stage 17 exception) - §3.14a / §3.14c / §3.14d, as built §3.14e.
Stage 20 (the Extra-Dimensional Jump Drive) not
started. ⚠️ Stages 12, 13
and 14 all reshuffle `MapmakerProbes`'s positional system ids and MUST deploy together; append
only after that. Stages 12–20 were added 2026-09-08 — the Mapmaker Sensor Probes' remaining abilities, the
Traveler's Docking Bay / repair / power sharing, and the traveler and extra-dimensional jump
drives.** Written 2026-09-02 after a full survey of the existing seams; re-surveyed 2026-09-08 for
the second wave, whose rulings and control sheet arrived the same day (D11–D26; Q8–Q15 all answered).

**Faction string is `Walkers of Sigma-957`** — plural, hyphenated, exactly as spelled here. It is
the switch key in `gamelobby.js`, the directory-map key in `ShipLoader::getFactionDirMap()`, the
filename of the lobby's per-faction JSON, and (D7) the CPD adaptation key. Getting it wrong is
silent everywhere.

---

## 0. Decisions already taken (user, 2026-09-02)

| # | Question | Ruling |
|---|---|---|
| D1 | EDF attribute drain on capital ships | Thrust roll reduces **Engine output**; Energy roll reduces **Reactor output**. Thrusters untouched. Flights use their real `freethrust`. |
| D2 | Sensor Charge Transceiver interaction | **Hex-target weapon with `canSplitShots`**, shots = manoeuvres available this turn. Each pick is a waypoint; yellow arc sprites draw the confirmed path, blue arcs from the last waypoint show reachable next hexes. No new plotting engine. |
| D3 | Energy Draining Net geometry | **Pairwise links AND closed-area fill.** Reuse the Gravitic Mine zone code. |
| D4 | Control-sheet numbers | **User supplies per system, per stage** — as a **Walker test ship carrying a basic version of that system**, so the stats arrive as a real hull file rather than a table. Classes get complete mechanics with clearly-marked placeholder stat tables until then. |
| D5 | EDF and concealment | **Out of scope.** No Walker hull has a stealth function, so the "a concealed ship discloses itself by projecting a field" case cannot arise. See §2.1 for when it would become live again. |
| D6 | Wide-Beam declaration timing | **Firing mode, chosen in the Fire phase.** The Prepare-Weapons timing deviation is accepted. |
| D7 | CPD adaptation scope | **The raw `$ship->faction` string.** No faction families. |
| D8 | EDN corridor choice | **Deterministic, no UI** — but tie-broken toward the corridor a player would pick: prefer a hex containing an enemy unit. |
| D9 | CPD adaptation vs Shading Field (2026-09-03) | Adaptation **may eat into the shaded bonus** — the Shading Field's whole defensive contribution, doubling included, is fair game. It has **no effect on the field's stealth/detection mechanics**. §3.4. |
| D10 | CPD runtime cost (2026-09-03) | *"A rare weapon in the overall game"* — every process that makes it work sits **behind cheap gates**. One static boolean, `TacGamedata::$cpdAdaptationPresent`, and no autoload in a game without it. §3.4. |
| D11 | Mapmaker EW pool size (2026-09-08) | **3 points per FLIGHT per turn**, not 3 per craft — *"like a ship"*, and every EW path in FV spends one per-unit pool. Confirmed 2026-09-08 (Q8). |
| D12 | Mapmaker OEW vs enemy defensive EW (2026-09-08, extended after Q10; ⚠️ **CORRECTED 2026-09-11**) | The contest belongs to the **Light Chromatic Pulsar**: OB + `max(0, OEW − (DEW + BDEW + SDEW))`, then all three are zeroed - *"an EW bonus of +0 (not -2)"*. *"BDEW and SDEW are separate EW functions so would stack with the fighter's own DEW."* The **Medium Lightning Array** is NOT contested: it uses ship rules (D25). The first build had the contest on the Array; see §3.13c. §3.11. |
| D13 | One jump point per Mapmaker flight (2026-09-08) | *"If one Jump Engine has a fireOrder they all do, but only generate ONE jump point."* ⭐ Already the law — `Firing::getVortexDeclarationBlock`'s one-vortex-per-**shooter** rule, and a fighter's shooter IS the flight. §3.12. |
| D14 | Mapmakers need no hangars (2026-09-08) | A **custom hangar category**, `'Mapmaker Probes'`, exactly as the Torvalus Stiletto uses `'Stilettos'`. ⚠️⚠️ **CORRECTED 2026-09-10 (D31) — the second half of this ruling was wrong.** §3.10c. |
| D31 | The 50% full-hangar rule DOES reach Mapmakers (2026-09-10) | *"While they can correctly be bought without any hangar, Walker ships are not exempt from the need to fill 50% of their hangar capacity e.g. `$fighters` with Mapmakers. At the moment fleet checker does not seem to be enforcing this."* So `noHangarRequired` lifts the **maximum only**: a fleet with no carrier may buy probes, and a Walker hull that declares Mapmaker Probe capacity must still have half of it filled. ⚠️ It also revealed that the two halves of the rule **never met**: the flag skipped the tally entirely and the hull's `hangarRequired` was left at `'fighters'`, so the capacity the four Walker hulls declare could not be filled by anything. §3.13a. |
| D15 | Medium Lightning Array (fighter) group sizes (2026-09-08) | **3 and 6 only, within one flight.** 4 or 5 declaring fire as a single 3-group and the surplus is wasted; 6 fires either two 3-groups or one 6-group, chosen as a **firing mode**. Damaged craft may not contribute. §3.13. |
| D16 | Weapon exclusivity inside a flight (2026-09-08) | **Flight-wide, not per craft.** If any Mapmaker fires the array, no Mapmaker may fire its Light Chromatic Pulsar that turn. §3.13. |
| D17 | The Traveler's aft bay (2026-09-08) | Relabelled **Docking Bay** as its own `Hangar` subclass (not just a `displayName`) — it needs a class allow-list, multi-class box costs and a per-turn type lock, none of which an ordinary hangar has. **One craft TYPE per turn**, launch or recover. §3.14. |
| D18 | Multi-box docking costs (2026-09-08, corrected after Q11) | Reuse `unitSize`, which already means "craft per box": Pathfinder `1/12`, Scribe `1/4`, Mapmaker `1`, Waymarker `1/24`. `HangarOps::boxesPerCraftForClass` turns those into 12 / 4 / 1 / 24 boxes with **no new arithmetic**, and all four fill the Traveler's 24-box bay exactly. §3.14. |
| D19 | Traveler Self Repair serves docked units (2026-09-08) | One new gate property on `SelfRepair`, in the shape the Kirishiac orbital's `repairRestrictedTo` / `linkedOrbital` already established. Structure, C&C, critical effects and Self Repair systems — the last of which is an explicit exception to this class's own standing rule. §3.15. |
| D20 | Docked power sharing rate (2026-09-08) | Every docked **ship's** reactor surplus is summed (never a flight's), divided by 4, floored, and added to the carrier's available power. Managing a docked ship's power during Initial Orders is a prerequisite and is the same stage. §3.16. |
| D21 | Traveler jump drives fire on the way out (2026-09-08) | The unit does **not** leave when it enters the vortex; it stays until the end of the turn, shoots normally, and **cannot be targeted** during Pre-Firing or Firing. Its drive also suffers **no destruction roll** while in use. §3.17. **Every Walker hull, not only the Traveler** (D32). Built at Stage 15, §3.17a. |
| D22 | EDJD abduction is note-persisted (2026-09-08) | Power-turns accumulate in `tac_individual_notes`, keyed by target, and "consecutive" is *"is there a note for the previous turn?"*. **No schema change**, and a gap needs no cleanup sweep — the same discipline that gives the Energy Draining Mine its one-turn life. §3.18. |
| D23 | The Waymarker's two-turn dock (2026-09-08) | A Waymarker fills the whole bay (24 boxes) **and takes two turns to dock or launch instead of one**. ⭐ **OPTIONAL within Stage 16** — the other three craft need nothing new, this one needs an intermediate state, and §3.14 proposes reusing `attached` for it. Ship the stage without it if the intermediate state turns out to cost more than it is worth. |
| D24 | Medium Lightning Array (fighter) stats (2026-09-08) | **Read from the control sheet**, not inferred: Electromagnetic, **Flash** mode, 4d10+12 at −1/3 hexes and FC +2/+4/+6 for the 3-group, 8d10+12 at −1/4 hexes and FC +5/+5/+4 for the 6-group, **RoF 1 per 4 turns** (`loadingtime = 4` — the brief's "2 turns" was a slip, corrected by the user 2026-09-08), and it may fire combined on turn 1. §3.13. |
| D25 | The two Mapmaker weapons lock on differently (2026-09-08; ⚠️ **CORRECTED 2026-09-11** from the rulebook text) | Light Chromatic Pulsar = **Offensive Bonus + max(0, OEW − target defensive EW)**, defensive EW otherwise ignored, and never a no-lock penalty. Medium Lightning Array = **ship rules**: flight OEW added, the target's DEW/BDEW/SDEW applied as the ordinary to-hit penalty, fire control used, no offensive bonus, and no OEW doubles the range penalty *"as usual"*. *"Flight-level combat"* on the sheet is not an FV concept and means nothing beyond that. §3.11, §3.13, §3.13c. |
| D26 | Flash collateral inside an Energy Draining Field (2026-09-08) | *"Flash damage always loses its collateral damage (friend or foe) ... unless the Lightning Array is boosted by the Wide Beam enhancement."* ⭐ Already how Stage 4 built it — `isHexInEdfField()` is team-blind and `edfSuppressesCollateral()` defaults to true — so `MedLightningArrayFtr` inherits it for free, and Wide Beam is a ship refit Mapmakers cannot buy. §3.13. |
| D27 | Where "the end of the movement segment" is (2026-09-09) | *"The 'End of movement' in FV is essentially the start of Pre-Firing phase (if there is one) or start of Firing phase. If we restrict the late EW allocation to these phases and don't worry too much about the Movement phase for now that's fine."* ⭐⭐ **This deleted the hard half of Stage 10B**: with the window opening AFTER movement there is nothing to *declare*, so "a point declared and then carried out of range is lost" needs no declaration and no reconciliation — the allowance is simply recomputed at the post-movement hex. Phases **5 and 3 both**, sharing one budget. §3.8. |
| D28 | What a saved EW point is drawn from (2026-09-09) | *"If a ship spends all their EW on non-DEW EW types, then they are unable to save a point of EW ... DEW is the only pool of unspent EW that saved EW can be drawn from in Pre-Firing/Firing."* So the allowance is `min(ladder, unspent pool)`, and the ship-window figure has to track the player's own clicking during Initial Orders rather than promising a point they have already spent. §3.8. |
| D29 | The Wanderer's weapons begin charged (2026-09-09) | *"Unlike other Walker ships, the Wanderer phpclass ship's weapons DO start the battle fully charged."* An exception keyed on **phpclass**, hung on `Weapon::setInitialSystemData($ship)` rather than on `getStartLoading()`, which does not know its ship. §3.10e, Stage 11. |
| D30 | Docked craft must be placeable on the Traveler's hex (2026-09-09) | *"Scribes, Waymarker and Pathfinders are all ships, which means they will not be eligible to stack on Traveler hex during Deployment at present. So we may need to loosen that restriction."* Loosened through the **existing** deploy-dock exemption in `getShipsInSameHex` (`pendingDeployDock` / `pendingLcvDeployDock`), never by weakening hull-versus-hull occupancy for everyone. §3.14, Stage 16. |
| D32 | Every Walker jump drive leaves slowly, and it is a MARK (2026-09-11) | *"All Walker ships possess this ability, not just Traveler. So the best approach seems to be to mark the Jump Engine as Walker (in the same way we mark Scanner as 'Advanced')."* `JumpEngine::markWalker()` on the six Walker hulls - a flag, not the `TravelerJumpDrive` class §3.17 first proposed, so no autoload change and no system id moves. The Mapmaker flight's engine stays unmarked (§3.12: it *"works normally"*). The targeting rule reuses the Energy Draining Mine's untargetable mechanism (§3.10c) and gives it its server half at last. Promoted from Stage 18 to Stage 15 the same day. §3.17a. |
| D33 | How a SHIP docks, and what damage does to it (2026-09-11) | **The LCV-rail rules, both halves.** Docking: the Traveler at speed 0, the ship ending its move in the Traveler's hex on the Traveler's heading with at least 1 thrust unspent - `canLCVDock`'s conditions, with its client-and-server thrust backstop. Damage: partial bay damage never removes a docked ship (probes are evicted first, as ever); the bay or the Traveler destroyed forces every docked ship out with the bay's damage + 2d10 to Structure and the launch initiative penalty. No landing damage. §3.14b. |
| D34 | What a ship costs against the launch rate (2026-09-11) — ⚠️ **SUPERSEDED by D37 the same day** | **Its box cost**, not 1 per ship: the bay's 12 a turn is paid in boxes by a ship, so one Pathfinder uses a whole turn and three Scribes fill it. With it the one-type-per-turn rule (D17) mostly follows from arithmetic - any two ship classes together exceed 12 - leaving Scribes against Mapmakers as the case the lock actually decides. §3.14b. |
| D48 | "Least damaged section" is MOST STRUCTURE BOXES REMAINING (2026-09-12) | The Waymarker's Front Structure is 60 and its Aft is 56, so the plausible readings disagree even on an undamaged hull. The user chose the raw remaining count - the number the player reads off the ship window - over proportion-remaining and over damage-taken, both of which tie on an unhurt hull and need a tiebreak of their own. So an undamaged Waymarker takes a redirected aft hit FORWARD, and swings aft only once the bow is five boxes worse off; ties go forward, the same side an unhurt hull picks. `HangarOps::leastDamagedFrontOrAft`. §3.14e. |
| D49 | A riding Waymarker can be shot, but cannot shoot (2026-09-12) | It is a ship under tow for the middle turn of its two-turn procedure: steering nothing, arcs bolted to another hull, its crew doing the manoeuvre. It remains a normal target - which is the rule, and is also what makes the aft-hit redirect (D48) a supplement rather than the only way to hurt it. `Firing::withdrawFireFromDockingRiders`, modelled on the Ancient-jump withdrawal beside it, with `weaponManager.selectWeapon` refusing the selection client-side. §3.14e. |
| D50 | The hangar-manoeuvre banner goes on the UNIT, and a new flight gets none (2026-09-12) | A fighter LAUNCH order names a phpclass and a size, so the flight it creates does not exist until the order resolves and there is nothing to label. Of the three options the user chose "on the unit only - skip new flights" rather than falling back to a banner on the carrier, which keeps *"this label is about the unit wearing it"* true with no exceptions. ⚠️ The gap is the ORDER SHAPE, not the unit's existence, so a RELAUNCHING docked flight is uncovered too; a future fix belongs in the order (a flight id), not in the reader. §3.14c. |
| D52 | A riding Waymarker KEEPS its DEW (2026-09-12) | *"Waymarkers should also not use EW on transition Docking/Launching turns, nor should ships have the opportunity to use any targeted EW on it."* The ambiguity is that unspent sensor points become DEW automatically (`convertUnusedToDEW`), and an attacker with no lock takes a doubled range penalty - so the two halves pull opposite ways. The user chose: only ACTIVE allocations stop, DEW is untouched, nobody may lock it, and the attacker eats the ordinary no-lock penalty on top. A riding Waymarker is therefore HARDER to hit than usual, not easier - it is shielded by the manoeuvre rather than exposed by it. §3.14f. |
| D51 | Left-click scrolls, right-click opens the window - on ALL docked units (2026-09-12) | *"Left-click is scroll only, right-click is open shipWindow on ALL docked units e.g. fighters, LCVs and Docking Bay ships."* This **withdraws Stage 17's own exception**, which argued that a docked FLIGHT has no hex of its own and should open its window on left-click. Its CARRIER has one, and that is the honest answer to "show me where this is". `shipManager.carrierHolding` grew the `hangarUsage.dockedFlightId` arm and `isOffBoardButOurs` lost its flight clause, leaving that predicate covering only a reinforcement in hyperspace - the one state genuinely inside no hull at all. §3.14e. |
| D35 | The Waymarker's two-turn procedure (2026-09-11) | **Deferred**, as §3.14a suggested: the bay is built and proved with Scribes, Pathfinders/Guideships and Mapmakers. The Waymarker is in the bay's class list and costs 24 boxes, so it counts in the Fleet Checker (D36), but `DockingBay::DEFERRED_SHIP_CLASSES` keeps it out of every dock, launch and deploy-dock until §3.14a lands. ⚠️ **SUPERSEDED 2026-09-12 (Stage 19):** §3.14a landed, the deferred list is gone, and its replacement `TWO_TURN_SHIP_CLASSES` is a LABEL rather than a refusal. |
| D36 | Docked ships count toward the Traveler's hangar requirement (2026-09-11) | *"Purchasing Waymarker (24), Pathfinder/Guideships (12) and Scribes (4) can help meet Traveler hangar capacity in Fleet Checker, along with Mapmaker fighters as usual. Since 24 of its 36 fighters slots are associated with its Aft Docking Bay system."* Every bought ship a Docking Bay in the fleet lists adds its box cost to that bay's `$fleetCheckCategory`, **capped at the fleet's total Docking Bay boxes** - a ship only counts for a bay it could sit in, so a lone Pathfinder cannot meet its own 6-probe minimum and three Pathfinders count 24, not 36, behind one Traveler. §3.14b. |
| D37 | The Docking Bay's launch rate, corrected (2026-09-11, review of Stage 16) | *"It should be 12 Mapmakers OR 2 Scribes OR 1 Pathfinder."* A **per-class count**, launches and recoveries together, in `DockingBay::$shipLaunchRates` (Scribe 2, Pathfinder / Guideship / Waymarker 1); the Mapmakers keep `$output`. Replaces D34's box pricing, which gave three Scribes. §3.14b "Revisions". |
| D38 | Fighters fill the side hangars first (2026-09-11) | *"Mapmakers should prioritise side hangars, since only the aft docking bay can store larger units."* `HangarOps::bayFillRank` / `HangarShared.bayFillRank`: reserved bays, then ordinary, then a Docking Bay, at every auto-fill and default-pick site. Also: docked weapons **recharge** as normal and an Energy Draining Mine restocks to its usual 3 - which the code already did; Stage 16's write-up had claimed otherwise without checking. |
| D39 | LCV deploy-docking works the Docking Bay way (2026-09-11) | *"I prefer the way we dock ships to the Traveler MORE than the current implementation of LCV hangars where we have had to allow them to stack in a hex."* The LCV same-hex exemption and the un-dock snap are removed; an LCV deploy-docks from its carrier's DOCK button and is never placed on the carrier's hex. |
| D47 | The opponent is told the IDS of the ships in a sharing bay (2026-09-12) | The docked-power grant was invisible to the Traveler's opponent, because `shipsDocked` is masked under the private-logistics gate and the grant is computed by each viewer's OWN client (D46). Of the two possible fixes the user chose **publish the ids**: the opponent's client then runs the identical function on the identical ships and reaches the identical number, which a server-side recomputation could not promise. ⚠️ On a SEPARATE key (`sharesDockedPowerIds`), because no client consumer of `shipsDocked` has ever met a partial entry and four of them read `boxes` / `phpclass` / `dockTurn` off those rows. Only on a bay that shares power. §3.16b. |
| D40 | A reinforcement Traveler brings its ships aboard (2026-09-11) | *"Travelers brought into the game via 'Manage Reinforcements' cannot select Pathfinder, Guideship, Scribe ... only Mapmakers."* A legacy-drive opener's manifest now admits the ships its Docking Bay takes, packed with the fighters in one pass on both sides, and they arrive docked. |
| D46 | The docked-power grant is client-computed and ADVISORY (2026-09-12) | §3.16 required an explicit written decision. **Advisory**, because there is no server twin of `getReactorPower` anywhere in the tree, `submitPower` validates nothing, and every power figure in Fiery Void is already a client number - a check here would be the codebase's only power validation and would still be reading a balance the server cannot compute. `DockingBay::$sharesDockedPower` therefore publishes the RULE and nothing on the server reads it. ⚠️ §3.16 asked for the tooltip to SAY it is advisory; it was written that way and the user TRIMMED that tail the same day, so the line is the figures alone - the disclosure lives here and on the faction page instead. Enforcing the balance is a cross-cutting project, not a Walkers stage. §3.16a. |
| D53 | Abduction power is a DECLARATION with a power level, not the drive's boost (2026-09-12) | A Walker drive is a legacy drive: its boost is max level 1, costs 0, and IS Jump to Hyperspace, so §3.18's "power-turns are boost levels" no longer fits. The user chose a type-'ballistic' order, damageclass `abduction`, declared by selecting the drive and clicking the enemy, whose FIRING MODE is the power level (1-4). Each level costs the drive's `powerReq` again, charged on the client's reactor balance and advisory like D46. Setting Jump to Hyperspace withdraws it. §3.18a. |
| D54 | ONLY THE INITIATOR must meet the two conditions (2026-09-12) | Contributors - a second EDJD, or a Pathfinder/Scribe - add their power whether or not the target is in THEIR field or their OEW beats its DEW. What the conditions decide is who ANCHORS the chain: it continues while at least one EDJD that met both on every turn of it meets them again. §3.18a. |
| D55 | Friendly abduction is NOT built (2026-09-12) | It was in §3.18 from an earlier rules text, not in the rules the user supplied for Stage 20, and FV has no way to allocate OEW at a friendly unit. The server refuses a friendly target. |
| D56 | The EDJD's detonation chance is HALVED (2026-09-12) | % of drive boxes lost, halved for an Ancient - the rule every other roll site applies. It rolls every turn the drive is abducting; `isJumpFailureImmune` is not overridden, so the same hull's own jump-out keeps its immunity. §3.18a. |
| D57 | What a target CARRIES never counts toward its abduction cost (2026-09-13) | *"Hangar contents shouldn't contribute to cost at all, in preview or actual cost."* Flights in hangars, ships in a Docking Bay and a rail's LCV are all left out; only ATTACHED units add their ramming factor. This also made the published preview and the locked cost ONE figure, and withdrew the first build's two-figure answer to the masking problem (trap 60). §3.18a. |
| D58 | A deactivated or destroyed drive CANCELS its abduction (2026-09-13) | *"The order itself should also be cancelled when Jump Drive is deactivated (or destroyed whilst in an attempt to abduct a ship)."* Client: `shipManager.power.onOfflineClicked` / `offlineAll` remove the order. Server: `EdjdAbduction::getCancellationReason` - drive boxes gone, its section destroyed this turn, or offline - writes a no-hold note and a "cancelled" log line; the drive delivers nothing and rolls no detonation. §3.18a. |
| D59 | No line of sight is required (2026-09-13) | The declaration inherited `weaponManager.targetShip`'s blocked-LoS skip; an abduction-capable drive now sets `ignoresLoS` per instance beside `hasSpecialTargeting`. The server never tested LoS. |
| D60 | A "Being abducted" ship-window banner (2026-09-13) | Purple, off the same `JumpEngine.getAbductionChain` reader as the tooltip line, in `ShipWindow.js` beside "Jumping to Hyperspace". |
| D61 | An EDJD cannot abduct a FIGHTER FLIGHT (2026-09-13, play test 4352) | *"An EDJD cannot target fighter flights."* The first build did not block it on either side. Client: `JumpEngine.doSpecialTargeting` refuses a `flight` target with a message; server: `EdjdAbduction::getDeclarationBlock` returns "a fighter flight cannot be abducted", after the team check. A flight ATTACHED to a hull still adds its RF to that hull's cost (D57) and leaves with it. §3.18a. |
| D62 | A held abduction CONTINUES by itself (2026-09-13, play test 4352) | *"If an abduction is successfully initiated then the relevant Jump Drive should automatically keep targeting until deactivated/destroyed."* Client-seeded, the way `repeatLastTurnPower` carries power forward: `JumpEngine::getLatestAbductionHold` publishes the drive's latest note as `abductionLastHold` {turn, targetid, level} when it delivered power, and `JumpEngine.continueAbductions` re-declares it at the start of Initial Orders if that turn was last turn, the chain is still published, and `canSelectForAbduction` passes (so deactivated, destroyed, uncharged or jumping drives stop). Supporting drives continue too. Not a server-written row: `submitFireorders` only INSERTS, so a pre-written row would duplicate on commit and survive a CANCEL. ⚠️ A page reload during Initial Orders re-seeds a cancelled order. §3.18a. |
| D63 | An abduction COOLDOWN (2026-09-13) | User rulings: each drive - EDJD and supporting alike - recharges for its OWN jump delay (Wanderer 4, Traveler 6, Waymarker 6, Guideship 3, Pathfinder 4, Scribe 8), counted from the turn after THAT drive last took part, however its part ended: completed, collapsed on a failed condition, the target destroyed, or cancelled - but only a COMMITTED declaration counts, never one withdrawn before Initial Orders was committed. Built as `JumpEngine::getAbductionRechargeLoad` (latest `EDJD` note of any value; 0 on its own turn, then 1.. up to the delay), folded into `getVortexRechargeLoad` by `min` so every other engine is unchanged and the published `turnsloaded` carries it. A drive that delivered power last turn may still CONTINUE that same unit while recharging (`isContinuingAbduction` / client `getContinuableAbductionTargetId`) - so mid-chain it reads 1/delay and keeps going. The resolver now writes a no-hold note when the target is off the board, which is what makes "target destroyed" count. The Walker's own boost jump is not charge-gated and is unaffected. §3.18a. |
| D64 | The FIRST turn only TAKES HOLD (2026-09-13, play test) | *"Power Turns should not start accumulating until the turn after the initial targeting has been successful."* It was possible to abduct a small ship on the declaration turn. Now a turn that STARTS a chain (fresh, or a restart after the prior anchors all failed) locks the cost and records the anchors but writes 0 halves for every declaration, and logs "takes hold ... power can be applied from next turn". Power counts from the next turn, which is also the first turn a SUPPORTING drive may declare: `EdjdAbduction::isHeldByTeam` (a chain as of last turn with an anchor on the shooter's team) at submit, `JumpEngine.getAbductionChain` on the client. Client `isAbductionPowered(order)` = a chain stands against the order's target: false -> no reactor draw, no halves, and `JumpEngineMenu` hides the Power row and shows a "Targeting" note. ⚠️ "Held" moved from `halves > 0` to `since > 0` in `getLatestAbductionHold` and `isContinuingAbduction`, or the targeting turn would never be continued. Read at the TARGET level: a second EDJD joining an abduction another EDJD took hold of applies power at once. A restart can still surprise a player - power paid on a turn the old anchors all failed is lost, which the client cannot foresee. §3.18a. |
| D65 | The field and EW conditions are for TAKING HOLD only (2026-09-13) | *"The Energy Draining Field and more OEW than DEW conditions are only relevant for the initial targeting of a vessel for abduction. They should not be checked in subsequent turns after Abduction has begun."* `EdjdAbduction::resolveTarget` now decides CONTINUING first: a chain that stood last turn continues while at least one EDJD anchored on every turn of it has a WORKING declaration (not cancelled, not jumping) - `getConditionBlock` is never called. Only when no such EDJD is left is the turn a new attempt, judged on the conditions: a restart ("takes hold", the earlier abduction "lapsed") or a collapse naming the reason. So a chain now ends only when its holding EDJDs stop - a gap, a cancel, deactivation or destruction - never because the target moved out of the field or out-jammed it. This also retires the D64 surprise for the common case: a restart now needs every holding EDJD to have STOPPED, not merely failed a check. The menu's powered note no longer mentions the conditions. §3.18a. |
| D66 | TERRAIN can be abducted (2026-09-20) | Q13's *"out of scope for now"* reopened at the user's request. Four rulings taken together: the cost is the **ordinary `ceil(RF / 50)`**, not the rules table's `10 x radius³` (FV terrain has no radius, only a `Huge` hex reach that is 0 on the three round asteroids, so the formula cannot be expressed - and the ramming-factor figures already make a small asteroid a two-turn job and a moon effectively impossible); **everything but a jump point** is a legal target - asteroids, moons, fixed jump gates and shipyards, with `SpawnJumpPoint` and its two subclasses refused by name and the Energy Draining Mine's orb refused for free by `unTargetable`; the **EW condition stays**, which on a unit with no EW means one OEW point; and terrain **belongs to nobody**, so the "enemy only" rule does not apply to it - the convention `gamedata.isMyorMyTeamShip` has always used. The one rule that changes shape is the FIELD: a multi-hex unit needs its WHOLE footprint inside the connected field. §3.18b. |

Everything below assumes these.

---

## 1. What already exists — the seams we build on

This faction needs remarkably little new machinery, because four of the hardest requirements
already have a proven implementation in the tree. Verified 2026-09-02.

### 1.1 Split shots — already fully wired, server and client
`canSplitShots` + `maxVariableShots` + `specialHitChanceCalculation` is a complete, live feature
used by the Slicer Beams, Ballistic Torpedo and Vorlon Discharge Gun.

- Server reference implementation: `MolecularSlicerBeamL`, [molecular.php:834](source/server/model/weapons/molecular.php#L834) —
  read its class comment before writing any of ours, it is the best-documented weapon in the tree.
- Its `beforeFiringOrderResolution` shows the **allocation-token pattern**: the client encodes a
  per-shot payload into `FireOrder->notes` (`MSB|d:<dice>|s:<set>`), the server re-clamps it against
  the real pool and strips the token. Both the Lightning Array's combined fire and the SCT's
  waypoints use this channel.
- Client entry points: `weaponManager.targetShip` → `doMultipleFireOrders`, and — the one we need
  for the SCT — `weaponManager.targetHex` → **`doMultipleHexFireOrders`**,
  [weaponManager.js:3843](source/public/client/weaponManager.js#L3843). Three weapons already
  implement it (`GravityNet`, `ProximityLaserNew`, `EWGraviticTractingRod`).
- ⚠️ `$this->guns` padding in `beforeFiringOrderResolution` is load-bearing for
  `Firing::automateIntercept`. Copy the Slicer's comment and its **skip for manual `intercept`
  orders** — that exact bug produced 44 defensive shots against 4 missiles in game 4306.

### 1.2 Accelerator weapons — already a pattern, not a feature
Damage keyed off `$this->turnsloaded`, with `$normalload` as the charge ceiling.
Reference: `LaserAccelerator`, [lasers.php:1378](source/server/model/weapons/lasers.php#L1378)
(`loadingtime = 2, normalload = 4`, `getDamage`/`setMinDamage`/`setMaxDamage` switch on
`turnsloaded`, and `stripForJson` re-publishes the min/max arrays so the client tooltip tracks).

**"Does not begin the game fully charged"** is a one-method override:
`Weapon::getStartLoading()` ([weapon.php:1011](source/server/model/weapons/weapon.php#L1011))
returns `new WeaponLoading($this->getNormalLoad(), …)`. Return `0` in the first slot instead.
Nothing else needs to change — `onConstructed` writes that straight into `tac_systemdata`.

### 1.3 Stored, launch-some-or-all ballistics — already a pattern
The Energy Draining Mine's "store up to 3, launch any number" is exactly `BallisticTorpedo`,
[torpedo.php:79](source/server/model/weapons/torpedo.php#L79):
`loadingtime = 1, normalload = N, canSplitShots = true, ballistic = true, hextarget = true`,
with the client's `initializationUpdate` setting `maxVariableShots = this.turnsloaded`
([torpedo.js:15](source/public/client/model/weapon/torpedo.js#L15)) and `checkFinished`
closing the menu when the pool is spent.

### 1.4 A computed hex-set published to the client — already a pattern
`TacGamedata::$blockedHexes` ([TacGamedata.php:55](source/server/model/TacGamedata.php#L55)) is
built once by `setBlockedHexes()` ([TacGamedata.php:1915](source/server/model/TacGamedata.php#L1915))
during `onConstructed`, serialised at [TacGamedata.php:181](source/server/model/TacGamedata.php#L181),
and consumed by BOTH `Weapon::isLoSBlocked` ([weapon.php:3025](source/server/model/weapons/weapon.php#L3025))
and the client's identical `mathlib.isLoSBlocked` inside `targetHex`.

**The EDF hex map is the same shape and should be built the same way.** That gives the client a
free, exact mirror of the to-hit penalty with no new sync mechanism.

### 1.5 Hex-line tracing — already written
`getHexLine(OffsetCoordinate $start, OffsetCoordinate $end)`, a cube-interpolating static on the
Spatial Cutter, [specialWeapons.php:11101](source/server/model/weapons/specialWeapons.php#L11101).
Used by both the EDF targeting penalty and every SCT path segment. **DONE (Stage 0):** it now
lives on `HexZone` (`HexZone::line`), with `SpatialCutter::getHexLine` kept as a delegating alias.

### 1.6 The critical machinery already does everything the three crit tables ask for
`ShipSystem::testCritical` ([ShipSystem.php:1370](source/server/model/systems/ShipSystem.php#L1370))
already rolls `Dice::d(20) + floor(getTotalDamage()) + $add`, which is verbatim
"roll a d20 and add the number of damaged boxes". Successive-roll escalation is free:
`hasCritical($type, $turn)` returns a **count**, not a bool.

Criticals carry a `param` (`tac_critical.param varchar(200)`,
[emptyDatabase.sql:195](db/emptyDatabase.sql#L195)) and `ShipSystem::sumCriticalParam` sums it —
that is the persistence channel for the EDF's rolled drain magnitudes. **No schema change anywhere
in this project.**

### 1.7 The drain's four targets each already have exactly one choke-point
| Rules effect | Where it plugs in |
|---|---|
| Initiative | `BaseShip::getCommonIniModifiers` ([ShipClasses.php:317](source/server/model/ships/ShipClasses.php#L317)) — already reads a stack of crits off `CnC`. ⚠️ **FV initiative is d100: every modifier here is 5× its tabletop value.** |
| Total EW | `EW::getScannerOutput` ([EW.php:42](source/server/handlers/EW.php#L42)) — already subtracts `RestrictedEW` and `SensorLoss`. |
| Energy | `Reactor` ([baseSystems.php:1018](source/server/model/systems/baseSystems.php#L1018)) — `outputType = "power"`, honours `outputMod`. |
| Free thrust | `Engine` ([baseSystems.php:1495](source/server/model/systems/baseSystems.php#L1495)) — `outputType = "thrust"`, honours `outputMod`; flights use `$ship->freethrust`. |

### 1.8 Per-system enhancements — machinery exists, one gate blocks us
`Enhancements::setSystemEnhancementOptions` ([Enhancements.php:3441](source/server/model/ships/Enhancements.php#L3441))
plus the `eligible`/`limit`/`price`/`apply` registry quartet is exactly the right shape for
Wide-Beam and EDF Range — and the per-array pricing the rules demand ("the player pays to enhance
each one separately") is only possible per-system.

⚠️ **[Enhancements.php:3445](source/server/model/ships/Enhancements.php#L3445) is
`if($ship->factionAge > 2) return;`** — a blanket refusal for Ancients. See §3.3 for the fix.

### 1.9 Zone geometry — already written, in the wrong place
`GraviticMine` carries a proven, exhaustively-verified hex-zone implementation:
`convexHull` ([gravitic.php:2796](source/server/model/weapons/gravitic.php#L2796)),
`pointInPolygon` ([gravitic.php:2825](source/server/model/weapons/gravitic.php#L2825)),
`getHexTouchTolerance` ([gravitic.php:2773](source/server/model/weapons/gravitic.php#L2773)) and
`isUnitInShearingZone` ([gravitic.php:2706](source/server/model/weapons/gravitic.php#L2706)).
All `private`. The EDN needs them; Stage 0 extracts them (see §2.3).

### 1.10 Adding the faction itself is cheap
A ships directory `source/server/model/ships/walkers/`, `$this->faction = 'Walkers of Sigma-957'`
on each hull (`ShipLoader::getFactionDirMap` derives the mapping at runtime), one tier line in
`gamelobby.js`'s switch ([gamelobby.js:120](source/public/client/gamelobby.js#L120) neighbourhood —
`'Tier Ancients'`), plus optional prose in `factions-tiers.php` and
`ammo-options-enhancements.php`. `$factionAge = 3` (Ancient).

⚠️ Filename must equal class name or the ship **silently does not exist**.

**As built (Stage 1, 2026-09-03).** It was that cheap: `Traveler.php` already existed, so the whole
stage was the faction string (`Walker of` → `Walkers of Sigma-957`), the `gamelobby.js` tier case,
and a statics regen. Three things worth knowing next time:

- **The lobby reads `source/public/static/json/<faction>.json`**, written by
  `generateStaticShipFile.php` and keyed on the faction string. A faction **rename leaves the old
  file behind**, and the lobby then lists both spellings as separate factions with the same hull in
  each. `source/public/static/` is gitignored, so nothing warns you. Delete the stale file by hand.
- **`fvbuild.ps1 -Statics` is not optional** for a new hull — without it the ship exists on the
  server and is invisible in the lobby.
- **`Traveler`'s hit chart already names Lightning Array, Chromatic Pulse Driver and Energy
  Draining Field**, which do not exist yet, and the ship-data validator fails on all five entries
  ("every hit here is silently rerouted to Structure"). They are **commented out in the hull with
  `//STAGE n` markers**; each of Stages 2, 3 and 4 uncomments its own row when it adds the system.
  Restoring a row is a two-character edit — do not forget it, a system with no hit-chart entry can
  never be hit.

---

## 2. The three new shared abstractions

Deliberately only three. Every system below is built out of these plus existing patterns.

### 2.1 `EdfField` — the field as a published hex map

**A new interface, not a base class:**
```php
interface EdfSource {          // implemented by systems AND by the EDM terrain unit
    public function getEdfRadius($turn);   // 0 for a Net; the crit-reduced radius otherwise
    public function isEdfActive($turn);    // offline / destroyed / voluntarily deactivated
}
```

**`TacGamedata::$edfHexes`** — built once in `onConstructed` immediately after `setBlockedHexes()`,
same shape, same lifecycle, serialised in `stripForJson` alongside it:

```
$edfHexes = [ 'q,r' => [ 'teams' => [teamId => true], ... ], ... ]
```

Keyed by hex so overlapping fields collapse for free — which is precisely the rules requirement
that *"overlapping hexes are only counted once"* for the targeting penalty. `teams` records which
teams' fields cover the hex, which is what makes *"the rest of the fleet of the ship deploying the
EDF is immune"* a single array lookup.

**Why a map and not per-ship distance checks:** the targeting penalty has to be evaluated for
every fire order in the game, and the client has to mirror it exactly to predict hit chance.
`blockedHexes` proves the map-once-publish-once shape works and stays in sync.

**Three consumers:**
1. `Weapon::calculateHitBase` — walk `getHexLine(shooterPos, targetPos)`, count hexes in
   `$edfHexes` not covered by the shooter's own team, `×2` per hex when
   `$this->weaponClass` is `Plasma` or `Antimatter` **and** `$this->factionAge < 3`.
   Client mirror in `weaponManager.calculateHitChange`.
2. `Weapon::doCollateralDamage` ([weapon.php:2344](source/server/model/weapons/weapon.php#L2344)) —
   the flash-suppression and 25%/50% rules.
3. The renderer — a translucent field overlay, reusing `HexagonSprite`.

**Not per-viewer**, exactly like `blockedHexes` — which is correct, because an EDF is a visible
phenomenon. **D5: concealment is out of scope**; no Walker hull has a stealth function, so a
concealed ship disclosing itself through its own published field cannot happen.

⚠️ **That is a property of the FLEET, not of the design.** The moment an EDF is granted to a hull
that can conceal itself — a Walker refit, a custom hull, a `whatif` pack — the map discloses its
position to every viewer, silently and with no error anywhere. If that day comes the answer is that
a concealed ship projects no field, and the test belongs in `setEdfHexes()` next to the
`isReinforcement()` guard `setBlockedHexes()` already carries for exactly this class of leak
([TacGamedata.php:1921](source/server/model/TacGamedata.php#L1921)).

⚠️ **Per-load static reset.** Any memoisation added here must be reset in
`DBManager::getSystemDataForShips`, immediately before the `onIndividualNotesLoaded` sweep — one
HTTP request loads gamedata **twice** (`Manager::advanceGameState`, then the phase's own
`getTacGamedata`) and a guard that is not reset there leaks between the two. This is the single
most expensive trap in this codebase; it cost a whole investigation on the Gravitic Augmenter.

### 2.2 `EdfExposure` — the drain, as param-carrying one-turn criticals

**Where it runs:** `Criticals::setCriticals` pass 2 calls `criticalPhaseEffects($ship, $gamedata)`
on every system of every ship ([criticals.php:96](source/server/handlers/criticals.php#L96)) —
which *is* the rules' "Critical Hit Step". The **EDF system on the Walker ship** owns the sweep, in
the `GraviticMineHandler` shape: one resolver, guarded by a static `$resolvedTurn` so N fields
resolve once and *"additional fields do not provide cumulative modifiers"* is structural rather
than a special case.

**Six new `Critical` subclasses** in `cricialClasses.php`, all `oneturn = true`:

| Class | Lands on | Read by |
|---|---|---|
| `EdfThrustDrain` (param) | victim's `Engine` | `getOutput()` via `sumCriticalParam` |
| `EdfPowerDrain` (param) | victim's `Reactor` | `getOutput()` via `sumCriticalParam` |
| `EdfIniDrain` (param) | victim's `CnC` (flight: sample fighter) | `getCommonIniModifiers` — **×5**, floor −100 (= tabletop −20) |
| `EdfEwDrain` (param) | victim's `CnC` | `EW::getScannerOutput` |
| `EdfFighterGrounded` | flight's sample fighter | fire-order validation — *"will not be able to shoot the next turn"* |
| `EdfExposed` (`forInfo`, param = consecutive-turn count) | victim's `CnC` / sample fighter | the resolver itself |

**The escalation counter needs no new storage.** `EdfExposed` carries the running count in its
`param`; turn N+1 reads turn N's marker and adds 1, or starts at 1 if absent. Enormous units are
clamped: *"the modifiers are limited to the first die and do not increase"* → always roll one die,
never read the marker.

**Fighter dropout** is a separate branch, not a crit: `Fighter::testCritical`
([fighter.php:274](source/server/model/systems/fighter.php#L274)) rolls `Dice::d(10)` and compares
against remaining health. The EDF needs `2d10`, `+1d10` per successive turn. Cleanest without
touching the signature: set `$fighter->critRollMod += Dice::d(10) * $consecutiveTurns` from the
resolver — `critRollMod` is already summed into that roll and is described in-file as the
"one-time penalty to dropout roll".

⚠️ **`advance()` has already set the NEXT phase before its ship loop.** Never branch on
`$gamedata->phase` inside the resolver. `criticalPhaseEffects` is reached from
`FireGamePhase::advance`, which is safe (it re-loads gamedata), but pass an explicit checkpoint if
this ever needs to run from a second site.

### 2.3 `HexZone` — extracting what already works — **BUILT 2026-09-03 (Stage 0)**

Two pure moves into `source/server/lib/HexZone.php`, with the originals delegating:

- `HexZone::line($start, $end)` ← `SpatialCutter::getHexLine` (plus its three private cube
  helpers, which had no other caller)
- `HexZone::containsUnit()`, `::hull()`, `::pointInPolygon()`, `::touchTolerance()`,
  `::pointToSegmentDistance()` ← the five `GraviticMine` privates

⚠️ **This is the only place in the plan that touches existing, working, subtle code**, and the
Gravitic Mine geometry is subtle in ways that cost real investigation time — the per-angle
tolerance (0.866→1.000, not the constant `sqrt(3)/2` it started as) and a `1e-9` epsilon that is
load-bearing on its own (game 7027 T1 sheared on a `+1.1e-15` margin). It was moved **byte-for-byte
with zero logic edits**: only the method names, the `static`/visibility keywords and the `$this->`
→ `self::` calls changed. The comments and local names inside `containsUnit()` still speak of
"mines" for exactly that reason — read "mine" as "any hex position defining the zone".

**How it was proven, and why the harness alone was not enough.** The local replay corpus does
exercise the shearing path, but barely: exactly **one** game touches it — game 4299, with two
`graviticShear` fire orders (`grep -rl graviticShear tests/replay/baseline/`). One mine layout is
one sample of a geometry whose whole difficulty is the awkward cases — collinear mines, a unit
sitting exactly on the tolerance, a corner-clipping line — so a green `check` was necessary and
nowhere near sufficient. The real proof was a throwaway differential test: the pre-move bodies were
pulled out of `git show HEAD:` into a `HexZoneRef` class and run side by side with the moved ones
over 13,504 randomised cases — 4,000 multi-hex lines, 7,504 zone tests and 2,000 direct hull /
point-in-polygon / tolerance / segment-distance comparisons — with **zero** mismatches. The test
asserted its own branch coverage (2-point in *and* out, 3+-point in *and* out, collinear in *and*
out, plus the same-hex and 0/1-point degenerate inputs) and exited `INCONCLUSIVE` if any bucket
stayed empty, because a differential test that only ever takes one path passes while testing
nothing. **Repeat that method for any future move of this code.**

Replay harness before and after: identical — 133 passed, 1 failed (game 4325, a pre-existing
clean-tree failure; the known-failure game recorded in memory has drifted from 4318 to 4325).

---

## 3. Per-system design

### 3.1 / 3.2 Lightning Array + Medium Lightning Array — **BUILT 2026-09-03 (Stage 2)**

`class LightningArray extends Weapon` and `class MediumLightningArray extends LightningArray`, both
in `specialWeapons.php` (server) and `special.js` (client) — that is where Walker weaponry goes,
because it is all `weaponClass = "Electromagnetic"` and there is no `electromagnetic.php`.

**Stats are the real control-sheet numbers** (user, 2026-09-03). Still unconfirmed and marked so
in-file: `$damageType` ("Standard" — switch to Raking if the sheet says otherwise), `$priority` and
`$loadingtime`. Re-statting stays a table edit: no method hard-codes a game number, the tooltip rows
are generated from the tables, and `setMinDamage`/`setMaxDamage` are derived from them too.

⚠️ **`$fireControl` and `$rangePenalty` must equal ROW 1 of their combined tables.** They are the
single-discharge profile and they are what the "Fire control" and "Range penalty" tooltip lines
report. Because the combined tables override them per shot, a mismatch does **not** change what gets
rolled — it just makes the ship window quote numbers the weapon never uses, which is worse than a
visible bug. They were out of step when the control sheet first landed and are now aligned.

**Combined fire, as built — REVISED 2026-09-03 after play testing.** ⚠️ **The allocation dialog is
gone.** These are ordinary split-shot weapons now, exactly like the Vorlon Discharge Gun: a gun IS a
discharge, one click declares one, and in Combined Fire mode a further click on the **same target**
fuses another discharge into the shot already standing there rather than declaring a second one.
Four clicks on one ship produce ONE 4-discharge shot; four clicks on four ships produce four single
ones. Single Shots mode never fuses. (The first build asked with a `confirm.askForMultipleValues`
dialog borrowed from `MolecularSlicerBeamL`; the user's verdict was that the guns/discharges
distinction it implied was confusion, not a feature.)

The count rides to the server in `->shots` **and nowhere else** — the same field every other
split-shot weapon carries its shot count in, and a whitelisted `FireOrder` constructor argument, so
it survives the POST rebuild on both the ship and the fighter branch. ⭐ The old `LA|n:<count>` token
in `->notes` is **removed on both sides**, because `->notes` has a job already: `notes` must read
exactly `"Split"` and `damageclass` exactly `'Sweeping'`, or the shot never appears in the target's
INCOMING list (`weaponManager.getAllBallisticsAgainst` admits a type-`normal` order **only** on
`damageclass === 'Sweeping'`) and its ballistic line is updated rather than re-created
(`BallisticIconContainer` keys that on `notes === 'Split'`). Those two literals are the split-shot
contract, not decoration — a weapon that invents its own values silently vanishes from the list the
shooter uses to see what it has committed (see "AND THAT ROW IS FOR THE SHOOTER" below).
`beforeFiringOrderResolution` re-clamps `n` against the tabled rows and then against what is
left in the pool, stashes it per order id, and sets `->shots = 1`. An order the pool cannot pay for
gets **0 discharges and does 0 damage** — the Slicer's behaviour, chosen because a 0-damage line in
the combat log is a loud failure and a silently-dropped order is not.

⚠️ **Growing a shot works by REPLACING its order, not by mutating it.** `doMultipleFireOrders` splices
the standing order out and returns a replacement carrying one more discharge, so the declaration stays
on `targetShip`'s ordinary push → `checkFinished()` → unselect path. Pushing from inside the hook and
unselecting there — which the dialog version did — splices `gamedata.selectedSystems` while
`targetShip` is still iterating it. The regenerated id is identical, because the array is the same
length again. ⚠️ `checkFinished()` therefore also has to call `updateGunAccounting()`: it is the first
moment the pushed order is visible to the weapon, and `->guns` must be fresh before anything reads the
manual-intercept cap.

**Fusing moves THREE things, off three tables keyed by the fused count:** `$combinedDamageArray`,
`$combinedFireControlArray` and `$combinedRangePenaltyArray`. They are applied by two different
mechanisms, and the difference matters:

- **Fire control — a DELTA on `$fireOrder->needed`, not a swap of `$this->fireControl`.**
  `fireControl` is read in several places inside `parent::calculateHitBase()`, and a
  temporarily-mutated copy would be a per-instance mutation of a property the blueprint shares. So
  the parent runs untouched and `needed` is then adjusted by
  `(combinedFC[n][fcIndex] - fireControl[fcIndex]) * 5` (the ×5 is d20 table → d100 roll), guarded
  by `needed <= 0` so the parent's auto-miss marker is never accidentally un-missed.
- **Range penalty — a real override of `calculateRangePenalty($distance)`, NOT a delta.** ⭐ This one
  cannot be a delta: the parent derives the **no-lock and jammer** modifiers from the range penalty,
  and in the `doubleRangeIfNoLock` branch calls `calculateRangePenalty` a second and third time at
  modified distances ([weapon.php:1787](source/server/model/weapons/weapon.php#L1787)). A flat delta
  on `needed` would have moved the base penalty and left both derivatives computing off the
  single-discharge value. Overriding the method keeps all three consistent for free.

  `calculateRangePenalty` is handed **nothing but a distance** on the server, so the fused count
  reaches it out of band: `calculateHitBase` publishes `$activeCombinedCount` for the duration of the
  parent call and clears it in a **`finally`** — without that, a parent that threw would leave a stale
  count standing and silently apply it to the *next* shot the weapon resolved.

  ⭐ **The client mirror needed the argument threaded instead.** `calculateSpecialRangePenalty` used to
  get only a distance too, which was survivable while the dialog set `pendingCombinedCount` around the
  one call that mattered — but with the dialog gone, the number the player reads *before* clicking has
  to describe the shot the click will produce, and that is only knowable from the target. So
  `weaponManager.calculateRangePenalty(distance, weapon, target, calledid, fireOrder)` now threads all
  three down (`computeJammerNoLock` and `computeShotModifiers` too), and every other weapon simply
  ignores the extra arguments. Both mirrors then resolve the count the SAME way, via
  `resolveCombinedCount`, so the fire-control half and the range half always describe one shot:
  1. `pendingCombinedCount` — set only while a click is being turned into an order;
  2. the fire order passed in — the INCOMING list and `calculataBallisticHitChange` both supply one,
     and a **declared** shot must read at its own count, never at a look-ahead, because the ship being
     shot at is deciding what to spend on interception;
  3. otherwise `getPreviewCombinedCount(target)` — what the NEXT click on that ship would fire.

**The Medium's accelerator rule falls out of the pool.** Its pool is `turnsloaded` capped at
`normalload`, so at one turn of charge there is exactly one discharge and *"may not combine until
charged two turns"* needs no rule of its own. ⚠️ **`getStartLoading()` returns loading `1`, not `0`**
(fixed 2026-09-03, game 4329): "does not begin the scenario **fully** charged" means 1/2, and 0 is
below `getLoadingTime()`, so the array could not fire at all on turn 1 and read "0/2" in the ship
window. The full Array is untouched and still starts ready (`getNormalLoad()` falls back to
`getLoadingTime()` = 1 when `normalload` is 0).

**Two firing modes.** `1 Combined Fire` (default) fuses repeat clicks on one target; `2 Single Shots`
makes every click a separate one-discharge order, which is what you want against a fighter flight
where four small shots beat one large one — and the fire-control table makes that concrete, since
fusing costs 6 points against fighters (8 → 2) while *gaining* 2 against capitals (4 → 6).
`beforeFiringOrderResolution` forces a mode-2 order's count to 1 and **ignores** `->shots` on it
rather than clamping it, so a stale or hand-edited client cannot smuggle a fused shot through the
cheap mode. ⚠️ It reads `$order->firingMode`, never `$this->firingMode`: `prepareFiring` calls
`changeFiringMode` only *after* this method, so the weapon's own mode is still last turn's here —
the same trap the Slicer's class comment records. Wide-Beam therefore becomes mode **3** at Stage 8.

⚠️ **`canSplitShots` is now ALWAYS true** — both modes, every pool size, including a pool of 1. It is
what routes a click through `doMultipleFireOrders` at all; the moment it goes false,
`weaponManager.targetShip` falls through to the ordinary path, which declares `weapon.guns` orders of
`defaultShots` each and stamps neither `'Sweeping'` nor `"Split"` — so a one-discharge Medium Array
declared four shots *and* they were invisible to the target's INCOMING list. The first build turned it
off at a pool of 1 to mean "nothing to divide"; with the dialog gone there is nothing to divide
anyway, and the flag only ever meant "this weapon declares its own orders".

**The INCOMING row COUNTS the discharges.** A combined shot is one fire order carrying several
discharges, so the row would read "1x Lightning Array (Combined Fire)" for a 4-discharge bolt.
`ShipTooltipBallisticsMenu` gained `shotsInGroup()` beside the existing Slicer `diceSuffix()`: a
weapon may implement `getIncomingShotCount(fireOrder)` and everything else keeps counting members. It
now reads "**4x** Lightning Array (Combined Fire)". ⚠️ Opt-in rather than a sum of `->shots`, because
a Molecular Slicer order carries DICE there and must stay one shot; and the group's `amount` stays the
MEMBER count, since that is what the disclosure caret opens into.

⭐⭐ **AND THAT ROW IS FOR THE SHOOTER, NOT THE DEFENDER** (user's ruling, 2026-09-03). A direct-fire
weapon declares and resolves in the same phase, so the opponent's gamedata never carries the order in
time: **they cannot see it and cannot manually intercept it.** Manual interception is a BALLISTIC
affair — declared in Initial Orders, resolved a phase later, visible in between — and that gap is the
whole mechanism. The INCOMING list on an enemy tooltip during Firing is a tally of what YOU have
committed to that ship. **Do not over-build defender-facing explanation into a Firing-phase weapon's
row.** The first pass gave the array a `getIncomingLabelSuffix` hook that wrote " (3 discharges)" so
"the defender could price interception"; that audience does not exist, and it was replaced by the
counting hook above. The `'Sweeping'` damageclass is still required — it is what puts the row there
for the shooter at all.

**Withdrawing a shot PEELS one discharge.** ⭐ "Remove a firing order" on a 4-discharge combined shot
makes it a 3-discharge shot; it does not delete the order. Deleting it would hand four discharges back
for one click on a button that says "remove **a** firing order", and there is no way to put three of
them back except by re-declaring. The order's stored `chance` is re-priced at the new count, because
fusing moves the fire control and the range penalty — a peeled shot still quoting the 4-discharge
number would show a hit chance the server will not roll. A Single Shots order is one discharge
already, so it just goes.

### multiModeSplit — the mode is a per-SHOT choice

`protected $multiModeSplit = true`. Fuse a couple of combined shots, switch to Single Shots, pepper a
flight with the rest, switch back. Without it `weaponManager.onModeClicked` / `onSetModeClicked` and
`SystemInfoButtons.canChangeFiringMode` all lock the firing-mode selector the moment the first order
is declared, which would make the two modes an either/or choice for the whole turn.

⚠️ **It is `protected` on `Weapon` and the base `stripForJson` does NOT publish it** — the override
has to pass `$strippedSystem->multiModeSplit`, or the client never sees the flag and the lock stays
on. Nothing on the server reads it: `Firing::prepareFiring` already calls `changeFiringMode` per ORDER
before resolving each shot, which is exactly why `beforeFiringOrderResolution` must read
`$order->firingMode` and never `$this->firingMode`.

⚠️ **It also hands WITHDRAWAL to the weapon.** `weaponManager.removeFiringOrderMulti` and
`removeFiringOrder` divert to `removeMultiModeSplit(ship, target)` / `removeAllMultiModeSplit(ship)`
and **return immediately** — no `SystemDataChanged`, no `SplitOrderRemoved`, no flight-movement
redraw, so the weapon fires those itself. `Weapon.prototype`'s versions are **no-ops**, so setting the
flag without overriding both silently kills every remove button.

Withdrawal takes the shot from the mode the weapon is sitting in, matching
`weaponManager.hasOrderForMode`, which is what gates the ship-window button. ⚠️ But the ENEMY-TOOLTIP
button is gated on `hasTargetedThisShip` with **no mode test**, so a mode-only search would leave it
doing nothing at all when the shot at that ship was declared in the other mode — hence
`findWithdrawableOrder` prefers the current mode and falls back to any. It filters to `type ===
'normal'`: a `selfIntercept` marker has its own button and must not be eaten by this one.

### ⭐ Interception: the engine counts ORDERS, this weapon spends DISCHARGES

Both arrays have an intercept rating, and that makes the engine's gun accounting wrong unless it is
corrected — because one combined shot of four is a **single fire order** that empties the whole pool.
Left alone, `Firing::isValidInterceptor` and `Firing::automateIntercept` would both read three
discharges as still available. This is the Slicer's `$guns`-padding problem in a different currency.

Both sites do their arithmetic against `$this->guns`, so `beforeFiringOrderResolution` rewrites it
every turn:

```
guns = pool − dischargesSpentOffensively + numberOfOffensiveOrders
```

With `O` offensive orders, `M` manual `intercept` orders, `S` `selfIntercept` markers and
`D` discharges spent offensively, and taking a manual intercept as costing one discharge and a marker
as costing nothing:

| site | expression | with the rewrite |
|---|---|---|
| `isValidInterceptor` refuses when | `O + M >= guns` | ⟺ `M >= pool − D` ⟺ nothing left ✔ |
| `automateIntercept` grants | `guns − O − M` | `= pool − D − M` = discharges left ✔ |

`S` cancels out of both, which is *why* a marker is free. The client mirrors the identical formula in
`updateGunAccounting()`, because `weaponManager`'s manual-intercept cap
(`counts.offensive + counts.intercept >= weapon.guns`) counts orders the same way.

⚠️ **The client cannot wait for `initializationUpdate`** to do that — it only runs when a system icon
renders ([[arch_lazy_window_side_effects]]), and the player reaches the INCOMING list without
necessarily re-rendering anything. So `resolveFireOrder` and `doMultipleSelfIntercept` call it
directly too.

**Consent differs between the two, and only one of them needed hooks.** `isValidInterceptor` demands
a `selfIntercept` marker when `max(loadingtime, normalload) > 1`. That is 1 for the full Array — it is
simply auto-assigned with whatever it did not fire — and **2 for the Medium**, which therefore must
consent. Because `canSplitShots` is true, `weaponManager.canSelfInterceptSingle` routes that question
through `checkSelfInterceptSystem()`, which is **`false` on `ShipSystem.prototype`**: without the
override the Medium could never consent and so could never defend at all. One marker is consent for
the whole weapon (a second buys nothing, since `S` cancels), and defensive orders are stamped Single
Shots mode.


**How it was proven — FIRST BUILD.** Four throwaway functional tests. The two that survive
re-statting are the range-penalty spy test and the client mirror; the other two were re-run after
every change.

- **Server, mechanics (33 checks):** the hull mounts it and `getSystemsByNameLoc("Lightning Array", 1)`
  — the lookup the hit chart itself uses — finds it and does *not* find it in another section; the
  allocation parsed and stripped; `->shots` reset; over-allocation clamped mid-volley; a spent pool
  yields 0; `n=99` clamps to the largest tabled row; an order that never went through
  `beforeFiringOrderResolution` does 0 rather than firing free; the Medium starts uncharged while the
  full Array does not.
- **Server, range penalty (18 checks):** ⭐ **the structural check a table test cannot make** — a spy
  subclass recorded every `calculateRangePenalty` call the *parent* made during a real
  `calculateHitBase` on a two-ship gamedata, and confirmed the parent **does** reach the override,
  **with the order's fused count live**. The test reports INCONCLUSIVE rather than PASS if the parent
  short-circuits before getting there, because a spy that was never called proves nothing. Plus: the
  transient is cleared on return, and cleared again when the parent path aborts.
- **Server, refinements (44 checks):** Single Shots forced to one discharge even when the order claims
  four; mixed-mode turns; and ⭐ **the gun accounting checked against the REAL engine** —
  `Firing::isValidInterceptor` invoked by reflection across ten scenarios (idle, 1–4 single shots,
  combined 2 and 4, manual intercepts, marker-only, marker + combined) with `automateIntercept`'s
  budget expression reproduced verbatim beside it. Both must agree with the discharges actually left.
  A control case proves the rewrite is what fixes it: without it, a combined-4 shot leaves **3 phantom
  intercepts**; with it, 0.
- **Client (62 checks):** `special.js` **evaluated**, not merely parsed, against stubbed globals
  (a parse would not catch a broken prototype chain — `howto_verify_react_bundle`); both classes
  exist as `window` globals, which is what `SystemFactory`'s `new window[name]` needs; **all six JS
  tables and both mode constants compared field-for-field against the PHP side dumped by
  reflection**; the gun accounting replayed across the same eleven server scenarios with the manual-
  intercept cap agreeing every time; the Medium's guns tracking `turnsloaded`; and the consent hooks.

**How the 2026-09-03 REVISIONS were proven.** Two more throwaway tests, same method, grown as the
refinements landed: **39 server checks and 99 client checks**.

- **Server (39 checks):** the count read off `->shots` alone and `->notes` left as the plain `"Split"`;
  clamping against tables then pool; a spent pool giving 0 damage; ⭐ **a MIXED-MODE turn** — two
  combined and two single orders resolved together, each priced by ITS OWN `$order->firingMode`, with
  the mode-2 order claiming four discharges still firing one and the `guns` arithmetic landing on the
  right answer across the lot; `guns` landing on the discharges left for `isValidInterceptor` **and**
  `automateIntercept` at combined-4 and combined-2; the damage bands (⭐ still compared against the
  1-discharge **ceiling**, because rows 1 and 4 overlap); `getStartLoading()` seeding 1 — asserted
  three ways: below `getNormalLoad()`, at or above `getLoadingTime()`, and producing a pool of 1 —
  while the full Array still starts ready; row 1 of each table still equalling the weapon's own
  `fireControl` / `rangePenalty`; and ⭐ **`multiModeSplit` proven to REACH the client** by calling
  `stripForJson()` on the Lightning Array of a real `Traveler` (it needs a hull behind it — the base
  method asks the ship about Hyach Specialists), with an ordinary weapon on the same hull as the
  control that must NOT publish it.
- **Client (99 checks):** `special.js` evaluated again, then **the declaration flow driven exactly the
  way `weaponManager.targetShip` drives it** (call the hook, push what it returns, ask
  `checkFinished()`): three clicks on one ship producing ONE order carrying 3 with `damageclass
  'Sweeping'` and `notes "Split"`; a fourth fusing to 4; a fifth refused with a message; two targets
  staying two orders; Single Shots producing three separate one-discharge orders; ⭐ **both prediction
  mirrors moving together** — the fire-control delta and the range penalty both stepping from row 1 to
  row 2 as the shot grows, with an assertion that they really differ; ⭐ **a declared order reading at
  its OWN count while the look-ahead reads one higher**; another weapon's order ignored; the Medium
  refusing to combine at one turn of charge and fusing at two; the PHP tables re-compared
  field-for-field; and `confirm.askForMultipleValues` stubbed to **throw**, so reaching the end of the
  run is itself the proof that no dialog is opened any more.

  The refinements added: ⭐ **peeling** — 4 → 3 → 2 → 1 → gone, the order keeping its id throughout,
  one discharge handed back each time (not four), `guns` re-derived, and the stored `chance`
  **re-priced at the new count** and compared against what the mirrors give for that count; a Single
  Shots withdrawal removing a whole order; ⭐ **mixed modes coexisting** — a combined shot and a single
  shot standing together, withdrawal preferring the mode the weapon is in and leaving the other alone,
  and fusing resuming when the mode is switched back; the enemy-tooltip fallback firing when the
  current mode has nothing at that target (with a non-vacuity assertion that it really had nothing);
  a `selfIntercept` marker surviving "remove a firing order"; `removeAll` clearing everything; and the
  INCOMING row counting **discharges, not orders**, with `shotsInGroup` reproduced verbatim and a
  hookless weapon as the control so the Slicer's dice-in-`->shots` cannot start counting as shots.

⭐ **Every test asserts it is not vacuous** — that the two classes really carry different tables, that
the scenarios really produce different gun counts, that the mode constants differ, that the fighter
and capital columns differ, that the two prediction mirrors really move. This earned its keep twice in
one sitting:

1. The damage-band check quietly stopped discriminating once the real stats landed, because row 1
   (25–70) and row 4 (40–220) **overlap**. The non-vacuity assertion caught it; the fix was to sample
   400 rolls of each and compare against the 1-discharge ceiling instead.
2. The table comparison caught the JS mirror still holding the old placeholder numbers after the
   control sheet was applied to the PHP only — exactly the silent client/server drift it exists for.

Regression gate after the revisions: `checkShipData.php` PASS (0 new findings), replay harness
131 passed / 1 failed — game 4325, the known pre-existing clean-tree failure, and its diff is
movement/`waiting` fields, nothing to do with weapons.

**Left for the user:** a hull mount for the Medium Array — the Traveler's hit chart never named one,
so it is built and unplaced. `Traveler` carries one `LightningArray` in the front section and its
chart row is restored. Icons landed 2026-09-03 (`LightningArray.png`, `LightningArrayMed.png`).

### 3.3 Wide-Beam Lightning Array (system enhancement) — **BUILT 2026-09-06 (Stage 8)**

Two registry entries — `SYS_WBLA` (300 pts) and `SYS_WBMLA` (200 pts) — following the existing
`eligible`/`limit`/`price`/`apply` quartet. `limit` is 1 (single-level refit, so `priceStep` is 0).
`eligible` = `$system instanceof LightningArray` (resp. `MediumLightningArray`) and
`!($ship instanceof FighterFlight)` — the latter is already guaranteed by the caller, but state it.

⚠️⚠️ **`MediumLightningArray EXTENDS LightningArray`, so that eligibility test as written offers
BOTH refits on every Medium** — at 300 points and at 200, on the same mount, each adding the same
firing mode. `sysEnhEligibleWBLA` therefore ends `return !($system instanceof MediumLightningArray);`.
Written as an instanceof rather than a `get_class()` equality so a future LightningArray subclass
that is *not* a Medium still gets the full-array offer, which is what the rules describe.

**As built, the purchase is the CAPABILITY and nothing else.** `sysEnhApplyWIDEBEAM` calls
`LightningArray::enableWideBeam()`, which sets `$wideBeamFitted` on that one instance - a plain
instance property, so two arrays on one hull refit independently, exactly what *"the player pays to
enhance each one separately"* asks for. `serialise` names `wideBeamFitted` alone (the class own
`stripForJson` was already republishing `data`, `minDamage`, `maxDamage` and both damage arrays per
instance for Stage 2 reasons, and it adds `active` for the arm). Arming is a per-turn toggle carried
by an individual note, NOT by the purchase - see below.

**The `factionAge > 2` gate** — **NARROWED 2026-09-04 (Stage 5).** It had to become per-enhancement
rather than whole-ship, or no Walker hull could ever be offered a per-system refit. **Do not simply
delete it** — that opens Gunsights, Hardened Armour and the rest to every Shadow, Vorlon and
Kirishiac hull in the game, which is a balance change nobody asked for.

**As built**, and it is smaller than the shape this section recommended because the registry was
already the right place for it:

- a new **`ages` registry slot** — `array(1, 2)` written out loud on each of the five existing
  refits, and **defaulting to `array(1, 2)` when absent**, so an entry added without thinking about
  age keeps the behaviour this file had before the slot existed;
- `systemEnhancementAllowsAge($ship, $enhID)` asks it, and is called from `systemEnhancementsFor`
  **and** from `sanitiseSystemEnhancements` (buy-time validation re-checks it independently);
- `hullAgeHasAnySystemEnhancement($ship)` replaces the two cheap whole-ship exits
  (`systemMayBeEnhanced` and `setSystemEnhancementOptions`), derived from the registry so it cannot
  disagree with the per-enhancement answer.

Stage 8's `SYS_WBLA` / `SYS_WBMLA` therefore need `'ages' => array(3)` and nothing else.

⚠️⚠️ **THE ANCIENT-WEAPON TEST NEEDS BOTH HALVES, and the corpus differential is the only thing
that catches it.** The old line was `$system->factionAge >= 3`, which was the right test only
because no Ancient hull could buy anything at all. Rewriting it as
`$system->factionAge > $ship->factionAge` — "a weapon from a more advanced tech base than its hull",
which is what the rule has always meant — looked obviously equivalent and **silently cost
`OmegaEpsilonDrakh` twelve systems' worth of Gunsights and Hardened Armour offers**: ~40 weapon
classes in `customs.php` declare `factionAge = 2` ("Middle-born"), and `2 > 1` refused every one of
them on a young hull. It is
`$system->factionAge >= 3 && $system->factionAge > $ship->factionAge`: the first half keeps it
about ANCIENT tech, the second stops it refusing an Ancient hull its OWN weapons.

⚠️ **Wide Beam is mode 3, not mode 2** — Stage 2 took mode 2 for `Single Shots`. Add
`MODE_WIDEBEAM = 3` alongside the existing constants in both `specialWeapons.php` and `special.js`,
and remember `beforeFiringOrderResolution` already branches on `$order->firingMode`.

**Per-turn declaration** is a **toggle**, not a purchase-time flag: the enhancement buys the
capability, the array is armed when firing. Effects, in either firing mode:
- `-2 per damage die, minimum 1 per die` — override `getDamage()`; ⚠️ the floor is **per die**, so
  it cannot be applied to the total. `Dice::d(10, $n)` returns a **sum** and cannot express it —
  five dice each floored at 1 can never total under 5, while a floor on their sum would allow 1 —
  so the roll is a loop of `max(1, Dice::d(10) - 2)`. The flat `+N` on the row is not a die and is
  untouched. ⭐ Because the arm is a property of the ARRAY rather than of the shot, `getDamage()`
  reads `isWideBeamShot()` and needs no fire order at all - which sidesteps the Stage 7 finding
  (`Firing::fireWeapons` never re-applies an order mode) rather than having to obey it.
- **The collateral, both halves.** Inside a field the general rule (§2.1) silences a flash weapon
  entirely; a wide beam is the exception the rules carve out, so inside a field it scores the
  ordinary **25%** and outside one it scores **50%**. ⭐ **As built this is two tiny hooks in
  `weapon.php`, not a rewrite of `doCollateralDamage`**: `edfSuppressesCollateral()` wraps the
  existing field test (LightningArray returns false for a wide beam) and `getFlashCollateralAmount()`
  wraps the `round($damage/4)` in `damageOneSheet` (LightningArray divides by 2 or 4). Both default
  to today's behaviour exactly, so every other flash weapon in the game is untouched.
  ⚠️⚠️ **The 50% must be computed from `$damage`, never by doubling the 25% figure** — rounding an
  already-rounded number is systematically high: 10 damage gives 3, doubled is 6, and 50% is 5.
  That is why the amount is a hook taking `$damage` rather than a scaling of the parameter
  `doCollateralDamage` is handed.
- **1-turn cooldown.** ⭐ Not a `$loadingtime` bump, and not the `overloadturns` machinery: it is
  **`calculateLoading()` returning `loading = 0` at the phase −1 turn advance** after a wide-beam
  shot. The parent hands an ordinary loading-1 weapon its charge straight back (fired on T →
  loading 0 → +1 → 1 → loaded on T+1); zeroing that one write is the whole cooldown, and the next
  advance finds no shot on T+1 and restores it. Raising `$loadingtime` instead would have been
  invisible to the client — `loadingtime` is a BLUEPRINT field riding the per-class static bundle,
  the exact trap Stage 7 hit with `powerReq` — whereas `turnsloaded` is already published per
  instance by `Weapon::stripForJson`.
  - ⚠️⚠️ **`overloadturns` has to be zeroed in the same write, and this is the finding to carry.**
    `weaponManager.isLoaded` is `loadingtime <= turnsloaded || loadingtime <= overloadturns`, and
    `calculateLoading`'s phase −1 branch does `overloadturns + 1` for **every** weapon, overloadable
    or not, clamped to `normalload`. A loading-1 weapon therefore sits at `overloadturns = 1`
    permanently (confirmed against `tac_systemdata` in games 4335 / 4337 / 4338), so a cooldown that
    zeroed only `turnsloaded` would have shown a fully loaded array for the whole cooldown turn.
  - ⚠️⚠️ **Nothing on the server refuses an offensive order from an unloaded weapon.** Both intercept
    gates in `firing.php` test `getTurnsloaded() < getLoadingTime()`, but `prepareFiring` /
    `fireWeapons` do not — the client's `isLoaded` is the only gate on that path. So the cooldown
    needed a server half: `beforeFiringOrderResolution` takes the discharge pool to **0** when
    `getTurnsloaded() < getLoadingTime()`, and every order then clamps to 0 discharges and 0 damage
    (this weapon's established loud failure). The two gates agree by construction, because
    `overloadturns` is 0 at the Fire phase for a non-overloading weapon.
  - The cooldown costs the array its **defence** as well, since both intercept gates read the same
    loading — which is what *"stressful on its systems"* ought to mean.

⭐⭐ **A PER-TURN TOGGLE, NOT A FIRING MODE** (user's ruling, 2026-09-06, after a four-mode build the
same day). The rules' *"in all modes"* means *whatever the shot's discharge count* — spreading the
beam and grouping the discharges are orthogonal choices. Modelling that as extra firing modes means
enumerating the product, and **four entries in the selector is what the player pays for it**; the
user's verdict on the working four-mode build was *"mechanically that all seems to work perfectly,
however the UI is a little bit clunky"*. The rules' own framing is a toggle anyway: *"the lightning
array **may be configured** to fire a wide beam"*, one declaration for the array for the turn.

So: two firing modes as before, plus a **"Wide Beam" / "Normal Beam" pair in the array's
`<SystemActivation>` box** during the Fire phase. Arming applies to every shot that array fires this
turn, in either mode. Two booleans carry it — `wideBeamFitted` (the refit, applied at construction
from the stored purchase) and `wideBeamArmed` (this turn's declaration, replayed from the notes) —
and `isWideBeamShot()` demands **both**, which is what makes a hand-written note on an unrefitted
array buy nothing.

**HOW THE TOGGLE REACHES THE SERVER, and why it needed a new hook.** The Fire phase is the one phase
that has never run the generic `generateIndividualNotes` sweep, and it must not start: **34 of the
~80 overrides in the codebase carry no phase guard at all** (one has an explicit `case 3`), so
switching that sweep on in phase 3 would wake every one of them in a phase they have never seen. The
route is therefore:

1. the client sets `->active` and posts `[1]`/`[0]` in the system's `individualNotesTransfer`;
2. `Manager::parseShips` calls `doIndividualNotesTransfer()` on **every** POST in **every** phase —
   this part needed no new plumbing at all — which stashes it on the POST-side array;
3. `FireGamePhase::process` calls the new **`ShipSystem::saveFirePhaseDeclaration()`**, a hook whose
   base version does nothing, inside the existing `$ship->userid != $gameData->forPlayer` guard, so
   a POST can only ever declare for its own units;
4. the advance re-loads gamedata and `onIndividualNotesLoaded()` sets `wideBeamArmed` before
   `prepareFiring` runs.

⚠️⚠️ **Step 3 cannot be gated on the refit.** A POST-side ship is rebuilt WITHOUT enhancements
([[arch_post_side_ship_reconstruction]]), so `wideBeamFitted` reads false there even on a fitted
array; gating the write on it would silently drop every declaration. The refit is re-checked at READ
time, on the real ship. This is the single most expensive recurring trap in the codebase and it bit
again here.

⚠️ **Both states are written, and the highest note id wins.** Writing only on arm leaves a
re-commit stuck on a stale 1. The load query is `turn <= current` and orders by turn and phase only,
so two notes written in the same phase come back in an order MySQL does not promise — ids are
auto-increment, so they are the write order. And the **turn equality test is what makes it a
per-turn declaration**: without it, arming once would arm the array for the rest of the game.

⚠️ **`system.active` is the field the generic activation box reads**, so that is what the arm is
published as (the same thing `ChameleonSensors` does). But that box also treats a *weapon's* "has a
fire order" as active, because for most weapons it IS the fire button — so the array sets
**`activationIsToggle`**, a one-word opt-out added to `SystemActivation.js`, or the toggle would
light up the moment anything was declared whichever way it was actually set.

⚠️ **A defensive order is stamped plain Single** (`getInterceptOrderMode`), and separately
`firedWideBeamOnTurn()` counts only `type == "normal"` orders — so intercepting never costs the
array the following turn.

⚠️⚠️ **THE ONE REAL BUG THE FOUR-MODE DETOUR EXPOSED, worth keeping even though its cause is gone.**
`getCombinableOrder` matched *"an order that is not Single Shots"*, which is the same test as *"an
order in this mode"* only while exactly ONE mode fuses. With Combined **and** Wide Combined both
fusing, a Wide Combined click fused into a standing ordinary Combined shot — and because growing a
shot **replaces its order** (§3.2), the discharge already committed was silently converted into a
wide beam, cooldown and all. It is now an **equality** against the weapon's current mode, which is
what the rule always was, and it stays that way: it generalises to any `multiModeSplit` weapon that
grows more than one kind of shot. ⭐ No test of the new mode in isolation would have found it; the
cross-product of "declare in mode A over a standing order in mode B" did, on the first run.

⚠️ **`isSingleShotMode()` is kept** on both sides even though it is now one comparison. It exists so
the grouping question is asked by name everywhere, precisely so the wide beam can never be confused
with it again.

**Timing deviation, accepted (D6, re-confirmed 2026-09-06):** the rules put this in Prepare Weapons
(Initial Orders); the toggle is in the Fire phase, alongside declaring the shots. The cooldown cost
keeps it a real decision rather than a free upgrade.

⚠️ **Initial Orders was offered and declined.** It is the faithful phase AND the cheap one — the
generic `generateIndividualNotes` sweep already runs there, so the note would have needed no new
hook and no change to any shared phase file at all. The user chose the Fire phase because it keeps
*when you click* unchanged, and the extra machinery (the narrow `saveFirePhaseDeclaration` hook) is
the price of that. If the phase is ever revisited, moving it to Initial Orders **removes** code.

**How it was proven.** Two throwaway tests, the Stage 2 method.
- **Server (143 checks):** the registry slots and the five older refits' `ages` left alone; the age
  gate both ways against a young control hull; the offers on a Traveler, including ⭐ the
  Medium-is-a-LightningArray subclass trap and a non-vacuity assertion that the two arrays really
  are different mounts; the prices, limit and zero `priceStep`; ⭐ **a 2,578-hull corpus scan** in
  which no non-Walker hull gains an offer; the buy applying to the ONE array and not its sibling,
  and `sanitiseSystemEnhancements` dropping `SYS_WBLA` bought on a Medium and re-pricing a legal
  row to 300; ⭐⭐ **the fitted × armed cross-product**, where three of the four combinations must
  do nothing — the one that matters is ARMED BUT NOT FITTED, which is what a hand-written note or a
  refunded refit looks like, and it is checked on the damage roll as well as on the predicate; the
  damage bounds and ⭐ its **mean** measured against `E[max(1,d10-2)] = 3.8` (a 20-dice row can
  never reach either cap in a few thousand samples, which the first draft failed on) in BOTH firing
  modes, with the per-die floor isolated on the 1-discharge row where the minimum IS reachable;
  ⭐⭐ **the toggle's whole round trip** — the POST-side stash on an array that reads as UNFITTED
  (the trap, asserted as such), the note it queues anyway, the un-armed note written too, a silent
  client writing nothing at all, the base hook proven inert on an ordinary system, and the replay
  with **highest-id-wins tested in both array orders** and the per-turn reset proven by a turn-4
  note failing to arm turn 5; the cooldown gated on armed AND fired, with unarmed, never-fired,
  unfitted, `intercept` and `selfIntercept` controls that must all reload normally; ⭐⭐ **the whole
  loading sequence driven the way `advanceGameState` drives it** — fire, advance, the phase-2 and
  phase-3 writes, advance again — asserting the CLIENT's `isLoaded` formula at every step, for both
  arrays, with an unarmed control and a standing assertion that `overloadturns` alone WOULD have
  read as loaded; the collateral at 25 / 50 / 25 with ⭐ the double-rounding case (10 damage → 5,
  not 6) and an armed-but-unfitted control; the damage span moving for BOTH modes at once (the arm
  is not per-mode); and `wideBeamFitted` + `active` reaching the client for the fitted mount only,
  with ⭐ an assertion that the refit publishes **no** per-instance `firingModes` — a per-instance
  mode list would mean the toggle had leaked back into the selector.
- **Client (84 checks):** `special.js` and `systemEnhancements.js` **evaluated**, not parsed,
  against stubbed globals; the constants compared against the PHP side dumped by reflection, plus
  an assertion that no `MODE_WIDE_*` constant survives; the fitted × armed cross-product again, on
  `isWideBeamArmed()` and on `getDieCeiling()`; ⭐ **all four damage spans distinct**, the firing
  mode picking the row and the arm picking the ceiling; the toggle's own controls — labels, the
  activate/deactivate pair flipping, and every gate (phase, ownership, destroyed, offline, and the
  cooldown itself) with a non-vacuity re-check after each; ⭐⭐ **`SystemActivation.js`'s active
  expression reproduced verbatim**, proving an unarmed array with a declared shot reads as INACTIVE
  while an ordinary weapon in the same state reads as active; the note post in phase 3 and its
  silence everywhere else, including that the buffer is cleared so an earlier phase cannot leak;
  ⭐ arming and un-arming RE-PRICING the shots already standing; the firing modes proven untouched
  by all of it (fusing, not fusing, cross-mode refusal, the INCOMING count, the defensive mode);
  and ⭐⭐ **the lobby applier leaving the shared `firingModes` object identical by reference** —
  the old build's whole failure mode was writing to it.

⚠️ Both harnesses stop at the database. The note's actual INSERT and re-load is the one link tested
only in play; the round trip is driven in memory on either side of it.

**Play-test fixes, 2026-09-06.** Two, both in the UI, neither in the rule.

- ⚠️⚠️ **A WEAPON WAS NEVER GIVEN A DEACTIVATE BUTTON AT ALL** — `SystemActivation`'s
  `showDeactivate` was `!system.weapon && …`, so the array could be armed and never un-armed. The
  rule it encodes is right for every other weapon (for them the box IS the fire button, and a shot
  is withdrawn with the top-row "remove fire order" control), so the fix is the same one-word
  opt-out the `isActive` line already uses: `(!system.weapon || system.activationIsToggle)`.
  ⭐ **The two halves of `activationIsToggle` are a pair and a new one must set both** — `isActive`
  answers *"is this lit?"* and `showDeactivate` answers *"is there an off switch?"*, and the first
  was written without noticing the second was gated on the same question.
- **The INCOMING list now names the arm.** `ShipTooltipBallisticsMenu` printed
  `firingModes[order.firingMode]` directly, so an armed and an unarmed shot read identically as
  "Combined" while rolling different dice. Now `Weapon.getFiringModeDisplayName(fireOrder)` - a
  plain `firingModes` lookup for every weapon in the game - with a LightningArray override
  appending `-Wide`, giving `Combined-Wide` / `Single-Wide`. ⭐ It reads the LIVE arm rather than
  anything stored on the order, which is correct by construction: arming covers every shot the
  array fires this turn, which is why `onWideBeamToggled` already re-prices the standing ones.
  ⚠️ It decorates the DISPLAY only - `firingModes` is a shared per-class object and the selector
  still offers exactly two entries.
- **Which mode is current now reads off the buttons.** An ordinary weapon's Activate button is
  orange whether or not it has fired - that colour is the "this menu belongs to a weapon" signal,
  not a state readout - so with both buttons showing, Wide Beam was lit even while the array was
  firing normally. `$isToggle` (`activationIsToggle` again) makes the orange conditional on
  `$active` for a toggle only, so the button that is NOT the current state falls through to the
  idle muted blue and exactly one of the pair is ever lit. Ordinary weapons are untouched.

### 3.4 Chromatic Pulse Driver — **BUILT 2026-09-03 (Stage 3)**

Accelerator (as §3.2) with a second firing mode, `'Scanning'`.

- Scanning mode: `getDamage()` returns 0, no pulse behaviour, but the to-hit roll resolves normally.
- On a hit against a unit carrying **any** `DefensiveSystem` whose `getDefensiveType()` is a shield
  type, record one point of adaptation.

**"The same race" is the raw `$ship->faction` string (D7)** — no faction families, no normalising.
Minbari Federation and Minbari Protectorate are two races and adapt separately; so does any custom
or `whatif` hull carrying its own faction string. ⚠️ Read `faction` off the **target hull**, never
off the ship directory name — [the two do not always match](source/server/controller/shipLoader.php),
which is the whole reason `getFactionDirMap()` exists.

**Persistence:** an `IndividualNote` on the CPD system, `notekey = "CPDSCAN"`,
`notekey_human = <faction>`, `notevalue = <count>`.
⚠️ **`notekey` and `notekey_human` are `varchar(40)` and overflow is a fatal that aborts the whole
player submission** — a 41-character value killed a movement submit once. `substr($x, 0, 40)`.

⚠️ Reset the registry in `DBManager::getSystemDataForShips` — §2.1's double-load trap.
⚠️ *"starting in the next Adjust Ship Systems segment"* — only count notes whose turn is **strictly
less than** the current turn.

---

**As built (Stage 3, 2026-09-03).** Stats for firing mode 1 are the control sheet, supplied per D4
as two rows keyed by turns charged: 1 turn = D3 pulses, max 4, 14 damage; 2 turns = D5 pulses,
max 8, 18 damage. Grouping 15, range penalty 0.5, FC 4/4/4 and intercept 1 are **identical on both
rows**, so they are plain properties rather than table columns. Still unconfirmed and marked so
in-file: `$priority` (5, the inherited Pulse default), the health/power defaults (24/12), the point
cost and the icon (`PulseAccelerator.png` as a placeholder — the class comment says which one line
to change). `$chargeProfile` is the sheet and nothing hard-codes a game number, so a re-stat is a
table edit.

⭐ **IT LIVES IN `pulse.php` / `pulse.js`, NOT in the Walker files.** Every other Walker weapon is in
`specialWeapons.php` / `special.js`; this one is not, and the reason is a hard constraint rather
than taste. It must extend `Pulse`, and **`game.php` and `gamelobby.php` both load `special.js`
BEFORE `pulse.js`** ([game.php:393](source/public/game.php#L393) vs
[:396](source/public/game.php#L396)), so `Object.create(Pulse.prototype)` evaluated inside
`special.js` would read `undefined` at load time and every Traveler would blow up in
`SystemFactory`'s `new window[name]`. The server half followed the client half so the pairing stays
symmetric; a pointer comment sits at the top of each class.

**The accelerator half.** `loadingtime 1 / normalload 2`. `getChargeRow()` is the single authority
and `getPulses`, `rollPulses`, `getDamage` and the tooltip all read it — but `$this->maxpulses` and
`$this->useDie` are ALSO kept in step by `applyChargeProfile()`, because **`Weapon::fire` reads
`$this->maxpulses` directly, twice** ([weapon.php:2125](source/server/model/weapons/weapon.php#L2125)),
for `->shots` and for the interception tally. `getStartLoading()` seeds **1**, not 0, exactly as
`MediumLightningArray` does and for the same reason (game 4329).

⭐ **The Scanning mode swap is `$damageTypeArray`, and it needs `changeFiringMode` inside `fire()`.**
`'Pulse'` → `'Standard'` is what stops `Weapon::fire` collapsing the volley and rewriting `->shots`
with `maxpulses`. But **`Firing::fireWeapons` does NOT re-apply an order's firing mode before
calling `fire()`** — only `prepareFiring` does, and it does so for ALL orders before ANY of them
resolve, leaving the weapon in whichever mode the LAST prepared order used. A driver that prepared
a Pulse order after a Scanning one would have resolved the scan as a pulse volley. `fire()` calls
`changeFiringMode($fireOrder->firingMode)` itself, the same idiom `AoE::fire` uses. This is the
Lightning Array's `$order->firingMode` trap in its second form: **`prepareFiring` sets the mode per
order and `fireWeapons` does not.**

**A scan is one shot, server-authoritatively.** `defaultShotsArray` gives the client `1` in Scanning
mode (rebuilt per instance in `setSystemDataWindow` and republished in `stripForJson`, because the
Pulse-mode entry tracks the charge), and `fire()` clamps `$fireOrder->shots = 1` regardless — a
forged 9-shot scan order banks exactly one point.

**Where a scan is recorded.** `beforeDamage()` — the per-hit hook — which **never calls the parent**
in Scanning mode, so nothing rolls a hit location, nothing touches armour and no `DamageEntry` is
created. It banks into `$pendingScans`, which `generateIndividualNotes` drains into notes;
`FireGamePhase::advance` calls that for every ship immediately after firing resolves. ⚠️ It branches
on *"do I have pending scans"*, **never on `$gamedata->phase`** — trap 3. Everywhere else the method
is reached (Movement, `generateAdditionalNotes`) the list is empty and it is a no-op.

⭐⭐ **PUBLICATION CHANGED FROM THE PLAN: the reduction is applied to the AGGREGATED BUCKET, not
inside each shield class.** The plan said `Shield::getDefensiveHitChangeMod` /
`getDefensiveDamageMod` "and their `EMShield` / `GraviticShield` siblings". That would have been
seven classes and seven client mirrors, and it would still have missed some: the tree has **nine**
systems answering `getDefensiveType() === "Shield"`, four of which are `Weapon` subclasses with
their own near-duplicate implementations (`AbbaiShieldProjector`, `FlareGenerator`,
`PakmaraPlasmaWeb`, `NexusWaterCaster`) plus `ShadingField` and `FlareShielding`.

`BaseShip::getHitChanceMod` / `getDamageMod` ([ShipClasses.php:2824](source/server/model/ships/ShipClasses.php#L2824))
and `FighterFlight`'s two redefinitions already collect every defensive system into
`$affectingSystems[<defensive type>]`, **keeping only the strongest single source per type**. One
line before each `array_sum` therefore:

- lands the reduction **exactly once** however many shields the target mounts — which is the rules'
  own "overlapping shields are not cumulative", for free;
- covers **every** shield-type system in the game with four edits instead of eighteen;
- is a **guaranteed no-op** for every game with no CPD in it (`CpdScanRegistry::$adaptation` is
  empty, and that is the first test).

`CpdScanRegistry::applyToShieldBucket()` holds the whole rule: only the `"Shield"` bucket, only when
it is positive, clamped at 0. The client mirrors it in exactly the same place —
`cpdApplyShieldAdaptation()` in [model/ship.js](source/public/client/model/ship.js), called from
`getHitChangeMod` and `getHitChangeModFlight` — off `gamedata.cpdAdaptation`, a
`{ teamId: { faction: points } }` map published from `TacGamedata::onConstructed`.

⚠️ **`cpdAdaptation` is NULL, never `array()`, when empty** — trap 9, an empty PHP array encodes as
JSON `[]` and the client indexes it as an object. `publishAll()` returns `stdClass` or null.

**Adaptation is published for EVERY team, not just the viewer's.** It is earned by a scanning shot
that resolves and is logged like any other, so it is public knowledge — and a defender needs to see
why their shields are reading low. A chameleon-disguised ship publishes its DISGUISED faction, so a
fleet that adapted to the disguise reads no benefit against the real hull: the deception working,
not a bug.

**A destroyed scanner keeps its knowledge.** The notes stay in the database and the team keeps the
adaptation — it was learned by the fleet, not stored in the hull.

**ShadingField — RULED 2026-09-03 (D9), and it is what the bucket already does.** The line falls
between the Shading Field's two jobs, not inside its shield maths:

- **Its defensive contribution is fair game, shading bonus included.** When the field is shaded its
  hit-chance mod is `output × 2`, and adaptation eats into that doubled figure point for point. The
  user's ruling: *"the CPD's scan reduction can eat into the Shading Field's bonus hit chance
  reduction when it's shaded"*. The alternative reading — cap the reduction at the base `output` so
  the shading bonus is untouchable — was considered and rejected.
- **Its stealth and detection mechanics are untouched, permanently.** *"it has no effect on the
  stealth/detection mechanics of the Shading Field"*. Nothing here may reach the Pre-Turn detection
  forecast, which `arch_stealth_toggle_forecast` requires to stay own-team-only.

That is exactly what falling out of the `"Shield"` bucket gives for free — the field's
`getDefensiveHitChangeMod` has already applied its own doubling by the time the bucket sees it, and
its detection function is not in the bucket at all. **No ShadingField-specific code exists or is
needed.** Read `CHAMELEON_SENSORS_PLAN.md` before touching it anyway.

⭐⭐ **ADVANCED SENSORS ALREADY EAT THE TO-HIT HALF, so half the scan is redundant for the Traveler**
(found investigating game 4332, 2026-09-03 — a user report that the "Defensive Systems" row was
missing from the CPD's hit-chance tooltip). `BaseShip::getHitChanceMod` zeroes any **positive**
defensive mod when the target's `factionAge < 3` and the shooter has `AdvancedSensors`
([ShipClasses.php:2836](source/server/model/ships/ShipClasses.php#L2836)) — the scanner's own
tooltip says it: *"Ignores any defensive systems lowering enemy profile (shields, EWeb…). All of the
above work as usual if operated by advanced races."* The `Traveler` calls `$scanner->markAdvanced()`,
so **against every young or middleborn race its shields contribute 0 to the goal already** — for the
Lightning Array, the Medium Array and all three CPDs alike. Verified on game 4332: all five weapons
read 0 against the Abbai and the Brakiri, all five read −4 against the Torvalus (Ancient), and
knocking `AdvancedSensors` out of `$trav->enabledSpecialAbilities` in memory brings all five to −2
(shield 3 − 1 adaptation) together.

Consequences worth knowing before re-statting:
- **The row's absence is not a CPD bug and not a regression.** Nothing in the hit-chance path is
  weapon-specific here; `pushIfNonZero` simply omits a zero.
- **Against young/middleborn races the scan buys DAMAGE ABSORPTION ONLY** (3 → 2 on the Lakara and
  the Tashkat in 4332). The to-hit half lands against **Ancient and Primordial** races, against any
  Walker hull whose scanner is not advanced, and for any ally the Walkers are shooting alongside.
**The hit-chance tooltip spells the discount out — BUILT 2026-09-04.** The first pass folded the
reduction into "Defensive Systems", which just read one point lower than the target's sheet says
with nothing to explain it. It is now two lines:

```
• Defensive Systems: -20%      <- the target's REAL shielding
• Shield Adaptation:  +5%      <- what the scan bought
```

⭐ **It is a REGROUPING, not a new term, and that is what keeps it honest.**
`Ship.prototype.getHitChangeMod` still returns the ADAPTED total — the number the server rolls,
untouched, for every one of its callers — and gained an optional `outDetail` out-parameter that the
helper fills with the reduction. `computeShotModifiers` adds that back onto `defensiveSystems` for
display and returns it separately; `calculateHitChange` then subtracts the full value and adds the
reduction back, so the goal is bit-identical and the sum-equals-`hitChance` invariant still holds
with both rows in the list. Nothing on the server changed: it builds no tooltips.

⚠️⚠️ **The helper reports what it ACTUALLY removed, never the points held.** The clamp at 0 means
3 points of adaptation against a 1-point shield removes 1 — reporting 3 would put a `+15%` line in a
tooltip whose total only moved 5% and would trip the invariant warning. Hence
`cpdApplyShieldAdaptation` now mutates the bucket in place and returns `before - after`, which is
also why it is shaped differently from its PHP twin (a PHP array is a value, a JS array is not).

Still gated (D10): the out-object is allocated only when `gamedata.cpdAdaptation` is present, so an
ordinary game pays one extra property read per hit-chance preview.

⭐⭐ **AND IT REACHED NOTHING UNTIL 2026-09-04, BECAUSE `gamedata` IS A HAND-MAINTAINED
SINGLETON.** The whole client half - the mirror, the gate, the tooltip split, its 35 green
checks - sat behind `gamedata.cpdAdaptation`, and `gamedata.js` never copied that key off the
payload. `parseServerData()` assigns the payload one NAMED key at a time
([gamedata.js:2703](source/public/client/gamedata.js#L2703), beside `blockedHexes` /
`isStealthPresent` / `areMinesPresent`); a new field on `stripForJson` reaches the page only
when a line is added there. So the server rolled adapted shields and the client previewed
un-adapted ones - the user report was exactly *"the server % seems correct, but the hit chance
tooltip is wrong"*, with no Shield Adaptation row, because the helper returned 0 every time.

The fix is one declaration and one assignment, `serverdata.cpdAdaptation || null`, assigned
**unconditionally** so a replay stepping back to a turn before the first scan clears it again.

⚠️ **Neither test suite could have caught it, and now both do.** Both drove the helper with a
hand-built `gamedata` stub - which is precisely the object the bug was that nothing built.
Three source-audit checks were added to `cpd_stage3_test.php`. **Any future field published
through `TacGamedata::stripForJson` needs the same line in `parseServerData` - check first.**

**"Scanned by Walkers" markers on the shield systems - BUILT 2026-09-04.** A hit-chance
tooltip only tells you about a shot you are already setting up; the defender wanted to see it
on the sheet. `ScannedByWalkers` (a `forInfo` `Critical`, `repairPriority` 0) is hung on every
shield-type system of every unit whose RACE any fleet has analysed, reading *"Scanned by
Walkers (-N shield effectiveness)"*. Public, like the adaptation itself.

⚠️⚠️ **NEVER PERSISTED.** `CpdScanRegistry::applyScanMarkers()` rebuilds them from the
registry on each load and leaves `updated` / `newCrit` false, so `getUpdatedCriticals()` cannot
list them and nothing reaches `tac_critical`; the CPDSCAN notes stay the single source of
truth. `forInfo` also keeps them out of pre-battle damage and Save Fleet, both of which refuse
`forInfo` classes generically. `markSystem()` is idempotent - a duplicate would render as
"(2 x) Scanned by Walkers".

⭐ **The call site is pinned by TWO orderings**, both inside `prepareForPlayer()`: before
`setPreTurnTasks()`, whose `beforeTurn()` sweep is what rebuilds `critData` out of `criticals`
(without it the marker rides the payload with no readable text), and before
`applyChameleonDisguise()`, so a marker can never land on a phantom sheet. `prepareForPlayer`
is also the right method rather than `onConstructed`: it runs only on the two READ paths, so
nothing in turn processing ever sees these objects. Gated on `$cpdAdaptationPresent` (D10).

⭐ **It exposed a real bug in `ShadingField::setSystemDataWindow`** - the one system in the
tree that never called its parent, so it had no `$critData` (and no ID, Arc or Power Used
either). ANY critical on a Torvalus Shading Field rendered as its raw phpclass. It now calls
the parent first.

⚠️ **Against a young or middleborn target neither line appears**, because Advanced Sensors zeroed
the shield before the bucket existed — see above. That is correct: nothing was discounted, because
there was nothing left to discount.

**The magnitude is one class constant.** `SCAN_POINTS_PER_HIT = 1`, and one point is one point of
shield — which is 1 off damage absorption and 5 off the d100 profile, because `getDefensiveHitChangeMod`
returns d20 units and the ×5 happens downstream. There is no explicit cap: the clamp at 0 is the cap.

⭐⭐ **COST: the whole feature hangs off ONE static boolean** (user's requirement, 2026-09-03 — *"this
is a rare weapon in the overall game, so as far as we can the processes to make it work should be
gated behind cheap checks"*). `TacGamedata::$cpdAdaptationPresent`, following the
`$chameleonPresent` precedent exactly. Set **only** by `CpdScanRegistry::record()` — i.e. once a
scan note has actually been replayed, not by the presence of a CPD on a hull, because an unfired
driver changes nothing:

| where | what an ordinary game pays |
|---|---|
| the four defensive-mod aggregators | one static-property read each. No call, no arguments. |
| `TacGamedata::onConstructed` | one property read (it tests the boolean, **not** `class_exists`). |
| `DBManager::getSystemDataForShips` | one assignment plus `class_exists('CpdScanRegistry', **false**)` — ⚠️ **autoloading OFF**, so the registry file is never read at all. If the class is not loaded its table is empty by definition, so there is nothing to reset. |
| client, both aggregators | one `window.gamedata && gamedata.cpdAdaptation` property read. The helper self-guards as a second line of defence, but it is never *reached*. |

⚠️ **Do not "simplify" any of these into an unconditional call.** `getHitChanceMod` /`getDamageMod`
run for every shot in every game of every faction. And do not restore autoloading on that
`class_exists` — that single character is what keeps the file out of every gamedata load in the
database. The test proves the gate is load-bearing by forcing it **false while the registry is still
full** and asserting the shields read un-adapted.

**Hull.** `Traveler` mounts three — front (270..90), left (180..360) and right (0..180) — and all
three of its `//STAGE 3` hit-chart rows are restored. ⚠️ That shifts every system id after the front
mount, which is the positional-id trap (trap 7): **a game in progress with a Traveler in it will
desync.** Same cost Stage 2 paid; `Firing::validateFireOrders` drops the stale orders rather than
crashing.

**How it was proven.** Two throwaway tests, kept in `c:\tmp\`: **165 server checks and 35 client
checks**, all green.

- **Server (`c:\tmp\cpd_stage3_test.php`, 165 checks):** the control sheet compared row by row with
  a non-vacuity assertion that the two rows really differ; over- and under-charge clamping;
  `getStartLoading` seeding 1 asserted three ways (at/above `getLoadingTime()`, below
  `getNormalLoad()`, and yielding the 1-turn profile); `$this->maxpulses` / `$this->useDie` tracking
  the charge, because `Weapon::fire` reads them; **800 rolls of `rollPulses` per charge level**
  proving the ceiling holds, the row's die is really used, and the two levels differ, plus the
  grouping bonus clamping at `maxpulses`; the mode swap in both directions with the ORDER's mode
  beating the weapon's own; `min/maxDamageArray` per mode; `unitHasShieldSystem` on plain, EM and
  unshielded targets; ⭐ **`beforeDamage` banking a point and creating NO damage entry, with a PULSE
  order on the same weapon still damaging as the non-vacuity control**; note round-trip including
  the varchar(40) truncation; ⭐ **the double-load trap demonstrated BOTH ways** — a second replay
  without a reset really does double the adaptation, and with the reset it does not; and ⭐ **the
  whole effect end to end through the REAL `BaseShip::getHitChanceMod` / `getDamageMod`** on a
  shielded hull, moving from −3/3 to −1/1 with 2 points and flooring at 0, with a same-team
  different-race bystander unaffected as the control. Plus the hull mounts, the three arcs, the
  restored chart rows, `stripForJson`, and a `ShipCompactor` audit that the blueprint keeps
  `damageTypeArray` / `min`+`maxDamageArray` / `firingModes`. ⭐ **And the D10 gate proved
  load-bearing rather than decorative** — forced FALSE while the registry is still full, the
  aggregators must read the shields un-adapted (−3, not −1), and re-arming must bring it back; plus
  a source audit that `class_exists` really has autoloading off, that both aggregator files carry
  two gates and exactly two `applyToShieldBucket` calls, and that a zero-point or teamless `record()`
  does not arm anything.
- **Client (`c:\tmp\cpd_client_test.js`, 35 checks):** `pulse.js` **evaluated**, not merely parsed,
  against stubbed globals — a parse would not catch a broken prototype chain
  (`howto_verify_react_bundle`) — with the instance checked to be a `ChromaticPulseDriver`, a
  `Pulse` and a `Weapon`, reachable as a `window` global (which is what `SystemFactory` needs) and
  with `VolleyLaser` / `EnergyPulsar` still building, since the file was appended to; the two mode
  constants compared against the PHP ones; and the adaptation mirror driven across the same nine
  cases the server test used, ending with ⭐ **the server's end-to-end number reproduced exactly**
  (bucket 3 − 2 points = 1). Plus the D10 gate: both call sites carry it, **no ungated call to the
  helper exists**, and the helper still self-guards for anything that calls it directly. And for
  the tooltip split: ⭐⭐ **the regrouping proven exact across 42 (shield, adaptation) combinations**
  — including negative and zero buckets and adaptation larger than the shield — asserting
  `−full + removed ≡ −adapted` every time, with a non-vacuity check that at least one case really
  produced two rows; plus the clamp trap asserted directly (3 points against a 1-point shield
  reports **1** removed, not 3).

Regression gate (re-run 2026-09-04 after the two fixes above): `checkShipData.php` PASS (0 new
findings, 235 accepted baseline), the throwaway suites now at **186 server checks + 35 client
checks**, all green, and the replay harness
**130 passed / 1 failed** - game 4325, the known pre-existing clean-tree failure, verified
byte-identical with the tree stashed.

⭐⭐ **CAPACITY-POOL SHIELDS — BUILT 2026-09-04, and this is the second half of the rule.**
The user's report: *"Thought Shields (and Thirdspace Shields and TrekShieldProjections) are able to
be scanned, but because their protection works in a different way I don't think the scanning has
any impact."* Correct, and the reason is exactly the bucket design above. Those four classes
(`ThirdspaceShield`, `ThoughtShield`, `TrekShieldProjection`, `TrekShieldProjectionKelly`) all
extend `Shield` and answer `getDefensiveType()` `"Shield"`, so a scanning hit banked a point off
them and they carried the marker — but their `getDefensiveHitChangeMod` / `getDefensiveDamageMod`
are **hard 0**. They do not reduce a shot's profile or its damage at all; they hold a **pool** and
absorb out of it in `doProtect()`. `applyToShieldBucket()` therefore reduced a number that was
already zero, and the whole thing was ceremony.

The ruling: **against an adapted fleet the pool's last N points are unspendable** —
`currentHealth − scannedAmount` is what the shield has to spend, per the user's wording.

- `CpdScanRegistry::applyToCapacity($capacity, $target, $shooter)` — the same `max(0, x − points)`
  clamp as the bucket twin, keyed the same `(shooter team, target faction)` way.
- Reached through **`Shield::getCapacityAgainstShooter()`**, one protected helper on the shared
  base class rather than four copies, carrying the `TacGamedata::$cpdAdaptationPresent` gate (D10).
  An ordinary `Shield` never calls it.
- Called from **both** hooks of each of the four classes: `doesProtectFromDamage()` (which ranks
  candidate protectors) and `doProtect()` (which actually absorbs). Eight call sites, two per class.

⭐ **`doesProtectFromDamage` GAINED A SIXTH PARAMETER, `$shooter = null`.** `doProtect` has always
been handed the shooter; the ranking hook never was, and without it an adapted-to-nothing pool
would still have won the "strongest protector" contest and then absorbed zero, silently shutting a
Bulkhead or a Diffuser out of a shot it should have taken. The parameter is **last and optional**,
so every existing caller and override stays valid; there are only **8 declarations and 4 call
sites** in the whole tree (`getSystemProtectingFromDamage` plus the three fighter-flight damage
estimators), all updated. ⚠️ A future override that copies the old five-argument signature is a
PHP declaration-compatibility fatal, not a silent miss.

⚠️ **COMPUTED PER SHOT, NEVER STORED.** It writes no `DamageEntry`, so the pool regenerates from
its **real** remaining health as usual, the ship sheet keeps showing the real number (the
`ScannedByWalkers` marker is what explains the gap), and the same shield reads at full strength
against every fleet that has not analysed the race. Two fleets with different adaptation shooting
it in the same turn each get their own figure.

⚠️ **A reinforced Thought Shield is the one system that feels the scan twice.** Its `defenceMod`
(the EM-Shield reinforcement layer) is a real, non-zero entry in the aggregated bucket, so the
points come off *that* through `applyToShieldBucket` **and** off the pool through
`applyToCapacity`. This is deliberate — they are two separate resources and the rule is "shields
are weaker" — but it is the only place a single point of adaptation buys two reductions, and it is
the thing to revisit first if the Mindriders read too soft in play.

**Out of scope, and why.** `TrekShieldFtr` (the Trek fighter's shield) and `DiffuserTendril` also
hold pools, but neither is a `DefensiveSystem` — `CpdScanRegistry::isShieldSystem()` rejects them,
so they are not scannable, carry no marker and are untouched. Trek's per-hit `output − armour` cap
is likewise untouched: the scan eats the **pool**, not the throughput.

**How it was proven.** `c:\tmp\cpd_capacity_test.php`, **56 checks green**: the registry
arithmetic including both clamps and the wrong-team / wrong-race / null controls; all four classes
driven through both hooks against an adapted and an unadapted shooter; ⭐ **the assertion that only
what was really absorbed reaches `$this->damage`** and that `getRemainingCapacity()` still reports
the true pool; the D10 gate forced false with the registry still full; ⭐ **the ranking flip proven
directly** — a 6-point pool with 6 points of adaptation loses `getSystemProtectingFromDamage` to a
4-point rival it beats against everyone else; non-regression for `Bulkhead`, a plain `Shield` and
both `applyToShieldBucket` clamps; and a source audit of the gate, the eight call sites and all
eight declarations. `cpd_stage3_test.php` re-run at **186 green** (one stale expectation fixed —
it still wanted the pre-ship marker wording `"Scanned by Walkers (-N shield effectiveness)"`; the
code says `"Scanned (-N effectiveness)"`, which is the right text now that the marker covers a
pool reduction as well as a modifier one). `checkShipData.php` PASS (0 new, 235 accepted) and the
replay harness **130 passed / 1 failed**, game 4325 again verified byte-identical with the tree
stashed.

**Left for the user:** a real `ChromaticPulseDriver.png` icon, and confirmation of `$priority`, the
health/power defaults and the point cost. Nothing else is open — D9 settled the Shading Field and
D10 settled the runtime cost.

### 3.5 Energy Draining Field / Variable EDF

`class EnergyDrainingField extends ShipSystem implements SpecialAbility, EdfSource`.

- `$radius` constructor arg (default from control sheet) plus a `$variable` flag.
- **Double power for extra radius** is the existing **boost** mechanism (`PowerManagementEntry`
  type 2, allocated in the Ship Power segment = Initial Orders = exactly where the rules put it).
  `boostable = true`, `maxBoostLevel = 1`, `getEdfRadius()` returns `$radius + ($boosted ? $bonus : 0)`.
  No new power concept.
- `canOffLine` — *"the player may deactivate the field"*, all-or-nothing, which is already what
  offlining a system means.
- Owns the §2.2 resolver.

**Criticals** — new classes + `$possibleCriticals`:
```php
// fixed-radius EDF
protected $possibleCriticals = array(21 => "EdfRadiusReduced");   // floor: radius 1
// variable EDF
protected $possibleCriticals = array(20 => "EdfBoostLost");       // then radius -1 each, floor 0
```
Both escalate on the `hasCritical()` **count**, so no per-crit bookkeeping.

**EDF Range enhancement** — **BUILT 2026-09-04 as the SHIP-level `EDF_RANGE`, not the per-system
`SYS_EDFR` this section assumed.** The user's call, and it was the right one: the Walkers now carry
their own `nonstandardEnhancementSet($this, 'WalkerShip')` set, and a ship-level entry can read the
hull's systems perfectly well (`CHAM_DISG` already does). Everything below is as built.

- **Human name "Extended Draining Field"**, enabled in the `WalkerShip` case and dropped again in
  `setEnhancementOptionsShip` on a hull that mounts no field.
- `price(level) = 300 * Σ(radius_i + level + 1)` over **every** EDF on the hull, i.e. 50 × the hexes
  added, summed. The rules' worked example (radius 5 → 6 = 6×6×50 = 1800) is `300 * (5+1)`.
  ⚠️ **Summed over the fields, not taken off the biggest.** It is a ship-level refit and the applier
  raises every field, so pricing off one would sell the second field's radius for nothing. With the
  usual single field it reduces to the rules' own formula exactly, and `priceStep` stays the
  constant `300 * fieldCount` that `enhancementOptions`' single step slot requires.
- **Limit 3.** Ours, not the rules' — the rules cap it nowhere and an offer tuple needs a number.
- ⭐⭐ **THE VARIABLE-FIELD RULE IS IMPLEMENTED BY SPENDING THE BOOST BONUS DOWN, and that is the
  one idea in this stage worth reusing.** "A vessel with a Variable EDF may only increase the radius
  of the normal-power field, and does not change the radius of the double-power field." The applier
  therefore does **two** things: `radius += n` **and**
  `boostRadiusBonus = max(0, boostRadiusBonus − n)`. The double-power radius is then unmoved by
  construction, with no second stored number, no clamp anywhere else, and no way for the two to
  drift; when the bonus reaches 0 the normal field has caught the boosted one up and boosting buys
  nothing, which is exactly what the rule describes. `getEdfBoostedRadius()` is the single place
  that arithmetic is written down.
- ⚠️ **Nothing may hard-code the `+3`.** `baseSystems.js`'s `initializationUpdate` did
  (`this.output + 3`, on a system whose `output` is always 0), so a refitted field would have shown
  a boosted radius it does not have. Fixed to `radius + boostRadiusBonus`.
- ⚠️ **Every number in the tooltip lives in its own `data` key and the prose carries none.** The
  refit is bought in the LOBBY, where there is no server round trip and `data` was baked into the
  blueprint long before the purchase, so `lobbyEnhancements.syncEdfFieldData` has to rewrite it —
  and rewriting one short numeric line is a mirror that survives, while re-deriving a number out of
  the middle of a four-sentence paragraph is one that rots. Hence `Field radius` **and** a new
  `Boosted radius` key, with `Special` reduced to "buys the boosted radius above".
- ⚠️ **`boostRadiusBonus` had to be added to `stripForJson`.** The client builds a system from the
  per-CLASS blueprint, which cannot know what one ship bought.

⚠️⚠️ **THE BUG THAT ACTUALLY SHIPPED, found in play (game 4336, 2026-09-05): the refit moved the
radius everywhere except the map that matters.** Three Travelers refitted to radius 3 / 4 / 5 all
still drained at 2. Every value was right — `$system->radius`, `getEdfRadius()`, `effectiveRadius`,
the tooltip, the enhancement box — and `TacGamedata::$edfHexes` was built from the BLUEPRINT radius
anyway, because `setEdfHexes()` was called beside `setBlockedHexes()` at the TOP of
`TacGamedata::onConstructed()` while `BaseShip::onConstructed()` — which is what applies
enhancements — runs in the per-ship loop **below** it. 29 hexes published where 91 were owed.

⭐ **The generalisation, and it is already written three lines above the loop for another
feature:** `markUnavailableSetMarkers()` sits below the loop *"because every ship is now fully
constructed - which the Chameleon gate requires, since onConstructed() is what applies
enhancements"*. **Anything that reads a number an enhancement can move belongs below that loop.**
`blockedHexes` genuinely does not (it reads only where ships ARE), which is exactly why the twin
looked safe beside it — the two maps are identical in shape, lifecycle and publication and differ
only in whether an enhancement can reach their input. `setEdfHexes()` now runs immediately after
`markUnavailableSetMarkers()`; nothing between the old and new call sites reads `$edfHexes` or
`$edfPresent` (every consumer is in firing / criticals / AoE, i.e. a later step of a later
request), so the call simply moved.

⚠️ **Stage 4 could not have caught this and neither could any unit test.** Without the refit the
blueprint radius and the effective radius are the same number, so every Stage 4 and Stage 5 check
passed on both sides of the bug. The guard is now **game 4336 in the replay corpus** — the snapshot
check is `stripForJson()`, which publishes `edfHexes`, so the baseline pins all 91 hexes and all
three post-refit radii. It is the first corpus game with an EDF at all.

⭐ **THE LIVE BOOST PREVIEW (user request 2026-09-05).** Double power is allocated in Initial
Orders and the server does not learn of it until the phase is SUBMITTED — so the map disc grew
only once it was too late to change your mind about paying for it. The fix publishes a SECOND
number, `boostedRadius` (from `getEdfBoostedRadius()`), beside `effectiveRadius`, and
`PhaseStrategy.getEdfRadiusForShip` **picks between the two** on the strength of the local,
uncommitted `system.power` entry that `shipManager.power.getBoost` reads. Unboosting picks the
first one again, so the disc shrinks. `showEdfField` already caches on radius + anchor hex, so a
changed radius redraws and an unchanged one is free; the seam that fires it is
`onSystemDataChanged`, which `power.clickPlus` / `clickMinus` both raise — **gated on
`system.name === 'EnergyDrainingField'`**, because that handler is one of the busiest in the file
and `syncAllEdfFields` walks every icon on the board.

⚠️⚠️ **IT PICKS, IT NEVER ADDS, and that is the whole reason for the second published number.**
The obvious client-side version — `radius + boostRadiusBonus` — is wrong in a case the client
cannot see: a field that has taken an `EdfBoostLost` critical can still have power allocated to
it (`hasMaxBoost()` does not test criticals and the crit is not readable from there), so the map
would promise hexes the server will never honour. `getEdfBoostedRadius()` already answers "the
boost is gone" by returning the unboosted radius, so the two numbers arrive EQUAL and clicking +
correctly moves nothing. Same rule as the note on `showEdfField`: **the radius is published,
never mirrored.** The SCS icon number now reads `boostedRadius` for the same reason, keeping the
blueprint sum only as the lobby fallback (where no critical has been rolled yet).

⭐ **DEACTIVATION previews the same way (user, 2026-09-05).** A field switched off projects
nothing, so its disc goes the moment `shipManager.power.isOffline` answers true and comes back if
the player changes their mind in the same phase. Two things about it are not obvious:

- **Offline is tested FIRST and wins outright.** Offlining does *not* clear a boost allocation, so
  a type-1 and a type-2 entry can sit on one system at once — and the server resolves that pair
  the same way, `getEdfRadius()` returning 0 on `!isEdfActive()` before it looks at anything else.
- ⚠️ **`onSystemDataChanged` arrives in TWO SHAPES and testing only the first misses half the
  feature.** The per-system power clicks send `{ship, system}`; `offlineAll` / `onlineAll` — the
  "all systems of this name" buttons in `SystemInfoButtons` and `SystemPowerSettings`, which an
  EDF is as eligible for as anything else — send `{ship}` alone. The gate is therefore
  `system ? system.name === 'EnergyDrainingField' : PhaseStrategy.shipCarriesEdf(ship)`, which
  stays exact instead of widening to every systemless event.

**What is deliberately NOT previewed: the to-hit penalty.** `weaponManager.getEdfPenaltyHexes`
reads `gamedata.edfHexes`, which is server-built and cannot know about an uncommitted change —
mirroring it would mean rebuilding the whole hex map client-side. It costs nothing here because
**a field never penalises its own fleet**: the only shots your own field's hexes affect belong to
the enemy, whose client cannot see your uncommitted allocation either way.

⚠️ **A Stage 4 BUG found while building this: the boosted radius was FREE.** The class declared
`boostEfficiency = 0` under a comment saying "double power" — but `boostEfficiency` is the EXTRA
power one boost level costs (`power.js countBoostReqPower`/`countBoostPowerUsed` multiply by it), so
0 means the boost costs nothing at all. It is now set from `$powerReq` in the constructor, which is
the only place it can be written because the requirement is a ctor argument. **This changes the
Traveler's power economy**: a boosted field now costs 32 rather than 16.

---

**As built — Stage 4a, the FIELD (2026-09-04). The DRAIN (§2.2) and the map OVERLAY are not
built yet; see "What is left" at the end of this block.**

Everything below is the field itself: the system, the published hex map, and the targeting
penalty on both sides. That is three of the stage's four exit criteria — *field map published*,
*targeting penalty matches server↔client to the point*, *own-fleet immunity and multi-field
non-stacking hold*. *Drawn*, *drain lands as crits* and *the Enormous clamp* belong to the
unbuilt half.

⚠️⚠️ **THE STATS ARE PLACEHOLDERS AND ARE MARKED SO IN-FILE.** D4 says the numbers arrive as a
Walker hull carrying the system; none has landed for the EDF. Six class constants at the top of
`EnergyDrainingField` carry every game number — `DEFAULT_RADIUS 2`, `BOOST_RADIUS_BONUS 1`,
`MIN_RADIUS_FIXED 1`, `MIN_RADIUS_VARIABLE 0`, `DEFAULT_HEALTH 12`, `DEFAULT_POWER 8` — and
nothing outside that block hard-codes one, so a re-stat is an edit to those six lines. Also
outstanding: a real `EnergyDrainingField.png` (it borrows `SparkField.png`, one line, marked), a
point cost, and the drain magnitudes §2.2 needs.

⭐ **IT LIVES IN `baseSystems.php` / `baseSystems.js`, and that is the SAME load-order rule §3.4
records for the CPD, applied in the other direction.** The EDF is a `ShipSystem`, not a `Weapon`,
so its client twin belongs in `client/model/system/baseSystems.js` — the FIRST model file both
`game.php` and `gamelobby.php` load, so nothing can be built before its prototype exists. A new
pair of files would have meant two `<script>` tags, two bundle rebuilds and a fresh chance to get
the ordering wrong. **Keep each server/client pair in matching files.**

**The system.** `EnergyDrainingField extends ShipSystem implements SpecialAbility, EdfSource`,
constructed `($armour, $maxhealth = 0, $powerReq = 0, $radius = null, $variable = false)` — 0/null
take the class defaults, the same convention the Walker weapons use so a hull can mount "a basic
version" without inventing numbers.

- **`interface EdfSource`** (`getEdfRadius($turn)` / `isEdfActive($turn)`) is the *only* thing
  `setEdfHexes()` consumes, so the Stage 6 mine terrain and the Stage 7 net plug in by
  implementing it and changing nothing else.
- **Variable fields are the existing BOOST mechanism** — `PowerManagementEntry` type 2, allocated
  in the Ship Power segment, which is exactly where the rules put the choice. No new power
  concept. Boost is per TURN, so a field boosted on turn 3 reads normal on turn 4.
- **Deactivation is `canOffLine`**, because all-or-nothing is already what offlining means.
- **Criticals escalate on the `hasCritical()` COUNT**, no per-crit bookkeeping: `EdfRadiusReduced`
  costs a hex each on a fixed field; on a variable field the FIRST `EdfBoostLost` costs the boost
  and every further one a hex. One crit class per field type, both ordinary persisted criticals
  (contrast `ScannedByWalkers`, which is rebuilt every load and never saved).
- ⭐ **ONE PHPCLASS, TWO CRIT TABLES.** The variable field swaps `$possibleCriticals` in the
  constructor rather than being a second class: `SystemFactory` builds the client twin with
  `new window[name]`, so a second phpclass would need a second client class and a second
  blueprint entry for one changed array. Safe on the server because `$possibleCriticals` is
  per-instance; on the CLIENT the same-phpclass reference sharing is real (trap 6), so the
  ctor clones `data` and the server republishes `data`/`radius`/`variable` per instance.
- ⚠️ **`startArc`/`endArc` are declared 0..360 on purpose.** A system whose arcs are both 0 has
  its SECTION's arc stamped on by `addSystem()` (`arch_addsystem_section_arc_trap`), so an
  aft-mounted EDF would advertise itself as aft-facing. The field is omnidirectional.
- ⚠️ **The radius floor is a floor on the CRITICAL REDUCTION, not on the blueprint.** A plain
  `max(1, …)` silently promotes a deliberately-designed radius-0 fixed field into a 7-hex one.
  It is `max(min($this->radius, MIN_RADIUS_FIXED), $reduced)`. The test caught this.

**The map.** `TacGamedata::$edfHexes`, `{ "q,r": { teams: { <teamId>: 1 } } }`, built by
`setEdfHexes()` in `onConstructed()` **immediately after `setBlockedHexes()`** and published
whole in `stripForJson()`. Same shape, same lifecycle, same call site, not per-viewer — because
`blockedHexes` has already proved that map-once/publish-once stays in sync with the client, which
is exactly what a penalty mirrored "to the point" needs.

- Keying by HEX makes *"overlapping hexes are only counted once"* and *"additional fields do not
  stack"* **structural** rather than special cases: two fields, one entry.
- `teams` being a SET turns *"the rest of the fleet of the ship deploying the EDF is immune"*
  into one array lookup instead of a per-shot sweep over every field in the game.
- ⚠️ **Three exclusions, each a bug if dropped:** destroyed units, units still in HYPERSPACE
  (`isReinforcement()` — worse here than in `setBlockedHexes` because this map goes to *every*
  viewer and would announce an arrival box a turn early), and units with no position yet.
- ⚠️ **NULL, never `array()`,** when nothing projects (trap 9).
- ⚠️ **There is ONE mid-request writer, added at Stage 6: `registerEdfField()`.** An Energy
  Draining Mine's probe lands during Firing, after this map was built, and has to drain at the
  Critical Hit step of its own turn — so it folds a single hex-disc in. It is **additive on
  purpose**: a rebuild would also re-test every existing field against post-firing state and drop
  the field of a Walker shot down in that same step. See §3.6.

⭐ **THE GATE NEEDS NO `DBManager` RESET, and that is a deliberate difference from
`CpdScanRegistry`.** `TacGamedata::$edfPresent` is cleared at the top of `setEdfHexes()`, which
rebuilds the whole map from scratch on every load — so the double gamedata load in one request
(trap 1) cannot double-count *by construction*. **Do not add an accumulating cache here without
also adding the reset**, which is the trap the Gravitic Augmenter investigation paid for.

**The penalty.** `-1` to hit per hex of somebody else's field the shot crosses, ×2 for Plasma and
Antimatter of a young or middleborn race. In d20 units like every other term, so the single ×5 at
the bottom of `calculateHitBase` converts it. `TacGamedata::getEdfPenaltyHexes()` is the server
authority; `weaponManager.getEdfPenaltyHexes()` / `getEdfPenalty()` mirror it, and the doubling
rule is duplicated in both — **change them together**. It uses `$launchPos`, so a ballistic is
drained along the line it actually flew.

⚠️ **`sPosLaunch`/`sPosTarget` are only filled in for BALLISTICS in `calculateHitChange`** —
direct fire leaves both null, and passing them straight through would have made the penalty
ballistic-only. Both ends are resolved locally instead, matching the server's `getFiringHex()`
(which answers the shooter's own hex for a non-ballistic).

⚠️ **It is gated on `TacGamedata::$edfPresent`, not on the map**, for the D10 reason: this runs
for every shot in every game of every faction. Client side, on `gamedata.edfHexes` being non-null.

⚠️ **`gamedata.js` had to be taught to copy `edfHexes` by name** — the same one-line requirement
that left the whole CPD client half dead for a day (`arch_gamedata_named_key_copy`). Declared at
the top of the singleton and assigned **unconditionally** in `parseServerData`, normalised to
null so a replay stepping back before the field clears it.

⭐⭐ **PHP's `round()` IS NOT JS's `Math.round()`, IN TWO WAYS, AND BOTH BIT.** The client needs
the identical hex line, so `mathlib.hexLine()` is a port of `HexZone::line()` — and a port of
`round()` with it, in `phpRound()`:

1. **Half away from zero.** PHP `round(-2.5) === -3`, JS `Math.round(-2.5) === -2`. Cube
   coordinates go negative all over a Fiery Void map. **843 of 4,000 corpus lines** disagree
   without this.
2. **PRE-ROUNDING.** PHP first rounds to `14 - floor(log10|v|)` decimal places, so a value
   floating-point error left at `-20.49999999999999644` is treated as `-20.5` and lands on `-21`.
   Fixing only (1) still left **13 of 4,000** lines wrong — the hardest kind of mismatch to
   notice, because it only appears when a line passes almost exactly through a hex corner.

Both halves have their own non-vacuity control in the test. ⚠️ Written against **PHP 8.2**; if FV
moves to a version that changes `round()`'s edge cases, re-run the differential test before
assuming the port still matches. **Any future JS port of a PHP geometry routine needs this.**

⚠️ It deliberately does NOT reuse `mathlib.isLoSBlocked`'s geometry, which asks a different
question (does the segment *clip* a hex, tested in pixels) and would count hexes the server's
line never enters. Line of sight and field crossing are two different rules.

**Hull.** The `Traveler` mounts one fixed field aft and its `//STAGE 4` chart row (Aft 9) is
restored. ⚠️ Positional-id trap again (trap 7): the new system is id 14 and everything after it
shifts, so **a game in progress with a Traveler in it will desync** — the same cost Stages 2 and
3 paid.

**How it was proven.** Two throwaway suites, kept in `c:\tmp\`: **71 server checks and 32 client
checks**, all green.

- **Server (`c:\tmp\edf_stage4_test.php`):** the system's shape, defaults and all-round arc; both
  crit ladders including each floor and the blueprint-radius-0 case that caught the floor bug;
  boost on and off and its per-turn scope; deactivation and the turn after; the hex map's disc
  size at radius 1 and radius 0; all three exclusions; ⭐ **overlap collapsing with both teams
  recorded once each, and two fields on ONE hull not stacking the map**; the penalty count,
  own-fleet immunity, the team-less shooter, and ⭐ **a hex covered by BOTH sides still being
  charged to each of them** (the exemption is "only my team covers it", not "my team covers
  it"); plus a source audit of the gate, the `onConstructed` ordering, the null-not-array
  publication, the `parseServerData` named-key line, the client mirror's three call sites, the
  `phpRound` pre-rounding, and the restored hit-chart row.
- **Client (`c:\tmp\edf_client_test.js`):** `mathlib.js` **evaluated**, not merely parsed, with
  `hexLine` asserted to be *on the object* (a helper that lands outside an object literal still
  parses) and `isLoSBlocked` asserted to have survived the insertion; then ⭐⭐ **the
  differential run: 4,000 lines / 128,745 hexes generated by the real `HexZone::line`, zero
  mismatches**, with non-vacuity assertions that the corpus really reached negative coordinates
  and spanned 20+ line lengths; **both rounding controls**; 500 penalty cases against the real
  `getEdfPenaltyHexes`, zero mismatches, with a check that they were not all trivially zero; the
  own-fleet and empty-map gates; all six doubling cases; and the client class evaluated as a
  `window` global with the trap-6 data clone and the compactor's `variable: undefined` case.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted baseline — the one new
warning it raised was the missing placeholder icon, which is why the class borrows an existing
one), the replay harness **130 passed / 1 failed** (game 4325, the known pre-existing clean-tree
failure, verified byte-identical with the tree stashed), autoload regenerated, statics
regenerated and the EDF confirmed present in `Walkers of Sigma-957.json`.

**What is left in Stage 4 (§2.2 and the overlay):**
1. **The drain resolver** — the six `EdfExposure` criticals, the `GraviticMineHandler`-shaped
   once-per-turn sweep owned by the EDF system, the four choke-point readers (§1.7), the
   `EdfExposed` escalation counter, the Enormous clamp and the `critRollMod` fighter-dropout
   branch. **Blocked on the control sheet:** the drain magnitudes, the dice and the per-turn
   escalation are not in this plan and D4 says they arrive with the hull.
2. **The renderer overlay** — a translucent field drawn from `gamedata.edfHexes`, reusing
   `HexagonSprite`. ⚠️ `requestRender()` (trap 10) or it silently will not appear.

---

**As built — Stage 4b, the DRAIN and the OVERLAY (2026-09-04). Stage 4 is now complete.**

The user supplied the field's own control-sheet numbers (radius 5, boost +3, 40 boxes, 16 power)
and then, the same day, the **full EDF rules text** - so nothing in Stage 4 is inferred any more.
Six constants at the top of `EdfExposure` still carry every game number, and nothing outside that
block hard-codes one; they are now quotes rather than guesses.

⭐⭐ **THE RESOLVER IS CALLED FROM THE TOP OF `Criticals::setCriticals`, NOT FROM
`criticalPhaseEffects()` — and the plan's own reasoning for the latter turned out to be wrong.**
§2.2 wanted the EDF system to own a sweep in `criticalPhaseEffects` (pass 2) and to feed the
fighter dropout by setting `$fighter->critRollMod`, which `Fighter::testCritical` already sums into
its roll. Two things kill that:

- **`testCritical` only runs on a craft that was DAMAGED this turn** — criticals.php pass 1 gates
  every call on `isDamagedOnTurn()`. A pristine flight parked in a field would never roll at all,
  so a `critRollMod` nudge would reach nothing in exactly the case the rule is about.
- `criticalPhaseEffects` is pass **2**, i.e. after the dropout rolls it was meant to influence.

So `EdfExposure` is a handler class (`source/server/handlers/EdfExposure.php`) called before pass 1,
in the shape `HkJamming` already uses from the tail of the same method, and it **rolls its own
dropout** — mirroring `testCritical`'s comparison (roll vs remaining health, plus the flight's
dropout bonus and any `critRollMod`) with `(consecutive turns + 1)` dice instead of one, which is
the plan's "2d10, +1d10 per successive turn". Running before pass 1 keeps the two rolls from
interleaving on one craft.

⭐⭐ **REPLAY DETERMINISM WITHOUT A ROLL NOTE, and this is the part worth reusing.** `setCriticals`
re-runs on replay and `Dice::d` is not deterministic, which is why `HkJamming` has to persist its
d20 in an `IndividualNote`. This resolver needs no note: **the RESULT is the record.** Every roll it
makes goes straight into a persisted critical's `param`, and the `EdfExposed` marker says "this unit
has already been resolved for turn N". A reload finds the marker and skips.

⚠️ The `$resolvedTurn` static only stops the sweep running twice inside ONE request. Idempotency
across requests is the marker, and reading it needs a **direct scan of `$system->criticals`, not
`hasCritical()`** — `EdfExposed` is `oneturn`, so `hasCritical` reports it on turn N+1 and asking
"is there one for THIS turn" always answers no. Get that wrong and every reload re-rolls the whole
drain.

⭐ **`oneturn` IS the "starting next turn" mechanism, and it also does the cleanup.**
`hasCritical`/`sumCriticalParam` report a `oneturn` critical only when `crit->turn + 1` equals the
turn being asked about, so a drain rolled in turn N's Critical Hit step is invisible on turn N,
lands on turn N+1, and expires by itself. Nothing sweeps them up.

⚠️⚠️ **NONE OF THE SIX CRITS MAY CARRY AN `$outputMod`, however tempting it looks.** Routing the
thrust and power drains through `outputMod` would have been free on both sides (the server's
`getOutput()` and the client's `shipManager.systems.getOutput` both already read it). But
`ShipSystem::effectCriticals()` sums `outputMod` across **every** critical with **no turn filter at
all** — it is built for permanent `OutputReduced*` crits and is called once from `onConstructed` —
so a one-turn crit with an `outputMod` would apply from the turn it was rolled and then for ever.
`Engine::getOutput()` and `Reactor::getOutput()` read `sumCriticalParam()` instead, which IS
turn-filtered, and publish the turn's figure as a separate `edfDrain` field for the client.

⚠️ **The four param crits are SUMMED, never counted.** One crit is a whole Nd10 roll, the same
convention `DamageReductionReduced` uses. `hasCritical()` would read every drain as 1.

**The four choke-points** (§1.7), all as the plan predicted:

| effect | where | note |
|---|---|---|
| thrust | `Engine::getOutput()` | flights have no Engine — see below |
| energy | `Reactor::getOutput()` | |
| initiative | `BaseShip::getCommonIniModifiers` | ⚠️ ×5 (plan trap 5) and the rules' −20 tabletop cap is applied to the EDF's own accumulated total, **not** to `$mod` — everything else in that method is a separate effect and must not be squeezed by it |
| total EW | `EW::getScannerOutput` | beside the existing `RestrictedEW` / `SensorLoss` terms |

⚠️⚠️ **THE READERS ARE GATED ON `!empty($this->criticals)`, NOT ON `TacGamedata::$edfPresent`** —
and that is not an oversight. `$edfPresent` means "a field is on the board **right now**". A Walker
destroyed on turn 5 leaves its turn-5 drain in effect through turn 6, when the gate is already
false. The criticals array is the correct and equally cheap test: no criticals, no drain, and an
undamaged system's array is empty anyway. The same reasoning ungates
`Firing::withdrawGroundedFighterFireOrders`.

**A FLIGHT is a different unit throughout, and every branch is deliberate:**
- **no Engine**, so the thrust crit rides the **sample fighter** and comes off `freethrust` via a
  new `FighterFlight::getEffectiveFreeThrust()` — which is also what `AutomatedMovement` now spends
  from, so an HK cannot plot a pursuit on thrust it does not have;
- **no Reactor and no EW-output system**, so those two drains are not rolled at all;
- the initiative crit rides the sample fighter, which is already where `getCommonIniModifiers`
  reads a flight's initiative criticals;
- `EdfFighterGrounded` withdraws its fire orders next turn.

⚠️ **`getEffectiveFreeThrust` had to resolve a null turn itself**, and the test is what found it:
`sumCriticalParam($type, $turn = false)` defaults to the current turn on **`=== false`**, not on
null. A null passed straight through makes every turn comparison fail and the drain silently reads
0 — while the same getter called with an explicit turn is correct, which is the only way the two
could disagree. **Check this on every `sumCriticalParam` caller with a nullable turn.**

⚠️ **The grounded-fighter check has to be on the ADVANCE path.** It lives in
`Firing::withdrawGroundedFireOrders`, called from both `prepareFiring` and the PreFiring path, in
the same withdraw-and-detach shape as the surrender sweep. **Not `validateFireOrders`**: a POST-side
ship is reconstructed with no criticals at all
([arch_post_side_ship_reconstruction](source/server/model/ships/ShipClasses.php)), so the same test
there would read "no crit" for every flight in the game and silently do nothing, for ever.

**The overlay.** A translucent disc on the projecting unit's own icon —
`ShipIcon.showEdfField(radius)` built from the existing `buildHexRegion` / `buildRegionOverlay` /
`addGridLockedOverlay` trio, driven by `PhaseStrategy.syncAllEdfFields()` from the same two call
sites as `syncAllDeclaredAreas` (a poll rebuilds every ship object, so a standing overlay has to be
re-read).

- ⚠️ **NOT drawn from `gamedata.edfHexes`.** That map is collapsed by hex and by team — it is built
  for the penalty arithmetic and cannot say which source a hex came from — so drawing from it would
  fuse two overlapping fields into one shapeless blob with no border between them. Each source
  draws its own disc and the fills compound where they overlap.
- ⚠️ **The radius is PUBLISHED, not mirrored.** `effectiveRadius` comes off `stripForJson` after
  criticals and boost; reimplementing the crit ladder client-side would be a second copy of a rule
  that changes. It reaches the client through the LIVE payload only — the static blueprint is
  `json_encode($ship)` and never calls `stripForJson`, which is correct, because the effective
  radius is per-turn state and a blueprint is not.
- ⚠️ `requestRender()` (trap 10), and **only when something changed** — a redraw of nothing must
  not wake the idle-gated loop on every poll of every game. `gamedata.edfHexes` being null is the
  cheap gate that skips the whole sweep in an ordinary game.

**How it was proven.** `c:\tmp\edf_drain_test.php`, **105 checks green**: all six crit classes
including ⭐ **an assertion that not one of them carries an `outputMod`**, and the `oneturn`
timing proved in all three turns (invisible, felt, gone); the resolver landing each drain on the
right system; ⭐ **idempotency demonstrated by re-resolving the same turn and asserting nothing
changed**; own-fleet immunity **and** the case that distinguishes it (a hex both sides cover still
drains an own-fleet ship); the three exclusions; the escalation counter driven over four turns with
⭐ **the Enormous clamp proved load-bearing** — 60 sampled runs showing an ordinary unit routinely
beats one die's ceiling where an Enormous one never does; all four readers including the ×5, the
−100 floor and the clamp at 0; the publication of `edfDrain` present-when-drained and absent-when-
not; the whole flight branch, with ⭐ **dropouts proved to happen on an UNDAMAGED flight** (the
whole reason the resolver rolls its own) and a tough-craft control proving the roll is a roll; and
a source audit of the call site's position relative to pass 1, the reset being inside the gate, the
two firing paths, the two ungated readers, and the client mirrors. Plus, for the corrections
below: ⭐ **300 sampled first-turn exposures proving EW never exceeds 6 while the other three
reach 10**; the blackout cascade reaching a powered system AND a powerReq-0 Weapon but NOT the
C&C, landing on turn+1 and not on the turn it was rolled, with a hardy-reactor control; and
`isHexInEdfField` asserted TEAM-BLIND.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted), the replay harness
**130 passed / 1 failed** (game 4325, the known clean-tree failure), autoload and statics
regenerated, and every changed client file `node --check` clean.

**THE REAL RULES ARRIVED THE SAME DAY (user, 2026-09-04) and corrected three things.** The build
above had been made against the plan's inferences; the rules text settled all of it. What changed:

⚠️⚠️ **THE EW DRAIN IS d6, NOT d10.** *"The ship's total EW is reduced by **1d6** for the next turn,
increased by a further 1d6 for every additional turn"* — while Free Thrust, Energy and Initiative
are all 1d10 escalating the same way. The first build used one die for all four and was wrong about
EW alone. `EdfExposure::EW_DIE` now sits beside `DIE`, and `roll()` takes the die **explicitly with
no safe default in practice** so the one exception cannot be forgotten again. The test samples 300
first-turn exposures and asserts the observed ranges — a single roll cannot tell 1d6 from 1d10.

✅ **Everything else the plan inferred was right:** d10 for the other three, dice = consecutive
turns, `ENORMOUS_DICE = 1` (*"the modifiers are limited to the first die (1d10 or 1d6) and do not
increase with every additional round"* — note it covers **both** dice), dropout at 2d10 +1d10 per
turn, the −100 FV initiative cap, and the own-fleet exemption. The `GROUND_FIGHTERS` flag is gone:
*"Even if the fighter/shuttle does not drop out, it will not be able to shoot the next turn, and
loses initiative and free thrust in the same manner as a ship"* is a rule, not an assumption, so it
is unconditional. That sentence also confirms a flight takes **initiative and thrust only** — no
energy, no EW — which is what the data model was already doing for want of a Reactor or an EW system.

⭐⭐ **THREE EFFECTS THE FIRST BUILD DID NOT HAVE, all now in:**

1. **The reactor blackout.** *"If the ship's reactor is completely drained of power, this will force
   the deactivation of everything on the ship that requires energy. This includes any weapon or
   system with a power diamond, even if that icon contains a zero (such as missile racks)."*
   ⭐ **That cascade already existed and did not need writing.** `Reactor::addCritical` propagates a
   `ForcedOfflineOneTurn` to every system with `powerReq > 0` **or** `instanceof Weapon` — which is
   precisely "a power diamond, even if that icon contains a zero", because a missile rack is a
   Weapon with `powerReq` 0. It is the same mechanism a reactor knockout already uses and it handles
   the StarBase per-section case for free. `EdfExposure::applyPowerBlackout()` is one call into it.
   ⚠️ The trigger is measured against the reactor's output **next** turn — raw output minus the
   whole `EdfPowerDrain` that will be in effect then, including the crit just added. `getOutput()`
   cannot answer that: it reads the CURRENT turn, where a `oneturn` crit is invisible by design.
   The timing needs nothing extra either — `ForcedOfflineOneTurn` is itself `oneturn`, so the
   blackout lands on exactly the turn the drain does.

2. **Flash weapons score no collateral inside a field.** *"If they strike a unit located within the
   field, they will only affect the target (collateral damage will not be scored). The first unit
   will still take full damage."* One early return in `Weapon::doCollateralDamage`.

3. **Proximity weapons lose their radius inside a field.** *"…only detonate in that hex, losing any
   explosion radius they might normally have. They will still cause their full damage within the
   target hex."* In `AoE::fire` that is exactly `$ships2 = $ships1` — and it is measured at the hex
   the shot **actually landed on, after deviation**, not at the declared one.

⚠️ **Both of those use `TacGamedata::isHexInEdfField()`, which is deliberately TEAM-BLIND** — unlike
`getEdfPenaltyHexes()`. The own-fleet exemption is about who the field is aimed *at*; these two are
the field dampening an explosion, which is a property of the hex, and the rules say of each that it
*"applies to advanced race weapons as well"*, i.e. no exemptions.

---

**As built — Stage 4c, PLAY-TEST FIXES (2026-09-04, game 4334).** Four reports off the first real
game with a Traveler in it. Three were one bug wearing three hats.

⚠️⚠️ **"THE REACTOR IS COMPLETELY DRAINED" IS NOT `output - drain <= 0`, AND THIS IS A FACT ABOUT
EVERY FV BLUEPRINT, NOT ABOUT THE EDF.** A Fiery Void `Reactor`'s constructor `output` is the
ship's **SPARE** power with every system powered up — not the reactor's generation. Virtually every
hull in the database is therefore built `new Reactor($armour, $maxhealth, 0, **0**)`: a Bin'Tak, a
Thoughtforce and an Abbai Bimith all read 0. Measuring the drain against that figure meant **one
point of drain blacked out any ship the field touched**, and took its owner's chance to trade
systems for power away with it. `EdfExposure::applyPowerBlackout` now measures against
`getMaxAvailablePower()` — the ceiling the owner could reach by switching off everything they are
allowed to — which is the user's ruling: *"the reactor can sustain as much negative power as the
total amount of power it provides to offline-able systems."* Anything less is an **ordinary
deficit**, handled where FV already handles one: the player powers systems down in Initial Orders
and `gamedata.doCommit` → `getShipsNegativePower` blocks the commit until they do.

`getMaxAvailablePower()` is the server-side mirror of the client's
`shipManager.power.getRemainingFreeablePower` (power.js) — **keep the two in step** — and it has
three special cases, all of which the user named in the report:

- ⚠️ **MAG-GRAV REACTORS (`$fixedPower`: Ipsha, and the Vorlon technical mount) ARE THE OTHER WAY
  ROUND.** Their `output` is the reactor's TOTAL generation, and every powered system is subtracted
  from it — which is exactly what *"provides fixed total power, regardless of destroyed systems"*
  means. So their ceiling is that total **minus only the draws that cannot be shed**; adding the
  freeable ones back would count the same power twice and make an Ipsha hull unblackout-able.
- ⚠️ **POWER CAPACITORS AND PLASMA BATTERIES (Vorlon, Pak'ma'ra) hold real spendable power that the
  SERVER OBJECT CANNOT SEE.** `PowerCapacitor.initializationUpdate` rewrites `powerReq` to
  **negative `powerCurr`** to inject the charge into the reactor display — a client-only rewrite.
  The stored charge is added here by class instead. Without it a Vorlon hull (whose Mag-Grav
  technical reactor generates **0** and runs entirely off its capacitor) blacks out with a full
  capacitor.
- ⚠️ **`powerLocked` IS `linkedOrbital !== null && !stowed`, NEVER `!stowed` ALONE.**
  `Weapon::$stowed` is **declared on `Weapon` and defaults to false**, so a bare `!stowed` test
  power-locks the entire armament of every hull in the game. The pairing is what makes a deployed
  Kirishiac orbital's beam unsheddable; a standard mount has `linkedOrbital === null`, which is
  what keeps the whole test free for everyone else.
- A StarBase's sweep is restricted to the drained reactor's own section, because
  `Reactor::addCritical` blacks out only that section.

⭐ **THE CLIENT NEVER SAW THE DRAIN AT ALL.** `shipManager.power.getReactorPower` reads
`reactor.output + reactor.outputMod` **directly** — it does not go through
`shipManager.systems.getOutput`, which is the only place the published `edfDrain` was being applied.
So a drained ship showed a perfectly balanced reactor and its owner was never asked to power
anything down. The subtraction is now in `getReactorPower` too, in **both** its branches (the
StarBase multi-reactor one and the ordinary one) and deliberately **NOT clamped at 0**: unlike an
output figure, a balance is meant to go negative, and that negative **is** the deficit.
**Generalises: `getReactorPower` is the ship's power BALANCE and `getOutput` is one system's
output — a rule that changes what a reactor supplies has to be applied in both.**

⭐ **THE THOUGHT SHIELD "REGENERATING ONLY THE SCANNED AMOUNT" WAS THIS SAME BUG, NOT A CPD BUG.**
Reported as a Chromatic Pulse Driver interaction; it is not. `ThoughtShield::criticalPhaseEffects`
**returns early when the Thought Shield Generator is offline**, and the blackout cascade had
forced it off. The reason it looked like a scan bug is arithmetic: `doProtect` absorbs
`min(capacityAfterScan, damage)`, so an **overwhelmed scanned pool always ends the turn holding
exactly the scan** (25 − 23 = 2 points, and the scan was 2). Nothing in the regeneration path is
scan-aware and nothing should be — the scan is computed per shot and never stored (§3.4). Proven
both ways: with the generator forced off, no regeneration entry at all; with it online, one entry
of −23 restoring the full 25, while a scanning fleet still only gets through 23 of it.

⭐ **A LOG-ONLY FIRE ORDER MUST CARRY A NON-ZERO `rolled` OR THE PRINTED COMBAT LOG SILENTLY DROPS
IT.** The EDF drain has always written its `pubnotes` row and it has never been visible.
`weaponManager.getAllFireOrdersForLogPrint` filters every order through `isResolvedFireOrder()`,
which is nothing but **`Number(fire.rolled) > 0`** — so the row was discarded before
`combatLog.logFireOrders` ever saw it, pubnotes and all. `HkJamming` passes its real d20 there and
so has always printed; `EdfExposure` spends all its rolls on criticals, so it now passes a bare `1`
as the "this order resolved" marker. `shots`/`shotshit` still stay 0 (`submitDamages` links unknown
damage by `shotshit > 0`). `"EdfExposure"` also joins `weaponManager.doShortLogText`'s list, or the
sentence prints behind *"firing 1x Ramming Attack at &lt;itself&gt;. 0/0 shots hit"*.
⚠️ Rows already in `tac_fireorder` keep their stored `rolled` 0 — turns played before this fix stay
blank in the log. **Check this on any future technical fire order.**

⭐ **THE GROUNDED FLIGHT NOW SAYS SO, IN PURPLE.** *"Even if the fighter/shuttle does not drop out,
it will not be able to shoot the next turn"* is enforced silently on the advance path
(`Firing::withdrawGroundedFighterFireOrders`), so a player declared a full turn's shooting and only
found out when the orders vanished. Two mirrors, both reading the `EdfFighterGrounded` `oneturn`
crit off the flight's **sample fighter** with the same `crit.turn + 1 === gamedata.turn` test
`ShipTooltip.js` already uses for `Uncontrolled`: a map-tooltip line and a ship-window status
banner (`getStatusBanners`, which the flight variant renders). The colour is `#d250ff`,
`EWIconContainer`'s existing `COLOR_JAM`, so the two "your unit is suppressed this turn" states
read as one family and are distinct from red damage.

**How it was proven.** Four throwaway suites in `c:\tmp\`, 62 checks, all green.
`edf_blackout_test.php` (26) drives the real `applyPowerBlackout` over the reported hull at drains
1 / 18 / 48 / 49 / 200 — with ⭐ **the pre-fix rule evaluated verbatim beside each one, asserting it
would have blacked out, so no case is vacuous** — plus the cascade actually landing, an Ipsha
Battleglobe proving the fixed-power branch differs from the standard one, a Vorlon Destroyer Escort
proving a charged capacitor raises the ceiling by exactly its charge, and the `$stowed`-defaults-
false trap asserted directly. `edf_shield_test.php` (12) reproduces game 4334's shield **both ways**.
`edf_log_test.js` (8) and `edf_power_test.js` (6) **evaluate the real `weaponManager.js` and
`power.js`** in a `vm` sandbox rather than testing a copy. `edf_grounded_test.js` (15) pulls the two
private helpers out of the real file source (the `HexZoneRef` trick) and drives all three turns.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted baseline), replay
harness **129 passed / 1 failed** — game 4325 only, the known pre-existing clean-tree failure — and
`yarn build` run for the React banner (`UI.bundle.js`) and the legacy bundle.

---

**As built — Stage 4d, THE DRAIN IN THE COMBAT LOG AND ON THE SCANNER (2026-09-04, game 4334).**
Three refinements off continued play-testing. Nothing about the drain's *magnitude* changed; this
is entirely about where the record lands.

⭐⭐ **A LOG-ONLY FIRE ORDER MUST HANG OFF THE SHOOTER'S OWN WEAPON, AND THAT DECIDES WHO THE
SHOOTER CAN BE.** The drain reported itself as a **self-targeted** order on the drained unit, so
the log read *"FIRE: &lt;your own ship&gt;"* in your own team's colour and every log filter — by
ship, by shooter, by team — filed the Walker's attack under its victim. It is now a normal
`Walker -> victim` order. The constraint that shapes the fix: the client resolves `fire.weaponid`
**against `gamedata.getShip(fire.shooterid)`** (`weaponManager.getAllFireOrdersLog`, and
`combatLog.logFireOrders`'s own `shipManager.systems.getSystem(ship, fire.weaponid)`), so the row
has to sit on a **Weapon belonging to the shooter** — the Walker's `RammingAttack`, not its
`EnergyDrainingField`, which is a `ShipSystem` and would send the log loop into
`weapon.changeFiringMode()` on an object that has none. **Generalises to every technical fire
order: pick the host off the SHOOTER, and pick a `Weapon`.**

⚠️ **A SHORT-FORM LOG ENTRY PRINTS ITS `pubnotes` ALONE.** `EdfExposure` is in
`weaponManager.doShortLogText`, and that branch is literally `html += notestext` — the *"at
&lt;target&gt;"* clause the long form would render is never built. So the moment the shooter stopped
being the victim, the **victim's name had to move into the pubnotes text**, or the entry named
only the Walker and left the player guessing which of their ships it meant.

⭐ **WHO IS DRAINING WHOM NEEDS A SECOND MAP, BECAUSE `$edfHexes` HAS THROWN THE ANSWER AWAY.**
`TacGamedata::$edfHexes` collapses every source over a hex into a *team set* — which is exactly
what makes overlap and own-fleet immunity structural (§2.1) and exactly why it cannot name a hull.
`setEdfHexes()` now fills a parallel `$edfSources` (`"q,r" => array(shipId => team)`) in the same
loop, and `getEdfSourceShip($pos, $victim)` returns the first source that is not on the victim's
team. ⚠️⚠️ **`$edfSources` is deliberately NOT in `stripForJson()`**: `$edfHexes` tells a viewer
that *some* enemy field covers a hex, which is all the targeting penalty needs, while this one
names the hull — a Walker outside everyone's scanner range would be announced by its own
footprint. It answers with **one** ship even where three fields overlap ("additional fields do not
provide cumulative modifiers"), and a null falls back to the old self-targeted row rather than
losing the entry.

⭐ **THE EW DRAIN MOVED FROM THE CnC TO THE SCANNER — AND THAT IS WHAT MADE THE CLIENT SEE IT.**
`EdfEwDrain` used to be parked on the CnC and subtracted inside `EW::getScannerOutput()`. No client
code could reach it: `ew.js getScannerOutput` sums `shipManager.systems.getOutput` over every
`outputType === "EW"` system and never looks at the CnC, so a drained ship let its owner allocate
EW the server would not honour — the **same class of bug as the reactor one in Stage 4c**, one
system further along. On the Scanner it needs *no new client code at all*: `Scanner::getOutput()`
subtracts it and `Scanner::stripForJson()` publishes `edfDrain`, which
`shipManager.systems.getOutput` **already** subtracts for the Engine and the Reactor. The three
drains are now one shape. ⚠️ The clamp is per-scanner rather than per-ship, which only differs on a
hull carrying a second EW source beside its Scanner; ⚠️ and a hull with no Scanner now loses no EW
at all, which the log no longer claims.

⭐ **AN EDF DROPOUT LEAVES NO DAMAGE ENTRY, SO THE LOG'S FIGHTER ROW NEVER SAW IT.**
`combatLog`'s *"Fighters disengaged / destroyed:"* row is built inside the **damage** loop
(`hasCrit && damageDone > 0`), and a craft the field drops out has a `DisengagedFighter` critical
and nothing else — so the flight's losses existed only as a count inside the drain sentence.
`combatLog.getEdfDropoutNames()` now emits the same row for an `EdfExposure` order.
⚠️ It tests `crit.turn == fire.turn` **exactly**, NOT `shipManager.criticals.hasCriticalOnTurn`:
a `DisengagedFighter` is permanent (`turnend` 0) and that helper's test is `crit.turn <= turn`, so
it would re-list the same craft under every later drain report. ⚠️ It dedupes through
`combatLog.critsShown`, the same tracker the damage path uses, so a craft that dropped out under
fire is never also claimed by the field.

**How it was proven.** `c:\tmp\edf_stage4d_test.php` — 42 checks, all green — drives the real
`Scanner`, `EW::getScannerOutput`, `TacGamedata::setEdfHexes` and `EdfExposure::resolve`: the
turn-filtered drain and its clamp, `edfDrain` published only when in effect, a drain on the CnC
now proven **inert**, `edfSources` covering exactly the same hexes as `edfHexes` and staying out of
`stripForJson`, the own-field-plus-enemy-field case resolving to the enemy, the whole order
(shooter, target, weaponid, `rolled`, zero shots, victim named) and the null-source fallback, plus
a flight's grounded crit, its dropouts and its row. `c:\tmp\edf_logrow_test.js` — 8 checks — renders
one entry through **the real `combatLog.logFireOrders`** and asserts the fighter row, the
this-turn-only filter, the dedupe on a second entry and that an ordinary order grows no such row.

Regression gate: `fvbuild.ps1 -Check` — autoload map up to date, `checkShipData.php` **PASS**,
replay harness **129 passed / 1 failed** (game 4325 only, the known pre-existing clean-tree
failure). ⭐ The 129 passes are themselves the proof that `Scanner::stripForJson()` adds nothing to
an undrained hull's payload: every ship in the corpus has a Scanner and every snapshot is
unchanged.

---

**As built — Stage 4e, THE RULES READ AGAIN (2026-09-04).** Six corrections off continued
play-testing. Three of them are the same shape: the first build read a rule as *"the same penalty,
doubled"* where the rules text actually says *"a different quantity, counted differently"*.

⚠️⚠️ **THE TARGETING PENALTY COUNTS INTERVENING HEXES ONLY — NOT THE SHOOTER'S HEX AND NOT THE
TARGET'S** (user ruling). `HexZone::line()` returns `i = 0 .. steps`, i.e. **both endpoints**, and
the first build counted every hex it returned. So a Walker shooting out of its own field paid for
the hex it was standing in, and a target sitting in one was charged for its own hex on top of the
crossing. `TacGamedata::getEdfPenaltyHexes()` now runs `for ($i = 1; $i < count($line) - 1; $i++)`
and `weaponManager.getEdfPenaltyHexes` mirrors it; adjacent and same-hex shots therefore cross
nothing at all. ⭐ **Worth remembering for any future "hexes between A and B" rule: `HexZone::line`
and `mathlib.hexLine` are inclusive at both ends, and almost every game rule phrased as "hexes the
shot passes through" is not.**

⭐⭐ **"ESPECIALLY DISRUPTIVE TO PLASMA AND ANTIMATTER" IS A DOUBLED HEX COUNT — AND PLASMA SPENDS
IT TWICE** (user ruling, simplified 2026-09-04 after a first pass over-complicated it). For a young
or middleborn race's Plasma and Antimatter, **each intervening field hex counts as two**:

| | EDF targeting penalty | `rangeDamagePenalty` |
|---|---|---|
| **Plasma**, young/middleborn | **×2** (-2 per hex) | **+1 hex of range per crossed hex** |
| **Antimatter**, young/middleborn | **×2** (-2 per hex) | — |
| everything else, and every advanced race | ×1 | — |

⚠️⚠️ **NOTHING HERE TOUCHES `$distanceForPenalty`.** The range the ordinary range penalty is
computed at is the real distance for every weapon in the game, EDF or no EDF — an intermediate
design that lengthened it for Antimatter was tried and rejected as more machinery than the rule
needs. Two helpers on `Weapon` carry the whole thing: `getEdfHitPenalty()` (the count, doubled for
the two classes) and `getEdfDamageRangeBonus()` (**Plasma only**, added to `$dis` in
`getDamageMod`), with `isEdfDisruptedClass()` holding the class + `factionAge` test in one place
and `getEdfCrossedHexes()` doing the gated lookup. The client mirrors the first in
`weaponManager.getEdfPenalty`; it needs no mirror of the second, because it draws no damage
preview.

⚠️ The damage half cannot carry the count over from `calculateHitBase`: damage is a separate pass
(`Firing::fireWeapons` → `getDamageMod`) with none of that method's locals, so it recomputes from
the `$pos` the caller already resolved — which is also the right hex for a ballistic.

⭐⭐ **THE DRAIN IS CUMULATIVE AND LOCKED IN, NOT RE-ROLLED** (user ruling). *"The ship loses 1d10
… increased by a further 1d10 for every additional turn ended in the EDF"* means turn one's roll
**stands**, and each further consecutive turn adds **one more die to the total already in force**.
The first build rolled N fresh dice every turn, which let a second turn in a field come out
**lighter** than the first — the opposite of what an escalation rule is for. A turn out of the
field resets `$consecutive` to 1 and the next entry starts again at one die.

⭐ **AND IT NEEDS NO NEW STORAGE, WHICH IS THE PART WORTH REUSING.** The running total *is* last
turn's `oneturn` critical: `sumCriticalParam($type, $turn)` reports a crit with `turn + 1 == $turn`,
so the accumulator and the reader are the same read. `EdfExposure::accumulate()` is the whole
mechanism — previous total, plus one die — and it takes the system the crit **rides** (Engine,
Reactor, Scanner, or the marker for initiative), because that is where the previous total was
written. ⚠️ An **Enormous** unit keeps its first roll and never adds to it: *"limited to the first
die (1d10 or 1d6) and do not increase"* is a statement about the total, so the roll is not repeated
either. ⚠️ The initiative figure is now clamped at `INI_CAP` **at write time as well as in the
reader**, or an accumulating total would climb for ever and the log would advertise a penalty the
ship never takes.

⭐ **A SERVER-AUTHORED `pubnotes` CANNOT COLOUR A SHIP NAME, AND THAT IS A GENERAL FACT.** A log
ship name is drawn in the **reader's** team colours (`gamedata.getShipLogColorCss`: mine green /
ally blue / enemy red for a 2-team participant, the absolute palette for an observer) and the
server has no idea who is reading. So `EdfExposure` emits the victim as a **bare link span** —
`<span class="shiplink" data-id="123">Name</span>`, no colour of its own — and the new
`combatLog.colourShipLinksInNotes()` fills the style in on the way to the DOM, for **any** pubnotes,
not just this one. It is gated on a single `indexOf('shiplink')`, so notes that name nobody cost
nothing, and an unknown id is left exactly as it came.

⚠️ **THE FIELD OVERLAY HOLDS AN ABSOLUTE WORLD z, NOT A LOCAL ONE.** The disc is a **child of the
ship's mesh** — it has to be, to follow the hull — and that mesh climbs the z ladder as the icon is
selected (`baseZ + 100`) or hovered (`+499`), so its local z rode up with it and washed purple over
the EW lines, which are drawn straight into the scene at **z -5** (`EWIconContainer`'s `LineSprite`).
`ShipIcon.updateEdfFieldZ()` now subtracts the parent's z to pin the disc at `EDF_FIELD_Z` (-20:
under the EW lines, over the hex grid at -500), and is called from `showEdfField` and from **both**
places that move `mesh.position.z`. ⭐ **Generalises to any standing overlay that belongs to the
BOARD rather than to the icon carrying it: parent it for position, compensate its z.**

⭐ **AND THE DISC IS NOW ONLY REBUILT WHEN IT CHANGES** (user: gate the expensive processes).
`syncAllEdfFields` runs on **every poll**, and `showEdfField` called `buildHexRegion` every time —
at radius 8 that is a 289-hex sweep, a fresh `BufferGeometry` and a disposal, for a disc identical
to the one already on screen; worse, it then reported `changed = true` and woke the idle-gated
render loop every poll for the whole game. A disc is fully described by its **radius and the hex it
is anchored on**, so those two are the cache key, `showEdfField`/`removeEdfField` return whether
they actually did anything, and `requestRender()` is called only when one of them did.
⚠️ **AND `setEdfHexes()` NOW ASKS ITS CHEAPEST QUESTION FIRST.** It runs on **every gamedata load
of every game**, and it was calling `isDestroyed()`, `isReinforcement()` and `getHexPos()` on every
ship in the fleet *before* discovering that none of their systems is an `EdfSource`. The system
sweep — a bare `instanceof` — is now the outer test and everything else is deferred behind it, with
the same three exclusions and no new state. The rest of the feature's gating was already right and
was re-asserted by test: `$edfPresent` (server) and `gamedata.edfHexes` (client) keep an ordinary
game to one boolean read per shot.

**How it was proven.** `c:\tmp\edf_stage4e_test.php` — **40 checks, all green**: the endpoint
exclusion from both ends and from both at once, adjacent and same-hex shots, own-fleet immunity
still applying to an interior hex; the whole plasma/antimatter table including `getDamageMod` end
to end (100 damage → 92 clear, **90 for Plasma** through one crossed hex, **92 for Antimatter**
through the same hex — the pair that proves the damage half is Plasma's alone) and ⭐ **the gate
asserted by flipping `$edfPresent` off and watching the same call return 0**;
the escalation driven over five turns with
⭐ **40 sampled 4-turn runs asserting the total never once dips**, which is exactly what a re-rolled
Nd10 would do routinely; the Enormous lock proved by equality across three turns; a turn out of the
field resetting it; the initiative clamp; and the log row's bare shiplink span.
`c:\tmp\edf_client4e_test.js` — **32 checks, all green** — evaluates the real `mathlib.js` and the
extracted `weaponManager` helpers against a **2,400-case corpus the PHP side generated**, zero
mismatches, with ⭐ **the old endpoint-inclusive count run beside it and shown to disagree on 250 of
them**, so the mirror test cannot pass against the bug it was written to catch; plus the doubling
table on both `factionAge` arms, the empty-map gate, and the ShipIcon / PhaseStrategy / combatLog
edits asserted in source.

Regression gate: `fvbuild.ps1 -Check` — autoload map up to date, `checkShipData.php` **PASS**
(0 new findings, 235 accepted), replay harness **128 passed / 1 failed** (game 4325 only, the known
clean-tree failure; game 4324 skipped as advanced-since-record). Legacy bundles rebuilt.



### 3.6 Energy Draining Mine — **BUILT 2026-09-05 (Stage 6)**

`class EnergyDrainingMine extends AoE` in [AoE.php](source/server/model/weapons/AoE.php) (the user's
instruction: *"Electromagnetic, but created in AoE files"*), client twin `EnergyDrainingMine` in
[aoe.js](source/public/client/model/weapon/aoe.js). Range 150, `factionAge = 3`, `weaponClass =
"Electromagnetic"`, `hidetarget`, no intercept rating, **and no damage of any kind** —
`getDamage`/`setMinDamage`/`setMaxDamage` all answer 0 and `fire()` never reaches `AOEdamage`.

Mounted on the Traveler in the **aft** section at arc 0..360, hit chart row 13 (placeholder
placement, like every other Walker stat, until a control sheet says otherwise — D4).

**Storage is `BallisticTorpedo`'s** (§1.3), with one change:
- `loadingtime = 1`, `normalload = 3`, `canSplitShots`, `hextarget`, `ballistic`,
  `defaultShots = 1`; `firedOnTurn()` sums `->shots`; `calculateLoading()` adds at phase 1 and
  deducts at phase 2.
- `getStartLoading()` returns **1**, not 0 and not `getNormalLoad()` — *"begins battles loaded with
  1 mine"*.
- ⚠️ **`turnsloaded` is the COUNT OF MINES HELD, so `loadingtime` must stay 1.** Both
  `Firing::getFireOrderBlock` and `weaponManager.isLoaded` answer "loaded" with
  `turnsloaded >= loadingtime`, so raising the loading time to model a slowed reload would mean a
  crippled launcher needed *two* mines in store before it could fire at all. The reload cadence is
  a separate number, `$reloadInterval`.

**The recharge critical is `IncreasedRecharge1`, reused as-is** (the plan's guessed
`EdfMineRechargeSlowed` was not needed): it already means "recharge increased by one turn" and it
is repeatable, so *"1 per 2 turns, then 1 per 3, and so on"* is the crit COUNT —
`getReloadInterval() = 1 + hasCritical("IncreasedRecharge1")`.

⭐ **THE CADENCE IS `turn % interval`, NOT A STORED COUNTER.** The obvious home for "turns since the
last mine" is the `WeaponLoading` overloading slot, which `BallisticTorpedo` leaves at 0 — but
`weaponManager.isLoaded()` answers `loadingtime <= turnsloaded || loadingtime <= overloadturns`, so
a counter sitting at 1 would make an **empty** launcher read as loaded on the client. The turn
number needs no storage, cannot drift across the double gamedata load (trap 1), and replays
identically. A test asserts `overloading` stays 0 and `loadingtime` stays 1 in every phase.
`reloadsOnTurn()` is the single authority.

⚠️⚠️ **TURN 1 MUST NOT RELOAD — `getStartLoading()` *IS* TURN ONE'S LOAD** (user report, game 4337:
the launcher opened the battle at 2/3 and Traveler #1 declared two probes on turn 1). The reload
branch fires when the game advances **out of DEPLOYMENT**, because
`Manager::advanceGameState` runs its `onAdvancingGamedata` sweep *after* the phase advance and
`DeploymentGamePhase::advance()` has already set phase 1 — so on turn 1 it lands **before** the
player's first Initial Orders. Read the two branch labels accordingly: `currentPhase == 1` means
"we just left Deployment" and `currentPhase == 2` means "we just left Initial Orders", which is
when ballistics commit. ⚠️ The fix is **not** to seed `getStartLoading()` with 0 and let the bump
make it 1 — the ship window would then read 0/3 for the whole Deployment phase, which is the
complaint `MediumLightningArray` already answered the same way (game 4329).

**Scatter: the rules' table, on the family's d100.** `d20` 1–15 on target, 16–20 → `d10` 1–6
scatters `d5` hexes / 7–10 no effect is *arithmetically identical* to `AoE::fire`'s shape —
75% / 15% / 10% — because `0.25 × 0.4 = 0.10`. So `fire()` rolls one `d100` against
`needed = 90` with the on-target threshold at 75, exactly like every other weapon in the family,
and `needed` is **derived from the two constants rather than written as a literal**
(`ON_TARGET_PCT`, `SCATTER_PCT`). The only real difference from the parent is `SCATTER_DIE = 5`
instead of 6. The direction is a separate uniform `d6` rather than being read off the same `d10`;
the distribution over the six facings is the same either way. *"Like Energy Mines, scatter rules
apply"* also brings the family's cap of the distance actually flown — a probe lobbed one hex cannot
land five hexes past its launcher. Measured over 40,000 resolutions: 74.55% / 15.22% / 10.23%, and
a flat d5 (1251/1187/1226/1254/1169).

⚠️ **No to-hit modifiers**, exactly as `AoE::calculateHitBase` — fire control, range and the EDF
penalty all sit a probe's scatter out.

**The field is a spawned Terrain unit,
[`SpawnEnergyDrainingMine`](source/server/model/ships/terrain/SpawnEnergyDrainingMine.php)**, using
the `spawnHyperspaceWaveform` recipe (`Manager::insertSingleShip` → `insertSingleMovement` deploy
order → `SystemData::initSystemData`/`insertSystemData` → `unset($gamedata->ships[$id])`).

⭐ **IT MOUNTS AN ORDINARY `EnergyDrainingField(0, 1, 1, 1, false)` — no bespoke `EdfSource`
class.** `TacGamedata::setEdfHexes()` collects anything implementing the interface, so the drain,
the targeting penalty, the overlap collapse, the own-fleet immunity and the map overlay all pick a
probe up with no code that knows what a mine is. A dedicated class would have needed a client twin
(`SystemFactory` builds with `new window[name]`), a blueprint entry, **and** its own name in
`PhaseStrategy.getEdfRadiusForShip` / `shipCarriesEdf`, which both match on
`system.name === 'EnergyDrainingField'` — three places to forget. Radius 1 is the rules' *"the
destination hex and those immediately surrounding it (seven hexes in total)"*.

⚠️ `Enormous = false`, like `SpawnJumpPoint` and unlike the waveform: Enormous terrain auto-rams
everything that flies through it and joins `blockedHexes`, and a drifting field does neither. Its
primary `Structure` is **indestructible** — the probe's life is one turn and letting damage end it
early would be a second, unwritten rule about when a field stops. One knock-on worth knowing: it is
Terrain, so a hex holding a probe refuses a Jump Engine's vortex declaration
(`Firing::getVortexDeclarationBlock` rejects any terrain-occupied hex).

⭐⭐ **THE ONE-TURN LIFE IS ENCODED IN THE SHIP'S NAME (`"EDM<turn>"`) and re-derived on every
load** in `SpawnEnergyDrainingMine::onConstructed()`. `tac_ship` has no spawn-turn column and the
waveform solves the same problem the same way. **This replaces the plan's `generateIndividualNotes`
cleanup sweep**, and is strictly better: there is nothing to persist, nothing to run, and the probe
still expires on time when the launcher that fired it has been destroyed. It lands during Firing on
turn N and is on the board for turns N and N+1.

⭐⭐ **IT DRAINS ON BOTH OF THEM — INCLUDING THE TURN IT LANDS** (user ruling 2026-09-05, revising
the first build). *"If enemy ships are caught in its AoE on the turn it lands then that counts as
the first turn they are in an EDF, then the field persists for another turn."* So a unit standing
in the seven hexes on turn N takes one die, and two (cumulative) if it is still there on N+1 — the
ordinary `EdfExposed` escalation, with nothing added to it.

Turn N+1 needs no machinery: the probe is an ordinary ship by then and `setEdfHexes()` finds its
`EnergyDrainingField` at load. Turn N does, because the map was built in `onConstructed()` long
before Firing. `EnergyDrainingMine::$pendingFields` queues each landed probe and
`Criticals::setCriticals` drains the queue through the new
**`TacGamedata::registerEdfField()`** — one hex-disc folded into the existing map.

- ⚠️⚠️ **ADDITIVE, NEVER A REBUILD.** Calling `setEdfHexes()` again after Firing would look like the
  obvious fix and is a trap: it skips `$ship->isDestroyed()` with no turn argument, so a Walker
  shot down in that same Firing step would silently stop draining on the turn it died — which
  contradicts `EnergyDrainingField::isEdfActive()`, whose `isDestroyed($turn - 1)` says a system
  killed this turn worked for this turn.
- ⚠️ **The queue is drained at the CRITICAL HIT STEP, not from `fire()`.** Registering at spawn
  time would make the field visible to weapons resolving LATER in the same Firing step —
  `AoE::fire` asks `isHexInEdfField()` to decide whether a proximity blast is contained — so
  whether an enemy energy mine kept its radius would depend on weapon resolution ORDER.
- ⚠️ **The flush sits BEFORE the `TacGamedata::$edfPresent` gate in `setCriticals`**, because
  `registerEdfField()` is what sets that static: inside the gate, a game whose only field is a
  probe that just landed would never reach the resolver. `class_exists('EnergyDrainingMine', FALSE)`
  keeps it to one hash lookup for every other game.
- The queued entries carry the **launcher's** team and id, not the orb's: the team is what makes
  the launching fleet immune, and the orb is not in `$gamedata->ships` this request so
  `getEdfSourceShip()` could not resolve it for the combat log.

⚠️⚠️ **`$removed` IS SET PER LOAD, NEVER ONCE AND FOR ALL.** `BaseShip::isDestroyed()` with no
argument answers **true for any unit whose `$removed` is set, whatever `$removedTurn` says** — so
stamping the flag at spawn time kills the field on the very turn it is meant to work.
`JumpEngine::restoreVortexState` sets it conditionally for the same reason. It is also set for
turns *before* the spawn, so a replay of an earlier turn cannot show the field a turn early
(`setEdfHexes()` skips destroyed units).

**The map marker is PURPLE** — `BallisticIconContainer`'s `modeMap` gains a
`'Energy Draining Mine': { type: 'hexPurple', text: 'Energy Drain Mine', color: '#7f00ff' }` row,
plus the mode name in the splash-hex list so the whole seven-hex disc is drawn rather than the
centre alone. Red is the map's colour for incoming fire and this probe deals none.
⚠️ **The `modeMap` key is the FIRING MODE NAME the server declares**
(`EnergyDrainingMine::$firingModes`), not the system name — a one-character drift between the two
files falls back to a plain red hex with no label and no error. A test compares the two sources.
⚠️ The splash radius is the switch's **default of 1**, which is `FIELD_RADIUS`; move them together.

**Client half.** `weaponManager.targetHex` routes a `canSplitShots` weapon to
`doMultipleHexFireOrders`; one right-click declares one mine, `checkFinished()` unselects the
launcher when the store is spent.
⚠️ **Remaining mines are DERIVED (`turnsloaded − fireOrders.length`), not counted down.**
`torpedo.js` keeps a decrement-only `maxVariableShots`, which the server re-publishes at its full
value on every poll — so a page reload with two mines already declared reports three still
available. `maxVariableShots` is still kept in step for the generic UI that reads it.

⚠️ Spawned ships: `LAST_INSERT_ID` returns a **string**. `Manager::insertSingleShip` casts it.
⚠️ A new shipid-keyed table would need adding to both `deleteGames()` and `leaveSlot()` — this
feature adds none, but the terrain rows follow the existing spawn cleanup.

**Stats as at 2026-09-05:** `maxhealth = 12`, `powerReq = 5` (user's numbers), mounted aft at
0..360 on hit-chart row 13. Art landed the same day —
`img/systemicons/EnergyDrainingMine.png` and `img/ships/WalkerEDMine.png`.

⭐ **THE FIGHTER DROPOUT WAS AUDITED AND IS CORRECT** (user query, game 4337: *"no Frazi fighters
dropped out on turn 1"*). §2.2's `rollDropouts` rolls `(consecutive + 1)d10` against each craft's
**remaining boxes**, the same comparison `Fighter::testCritical` makes with its single d10 — so an
undamaged Frazi's 12 boxes need a 13+. Driving the real resolver 20,000 times from a probe's field
measured **0.362 per craft on turn 1** against a closed-form 0.360, and **0.067 for zero-of-six**
against 0.0687. Game 4337 hit that ~1-in-15 tail on turn 1 and then lost 5 of 6 on turn 2, which is
the *modal* result at 3d10 (37.8%). Nothing to fix.

- ⚠️ **A healthy heavy fighter is 36% to drop out on its first turn in a field, and 78% on its
  second.** That is the rule as written (*"2d10 instead of the usual 1d10, also increased by an
  additional 1d10 for every successive turn"*), and it is brutal — but the die COUNT is the whole
  rule, so the harness now pins it deterministically: at 13 dice the minimum roll of 13 exceeds 12
  boxes, so every craft must drop.
- ⚠️ A craft that has already disengaged is skipped on later turns — `Fighter::isDestroyed()` folds
  `DisengagedFighter` in, and `rollDropouts` tests it. No double-rolling.

### 3.7 Energy Draining Net — **BUILT 2026-09-05 (Stage 7)**

`class EnergyDrainingNet extends ShipSystem implements SpecialAbility, EdfSource` in
`baseSystems.php` beside the field, client twin in `baseSystems.js` — `getEdfRadius()` returns 0.
Control sheet: **health 12, power 4, "Special Electromagnetic weapon"** (a classification, not a
gun: the Net has no fire order, no arc and no target). Linking lives in a new
`source/server/handlers/EdfNetLinks.php`.

⭐⭐ **LINKING RUNS IN `setEdfHexes()`, NOT IN THE §2.2 RESOLVER — the one substantive departure
from this section, and it is a correction rather than a preference.** The section was written
before Stage 4 built the map. The resolver runs at the Critical Hit step, *after* `$edfHexes` has
been built **and published**, so corridors computed there would have drained units while being
invisible to the targeting penalty, to the client's mirror of it and to the map overlay — three of
the four things a field is for. Built at map time instead, all four are served by construction and
the drain still sees them, because `EdfExposure` reads the same map. Everything below stands.

⭐ **THE PAYOFF IS THAT NO CONSUMER CHANGED.** The penalty, the drain, own-fleet immunity and
overlap collapse all key off `$edfHexes` and ask nothing about where a hex came from, so
contributing hexes *is* the whole integration — the same argument Stage 6's orb records.

⚠️ **ORDER IS FIXED: after the per-ship disc sweep, inside its `try`.** The fill cap's *"it is not
necessary to count those hexes in an EDF generated by another vessel"* is answered by asking
whether a hex is already in the map, so the map has to be complete first — including the Nets' own
hexes, which the disc sweep adds at radius 0.

Linking contributes hexes to `$edfHexes`:

1. **Pairwise:** every pair of active Nets **of the same team** at distance ≤ 3 links; the hexes
   between them join the field.

   ⚠️ **`HexZone::line()` is the WRONG tool here and was not used.** It answers *one* line and
   includes *both* endpoints — right for "which hexes does this shot cross", wrong for a rule that
   hands the player a **choice** between corridors whose endpoints are the Nets themselves.
   `corridorCandidates()` enumerates every shortest path instead (a hex is on one iff
   `dist(A,H) + dist(H,B) == dist(A,B)`, walked one step at a time); at range 3 that is at most
   three candidates of two hexes each, so exhaustive enumeration is cheaper than being clever.
   ⚠️ *"The two hexes between them"* is the **range-3 case, not a constant**: 2 hexes at distance
   3, 1 at distance 2, none at distance 1. Adjacent Nets therefore still **link** (they count
   towards a closed area) while contributing no corridor of their own.

   **Ties are resolved deterministically, no UI (D8)** — but toward the corridor a player would
   actually pick, since the field exists to drain enemies and to lengthen the targeting corridor
   through it. Rank candidate corridors by, in order:
   1. **most enemy units standing in the corridor's hexes** (the player's obvious choice);
   2. **most hexes not already in `$edfHexes`** (adds the most new field);
   3. **lowest `(q, r)`**, compared hex by hex — a pure stability tie-break, never a game rule.

   ⚠️ **The ranking must be a total order.** It runs on **both** of the request's two gamedata
   loads, on every poll of every viewer and again on every replay, so any residual tie left
   unbroken would let the corridor flip between them — and the client mirrors the to-hit penalty
   off the published map, so "the corridor moved" reads as *"the client's predicted hit chance
   disagrees with the dice"*. Rule 3 exists solely to guarantee it cannot, and the harness asserts
   it three ways (input order reversed, run twice, and rules 1 and 2 each shown to override it).
   ⚠️ Rule 3 compares coordinates **numerically**, never as a joined string: `"-10,0"` sorts before
   `"-2,0"` as text, which would make the tie-break depend on where on the map the fleet is flying.
   ⚠️ Rule 2 reads coverage **per team** and **live**, updated as pairs are walked — so a hex two
   pairs would both produce counts as new exactly once. That makes the pair order load-bearing;
   it is `usort` by ship id then system id.
2. **Closed-area fill:** the links must actually **close**. `HexZone::containsUnit()` over the
   bounding Nets' positions (which is `hull()` + the edge walk) decides membership, **capped at
   `2 × (bounding Nets) − 1` filled hexes** (*"less than double"*). Over the cap, fill
   nothing — silently filling a partial area is worse than filling none, because dropping *some*
   hexes needs a rule for *which*, and the sheet has no such rule.

   ⚠️⚠️ **A CHAIN IS NOT A CLOSED AREA — the bug play testing found in game 4338 (2026-09-05).**
   The first build filled any *connected* group of ≥ 3 Nets. Three Waymarkers stood at (1,2), (0,0)
   and (1,-3): #1–#2 linked at 2 hexes and #2–#3 at 3, but #1–#3 were **5 apart**, so the three
   were a **chain**, not a ring — and a chain encloses nothing. It filled anyway and painted hexes
   like **(1,0)** that lie beside the chain rather than inside anything. *"Several ships linking up
   in such a manner **form a closed area**"* requires a cycle, and a connected group of N Nets with
   only N−1 links is a tree.
   ⭐ **The area is the group's 2-CORE**: repeatedly drop every Net with fewer than two links until
   none is left to drop. A chain erases itself completely; a ring survives whole; a ring with a Net
   trailing off it keeps the ring and drops the trailer — right for the same reason, since the
   trailer reaches the area by corridor and is not part of its boundary. **One loop replaces both
   "is there a cycle" and "which Nets bound the area"**, with no bridge-finding or
   biconnected-component machinery. ⚠️ The pruning must **iterate**: dropping a chain's endpoints
   leaves its middle with no links, and a single pass would hand a one-Net "area" to the hull.
   ⚠️ **N is the surviving CORE's Net count** — those are the Nets that form the area, so neither a
   second formation elsewhere in the fleet nor a trailing Net buys this one a bigger fill.
   ⚠️ `convexHull` discards collinear middles, so three Nets in a line leave a 2-point hull that
   `pointInPolygon` rejects; the edge-walk inside `HexZone::containsUnit` handles this and is used
   as-is. It also gives "the hull *touches* the hex" for free, which is the reading that matches
   the corridor rule.
   ⚠️ **An over-cap area needs MORE NETS, not more spread** — three Nets at maximum spread have
   corridors along all three sides that swallow the interior, leaving 3 fill hexes against a cap of
   5. The harness had to go to a five-Net arc to exercise the refusal at all.
3. *"It is not necessary to count those hexes in an EDF generated by another vessel"* — hexes
   already in **this team's** field do not count against the cap and are not re-added. An
   *enemy's* field over the same hexes spares you nothing, which the harness asserts both ways.

Criticals: `20+ → EdnPowerDoubled`, escalating on count (×2, ×3, ×4 …). `powerReq` becomes
`base × (1 + count)`, applied in `onConstructed()` from a separate `$basePowerReq` so it is
idempotent.

⚠️⚠️ **`powerReq` IS A BLUEPRINT FIELD AND DOES NOT TRAVEL BY DEFAULT — the assumption that it
"is already published" was wrong, and the smoke test is what caught it.**
`ShipSystem::stripForJson` deliberately leaves it out of the poll payload
(`addBlueprintFieldsForJson` lists it) because it rides the **per-class static ship bundle**, which
was baked long before anybody rolled a critical. A Net the server had escalated to 12 went on
telling the client's power UI, its allocation arithmetic and its "Power Used" tooltip row that it
cost 4 — the player allocates 4, the server refuses, and nothing on screen says why.
`EnergyDrainingNet::stripForJson()` therefore republishes `powerReq` **and `data`** per instance
(`SystemFactory` merges the per-instance JSON over the blueprint, and trap 6 means two Nets on one
hull with different crit counts would otherwise share one tooltip). **This applies to any system
whose criticals move a blueprint number**, which is most of that field list.

**The overlay.** A Net's field is the one field shape with **no unit at its centre** — a corridor
between two hulls, an area between several — so `ShipIcon.showEdfField`'s per-source disc cannot
draw it. `TacGamedata::$edfNetHexes` publishes the hexes themselves as a flat `[{q,r}]` and
`BallisticIconContainer.generateEdfNetHexes` lays a purple blanket over them.
⚠️ **A rendering hint, never an authority**: every one of those hexes is also in `$edfHexes`,
team-tagged, and that is what every rule reads.
⚠️⚠️ **It is drawn as ONE OVERLAY PER CONNECTED CLUSTER, and that is a performance requirement.**
`HexRegion.buildRegionFromHexes` sizes its sweep from the farthest hex from the anchor and then
tests every hex in that square, so a single overlay spanning two Walkers 60 hexes apart would
sweep 121 × 121 = 14,641 hexes to draw a handful — and two lone unlinked Nets at opposite corners
of the board is an ordinary thing to happen, not a corner case.

**The live preview (user request, 2026-09-05).** A Net's whole tactical question is *where do I put
this ship*, and the server's answer is always one commit behind the player. So during **deployment
and movement** the client recomputes the field from **plotted** positions and draws that instead:
`model/EdfNetLinks.js` is a line-for-line port of the resolver, `PhaseStrategy.buildEdfNetPreview`
feeds it from icon positions, and `BallisticIconContainer.refreshEdfNetHexes` redraws.

- ⭐ **Why a mirror is acceptable here when the plan usually resists them:** it is **advisory
  only**. Nothing reads its output but the overlay — the to-hit penalty, the drain and own-fleet
  immunity all read `gamedata.edfHexes`, which is the server's alone. A divergence is a preview
  that redraws on commit, never a rule resolved two ways. **Keep it that way.**
- ⚠️ **Only in phases where positions are still moving** (`-1` and `2`). Everywhere else the
  server's answer is authoritative *and* current, so letting a mirror override it would turn any
  divergence into a permanent wrong picture instead of a self-correcting one.
- ⚠️ **Icon positions, never `shipManager.getShipPosition`** — that reads the last *committed*
  move and would preview the field the player is trying to move away from.
- ⚠️ **`refreshEdfNetHexes` has to prune its own stale clusters.** `consumeGamedata` clears `used`
  on everything and prunes at the end; nothing does that when a plotted step redraws outside a
  poll, so a cluster that *moved* — which is what happens on every step — would leave its previous
  shape behind and the field would smear along the whole plotted path.
- ⚠️ It is recomputed from `onShipMovementChanged`, gated on `PhaseStrategy.anyEdfNetPresent()`,
  because **any** ship's move can change the field — a Net's own, or a bystander's, since corridor
  tie-break rule 1 counts enemy units standing in the candidate hexes.
- ⭐ **Proven by differential, not by unit tests.** `tests/replay/ednDifferentialBoards.php` +
  `tests/replay/ednDifferential.js` run randomised boards through both implementations and compare
  hex, team and attribution. The ported geometry (per-angle touch tolerance, the load-bearing
  `1e-9`, a hull that discards collinear middles) fails on awkward cases nobody writes a unit test
  for — the method §2.3 records for the `HexZone` extraction, for the same reason.
  ⚠️ **The generator had to be taught to build rings deliberately.** Four seeds and 13,000 random
  layouts produced *one* cap refusal between them: six Nets landing on six exact hexes does not
  happen by accident in a 9×9 window. The JS half's **INCONCLUSIVE** exit on an empty coverage
  bucket is what made that visible rather than letting a green run mean nothing.

⚠️⚠️ **`Debug::log` IS NOT USABLE IN THIS PATH, and the reason generalises.** The plan said "fill
nothing **and log it**". `setEdfHexes()` runs on every gamedata load — every poll of every viewer,
twice per request — and `Debug::log` writes a timestamped block carrying the request method, URI,
IP, the whole `$_REQUEST` **and the whole `$_SESSION`** to `fieryvoid.log` per call. A fleet parked
in an over-cap formation is a *persistent state, not an event*, so logging it would dump every
watching player's session to disk every couple of seconds for as long as the ships sat there. The
refusal goes into `EdfNetLinks::$refusals` instead — an in-memory array, cleared at the top of
`resolve()`, which is also why it needs no `DBManager` reset (§2.1's per-load-static trap).

### 3.8 EW Detector — **BUILT: Stage A 2026-09-09, Stage B 2026-09-10**

`class EWDetector extends ShipSystem implements SpecialAbility`, in `baseSystems.php` below
`EnergyDrainingNet`. Control sheet (user, 2026-09-09): **health 20, power 6, range 20**.

> *"The sensors on an EWD-equipped ship can detect the configuration of any enemy's EW suite and
> instantaneously report it to the ship's fleet ... This system provides every friendly unit within
> 20 hexes of the EW Detector the enhancement of Expert Scanner: all friendly ships may save one
> point of EW for allocation as late in the combat turn as the end of the movement segment. The
> effects are cumulative with multiple EW Detectors, but the efficiency degrades. The first four EW
> Detectors allow the fleet to save 1 point of EW each. EW Detectors number 5-8 allow the fleet to
> save 1/2 of a point each. All additional EW detectors allow only 1/4 of a point each. Round down
> fractions of 1/4 and 1/2 and round up fractions of 3/4. If a vessel declares that it is saving an
> EW point but ends its movement step out of range of the EW Detector, the point is lost. It is
> possible to save ELINT EW points as well, as long as the ELINT vessel is within range both before
> and after movement."*

**Two stages, because the second one is expensive.** Both landed.

- **Stage A — the allowance. DONE 2026-09-09.** A fleet sweep computes the saved-EW budget:
  detectors 1–4 give 1 each, 5–8 give ½ each, 9+ give ¼ each; round ¼ and ½ down, ¾ up. The
  allowance is displayed and nothing yet allocates it.
- **Stage B — late allocation. DONE 2026-09-10.** The saved points are spendable in Pre-Firing and
  Firing, clamped by the unspent (DEW) pool, with the same EW buttons the Initial Orders menu uses.

⚠️ **Stage B changed a shared, load-bearing path for every faction in the game** — it was the
highest-blast-radius item in this plan. It came out clean: replay harness **byte-identical** to the
same run with the six server files stashed, all five checks including `masking` and `snapshot`.

#### Two rulings that shaped Stage B (user, 2026-09-09)

**R1 — WHERE "the end of the movement segment" IS.** *"The 'End of movement' in FV is essentially
the start of Pre-Firing phase (if there is one) or start of Firing phase. If we restrict the late EW
allocation to these phases and don't worry too much about the Movement phase for now that's fine."*

⭐⭐ **This ruling is what made Stage B tractable, and it deleted a whole sub-problem.** The plan had
budgeted for an EW write path in `MovementGamePhase::process` plus a separate "declare now, verify
after you move" mechanism for *"if a vessel declares that it is saving an EW point but ends its
movement step out of range … the point is lost"*. With the window opening AFTER movement there is
**nothing to declare**: the allowance is simply recomputed at the unit's post-movement hex, and a
ship that drifted out of range finds it is zero. The rule is enforced by the arithmetic already
written for Stage A, asked at a different moment. No declaration, no reconciliation, no note.

⚠️ **BOTH phases, sharing ONE budget.** Phase 5 is Pre-Firing and 3 is Firing. Gating on 5 alone
would silently deny the allowance on any turn whose Initial Orders had nothing to activate —
`InitialOrdersGamePhase::advance` jumps straight to phase 3 in that case, which is exactly the
situation where a player has fewest units left. One budget across both, because the write diffs
against what is already stored: points spent in Pre-Firing are stored rows by the time Firing opens,
so only the remainder is still spendable.

**R2 — WHAT THE POINT IS DRAWN FROM.** *"If a ship spends all their EW on non-DEW EW types, then
they are unable to save a point of EW (and the EW panel should reflect this during EW orders, so it
doesn't misleadingly show a player saving some EW points for later when in fact they've spent them
all on non-DEW uses). Essentially DEW is the only pool of unspent EW that saved EW can be drawn
from in Pre-Firing/Firing."*

So the number that matters is **`min(ladder, unspent pool)`**, not the ladder. `EW::getDetectorAllowance`
answers what the detectors offer, `EW::getSavedEwAllowance` answers what the ship can actually take
up, and only the second is ever displayed or budgeted to.

#### What Stage B built

**The pool, and the one subtlety in it.** `EW::getUnspentEw` / `ew.getSavedEwPool` read the
**committed DEW row** when one exists, and fall back to the derived remainder
(`scannerOutput − allEWExceptDEW`) when it does not.

- ⚠️ **There is no DEW row during Initial Orders.** `convertUnusedToDEW` writes it inside `doCommit`
  ([gamedata.js:1948](source/public/client/gamedata.js#L1948)), so while the player is still
  allocating there is nothing listed — and the derived fallback is what makes the panel's figure
  fall as they spend, which is the half of R2 the user asked for explicitly.
- ⚠️ **The committed row WINS when it exists**, because the client wrote it with `getEWLeft()`,
  which also subtracts EW-boosted system boosts (Particle Impeders, Psionic Lances) that nothing
  on the server re-derives. Reading the row is the only way the two ends agree on those hulls.
- ⚠️ **`getDEW()` cannot tell "no row" from "a row reading 0"** — it returns 0 for both — and the two
  mean opposite things to the write path (below). Hence `EW::hasCommittedDewRow`.

⭐⭐ **THE LATE-WINDOW BOOKKEEPING IS ONE DERIVED NUMBER AND TWO BOUNDS.** This is the part worth
carrying to any similar feature. Every point spent moves a point OUT of the unspent remainder, so:

```
spent      = pool − getEWLeft()          // pool is the ANCHOR; getEWLeft is live
remaining  = allowance − spent
```

- The **upper** bound (`remaining ≥ cost`) is the budget.
- The **lower** bound (`spent ≥ cost` before a de-allocate) is what stops the player undoing an
  *Initial Orders* allocation in a phase where the server would ignore the removal anyway.

No snapshot of the committed EW array, no per-entry marking, nothing a poll rebuilding
`gamedata.ships` can get out of step with. ⚠️ **The anchor must be the committed pool, never
`getEWLeft()`** — `getEWLeft()` falls by one with every point spent, so using it as the budget would
shrink the budget as the budget was spent and each point would cost two.

**The write.** `EW::submitLateEw`, called from `PreFiringGamePhase::process` and
`FireGamePhase::process`, outside their per-ship loops.

- ⚠️⚠️ **ADDITIVE ONLY, ENFORCED BY THE SHAPE OF THE DIFF RATHER THAN BY A CHECK.**
  `EW::diffLateEw` emits only **positive** deltas against the stored rows, so a POST that removed or
  reduced an Initial Orders allocation changes nothing at all. The one row driven downwards is the
  ship's own DEW, which is not an allocation but the remainder the saved point is defined to come
  out of.
- ⚠️ **AN EXISTING ROW IS RAISED, NEVER DUPLICATED.** `getEWbyType()` and `getDEW()` return the
  **first** matching row while `getOEW()` **sums** — so a second row for one (ship, turn, type,
  target) would be counted by the shooting maths and ignored by everything else. Hence
  `DBManager::adjustEwAmount` (UPDATE … LIMIT 1) beside `insertEwEntry`.
- ⚠️ **IDEMPOTENT BY CONSTRUCTION.** A second submission in the same phase diffs the posted array
  against rows that now already contain it, finds nothing, writes nothing.
- ⚠️ **ALL OR NOTHING PER ENTRY.** A Disruption allocation is 3 points (4 on a `ConstrainedEW` hull)
  and means nothing as a fragment, so a budget that cannot take the whole entry takes none of it.
- ⚠️ **NO COMMITTED DEW ROW MEANS NO SPEND.** There is nothing to debit, and a spend that cannot be
  debited is free EW. A flight never gets a row (`convertUnusedToDEW` returns early on one), which is
  also the right answer — the rule is about ships.
- ⚠️ **THE BUDGET IS RE-DERIVED SERVER-SIDE, NEVER FROM THE POST.** A POST-side ship carries whatever
  movement the client sent, so its `getHexPos()` is client-controlled — and its EW array is the thing
  being validated.
- ⭐ **AND IT RELOADS NOTHING.** `Manager::submitGamedata` builds the authoritative gamedata with
  `DBManager::getTacGamedata` and hands **that same object** to `process()` as `$gameData`; only
  `$ships` is POST-side. Nothing earlier in either phase’s `process()` touches EW or a unit’s hex (a
  Fire-phase combat pivot changes facing, not position), and the player cannot already have submitted
  this phase because `hasAlreadySubmitted()` throws first — so a second load would be one of the
  heaviest calls in a submit, spent to re-read what is already in hand. ⚠️ That
  `InitialOrdersGamePhase::process` *does* re-load before `EW::validateEW` is not an inconsistency:
  it has written power and notes earlier in the same method and needs to see them.

**The UI.** The EW buttons the Initial Orders menu already carries are **reused verbatim**:
`ShipTooltipInitialOrdersMenu.ewButtons` is now a named subset, and
`ShipTooltipFireMenu.getAllButtons()` concatenates it while the late window is open.

- ⭐ **ONE GATE, AT THE MENU LEVEL, NOT TWENTY CONDITIONS.** The button objects are *shared* between
  the two menus, so a phase condition pushed into them would apply to Initial Orders too.
  `ew.isLateEwWindowOpen` answers the whole question — right phase, my ship, not yet committed, and
  an allowance worth something. The alternative was a second copy of twenty-two entries and their
  fifteen condition helpers in `shipTooltipFireMenu.js`.
- ⚠️ **The subset stops before `removeAllEW`.** "Remove All EW" clears the whole turn, Initial Orders
  allocations included, and those are committed rows the late window cannot un-write — the button
  would blank the panel and the next payload would put it all back. `ew.removeEW` refuses outside
  phase 1 for the same reason.
- ⚠️ `this.selectedShip` is routinely **null** in these menus and one throwing condition deletes the
  whole tooltip, Open Ship Details included; `isLateEwWindowOpen(null)` is false, and the harness
  asserts the null case explicitly.
- The `Saved EW` row becomes **`remaining / total`** once the window opens, and is now suppressed on
  a zero *allowance* rather than a zero *value* — a unit that has spent its whole budget still has
  one, and the row vanishing at the moment it is used up would read as the feature breaking.

**Initial Orders is bit-for-bit unchanged.** `ew.canAllocateEwNow` / `canDeallocateEwNow` open with
an unconditional `return true` on phase 1 rather than with a condition that happens to pass — the
phase has its own long-standing rules, including different `gamedata.waiting` handling on the assign
and de-assign paths, and Stage B must not quietly move any of them.

#### ⚠️ The masking review, and the one residual it leaves

`deleteHiddenData` blanks enemy `EW` **only in phase 1** ([TacGamedata.php:1543](source/server/model/TacGamedata.php#L1543)).
In phases 2, 5 and 3 an opponent's EW allocation has always been fully visible — that is not
something Stage B introduced, and the aiming UI depends on it.

**What Stage B does introduce is that EW can now CHANGE inside phases 5 and 3.** The review:

- ⭐ **In ordinary play the change is invisible until the phase advances.** `Manager::getTacGamedata`
  serves a body only when `DBManager::isNewGamedata` says turn, phase or activeship moved
  ([Manager.php:654](source/server/controller/Manager.php#L654)). Both late phases run at
  `activeship = -1`, so a poll inside a steady phase 5 or 3 returns `{}` and the opponent is served
  nothing. By the time they see it, both players have committed.
- ⚠️ **A deliberate page RELOAD would show it.** `game.php` calls `getTacGamedataJSON(..., force =
  true)`, which bypasses the APCu cache — so an opponent who hard-refreshes after you have committed
  and before they have would see your late allocation. The information is one EW point's worth, in a
  phase where the rest of your EW is already public, and the rule this implements is explicitly
  about *"enabling the ships to react to any change"* — so this was judged acceptable rather than
  designed around.
- **If it ever matters, the fix is a `phase` column on `tac_ew`** and a `deleteHiddenData` branch
  dropping enemy rows written in the current phase. ⚠️⚠️ It is not free: `DBManager::submitEW`,
  `insertEwEntry` and `adjustEwAmount` all use **positional** `INSERT INTO tac_ew VALUES (...)`, so
  the column and all three writes must change in one edit, and the live migration must land before
  the code.

#### Exit criteria met

**Stage A.** 77 server checks and 90 client checks green, including a **41-count ladder differential**
in which the JS reads back the table the PHP wrote (so the two are compared over the same inputs
rather than two independently-typed expectations, and the run asserts its own non-vacuity), the
stage's own 1/4/5/8/9 tuple on both sides, inclusive-at-exactly-range geometry, per-system ranges,
and the ladder exercised **end to end through the real sweep** at 13 detector counts.

**Stage B.** **210 checks green across three throwaway harnesses** — 50 server, 135 client, 25
tooltip-menu — covering the pool and its two sources, the clamp (including the R2 case: a ship that
spent everything on non-DEW types saves nothing), the phase window, the diff in all six of its
refusal modes, `submitLateEw` end to end against a recording DBManager (budget clamp, all-or-nothing
Disruption, idempotence, wrong phase, another player's ship, and the no-detector fast path proving
it never loads gamedata at all), the derived-bookkeeping invariant, both gates with Initial Orders
asserted unchanged, the real `AssignOEW`/`assignEW`/`deassignEW`/`removeEW` paths end to end, and
the menu split proved lossless **by object identity** with the null-selection case asserted.

**Both.** `checkShipData.php` PASS — 0 new findings, the same 237 in baseline. Replay harness 119
passed / 4 failed, and the same run with the six server files stashed is **byte-identical with
timings normalised**, so zero drift on any of the five checks, `masking` and `snapshot` included.
⚠️ The four failures are the known clean-tree baseline (4325, plus 3676 / 4249 / 4297 moved by the
*Elite crew* and *Kelly Phaser* commits and never re-recorded).

⚠️ **Two things a play test needs to know.** One detector grants 1 point, so the degrading ladder
needs **four** Waymarkers to see the first bracket end and **nine** to reach the quarter-point one.
And a ship that allocated all its EW during Initial Orders has nothing to save — that is R2 working,
not the detector failing.

#### What Stage A built

**The system.** `EWDetector` carries a `$range` and nothing else — no output, no arc, no order. Its
ctor takes `($armour, $maxhealth = 0, $powerReq = 0, $range = 0)` and 0 takes the control-sheet
value, the same convention every other Walker system uses. `isEwDetectorActive()` is
`!isDestroyed($turn - 1) && !isOfflineOnTurn($turn)`; `getDetectorRange()` returns 0 when it is not,
which is what removes it from the count. Arcs are declared 0..360 so `addSystem()` cannot stamp the
FRONT section's arc onto a front-mounted detector (`arch_addsystem_section_arc_trap`). Mounted on
the **Waymarker's front** for testing, with hit-chart row `10 => "EW Detector"` in the FWD section —
the row was already there, commented out.

⚠️ **NO CRITICAL TABLE, deliberately.** The rules list none, and a plausible-looking invented entry
would be a rule nobody wrote. Destruction and power-down are the whole of the damage model. If one
is ever wanted, `getDetectorRange()` is the single place a ladder goes.

⚠️ **THE RANGE IS PER SYSTEM, NOT A CONSTANT.** Every sweep asks the detector for its range rather
than assuming 20, so a hull may mount a shorter- or longer-ranged one from a control sheet without a
second class, and a future refit has one number to move.

**The arithmetic**, in `EW::` beside `getBlanketDEW` — which is the same shape of sweep, every
friendly unit within 20 hexes of an ELINT hull:

```php
EW::collectEwDetectors($gamedata, $turn)          // flat (team, pos, range) tuples, collected ONCE
EW::savedEwAllowanceFromDetectors($count)         // the degrading ladder
EW::countEwDetectorsCovering($detectors, $team, $position)
EW::getSavedEwAllowance($gamedata, $ship, $detectors = null, $turn = null)
```

⭐ **THE LADDER IS COUNTED IN QUARTERS, AS INTEGERS, START TO FINISH.** Every term is a multiple of
¼, so the whole rule is exact in integer arithmetic — and integer arithmetic is the only kind that
can be *promised* identical in PHP and JavaScript. A float version works today and drifts the first
time somebody adds a bracket. And the rounding rule collapses to one expression: down at ¼ and ½, up
at ¾, is `floor(x + ¼)`, i.e. `(quarters + 1) intdiv 4`. Worked through: 1→1, 4→4, 5→4, 8→6, 9→6,
10→6, 11→7, 12→7.

⭐⭐ **AND THE CLIENT MIRRORS ALL OF IT — the one EW sweep in the game that is not server-only.** The
rule is *"in range both before and after movement"*, and the after-movement position is a **plotted,
uncommitted** one: the server has not been told about it and by definition cannot be, because the
whole point of the allowance is that the player spends it at the end of the Movement segment on the
strength of where they ended up. `shipManager.getShipPosition()` already follows the plot, so
`ew.collectEwDetectors` / `savedEwAllowanceFromDetectors` / `countEwDetectorsCovering` /
`getSavedEwAllowance` track the drag for free. The server keeps the authoritative copy for Stage B's
validation. ⚠️ **MIRROR SET — a change to any one of the four is a change to its twin.**

**Both sweeps defer every question behind the `instanceof` / name test**, exactly as
`TacGamedata::setEdfHexes()` records: in all but a handful of games nothing is an EW Detector, and
`getHexPos()`, `isDestroyed()` and `isReinforcement()` are not worth asking of a ship that carries
none. Same three exclusions as the EDF sweep (destroyed, still in hyperspace, no position yet), plus
the detector's own two (destroyed system, powered down).

⚠️ **`range` and `effectiveRange` ride `stripForJson`, and they have to.** The client sweep needs the
range live, and `range` is not one of the 21 keys the client constructor re-defaults
(`arch_shipcompactor_key_stripping`). `effectiveRange` is the server's answer *after* destruction and
power-down, so the client does not reimplement `isEwDetectorActive` — but the client still tests
`systems.isDestroyed` and `power.isOffline` itself, because a player may power a detector down during
Initial Orders and the server will not know until the phase commits, which is exactly the window in
which the allowance is being read. `range` alone is the LOBBY fallback, since `stripForJson` is the
in-game payload. `data` goes with them for TRAP 6 (client system fields are shared by reference
across same-phpclass instances).

**Display.** A gold `Saved EW` row at the bottom of the ship window's Electronic Warfare panel
(`ShipWindowEw.getShipRows`), suppressed at zero. ⚠️ **OWN SIDE ONLY** — the number is a live read of
where friendly detectors are, so rendering it on an enemy hull would answer *"how many EW Detectors
cover this hex"* for a fleet the viewer is not in. Same ruling the stealth-toggle forecast carries;
`isPlayerInGame()` guards the observer, who has no side.

**The reading of "the fleet" that Stage A implements**, since the rules text says both things: the
allowance is **PER SHIP**, equal to `degrade(number of friendly detectors covering THAT ship)`.
*"This system provides every friendly unit within 20 hexes ... all friendly ships may save one point
of EW"* is unambiguous; *"allow the fleet to save 1 point of EW each"* is read as *"allow the fleet's
ships to save one more point each"*, with the "each" attaching to the detector. If the user rules the
other way — a single pooled fleet budget — `getSavedEwAllowance` is the only body that changes.

### 3.9 Sensor Charge Transceiver — **BUILT 2026-09-06 (Stage 9)**

Per D2. Hex-target, split-shot weapon where **each declared shot is a waypoint**. The full rules
text arrived with the stage, so nothing here is inferred. Stats: health 8, power 5, FC 1/2/3,
Standard damage 6d10 with no overkill, loading time 3, weaponClass Electromagnetic, no range
penalty. Server: `SensorChargeTransceiver` in `specialWeapons.php`. Client:
`SensorChargeTransceiver` in `model/weapon/special.js`. Mounted on the **Scribe**, Front section,
**arc 300–60**, hit chart roll 6–8.

⭐⭐ **THE MOUNT AIMS THE LAUNCH, NOT THE COURSE** (user ruling 2026-09-06; this **supersedes** the
original "arc 0–360, the charge steers so the section is for damage, not coverage"). The arc is
real and it bounds the direction the charge **leaves on**: the first leg must run along one of the
hex lines the mount covers — for a 300–60 transceiver that is bearings 300, 0 and 60 — and after
that the charge steers wherever its manoeuvres will take it.

⚠️⚠️ **AND UNTIL THAT RULING THE ARC WAS BEING APPLIED TO EVERY WAYPOINT, SILENTLY.**
`weaponManager.targetHex` gates every hex click on `isPosOnWeaponArc(shooter, hex, weapon)` — the
mount's wedge measured from the **ship** — which is exactly right for a weapon that SHOOTS at a
hex and exactly wrong for one that flies a course through it. So as a course wandered out of the
Scribe's forward 120° the clicks were dropped, **with no message at all** (that `if` has no else),
while the charge's own read-out sat at 9/16 hexes and 2/4 manoeuvres. Reported as *"prevented from
travelling any further… despite only being at 9/16 hexes"* (game 4341); reproduced hex for hex, and
the split between the refused and accepted hexes was exactly the 300–60 wedge (67.6° and 68.9° out,
60°/60°/52.4° in). Fixed with a small generic hook: a weapon may now answer the arc question
itself (`weapon.isHexOnFiringArc`), and the transceiver answers *"only the launch"*.

⭐ **Both ends ask their OWN existing arc function about the hex ONE STEP along the launch bearing**
— `weaponManager.isPosOnWeaponArc` on the client, `$shooter->getBearingOnPos()` on the server —
rather than comparing degrees. That inherits facing arithmetic, the roll mirror, split arcs and a
jammed turret's reduced arc for free, and neither side ever has to assume a hex direction and a
compass heading are the same number.

⭐ **AND A REFUSED CLICK NOW SAYS NOTHING** (user request 2026-09-07). `isHexOnFiringArc` answers
**both** geometry questions — is the hex on a hex axis *from the head of the course*, and, on the
first leg only, is that axis one the mount covers — so `targetHex` drops the click in silence, the
way it refuses an out-of-arc hex for every other straight-arc weapon (that `if` has no else). The
two sentences `measureLeg` used to raise for these are gone: the **blue fan already draws the
answer**, and a pop-up repeating it on every stray click is spam. ⚠️ The two refusals stay in
`measureLeg` with `reason` left **null**, because `measureLeg` is also what replays the standing
orders in `getCoursePlan` — a course read back out of the database has to truncate at leg one
exactly where the server's resolver truncates it. A null reason is the signal to refuse
*quietly*, and `doMultipleHexFireOrders` guards on it rather than concatenating a null into the
player's face. ⭐ The anchor moved with it: the bearing is taken from **`plan.head`, not
`plan.origin`**, because after the launch it is the end of the course a new leg runs from — and
`getHexDirection` returns **0, not null**, for the head itself, so clicking the head still reaches
the one sentence it always had ("at least one hex further on"). The two refusals that are *not*
geometry — a zero-length leg and an unaffordable one — keep their sentences, because the fan
cannot express either.

⭐ **CONTACT WITH A RECEIVER FINISHES THE COURSE** (user ruling 2026-09-07; this **supersedes** the
original "deliberately NOT once the course has reached a receiver"). `checkFinished` now unselects
the transceiver the moment the last waypoint holds a friendly transceiver, exactly as it does when
the charge runs out of hexes — the charge is home, so the declaration is done and the weapon gets
out of the player's way. ⚠️ Unselecting is **not a lock**: the course lives in `->fireOrders`,
`getCoursePlan` rebuilds it from them, and nothing gates re-selecting a weapon on `checkFinished`,
so a player who does want to fly on past a receiver picks the transceiver up again and keeps
clicking. ⚠️ Both branches now go through one predicate, `isCourseFinished(plan)`, which the ship
window's `maxVariableShots` reads as well — two derived read-outs disagreeing about whether a
weapon is done reads as a bug, and the shots figure was the one that would have kept counting.

**The shape of it, and why it needs one:** this weapon does not shoot at a target. The player plots
a COURSE and the charge damages whatever enemy units it passes through on the way, so the
declaration and the shots are two different things:

1. the client declares one hex fire order **per waypoint**, each carrying a `SCT|w:<n>` token in
   `->notes` — the Slicer's allocation channel, used here to carry a path;
2. `beforeFiringOrderResolution()` re-walks the course from scratch, **detaches every waypoint
   order** from `$this->fireOrders`, and puts back one real damage order per enemy-occupied hex
   plus one informational order for the combat log;
3. `Firing::prepareFiring` rolls those the ordinary way.

⚠️ The waypoint orders are **already in the database** when step 2 runs (`FireGamePhase::process`
persisted them at the POST), so detaching is an in-memory act only — and that is what makes the
course re-derivable, which the recharge depends on. Detaching is not optional: left in place,
`prepareFiring` hands each waypoint to `calculateHitBase` with `targetid -1` and stamps *"ERROR:
Null target shot attempted in normal fire routines"* into the player's own combat log.

**The path model (the one simplification worth arguing about).** The rules move the charge "in a
manner similar to a fighter… speed 16 with 16 thrust and a 1/4 turn cost", i.e. 16 hexes and 4
manoeuvres, "with a manoeuvre counting as a turn or slide". A full fighter plotter would be a second
movement engine; instead **a leg between two waypoints must run along one of the six hex axes**, and
changing axis at a waypoint costs `mathlib::getHexTurnCost()` manoeuvres — 1 for 60°, 2 for 120°, 3
for a reversal — which is what the same course costs a fighter. A one-hex leg at 60° is a slide,
priced at a slide's own cost. **The first leg is free**: the charge is launched, not turned.

**⭐ The boost's split is inferred, not declared.** "Every additional 2 points of power applied as a
boost produces either 1 additional hex of range or… an extra manoeuvre." `boostEfficiency = 2` is
those two points and one level buys one point — but which axis it is spent on is worked out at
resolution from the course actually plotted: `max(0, hexes−16) + max(0, manoeuvres−4) <= boost`.
One expression, no Initial Orders allocation dialog, and the totals are identical to declaring the
split in advance. The boost itself is still bought in Initial Orders, which is what the rules'
"must be configured for firing" asks for. `maxBoostLevel = 4` is the one number the rules do not
give.

**Truncation, not skipping.** The legs of a course are a chain, so dropping one in the middle would
teleport the charge across the gap. The first leg the budget cannot pay for ends the course and
everything after it is rejected with it.

**Targets.** One shot per enemy-occupied hex, origin hex excluded (a charge has not "passed
through" the hex it launched from). The rules make hitting optional and that half is still resolved
server-side — declining a free hit is never a decision. The automatic pick is deterministic and
replay-stable: capitals before small craft, bigger before smaller, then lowest id.

⭐ **The choice between units sharing a hex is the PLAYER'S, and it needed no new gesture**
(user ruling 2026-09-06). It first shipped server-side too, on the grounds that a per-hex target
picker would be a whole second gesture on top of plotting — but it does not have to be one, because
a waypoint that carries straight on costs **no manoeuvre and no extra range beyond the hexes it was
going to cross anyway**. So dropping a waypoint on a crowded hex is already free, and the choice
rides on the waypoint: the ship tooltip's hex button reads **"Target Ship"** whenever a transceiver
is selected and the unit under the cursor is an enemy, and the id it names is appended to the
waypoint token as `SCT|w:<n>|t:<shipid>` (`TARGET_TOKEN`).

⚠️ **ADVISORY, NEVER AUTHORITATIVE.** `pickTargetInHex()` takes the named id as a hint and it wins
only by passing the same eligibility loop as every other candidate — enemy, alive, not terrain,
deployed, and actually standing on that hex. Anything that fails one of those is not an error: it
falls silently back to the automatic pick, so a stale or hostile POST can change WHICH legal enemy
in a hex is hit and nothing else. The client refuses the same cases before writing the token
(`resolveHexTargetChoice`), so the two ends agree, but the server never trusts that.

⚠️⚠️ **AND IT NEEDED A MASK.** `hidetarget` blanks `x`, `y` and `targetid` for an enemy viewer,
but it has never touched `->notes` — so the token would have named a ship standing on a course whose
hexes had just been blanked, which is exactly the information `hidetarget` exists to withhold. New
opt-in flag `Weapon::$hideNotesFromEnemies` (protected, with a getter, like `$alwaysHideFireOrders`)
blanks the notes in the same branch. Opt-in rather than blanket: `->notes` is a general-purpose
channel and most of what rides it is either public or written at resolution.

**The receiver.** The last waypoint must hold a friendly ship (same TEAM) with an undestroyed
transceiver — the firing ship's own hex counts, per "or possibly returning to the originator", and a
closed triangle of side 5 costs exactly 15 hexes and 4 manoeuvres, so a **single** transceiver is a
usable weapon. If nothing is there the charge is lost and does no damage at all, whatever it flew
over. A receiving transceiver takes 1 point per 2 full hexes of **base** range left unused (never
the boosted range: boost spent on manoeuvres has not lengthened the charge, and boost spent on hexes
has by definition been used), as an ordinary `DamageEntry`, so `Criticals::setCriticals` rolls its
critical in the same pass as everything else damaged that turn.

**The dual recharge.** 3 turns after a recovered charge, 1 turn when it was not recovered.
⚠️ **Interception is not modelled as an event of its own** — FV only intercepts ballistics and this
weapon declares and resolves inside the Fire phase, so "not recovered" is the general case the rules
give interception as one example of.

**The overlay** (`BallisticIconContainer.generateSensorChargeCourses` /
`refreshSensorChargeCourses`) — **four** scene objects, all of them built from the weapon's own
`getCoursePlan()` so what the player sees and what `doMultipleHexFireOrders` will accept can never
disagree. Reworked 2026-09-06 after play testing; what the first build got wrong is recorded with
each one.

- **The course**, one line per leg, **green** once it ends on a friendly transceiver and **yellow**
  until then — that colour change is the whole "or the charge is lost" rule, shown rather than
  explained. Now with **chevrons every 2 hexes**, walked BACKWARDS from each waypoint so every leg
  finishes with one pointing into its corner. A course crosses itself, doubles back and ends
  nowhere in particular, so which way round it is flown is not something the geometry says on its
  own — and it is what decides which end has to hold the receiver.
- **The reachable fan**, in the ordinary **weapon-arc blue** (`rgb(20,80,128)`, mirrored from
  `ShipIcon.showWeaponArc` — it makes the same statement a firing arc does, so it should not have
  a colour of its own; the cyan it launched with belongs to "not here yet" markers), drawn only
  while the transceiver is SELECTED. ⚠️ **This was six
  LINES and is now HEXES** (`getReachableHexes` → `buildHexRegionOverlay`). The original argument
  for lines was cost — ~96 hexes over a 41×41 sweep — and it was true but beside the point: a line
  asks the player to judge whether a hex centre sits on it, and the answer to *"can I click there?"*
  is a hex. The sweep only runs when the weapon is selected AND the course changed
  (`syncSceneObject`'s signature covers both), which is a handful of rebuilds per turn.
- **The markers**, green hexes, and NOT one per waypoint: a course run along one axis can hold a
  dozen and marking them all buries the line under hexes that say nothing. What earns a hex is a
  place where something HAPPENED — where the charge **manoeuvred**, the **head** (the last hex
  clicked so far), and any hex where the player **named a unit**. That third case is not decoration:
  a straight-through waypoint costs no manoeuvre, which is exactly what makes it the free way to
  pick a unit out of a crowded hex, and without a marker the choice would have nothing on screen to
  confirm it. Drawn as `BallisticSprite`s rather than one `HexRegion` blanket because they are
  scattered (a region would be one loop per hex anyway) and because a sprite can carry TEXT — which
  is how a named hex says whose name it is.
  ⚠️ **THE TEXT IS GONE as of Stage 11** (§3.10b, user ruling 2026-09-08: obscured by ship sprites,
  and unreadable where several charges cross one hex). The marker SET is unchanged — a named
  straight-through hex still earns its green hex — but a marker is now `{q, r}` and nothing else.
- **The budget**, two rows of figures at the head, SELECTED-only, in the LoS ruler's idiom
  (`mathlib.drawRuler`). Hexes and manoeuvres, each as **spent-of-total**. ⚠️ The totals are
  DERIVED (`used + left`), never the class constants: boost levels are bought in Initial Orders and
  go to either axis, so "16" stops being right the moment the player buys one.
  ⚠️⚠️ **AND A THIRD ROW, `+N`, WHENEVER THE WEAPON IS BOOSTED** — added 2026-09-06 after the
  report *"seems to run out of hexes after it goes above the normal 4 manoeuvres"* (game 4341,
  Scribe #2, boosted 3). **There was no arithmetic bug**: the two totals are each *"the most THIS
  axis could take if nothing further goes to the other"*, and boost is a **shared** pool — so they
  can never both be reached, and the hex total visibly shrinks 19 → 18 → 17 as manoeuvres eat
  levels off it. Carrying straight on really does still have the whole remainder (proved on both
  ends over the identical course). What was missing was any sign of the pool the two rows compete
  for; `+N` is that pool. The ship window's "Charge remaining" line says the same thing in words.
  ⭐ Generalises: **two "remaining" figures drawn from one pool will always contradict each other
  — show the pool, or show one figure.**
  ⚠️⚠️ It sits at **z 130**. The head of a course is exactly the sort of hex that holds a stack of
  ships and a selected or active-mover hull sits at +100 for a whole phase, so the requirement is
  z > 100, not z > 0 ([[arch_map_z_planes]]).

**Tuning it by hand.** Every number that sizes this overlay is one `const` in the block above
`buildLineChain`, and they are meant to be edited: `SCT_ARROW_SIZE` (barb length in hexes — the
arrow knob), `SCT_ARROW_EVERY` (hexes between chevrons), `SCT_ARROW_ANGLE` (how wide a chevron
opens), `SCT_LABEL_SCALE` (the whole read-out's size in hexes — the font knob), `SCT_LABEL_FONT_PX`
(glyph against padding inside the canvas) and `SCT_FAN_DIM` (how strongly the fan tints). All six
were halved or better on the first play test. Nothing else changes when one moves — they feed the
builders directly, and each is in hexes or canvas pixels so it holds at every zoom.

⚠️ **AND THE WAYPOINTS ARE NOW OFF THE BALLISTIC LAYER ENTIRELY.**
`getAllFireOrdersForAllShipsForTurn` admits any normal order with `targetid -1` as a ballistic, so
every waypoint had been drawing a red "incoming fire" hex and a white arrow back to the shooter —
a dozen of them fanning out of one hull, each describing a straight line to a corner of a course
that is not straight and does not start there. `consumeGamedata` now drops the order on its
`damageclass` (`'sensorcharge'`), at the LOOP level rather than inside `createBallisticIcon`, for
the same reason the Gravitic Mine suppression is there: `updateBallisticIcon` would otherwise keep
icons left over from a Replay session alive.

⚠️ `refreshSensorChargeCourses` marks and sweeps BY PREFIX and now owns **four** of them
(`SCT_KEY_PREFIXES`). It runs outside the `consumeGamedata` pass, which is the only thing that
clears `used` on everything — so a prefix left out of that list is an overlay that never goes away.

**The hover arc.** ⚠️ The transceiver drew **no arc at all** on hover until 2026-09-06.
`ShipIcon.showWeaponArc` sizes every arc through `getWeaponReachInHexes()`, which reads `range`
and `rangePenalty` — and this weapon carries **both at 0**, meaning "no range limit" and "no range
penalty", which works out to a reach of zero hexes and nothing drawn. Its `stripForJson` now also
publishes `shootsStraight` (so it takes `showStraightArcs`' **star of hex lines**, clipped to the
mount's arc — three arms for a 300–60 mount, which is precisely the set of hexes a charge can
launch onto) and `arcDisplayRange = CHARGE_RANGE` (the reach to draw, since `range` does not
describe it). Base range rather than boosted: a hover arc is a property of the mount and the boost
is bought per turn. ⭐ Both ride the **poll payload**, published per instance, so no static
regeneration is needed — the same fix shape as the Stage 7 `powerReq` trap.

**Deliberately not done:**
- the charge gets no hit section of its own — a shot resolves on the FIRING ship's bearing like any
  other direct-fire weapon, not on the bearing of the leg it arrived along. Doing it properly needs
  a ballistic-style hit LOCATION *and* a ballistic-style defence PROFILE, and the two have to move
  together or they describe different shots;
- no line-of-sight test along the course. A steered charge is not a beam, and the engine's only LoS
  test is a straight line from shooter to target hex, which a course is not.

**New shared geometry:** `mathlib::getHexDirection()` and `mathlib::getHexTurnCost()` (server) with
mirrors in `mathlib.js`. ⚠️ The client mirror uses **`hexagon.Offset`'s** neighbour table, which is
byte-for-byte the PHP `mathlib::$neighbours` one — *not* `mathlib.offsetNeighbors` underneath it,
which lists the same six hexes in a different order. The order of that table is the entire meaning
of a direction index, so the wrong one gives the client a different compass and every leg but due
east disagrees.

### 3.10 Housekeeping — five corrections to shipped stages — **BUILT 2026-09-10 (Stage 11)**

Five unrelated small items, collected because none of them is worth a stage of its own and all five
are cheap. They land together as Stage 11.

**As built.** All five, each provable on its own: 14 checks on the bracket arithmetic, 15 on the SCT
markers, 11 + 14 on the untargetable pair (client and server), and the seeding demonstrated on real
hulls both ways. `checkShipData.php` unchanged (238 findings / 237 baselined / **1 new error that
is NOT ours** — see below); replay harness **119 passed, 4 failed**, exactly the four documented
clean-tree failures (3676, 4249, 4297, 4325) and byte-identical to a stashed-tree run.

⚠️⚠️ **AND THE HARNESS EARNED ITS KEEP ON A ONE-LINE CHANGE.** 3.10e's hull list started life as
`public static $fullyChargedHullClasses` on `Weapon`, which broke **game 4151** outright:
`MissileRack::stripForJson` walks its ammo objects with
`ReflectionObject::getProperties(IS_PUBLIC)` and then reads each name as `$missile->$key`
([missile.php:62](source/server/model/weapons/missile.php#L62)) — and **reflection lists public
STATICS beside the instance properties**, so every missile-armed ship in the game threw
*"Accessing static property LightBallisticTorpedo::$fullyChargedHullClasses as non static"* and
lost its whole gamedata payload. ⭐ **A shared list on `Weapon` must be a `const` or `private
static`, never a `public static`** — it is the same trap `ShipCompactor::annotateSystems` records
in its other form ("a public property on Weapon lands on every ammo entry of every poll"), and a
constant is neither iterated nor serialised. Nothing but the replay corpus would have found it:
every unit test of the feature passed, on both trees.

⚠️ **One pre-existing FAIL sits on top of this stage and is not part of it.** `checkShipData.php`
reports `Wanderer :: location 1, roll 9 — no system named "EW Detector" on location 1`: the hull's
front hit chart says `"EW Detector"` while `EWDetector::$displayName` is
`"Electronic Warfare Detector"` (the Waymarker's chart, [Waymarker.php:114](source/server/model/ships/walkers/Waymarker.php#L114),
spells it correctly). Every roll of 9 on the Wanderer's front chart is therefore silently rerouted
to Structure and the EW Detector can never be hit. It fails identically on a stashed tree, so it
came in with the hull; the fix is one string in
[Wanderer.php:94](source/server/model/ships/walkers/Wanderer.php#L94).

#### 3.10a The 50% deployment bracket — **BUILT 2026-09-10**

**As built.** `units50 / points50 / limit50` beside the two existing sets, `limit50 = floor(calcPoints * 0.5)`,
and a third report line with its own copy of the one-ship exception. The prose gained a fourth
bracket in the availability list at [fleetchecker.php:164](source/public/fleetchecker.php#L164)
("three categories" → four), and the "up to 33% ... on Limited units" sentence now says
*Limited (33%)*, because with two Limited brackets "Limited" alone no longer names one.

⭐ **THE "IT EXISTS TWICE" WARNING BELOW IS NOW STALE, AND THAT IS WORTH KNOWING BEFORE THE NEXT
EDIT.** The second copy is `checkChoices_LEGACY`, and it is **inside a block comment** — `/*` at
[gamelobby.js:4722](source/public/client/gamelobby.js#L4722) closing at
[:5860](source/public/client/gamelobby.js#L5860), with a header that says it is kept verbatim and
inert. So there is exactly ONE live fleet checker and it was the only one edited; a test asserts
the archived copy did *not* grow a 50% bracket, which is the check that keeps the two from being
confused again. The `oneOverAllowed` remark stands as a description of the archived copy.

⚠️ Two rules stayed 10%-only, deliberately: the escort rule
([gamelobby.js:1644](source/public/client/gamelobby.js#L1644)) is about Restricted units and now
says so in a comment, and nothing else in the function learned about the new bracket.

`Pathfinder` already carries `$this->limited = 50`
([Pathfinder.php:16](source/server/model/ships/walkers/Pathfinder.php#L16)), and the buy list
already prints "50%" — [gamelobby.js:2574](source/public/client/gamelobby.js#L2574) tests
`limited > 0 && limited < 100` and shows whatever number it finds. **The fleet check does not**:
it knows exactly two brackets.

- `units10 / points10 / units33 / points33` are declared at
  [gamelobby.js:1028](source/public/client/gamelobby.js#L1028), accumulated at
  [1099–1107](source/public/client/gamelobby.js#L1099) and reported at
  [1590–1626](source/public/client/gamelobby.js#L1590). Add a third set and
  `limit50 = Math.floor(calcPoints * 0.5)`.
- ⚠️⚠️ **THE FLEET CHECKER EXISTS TWICE IN THIS FILE.** The same three blocks appear again at
  [4754](source/public/client/gamelobby.js#L4754), [4824](source/public/client/gamelobby.js#L4824)
  and [5325](source/public/client/gamelobby.js#L5325), near-identical but not identical (the second
  copy still consults the dead `oneOverAllowed` flag). Editing one and not the other produces a
  lobby that passes a fleet the Fleet Checker fails, or the reverse, with nothing to say why.
- ⚠️ **Two rules are 10%-only and must stay so**: the "one single ship is allowed to break limit"
  exception applies per bracket (so 50% gets its own copy, correctly), but *"Restricted (10%) ship
  present without escort"* ([1626](source/public/client/gamelobby.js#L1626)) is a rule about
  10% units and must not learn about 50% ones.
- The prose at [fleetchecker.php:111](source/public/fleetchecker.php#L111) lists the brackets and
  [163](source/public/fleetchecker.php#L163) the occurrence bands; both need the 50% line.

**Exit criterion:** a fleet containing one Pathfinder passes and fails at the right points cap on
BOTH the lobby's live check and the standalone Fleet Checker, and no existing 10%/33% verdict moves
anywhere in the 2,500-hull corpus.

#### 3.10b The Sensor Charge Transceiver's green name — **BUILT 2026-09-10**

**As built.** `getCourseMarkers` pushes `{q, r}` and nothing else — the `gamedata.getShip` lookup
and the `text` key are both gone, so `add()` no longer takes a target id at all — and
`buildCourseMarkers` calls `new BallisticSprite(position, 'hexGreen')` with no text argument, which
falls through to the plain cached green-hex texture. `SCT_MARKER_TEXT_COLOUR` is deleted and the
marker signature is hex-only.

⭐ The marker SET is unchanged, and that is the half a test has to pin: a hex where the player named
a unit while carrying straight on costs no manoeuvre and would otherwise have nothing on screen at
all. 15 checks, run against the real method lifted out of `special.js`, with `gamedata.getShip`
stubbed to **throw** — so a re-introduced name lookup fails loudly rather than quietly.

User ruling 2026-09-08: *"somewhat useless, since it's obscured by ship sprites and would be hard to
read if several SCT charges passed through the same hex."*

The name is written by `SensorChargeTransceiver.prototype.getCourseMarkers`
([special.js:2270](source/public/client/model/weapon/special.js#L2270)) — `text: target ?
target.name : ""` — and drawn by `buildCourseMarkers`
([BallisticIconContainer.js:655](source/public/client/renderer/icon/BallisticIconContainer.js#L655))
in `SCT_MARKER_TEXT_COLOUR`. Stop supplying the name; the green hex stays.

- ⚠️ **Keep the marker, drop the word.** The named-unit marker exists because "a waypoint that
  carries straight on costs no manoeuvre, which is exactly what makes it a free way to pick a unit
  out of a crowded hex" — the *hex* is still the only on-screen confirmation that a choice was
  recorded there, and the `SCT|w:<n>|t:<id>` token still rides the order. Removing the marker as
  well as the text would make the pick invisible. (Q9, confirmed 2026-09-08: *"keep hex but remove green text"*.)
- The renderer's marker signature
  ([BallisticIconContainer.js:755](source/public/client/renderer/icon/BallisticIconContainer.js#L755))
  interpolates `marker.text`; with the text always empty it collapses to hex-only, which is still
  correct — it changes whenever the marker SET changes, which is the only thing it has to do.
- `SCT_MARKER_TEXT_COLOUR` becomes dead. Delete it rather than leaving a constant nothing reads.

#### 3.10c The Energy Draining Mine cannot be shot at — **BUILT 2026-09-10**

**As built**, as the section proposes: `BaseShip::isTargetableBy($shooter = null, $turn = false)`
([ShipClasses.php:3248](source/server/model/ships/ShipClasses.php#L3248)) answering `empty($this->unTargetable)`,
mirrored by `shipManager.isTargetable(ship)` ([ships.js:529](source/public/client/ships.js#L529))
and consulted beside `Huge > 0` at **both** weaponManager sites — the tooltip line (which keeps
saying just "Cannot Target", one refusal rather than a new reason) and the `targetShip` click.

⭐ **ONE FACT, AND IT IS A SHIP PROPERTY RATHER THAN A METHOD OVERRIDE.** `public $unTargetable = true`
is declared **only** on `SpawnEnergyDrainingMine`, so the server method and the client mirror read
the same field and the 2,556 other hulls carry nothing new: the key rides the static blueprint
verbatim (the generators `json_encode` the raw ship), which is how the client learns it without a
`stripForJson` line, and its absence everywhere else is why the client test must be a **truthy**
one. `empty()` rather than a plain property read, because on every other class the property does
not exist. A ship-data corpus check confirms the compacted blueprint carries the key on the orb and
on nothing else.

⚠️ Verified NOT to touch the field: the orb still mounts its `EnergyDrainingField`, still answers
`getEdfRadius`/`isEdfActive`, is still not `Enormous`, and still keeps its notes and its map disc.
Ramming, collateral and area effects are untouched — nothing but the two deliberate-selection sites
asks the question. The server-side refusal was built at Stage 15 (§3.17a): `Firing::validateFireOrders`
now refuses a POSTed shot at the orb too.

*"Can we hide the Energy Draining Mine from sight or at least prevent people targeting it (since
there's no point in destroying it)"* — **prevent targeting, do not hide.**

⭐ **Hiding it would delete the field.** The orb's icon *is* the field marker: the purple seven-hex
disc labelled "Energy Drain Mine" added in Stage 6 is drawn from the unit. Its `notes` already say
*"Destroying this probe has no in-game effect"*
([SpawnEnergyDrainingMine.php:63](source/server/model/ships/terrain/SpawnEnergyDrainingMine.php#L63))
and its Structure is indestructible; what is missing is only that the client still offers it as a
target.

The existing mechanism is the Moon one the user names: `weaponManager` refuses `ship.Huge > 0` in
two places — the tooltip line at
[weaponManager.js:817](source/public/client/weaponManager.js#L817) (`Cannot Target`) and the click
itself at [3571](source/public/client/weaponManager.js#L3571). ⚠️ **It is client-only.** Nothing on
the server refuses a fire order at a Huge unit at all, which is tolerable for a wasted shot and is
*not* tolerable for §3.17, where a real rule depends on the refusal.

So introduce the pair now and give it its server half in Stage 15 (**built 2026-09-11**, §3.17a):

```php
BaseShip::isTargetableBy($shooter, $turn)   // default true; false on the EDM orb
```
mirrored as `shipManager.isTargetable(ship)` and consulted alongside `Huge > 0` at both
weaponManager sites.

- ⚠️ **It must not reach the field.** `TacGamedata::setEdfHexes()` collects every `EdfSource`
  regardless; nothing about targeting may gate that.
- ⚠️ **It must not block ramming, collateral or area effects.** The orb is `Enormous = false` on
  purpose (it does not auto-ram and does not block line of sight), and a blast that happens to
  cover its hex still resolves against everything else standing there. This flag governs the
  *deliberate selection of this unit as a target* and nothing else.

#### 3.10d The faction entry in `factions-tiers.php` — **BUILT 2026-09-10**

**As built**, at [factions-tiers.php:1803](source/public/factions-tiers.php#L1803) with its TOC line
at [:98](source/public/factions-tiers.php#L98), in the Torvalus block's shape: an intro, then one
`<h5>` per system — Electromagnetic Weaponry, Lightning Array / Medium Lightning Array, Wide-Beam,
Chromatic Pulse Driver, Energy Draining Field, Extended Draining Field, Energy Draining Mine,
Energy Draining Net, EW Detector, Sensor Charge Transceiver, Gravitic Drives — plus the hangar
exemption, a Fleet Composition block (the Ancient brackets, the Pathfinder's 50% and the Walkers'
own short enhancement list) and the hull roster.

Two things it does that the section did not ask for and that the standing obligation needs:

- an italic line under the intro saying the entry describes **what is implemented today**, so a
  reader knows the page is a moving record rather than a design document;
- a closing list of **what is not implemented yet** (the Mapmakers' EW, their Medium Lightning
  Array and jump engine, the Traveler's Docking Bay, repair and power sharing, and both jump
  drives). ⭐ The alternative is worse than an omission: the Mapmaker hull is already **buyable**,
  and its array and jump engine are commented out in `populate()` with `//STAGE` markers, so a
  player reading a systems list would otherwise buy a flight expecting a weapon that is not there.

⚠️ Every figure in it was read out of the code rather than out of this plan (the hull costs and
system lists from a constructed instance of each of the seven hulls, the drain dice from
`EdfExposure`'s constants, the refit prices from the registry) — which is the only way an entry
like this stays true.

A `<h4 id="walkers">WALKERS OF SIGMA-957</h4>` block, placed after `#vorlons`
([factions-tiers.php:1721](source/public/factions-tiers.php#L1721)) and before
`<h3 id="otherfactions">` ([1802](source/public/factions-tiers.php#L1802)), plus one TOC line after
the VORLON EMPIRE entry at [97](source/public/factions-tiers.php#L97).

Written from what is **built**, not from what is planned — Lightning Array and Medium Lightning
Array with their two firing modes and the Wide Beam toggle; the Chromatic Pulse Driver's Pulse and
Scanning modes and the fleet-wide shield adaptation; the Energy Draining Field, fixed and variable,
with its four drains and the targeting penalty; the Energy Draining Mine; the Energy Draining Net's
corridors and closed areas; the two refits (`EDF_RANGE`, `SYS_WBLA`/`SYS_WBMLA`); the Sensor Charge
Transceiver; and the hull roster (Traveler, Waymarker, Pathfinder, Scribe, Guideship, Mapmaker
Sensor Probes). Model the structure on the Torvalus block at
[1399](source/public/factions-tiers.php#L1399), which is the closest in shape — an Ancient faction
with a hangar exemption to explain.

⭐⭐ **AND IT IS NOW A STANDING OBLIGATION.** Every stage from here on adds a paragraph to this
entry as part of its own exit criterion. A faction page written once and never updated is worse than
none: players read it as authoritative and it silently describes a game that no longer exists.

#### 3.10e The Wanderer's weapons DO begin the game fully charged — **BUILT 2026-09-10**

**As built**, hung on `setInitialSystemData` exactly as the ⭐ below argues, in three parts:

- `Weapon::getStartLoading()` now delegates to a new **`getFullStartLoading()`** carrying its old
  body verbatim. That is what gives a class which overrides `getStartLoading()` (to seed less) a
  way back to the unrestricted seed; nothing else changed for any of the ~2,500 weapons that do not.
- `Weapon::setInitialSystemData($ship)` asks **`getStartLoadingForShip($ship)`**, which lifts the
  restriction when `$this->seedsBelowFullCharge` **and** the hull is in
  `Weapon::FULLY_CHARGED_HULL_CLASSES`. `getStartLoading()` keeps its no-argument signature for
  HangarOps' four re-seeds and for `dualWeapon`/`duoWeapon`'s sub-weapons.
- `protected $seedsBelowFullCharge = true` on **`MediumLightningArray`** and
  **`ChromaticPulseDriver`** — the two classes that seed 1 instead of `normalload`. ⭐ One flag per
  class rather than an override per class, so the exception logic is written once; and **protected**
  because `json_encode` drops protected properties and a public flag would ride every one of the
  ~57,000 system objects in the blueprint tree.

⚠️ **The `EnergyDrainingMine` launcher is deliberately NOT in scope** and still opens the battle at
1/3 on a Wanderer as on every other hull: it does not set the flag. The ruling and this section are
about weapons that *charge*, and the launcher's seed is an ammunition count with a documented
"turn 1 must not reload" rule of its own (§3.6). Flag it if the user wants 3/3 there too — it is one
line.

**Proven** on real hulls both ways: a Wanderer's three Chromatic Pulse Drivers seed 2/2 while a
Traveler's and a Scribe's seed 1/2, and a Waymarker/Pathfinder Medium Lightning Array seeds 1/2
until the same hull is asked with `phpclass` forced to `Wanderer`, when it seeds 2/2 — which also
proves the mechanism reaches `MediumLightningArray`, a class no Wanderer currently mounts. See the
public-static reflection trap at the head of §3.10 for the one thing that went wrong.

User ruling 2026-09-09: *"Unlike other Walker ships, the Wanderer phpclass ship's weapons DO start
the battle fully charged."*

Every Walker weapon that charges over turns currently overrides `getStartLoading()` to seed less
than `normalload` — the Lightning Array pair
([specialWeapons.php:12562](source/server/model/weapons/specialWeapons.php#L12562)) and the
Chromatic Pulse Driver ([pulse.php:1381](source/server/model/weapons/pulse.php#L1381)). The
Wanderer is the exception, so that override has to become conditional on the mounting hull instead
of absolute.

⭐ **`getStartLoading()` is the wrong hook, because it does not know its ship.** Its one caller does:
`Weapon::setInitialSystemData($ship)` ([weapon.php:1010](source/server/model/weapons/weapon.php#L1010))
receives the hull and writes the result into `tac_systemdata`. Put the exception there — a
`Weapon::$startsFullyChargedOnClasses`-style opt-out consulted against `$ship->phpclass`, or a plain
override on the two Walker weapon classes — and `getStartLoading()` keeps its no-argument signature
for every other caller in the tree (`HangarOps` re-seeds launched craft through it in four places,
and `dualWeapon`/`duoWeapon` call it on their sub-weapons).

- ⚠️ Key it on **`phpclass`**, not on faction: every other Walker hull shares
  `"Walkers of Sigma-957"` and must keep the restricted seed.
- ⚠️⚠️ `setInitialSystemData` runs **once, at ship creation**. An existing game does not re-seed, so
  a play test needs a **fresh game**, not a reload — and any Wanderer already on a board keeps the
  charge it was created with.

**Exit criterion:** a newly created Wanderer's Lightning Array and Chromatic Pulse Driver report
full charge on turn 1, while the same weapon classes on a Traveler still report their seeded value.

### 3.11 Mapmaker Electronic Warfare

*"Can use up to 3 OEW or DEW per turn, like a ship. Mapmakers are the only fighter unit that need
this, so we should make sure it has a simple gate to prevent unnecessary work for all other
fighters."*

**THE GATE IS ONE PROPERTY.** `FighterFlight` gains `public $ewCapacity = 0;`, set to `3` on
`MapmakerProbes` and left at 0 on every other flight in the game. Every branch this section adds
opens with `$flight->ewCapacity > 0` (client: `ship.ewCapacity`), so an ordinary flight pays one
falsy property read per site and nothing else. Mirrored to the client by `stripForJson` —
⚠️ and it must actually be published, because `ShipCompactor` strips defaults and `undefined > 0`
is false, which is the wanted answer (trap 8).

**What already works, unmodified:**

- `FighterFlight::getDEW($turn)` and `getOEW($target, $turn)`
  ([FighterFlight.php:504](source/server/model/ships/FighterFlight.php#L504) and
  [516](source/server/model/ships/FighterFlight.php#L516)) already read the flight's `EW` array.
  The server-side storage for flight EW has existed all along; nothing has ever written to it.
- Enemy masking is already right. `TacGamedata::deleteHiddenData` blanks `$ship->EW` wholesale for
  every unit not owned by the viewing player during phase 1
  ([TacGamedata.php:1545](source/server/model/TacGamedata.php#L1545)), flights included, so a
  Mapmaker's declared OEW is hidden exactly as a ship's is.
- **Mapmaker DEW is free.** *"Works exactly the same as ship DEW e.g. is ignored by fighters"* — a
  shooter's "fighters ignore defensive EW" line is `$dew = 0` inside the `instanceof FighterFlight`
  branch of the SHOOTER's hit calculation ([weapon.php:1741](source/server/model/weapons/weapon.php#L1741)),
  which is untouched for everyone else. A Mapmaker's DEW is read off the flight by the ordinary
  `$target->getDEW($turn)` line above it. Nothing to build.

**What has to be built.**

**1. The allowance, and ⚠️⚠️ THE NAME COLLISION THAT WILL BITE.**
`ew.getScannerOutput(ship)` **already has a flight branch**
([ew.js:9](source/public/client/ew.js#L9)) and it answers a *different question*: it returns
`floor(offensivebonus / 2)` (full OB for a minesweeper) as the **mine-detection** allowance, which
`assignEW` then spends on `"Detect Mines"` entries — and `getEWLeft` is
"scanner output minus everything that is not DEW" ([ew.js:252](source/public/client/ew.js#L252)),
which every assign path measures against.

Adding 3 to that one number would let an ordinary fighter spend its mine-detection allowance on OEW
**and** let a Mapmaker spend its OEW pool on mine detection, in both directions, silently.

**Keep two pools.** `getScannerOutput` stays exactly as it is for flights; a new
`ew.getFlightEwCapacity(ship)` (0 unless `ship.ewCapacity`) is consulted by the OEW/DEW paths only,
with its own `ew.getFlightEwLeft(ship)` that counts only `OEW`/`DEW` entries. The server twin goes
in `EW::getScannerOutput` ([EW.php:42](source/server/handlers/EW.php#L42)), which today has no
flight branch at all and returns 0 for one.

**2. The hit chance, server and client.** Inside the `instanceof FighterFlight` branch at
[weapon.php:1735](source/server/model/weapons/weapon.php#L1735), gate on a new weapon property
`$useFlightEW` (§3.13's array is its only user). **Only the `$oew` assignment changes** — the
combat-pivot penalty and the rest of that branch stay exactly as they are (user, 2026-09-08:
*"flight-level combat does not exist in FV"*):

```
ordinary fighter weapon   $oew = $effectiveOB + getOEW($target);
                          $dew = $bdew = $sdew = 0;

flight-EW weapon          $oew = max(0, getOEW($target) - ($dew + $bdew + $sdew));
                          $dew = $bdew = $sdew = 0;
```

⭐ **The Light Chromatic Pulsar keeps its Offensive Bonus AND gains any OEW the flight allocated**
(user ruling 2026-09-08: *"The LightChromaticPulsar DOES use Offensive Bonus (and any OEW)"*). So
the first row is not "unchanged" — `getOEW()` returns 0 for every flight in the game today, which is
what makes the added term free everywhere else, but on a Mapmaker it is real. The two rows are the
whole difference between the faction's two fighter weapons: the Pulsar **adds** EW to its bonus and
ignores defensive EW entirely; the Array **replaces** the bonus with EW and is contested by it.

That contest is D12 — *"DEW can only cancel out Mapmaker's OEW to the normal hit chance against the
target's defence profile"* — and (Q10, answered 2026-09-08) **blanket and supported defensive EW
stack with the target's own DEW** inside the subtraction, because they are separate EW functions.
The control sheet's *"Effected by DEW"* is that line, and it appears on the Array's sheet only.

⚠️ The subtraction happens **before** the zeroing, and the `max(0, …)` is the whole rule: 3 OEW
against 5 DEW is 0, never −2. Zeroing afterwards is what keeps the weapon from being *worse* than
an ordinary fighter gun against a heavily screened target.

⚠️ **Mirror in `weaponManager.calculateHitChange`**, which has its own fighter branch; a
preview/resolution disagreement on a weapon whose whole point is the lock is the worst possible
place to have one.

**3. `convertUnusedToDEW` refuses flights outright** —
`if (ship.flight) return false;` ([ew.js:181](source/public/client/ew.js#L181)). A Mapmaker's
unspent points must become DEW the way a ship's do, so this needs the `ewCapacity` exception too.

**4. The Initial Orders UI.** The OEW/DEW rows are hidden for a selected flight by `sourceNotFlight()`
([shipTooltipInitialOrdersMenu.js:326](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L326)).
Relax it to `sourceNotFlight() || sourceIsEwFlight()`.
⚠️ **Do not touch `notFlight()`** ([318](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L318)) —
it also refuses when the **target** is a flight, which is a separate and still-correct rule.

**5. The flight window's EW block.** *"A new Electronic Warfare list should be added to
flightWindow to show EW amounts (placed top-right, outside the normal flight icons is fine, and it
can just be the same style as ship EW block)."*

The flight branch of `ShipWindow.render`
([ShipWindow.js:1768](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L1768)) renders
header + `FighterList` + status strip; the ship branch renders `<ShipWindowEw ship={ship} />`
([1872](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L1872)). Render the same component,
gated on `ship.ewCapacity`.

- ⚠️ `ShipWindowEw` builds a *ship's* row list — BDEW, CCEW, Detect Stealth, Detect Mines and the
  per-target OEW/DIST/SOEW/SDEW rows. A flight has one meaningful subset (DEW plus its OEW target
  rows). Give it a flight row list rather than letting `ew.getCCEW` and friends answer for a unit
  they were never asked about.
- ⚠️ **The flight window has no room.** `$variant="flight"` is capped at `400px`
  ([ShipWindow.js:104](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L104)) and
  `FighterList` wraps against that cap. Top-right *outside* the icons means either a wider flight
  variant or an absolutely positioned block; the scale-to-fit budgets in
  `project_shipwindow_redesign` decide which, and the resize grip has to keep working either way.

**6. The cap is currently enforced nowhere on the server.** `EW::validateEW` returns `true`
unconditionally ([EW.php:5](source/server/handlers/EW.php#L5)) — the ship-side budget check was
disabled years ago for Constrained ELINT hulls. A flight-only clamp in
`InitialOrdersGamePhase::process`, gated on `ewCapacity > 0`, costs every other unit one property
read and is the only thing standing between a tampered client and unlimited fighter OEW.

**Exit criterion:** a Mapmaker flight allocates 3 points across OEW and DEW and no more; an
ordinary fighter flight's mine-detection allowance is byte-identical before and after; the hit
chance for a `useFlightEW` shot agrees server↔client across a differential corpus spanning
OEW 0–3 × DEW 0–6; and the enemy sees none of it during Initial Orders.

### 3.11a Stage 12 as BUILT (2026-09-10)

Built as specified above, with **three deliberate departures** — each one a case where following
the section as written would have changed behaviour it never meant to touch.

**1. The server twin is NOT a branch in `EW::getScannerOutput`.** The section put it there; that
function has three other callers and every one of them would have inherited a meaning nobody asked
for. `EW::getUnspentEw` would let the EW Detector's saved point be drawn out of a flight's OEW pool
*and* count its `"Detect Mines"` rows — bought with Offensive Bonus, not with EW — as spending it;
the Chameleon plausibility ceiling (`getDisguisedDEWFor`) is a question about hulls; and
`JumpEngine::rollExitDeviation` reads it as a **sensor rating**, so a Mapmaker's jump-point exit
scatter would have silently changed the day Stage 13 gives it a Jump Engine. Instead:
`EW::getFlightEwCapacity` / `EW::getFlightEwLeft`, one caller each, gate still one property read.

**2. `getEWLeft` needed the split too, in BOTH other directions.** The section protects the
mine-detection allowance from being spent on OEW; the two reverse leaks were not mentioned and are
just as real.

- `ew.getEWLeft` counts "everything that is not DEW" as used, so a Mapmaker holding 3 OEW would have
  watched its mine-detection allowance fall from 4 to 1. One `continue` on the flight-EW types,
  behind the same `ewCapacity` gate.
- ⭐ **And `ew.getEwLeftFor` had to test `ship.flight`, not `isFlightEwPool`** — the Stage 12 client
  harness caught this one. Falling through to `getEWLeft()` for an ordinary flight answers with its
  MINE-DETECTION allowance, so `ew.AssignOEW(kotha, target, 'OEW')` cheerfully wrote 2 points of OEW
  paid for out of the Offensive Bonus. No button offers that (`sourceCanAllocateOEW` refuses a
  flight with no pool) and `EW::clampFlightEw` drops it server-side regardless — but the rule is
  *OEW and DEW always come out of the flight pool, and an ordinary flight's pool is 0*, and it has
  to hold where the arithmetic is rather than only at the two places that happen to guard it today.

**3. ⚠️⚠️ THE EW DETECTOR HAD AN IMPLICIT FLIGHT GUARD, AND STAGE 12 REMOVED IT — then the user
ruled that it SHOULD be gone.** Stage 10A/10B's saved-EW rule was never gated on `!flight`: it did
not need to be, because `getSavedEwAllowance` is clamped by `getUnspentEw`, which wants a
**committed DEW row**, and no flight had ever had one (`convertUnusedToDEW` returned early on every
flight). A Mapmaker now has one, so the guard was gone by accident; it was reinstated explicitly,
flagged, and then **removed again on the user's ruling the same day: *"the effect applies to all
Walker units"* (2026-09-10).**

⭐ **AND NOTHING TESTS FOR A FLIGHT ANYWHERE ON THAT PATH, which is the point.** The ladder
(`getDetectorAllowance`) is a question about POSITION and answers for any unit in range;
`getSavedEwAllowance` then clamps it by what the unit actually has left to hold back. An ordinary
flight has no EW pool, so that clamp answers 0 for it — the Mapmaker is the only flight in the game
this reaches, and it is reached by arithmetic rather than by a special case.

The one thing that DID need writing is the pool the late window measures against. Stage 10B's
`getSavedEwPool` / `getLateEwSpent` both derived from `getEWLeft`, which on a flight answers about
the **mine-detection allowance** — so a Mapmaker holding 4 points of mine detection would have read
as having already spent its saved point. One helper, `ew.getUnspentEwLive` (JS) and the matching
`FighterFlight` branch in `EW::getUnspentEw` (PHP), is what both now go through. The flight window
shares the ship's `Saved EW` row rather than copying it (`getSavedEwRow`).

**And one trap the section could not have predicted.** `useFlightEW` is a public property on
`Weapon`, and `MissileRack::stripForJson` **reflects every public property on `Weapon` onto every
missile in every missile ship's payload** (`Ammo extends Weapon`). The replay corpus caught it
immediately — game 4151, 36 differing paths, all `missileArray/*/useFlightEW: added (false)`. Fixed
with a named `$unusedOnAmmo` exemption list on `MissileRack`, which is now the documented home for
the next base-class flag that means nothing on a round. This is the same function
`arch_public_static_on_weapon` warns about; the static case crashes, the instance case merely bloats.

**Also built, and not in the section:** `weaponManager.computeBaseDefenceBreakdown` reads a
**Mapmaker's own DEW** when a ship shoots at it (`if (!target.flight) dew = ...` answered 0 for every
flight). The server has always read it — `$target->getDEW($turn)` works on a `FighterFlight` — so the
two agreed only because no flight had ever had a DEW row. That is D12's other half, *"Mapmaker DEW
works exactly the same as ship DEW"*, and without it a ship's preview disagrees with its own
resolution. `computeBaseDefenceBreakdown` also now returns `defensiveEwBeforeWaiver`, because the
fighter waiver zeroes DEW/BDEW/SDEW **before** `computeOEW` runs and the contested lock needs the
pre-waiver total.

**Harnesses.** `tests/replay/walkersStage12Harness.php` (26 assertions: the gate, the two pools, the
server clamp incl. a tampered ordinary flight, what `getDEW`/`getOEW` then read, the detector guard)
and `tests/replay/walkersStage12ClientHarness.js` (59 assertions, real `ew.js` + `weaponManager.js`
in a VM: the same rules plus the **whole OEW 0–3 × DEW 0–6 lock-on grid** for both weapon kinds).
Replay corpus back to its four known clean-tree failures (3676, 4249, 4297, 4325).

**What Stage 14 now inherits.** `public $useFlightEW = false;` on `Weapon` and the two-row rule in
`Weapon::calculateHitBase`; `MedLightningArrayFtr` sets the flag and nothing else.

### 3.12 The Mapmaker Jump Engine

*"They also have their own Jump Engine system that works normally and has a recharge time of 10
turns. For simplicity we should only let an entire flight of Mapmakers open 1 jump point, not one
per fighter."*

The line is already stubbed in the hull —
`//$fighter->addAftSystem(new JumpEngine(0, 1, 0, 10));`
([MapmakerProbes.php:56](source/server/model/ships/walkers/MapmakerProbes.php#L56)) — and the
constructor's 4th argument is `$delay`, the B5W jump delay, which the class overwrites
`loadingtime`/`turnsloaded` from. ⭐ **A recharge rule must read `$delay`, never `$loadingtime`**;
that is a JUMP_GATES finding and it applies verbatim here.

⭐⭐ **"ONLY ONE JUMP POINT PER FLIGHT" IS ALREADY THE LAW, AND THERE IS NOTHING TO BUILD FOR IT.**
`Firing::getVortexDeclarationBlock` refuses any second declaration from the same **shooter** in the
same turn — *"covers a second order on this engine and a second engine on the same hull alike"*
([firing.php:238](source/server/handlers/firing.php#L238)) — and a fighter's fire order names the
**flight** as its shooter. Six engines on six craft in one flight are six engines on one hull as far
as that loop is concerned: the first declaration survives, every later one is dropped. The rule the
user asked for is a property of where the check already sits.

**What does have to be built:**

**1. The charge is per SYSTEM and the rule is per FLIGHT.** After a jump, only the declaring
fighter's engine is spent; its five siblings are still fully charged and could declare again next
turn. Route every read and write through one accessor, `FighterFlight::getFlightJumpEngine()`,
answering the **sample fighter's** engine — `getSampleFighter()` returns `$this->systems[1]`
([FighterFlight.php:262](source/server/model/ships/FighterFlight.php#L262)) whether or not that
craft is alive, which is exactly the stable identity this needs and is the same anchor
`EdfExposure` and `Movement::applyJumpOut` already use for flight-wide records.

⚠️ **Do not store the charge on a craft that can die.** If a per-flight charge ever needs to
outlive the sample fighter's system data, it belongs in an `IndividualNote` on the sample fighter,
not in a new column — the same "derive it on every load" discipline that gives the Energy Draining
Mine its lifetime.

**2. The client has never declared a vortex from a flight.** `weaponManager` builds the weapon list
from the selected unit's systems; a flight's systems are `Fighter` objects and their weapons are one
level below that. The on-map facing arrow, the `hextarget` click path and the suppressed firing-mode
selector (`hideFiringModeSelector`) have all only ever run for a capital hull. **This is the whole
stage.**

**3. Three server questions the declaration asks that must be answerable for a flight.**
`getVortexDeclarationBlock` calls `$weapon->isDestroyed($turn)`, `$weapon->isOfflineOnTurn($turn)`
and `$shooter->getHexPos()`. The middle one is the risk: a fighter subsystem has no power rows at
all, so confirm it answers `false` rather than throwing or reading a sibling's entry (trap 6 —
client system fields are shared by reference across same-phpclass instances, and the server has its
own version of that hazard in `Fighter` construction).

**4. Leaving through the vortex is already done.** `Movement::applyJumpOut` grew its FighterFlight
branch in JUMP_POINTS Stage 6 — one `HyperspaceJump` damage entry per craft, the CV note on the
sample fighter, the `RammingAttack` log order shared
([movement.php:461](source/server/handlers/movement.php#L461)). A Mapmaker flight flying into its
own jump point needs no new code.

**Exit criterion:** a Mapmaker flight opens exactly one jump point however many craft declare; a
second declaration in the same turn is refused with the existing reason string; the engine reads 10
turns of recharge from `$delay` on every craft; and the flight can fly out through its own vortex
and is recorded as jumped, not killed.

---

### 3.12a As built — Stage 13, 2026-09-10

Harnesses: `tests/replay/walkersStage13Harness.php` (59) and
`tests/replay/walkersStage13ClientHarness.js` (35). Both **fatal on the pre-edit tree**.
`checkShipData.php` PASS, 0 new findings (237 baselined). Replay harness 115/8, byte-identical to a
`git stash push -- source/` run with timings normalised — the 8 are pre-existing on a clean tree
(the four documented ones plus 3671/4256/4303/4328, which moved with the *ReducedRange crit for
ballistics* and *Homing Missile* commits and have never been re-recorded).

⭐⭐ **THE SECTION'S TWO HEADLINE CLAIMS WERE BOTH HALF-RIGHT, AND IT MATTERS WHICH HALF.**

**"Only one jump point per flight is already the law and there is nothing to build for it."** The
mechanism named is right — `Firing::getVortexDeclarationBlock`'s loop refuses a second declaration
from the same **shooter**, and a fighter's `shooterid` really is the flight's id — but that loop
tests `$shooter->getSystemById($other->weaponid) instanceof JumpEngine`, and it never gets the
chance to speak unless the orders are *comparable*. The thing that makes D13 true is the
**normalisation**: `validateVortexDeclaration` re-points `$fire->weaponid` at
`FighterFlight::getFlightJumpEngine()` before the rule list runs. Two lines, and without them each
craft's engine is judged on its own state — a fully charged engine holding no vortex — so a flight
that jumped last turn could open a second doorway simply by clicking a different craft.

**"The client has never declared a vortex from a flight. This is the whole stage."** It was the
smaller half. Five SERVER sweeps walked `$ship->systems` looking for a `JumpEngine` and found
nothing on a flight, because a flight's systems are *craft*: `spawnDeclaredVortices`,
`closeExpiredVortices`, `hasVortexDeclaration`, the vortex-holder lookup and both reinforcement exit
sweeps. The declaration would have validated, persisted, and then vanished — a commit that looks
completely clean and does nothing. They all go through **`JumpEngine::getUnitJumpEngines($unit)`**
now, which is the one place that answers "which engines speak for this unit" and which returns
exactly ONE for a flight.

⚠️ **THREE FINDINGS WORTH CARRYING.**

1. **A FIGHTER SUBSYSTEM'S `this.ship` IS THE CRAFT, NOT THE FLIGHT — on the CLIENT.**
   `SystemFactory` builds a hull's systems with `new window[name](args, ship)` but a fighter's with
   `new window[name](args, fighter)`, so every `this.ship` in the client `JumpEngine` was reading an
   object with a per-flight autoid for an id, no position, no team and no vortex. Every vortex
   question — `getVortexHeldBy`, `isMyShip`, the range test — silently answered "no". The join back
   is `flightid`, which `Fighter::stripForJson` has always published, and it is now one accessor,
   `JumpEngine.prototype.getOwningUnit()`. **Any future per-unit behaviour on a fighter-mounted
   system has this bug waiting for it.**

2. **A NEW SHIP FLAG MUST BE A DECLARED PROPERTY, NOT A CONSTRUCTOR ASSIGNMENT.** `$this->noHangarRequired = true`
   in the constructor is a *dynamic property*, deprecated since PHP 8.2 — one notice per Mapmaker
   built, and the ship-data validator builds 2,580 of them. `public $noHangarRequired = true;` at
   class level is the shape `SpawnEnergyDrainingMine::$unTargetable` already uses, and it rides the
   static blueprint the same way. The exemption itself is **fleet-building only**: `HangarOps` is
   deliberately not taught about it, because a Mapmaker still fills boxes the moment Stage 16's
   Traveler bay carries one.

3. **`BlueprintCache`'s SPAWNABLE-CLASS SCAN DOES NOT DESCEND INTO CRAFT EITHER.** It reads
   `$system->spawnableClasses` off a hull's systems, which was free until a fighter-mounted weapon
   could put a unit on the board. The Mapmaker's Jump Engine can, and without the descent the FIRST
   jump point a Mapmaker flight ever opens has no blueprint to resolve against and draws as an empty
   hex until the page is reloaded — exactly the failure `$spawnableClasses` exists to prevent.

⭐ **WHAT MAKES "IF ONE HAS A fireOrder THEY ALL DO" TRUE ON SCREEN** is three small diverts, not a
new concept: `JumpEngine::stripForJson` publishes the FLIGHT engine's charge and vortex counter on
every craft (so six icons never disagree about one fact), and `weaponManager.hasFiringOrder` /
`removeFiringOrder` ask the flight engine when the ship is a flight and the system is a `jumpEngine`
(so all six read the one order, and the player can withdraw it from whichever icon they clicked).
`targetHex` resolves to the flight engine before queuing and takes one declaration per pass.

⭐⭐ **USER RULING 2026-09-10, AFTER PLAY TESTING — A FLIGHT HAS NO MAINTAIN AT ALL, AND ITS JUMP
POINT IS OPEN FOR EXACTLY ONE TURN.** *"As fighters, Mapmakers cannot hold a jump point open for
more than 1 turn, so we can default to showing 1/1 in their Jump engine output display on the turn
the Jump Point is open, and don't have to check for maintaining power etc."*

⚠️ **AND THE REASON IT NEEDED A RULE OF ITS OWN IS A VACUOUS PASS.** `getVortexPowerViolations`
looks for systems drawing power, and a flight's are `Fighter` objects with `powerReq` 0 — so the
all-systems-offline test that Maintain is built on **passes on a flight for free**. As first built,
that read as "this unit satisfies the rule" when the truth is that the rule does not apply to it,
and a Mapmaker could have held a doorway open for the full four turns at no cost. A test that
cannot fail is not a test that passed.

The rule is one predicate, `JumpEngine::isFlightMounted()` (mirrored on the client as
`JumpEngine.prototype.isFlightMounted`, which asks the direct fact — `this.ship.fighter` — rather
than resolving the flight, so it cannot be wrong when the lookup finds nothing), and four short
consequences:

- `getMaintainDeclaration()` refuses a flight outright, the same shape as its gate refusal. That
  alone closes the vortex at the end of its first open turn.
- `getVortexClosureReason()` gains a branch **for the log**, which is a persisted note and the only
  explanation the player gets: *"a fighter flight cannot hold a jump point open"* rather than
  *"not maintained"*, which reads like a mistake the player made.
- `Firing::getVortexDeclarationBlock`'s Maintain branch refuses a flight at the wire — because
  every test below it *passes* on one (the vortex is open, it formed last turn, the hex matches),
  so a forged mode-7 order would otherwise be accepted and persisted while meaning nothing.
- `stripForJson` sends `vortexMaxTurns = 1`, so the icon reads **1/1** rather than 1/4. The client's
  fallback denominator is flight-aware too, so every path agrees.

⭐ **And the client simply does not offer the control** (`canMaintainVortex` returns false for a
flight), which is what makes "don't have to check for maintaining power" literally true: the power
questions are never reached, rather than being answered specially. An earlier draft of this stage
special-cased them instead — a `getFlightEngine()` accessor plus `ship.flight ? [] : …` guards in
`doActivate`/`doDeactivate` — and all of it came back out under this ruling.

⚠️ **THE FAILURE ROLL IS ALWAYS ZERO.** `openVortex` prices failure off `maxhealth - getRemainingHealth()`
on the engine, and a fighter's SUBSYSTEMS are never damaged (a craft is destroyed as a whole), so a
Mapmaker's drive can never fail. Same reason `getFlightJumpEngine` can safely answer the sample
fighter's engine even when that craft is dead: the engine reads `isDestroyed()` false, which is what
lets a flight with craft 1 gone still jump.

⚠️ **THE POSITIONAL SYSTEM-ID TRAP FIRED, unavoidably.** Adding a system to each craft shifts every
construction-order id after it — `Fighteradvsensors` 4→5, craft 2 5→6, and so on down the flight —
so any game in progress with Mapmakers in it has stale per-system ids in `tac_critical` /
`tac_systemdata`. Nothing can avoid that (a craft gaining a system shifts the next craft's id
whatever the placement), and the only Mapmakers in existence are Stage 12's play tests.

### 3.13 Medium Lightning Array, fighter mount — `MedLightningArrayFtr`

**THE CONTROL SHEET (D4, supplied 2026-09-08).** Read from the sheet, nothing inferred:

| | 3-Probe group | 6-Probe group |
|---|---|---|
| Guns | 1 per 3 craft | 1 per 6 craft |
| Damage | **4d10 + 12** (16–52) | **8d10 + 12** (20–92) |
| Range penalty | −1 per **3** hexes (`rangePenalty = 1/3`) | −1 per **4** hexes (`rangePenalty = 0.25`) |
| Fire control | **+2 / +4 / +6** | **+5 / +5 / +4** |
| Class | Electromagnetic | Electromagnetic |
| Mode | **Flash** | **Flash** |
| Rate of fire | **1 per 4 turns** | 1 per 4 turns |

**Special:** *"Uses EW for lock-on. Effected by DEW. Does not use Flight-Level combat or Offensive
Bonus. Cannot fire MLA and LCP in same turn. May fire in combined mode on first turn."*

Six things the sheet settles or changes:

- ⭐ **RATE OF FIRE IS 1 PER 4 TURNS**, so `loadingtime = 4`. The prose that came with this stage
  said *"a recharge rate of 2 turns"*; the sheet won and the user confirmed it on 2026-09-08
  (*"Ah yes, loading time is 4, my mistake"*). Recorded because the two sources disagreed in
  writing and the next reader will find both.
- ⭐ **`fireControl` is `array(fighters, mediums, capitals)`** in FV, so those read
  `array(2, 4, 6)` and `array(5, 5, 4)`. Note the 6-group is BETTER against fighters and WORSE
  against capitals than the 3-group — the modes are a real choice, not a strict upgrade, and any
  "bigger is better" shortcut in the client's mode hinting would be wrong. Confirmed 2026-09-08.
- ⚠️ **The flat +12 does NOT double.** 8d10+12 is not two lots of 4d10+12. Same shape as Stage 8's
  finding that *"50% collateral must be computed from the damage, never by doubling the 25%
  figure"* — write both profiles out, never derive one from the other.
- ⭐⭐ **Mode: FLASH, AND IT SCORES NO COLLATERAL INSIDE *ANY* ENERGY DRAINING FIELD.** Not just an
  enemy's — *"Flash damage always loses its collateral damage (friend or foe) in Energy Draining
  Fields, unless the Lightning Array is boosted by the Wide Beam enhancement"* (user, 2026-09-08).

  ⭐ **That is already exactly what Stage 4 built, and it costs this weapon no code at all.** The
  test is `TacGamedata::isHexInEdfField()`, which is **deliberately team-blind** — the own-fleet
  exemption belongs to `getEdfPenaltyHexes()` and the drain, not to this; a field dampening an
  explosion is a property of the hex (§3.5, Stage 4b). The Wide Beam carve-out is the
  `edfSuppressesCollateral()` hook Stage 8 added, which `LightningArray` overrides to return false
  for a wide-beam shot and which **defaults to true for everything else**. Wide Beam is a
  ship-system refit and is not offered to Mapmakers, so `MedLightningArrayFtr` simply inherits the
  default and is silenced in every field on the board.

  ⚠️ Which is one more reason **not** to extend `LightningArray` (see the subclass caution below):
  inheriting its override would hand a fighter weapon a wide-beam exemption it can never legally
  arm.

  `HyperplasmaMatrix` is also a flight-combined **Flash** weapon and already carries the
  self-immunity handling for a flight caught in its own splash at range 0
  ([plasma.php:2380](source/server/model/weapons/plasma.php#L2380)). Read that before writing
  `fire()`.
- ⭐ **"May fire in combined mode on first turn"** is the DEFAULT, not an override.
  `Weapon::getStartLoading()` returns a full charge
  ([weapon.php:1011](source/server/model/weapons/weapon.php#L1011)); §1.2's *"does not begin the
  game fully charged"* override is the thing NOT to add here.
- ⭐ **"Does not use Flight-Level combat or Offensive Bonus" is ONE exclusion, not two.**
  *"Flight-level combat does not exist in FV, so we can take that text to just mean that we use
  Flight EW for this weapon, not Offensive Bonus"* (user, 2026-09-08). So the change inside the
  `instanceof FighterFlight` block is the `$oew` assignment and nothing else — the combat-pivot
  penalty and the rest of that branch stay exactly as they are. ⚠️ The earlier reading of this line,
  which had the whole block skipped, was wrong; do not go looking for more to disable.

**The shape: `NeutronBlaster`'s mode structure over `HyperplasmaMatrix`'s collection loop.**

- **`NeutronBlaster`** ([customDevelopment.php:1189](source/server/model/weapons/customDevelopment.php#L1189))
  is the tree's reference implementation of *"a firing mode names a group size, the stats differ per
  size, and a mis-declared group auto-misses"*. It carries `blastersRequiredArray`, per-mode
  `damageType` / `fireControl` / `rangePenalty` / `raking` arrays, an `isCombined` flag that turns a
  subordinate order into a technical no-shot, and `alreadyConsidered` so one pass cannot claim a
  partner twice ([1337](source/server/model/weapons/customDevelopment.php#L1337)). It combines
  across **one ship's mounts**.
- **`HyperplasmaMatrix`** ([plasma.php:2305](source/server/model/weapons/plasma.php#L2305)) is the
  tree's only weapon that combines across a **flight**: `beforeFiringOrderResolution` walks
  `$flight->systems` (the craft) and each craft's own systems (their weapons), elects the lowest-id
  weapon holding an order as primary, and fully nullifies the rest —
  `shots = 0, shotshit = 0, needed = 0, rolled = 100` — so they draw no log line, no animation and
  no missed-shot display, with `calculateHitBase` and `fire` both short-circuiting on that exact
  quadruple.

Take NeutronBlaster's per-mode stat arrays and its "not enough partners, mark technical" branch, and
reach for partners the way HyperplasmaMatrix does.

**Group arithmetic (D15).** Modes `1 => "3-Probes"`, `2 => "6-Probes"` (shortened from "…Array" by the user after the first play test - the ids are what the code depends on). Within one flight,
collect this turn's `normal` orders on `MedLightningArrayFtr` at the **same target and the same
mode**; form `floor(n / needed)` complete groups; every leftover order becomes technical. A flight
of six may fire two 3-groups (at the same or at different targets) or one 6-group; four or five
declaring in mode 1 fire one 3-group and waste the rest, which is the ruling exactly.

- ⚠️ **"Damaged fighters cannot contribute" is not `isDestroyed()`.** HyperplasmaMatrix's
  `getAliveFighterCount` counts everything not destroyed
  ([plasma.php:2277](source/server/model/weapons/plasma.php#L2277)); this weapon needs
  `getRemainingHealth() >= maxhealth` on the craft. Copying the precedent's test is the obvious
  mistake, and it is silent — a battered flight would simply keep firing at full strength.
- ⚠️ **`$this->guns` padding and `Firing::automateIntercept`** (trap 11). One order carrying a 3- or
  6-craft discharge must not present as three or six shots to the interception engine. Copy the
  Slicer's comment and its skip for manual `intercept` orders; that exact bug produced 44 defensive
  shots against 4 missiles in game 4306.

**EW lock-on (D12).** `public $useFlightEW = true;` — the flag §3.11 introduces, and this weapon is
its only consumer. ⭐ **Stage 14 therefore depends on Stage 12** and cannot ship before it.

**Exclusivity (D16).** If any craft fires the array, no craft may fire its `LightChromaticPulsar`
that turn, and the reverse. The check is **flight-wide**, not per craft.

⚠️⚠️ **It needs a server half of its own.** Stage 8's finding stands: *nothing on the server refuses
an offensive order from an unloaded weapon at all*, and by the same token nothing refuses a
mis-paired one. The client refusal (in `weaponManager.targetShip`, with a reason, before the click
lands) is the usable half; nullifying the later-declared kind in `beforeFiringOrderResolution` is
the honest one.

**Two mechanical traps in the hull itself:**

- ⚠️ **Positional system ids** (trap 7). `MapmakerProbes::populate()` constructs
  `LightChromaticPulsar`, then `RammingAttack`, then `Fighteradvsensors`, with the commented
  placeholders between them. Every id after the insertion point shifts, on every Mapmaker in every
  live game. Land Stages 12, 13 and 14 in **one deploy** so the reshuffle happens once, or append
  the new systems at the end and accept a constructor order that no longer reads top to bottom.
- ⚠️⚠️ **DO NOT EXTEND `LightningArray`.** `MediumLightningArray extends LightningArray`
  ([specialWeapons.php:12500](source/server/model/weapons/specialWeapons.php#L12500)), which already
  cost Stage 8 an explicit subclass exclusion in the Wide-Beam refit registry — and inheriting that
  branch would give `MedLightningArrayFtr` two things it must not have: an entry in a refit registry
  a fighter cannot buy from, and `LightningArray`'s `edfSuppressesCollateral()` override, which is
  the wide-beam exemption from the field rule above (D26). Extend `Weapon` directly. If that is ever
  revisited, **every `instanceof LightningArray` in the tree** has to be re-read first.

**Exit criterion:** 3 and 6 combine and 1, 2, 4 and 5 do not; a damaged craft is excluded and its
order goes technical; the flight cannot mix the two weapons in one turn on either side of the wire;
the Array locks on with flight EW while the Pulsar keeps its offensive bonus **plus** any OEW
(D25); both damage profiles are written out independently; `loadingtime = 4` survives a reload and
the weapon can fire combined on turn 1; a shot into ANY Energy Draining Field scores no collateral,
own team's included (D26); and the combined shot presents as ONE discharge to the interception
engine.

### 3.13a As built — Stage 14, 2026-09-10

**215 checks green** across four throwaway harnesses — 99 server, 62 client, 43 fleet-check and an 11-check live-game probe — and
all three **fail on a stashed tree** (the two node ones exit 1, the PHP one fatals: the class does
not exist there). `checkShipData.php` **PASS, 0 new findings** against the same 237 baseline; replay
harness **114 passed / 8 failed, byte-identical with timings normalised** to the same run with
`source/` stashed — zero drift on all five checks, `masking` and `snapshot` included. (8 is the
clean-tree count on this corpus today, exactly as Stage 13 recorded it.)

`MedLightningArrayFtr` is at the end of `specialWeapons.php`, beside `LightChromaticPulsar`; the
mount is uncommented in `MapmakerProbes::populate()` at the placeholder position, so Stages 12, 13
and 14 land in **one deploy** and the positional-id reshuffle happens once. ⚠️ Confirmed against
`C:\FV_env\DouglasChanges`: the deployed tree still has *both* the array and the Jump Engine
commented out, so nothing live has moved yet. Anything landing after that deploy must be **appended**.

⭐ **THE STATS NEEDED NO CLIENT MIRROR AT ALL**, which is the single biggest departure from what
§3.13 implies. Everything the two modes change — fire control, range penalty, damage span — already
travels as the engine's own per-mode arrays (`$fireControlArray` / `$rangePenaltyArray` and the
min/max damage arrays that `Weapon::setSystemDataWindow` fills), and `Weapon.prototype.changeFiringMode`
in `shipSystem.js` reads all of them. So the client half is **not** a second copy of the control
sheet the way `LightningArray`'s six hand-mirrored tables are: it is the group rule and nothing else,
and the ONE number it duplicates (`craftRequired`) is re-derived server-side anyway, so a drift costs
a wrong warning and never a wrong shot. A test reads the PHP `const` out of the file and compares.

⭐⭐ **AND THE REAL WORK WAS THE EXCLUSIVITY, NOT THE COMBINING.** The combining is
`HyperplasmaMatrix`'s pattern almost verbatim. D16 is the part with no precedent: **it is
flight-wide, and every exclusivity mechanism the codebase had is per-CRAFT**.
`weaponManager.checkConflictingFireOrder` narrows to `getFighterBySystem(ship, weapon.id)` before it
looks — correct for the `$exclusive` flag it enforces, and useless here, because it would happily let
probe #2 fire the pulsar while probe #1 fired the array. So D16 needed a new predicate on both
sheets, keyed on a new `flightExclusiveGroup` string declared on the two Mapmaker classes only.

⚠️ **FOUR FINDINGS WORTH CARRYING.**

1. **`Firing::fireWeapons` DOES NOT CALL `changeFiringMode`** — only `prepareFiring` does, once per
   order, in an earlier loop. So by the time the dice are rolled `$this->firingMode` is whatever the
   LAST hit-chance pass left behind, and a per-mode `getDamage()` that reads it is one order away
   from rolling the wrong profile. One order per mount makes it harmless here today;
   `getDamage($fireOrder)` reads `$fireOrder->firingMode` so it stays harmless. **`NeutronBlaster`
   has the same latent bug** and it will bite the first time one mount carries two orders in
   different modes.

2. **AN UNTOUCHED FIRE ORDER LOOKS EXACTLY LIKE A RESOLVED PRIMARY.** `FireOrder`'s constructor
   defaults `$shots = 1`, which is also what a formed group's primary carries — so the harness's
   first "last turn's orders are left alone" assertion passed vacuously in both directions. It has
   to be a **before/after snapshot**, not a classification. The same shape of mistake would make any
   "we did not touch it" test on a fire order meaningless.

3. **THE 50% RULE HAD TO BE SEEDED, NOT DERIVED** (D31). Marking the no-maximum categories from the
   craft actually bought is right for the maximum and useless for the minimum: the case the rule
   exists to forbid — a Traveler with no probes at all — is exactly the case where that set is
   empty. `noHangarMaxCraftTypes` is therefore declared as `['Mapmaker Probes']` and *grown* by the
   `$noHangarRequired` flag, so both halves are one array and both cases are right.

⚠️ **AND ONE MIRROR-PAIR TRAP THE FIRST DRAFT WALKED INTO.** `weaponManager.hasFiringOrder()` is the
obvious predicate for "has this weapon already declared", and it is the wrong one for D16: it answers
true for a manual `intercept` order and for a `selfIntercept` marker as well. The server's own test
filters to `type == 'normal'`, so using it would have refused, client-side, a declaration the server
then happily allowed — a probe that has committed its pulsar to INTERCEPTION has not fired it, and
D16 is a rule about firing. `getFlightExclusivityBlock` counts offensive orders itself, both sheets
are tested on the same three order types, and the client harness stubs `hasFiringOrder` to **throw**
so a re-introduced call fails loudly.

**The fleet-check half, in three edits** (`gamelobby.js`, live copy only — the second copy is
`checkChoices_LEGACY` inside a block comment, and a test asserts it did not grow either change):

- `MapmakerProbes::$hangarRequired = "Mapmaker Probes"`. ⚠️ **This was the actual bug.** The four
  Walker hulls declare `$fighters = array("Mapmaker Probes" => N)`, but the flight left
  `hangarRequired` at the `'fighters'` default and so classified itself off its jinking limit as an
  ordinary **medium** fighter (8 is medium, not heavy — the bands are ≥10 light, ≥8 medium, ≥6
  heavy). The capacity and the craft were in two different buckets that could never meet.
- the tally gate drops `&& !noHangarRequired`, so the probes are counted; the flag now records the
  category in `noHangarMaxCraftTypes` instead.
- the small-craft report row grows `scNoMaximum` / `scMinRequired`. **`'Fighter Squadrons'` is kept
  byte-identical, unrounded halving included** — those capacities are fractional (0.5 on several
  Star Wars hulls) and `Math.ceil` would move existing verdicts. The Mapmaker minimum IS
  `Math.ceil(cap / 2)`, matching `minFtrRequired`, because it is the fighter rule applied to a
  custom category. Every other custom category (Stilettos, Vipers, ...) is untouched.

**Not built, deliberately:** `HyperplasmaMatrix`'s self-immunity at range 0. That is a rule of that
weapon; the control sheet grants this one nothing of the kind, so a Mapmaker flight sharing a hex
with its target takes the ordinary 25% collateral — or none, if the hex is in a draining field.

**Still open for the user:** the flight's `pointCost` is unchanged at `210*6`. It has gained a real
weapon and the number is a balance call, not a mechanic.


#### 3.13b Play-test fixes — game 4347, 2026-09-10

Two bugs, both found on the first shot fired in anger, and **neither one reachable by any unit test
of the weapon**: one is a payload shape the harness's own fixtures papered over, the other only
exists once a real target with real EW is on the board. Both are now covered — 62 client checks and
an 11-check probe that loads game 4347 through the production path and runs the real
`Weapon::calculateHitBase`.

⚠️⚠️ **1. THE ARRAY COULD NOT BE TARGETED AT ALL.** `MedLightningArrayFtr.isCraftEligible` asked
`shipManager.systems.getRemainingHealth(craft)`, which reads `system.damage.length` — and
`ShipCompactor` strips an EMPTY damage array out of the payload entirely (`$emptyArrayKeys`,
trap 8). So `craft.damage` is `undefined` on every craft that has not been hit, which is every craft
in a fresh game, and the click threw before it ever reached the arc test.

- **The safe read is `damageManager.getDamage(ship, system)`**, a `for..in` rather than an indexed
  loop. It is also what `FighterIcon.js` uses to draw a craft's health bar, so the weapon and the
  UI now read one number.
- ⭐ **THE HARNESS FIXTURE IS WHAT HID IT.** Every fixture craft carried `damage: []`, which is
  exactly the shape the payload never has. A fixture must be built from what the WIRE sends, not
  from what the constructor declares — the regression test now `delete`s the key.
- ⚠️ This is trap 8 in its second form: the first is "is the key stripped?", the second is "does the
  reader survive it being absent?". `getRemainingHealth` does not, and it is used all over the
  client — safely, because everywhere else it is handed a SYSTEM, and a system's damage array is
  rebuilt by `SystemFactory`. A CRAFT is not.

⭐⭐ **2. A `useFlightEW` SHOT WAS TAKING A NO-LOCK PENALTY, AND D12 FORBIDS IT.** The user's report:
*"Offensive Bonus is correctly not being used ... but it is not getting the benefit of any OEW used
by the Mapmaker flight, and as a result is attracting a No Lock penalty as well."*

- **The OEW lookup was NOT the bug.** The probe proves `FighterFlight::getOEW` returns exactly the
  row the player allocated. What cancels it is the target's defensive EW, which is D12/Q10 working:
  in game 4347 the flight had 1 OEW against a Thentus carrying **7 DEW**, so `max(0, 1 − 7) = 0`.
- ⚠️ **AND THAT IS THE ORDINARY CASE, NOT AN EDGE ONE.** A Mapmaker flight's whole pool is 3 points
  and enemy capitals in that game carried **7 to 16 DEW**. A `useFlightEW` shot's lock is therefore
  0 against any real warship, every time. Stage 12 could not see this because no weapon carried the
  flag yet.
- **The penalty is the part that is wrong.** D12 says enemy EW may cancel the lock back to the
  ordinary fighter-versus-profile chance *"and no further"*, and a no-lock penalty is further: it
  made the array strictly WORSE than an ordinary fighter weapon against the same target. Measured
  at −5% at range 3 and it scales with range, because the penalty is a multiplier on the range
  penalty rather than a flat modifier.
- ⭐ **THE EXEMPTION USED TO EXIST AND WAS LOST.** The commented-out block immediately above the
  no-lock calculation in `weapon.php` reads
  `if (($oew < 1) && (!($shooter instanceof FighterFlight)))` — flights were exempt outright before
  partial locks came in. Nothing could reach `$oew < 1` on a flight afterwards, because every
  fighter weapon folds the offensive bonus into `$oew`, so the loss was invisible for years.
- ⚠️ **GATED ON THE WEAPON, NOT ON `$shooter instanceof FighterFlight`.** An ordinary flight CAN
  still reach `$oew = 0` — a `tmpsensordown` crit, or mine detection eating the whole bonus — and
  has always taken the penalty when it does. Widening the exemption to every flight would move
  games in the replay corpus; gating on `$useFlightEW` is free by construction, and the corpus run
  confirms it (byte-identical).
- **Mirror pair**: `Weapon::calculateHitBase` and `weaponManager.computeJammerNoLock`.

⭐ **What the fix leaves standing, deliberately:** with the lock at 0 the array is a
*profile-only* shot. It is not worse than an ordinary fighter weapon — the probe measures both at
**75%** against the same Thentus at range 3, the array's fire control (+4 vs a medium) buying back
exactly what the missing offensive bonus cost. That is the trade the sheet describes, and it is now
the trade the engine makes.

⚠️ **Still open for the user:** whether a 3-point flight pool contested by 7–16 points of capital
DEW is the intended reading of *"Uses EW for lock-on. Effected by DEW."* The rule as built (D12) is
being followed exactly; what it means in play is that the array's OEW only ever helps against
lightly-EW'd targets — fighters, small craft, and hulls that spent their EW offensively.

**Also this session:** the firing-mode labels were shortened by the user to `"3-Probes"` /
`"6-Probes"`. Nothing reads the wording — the ids are what the code and the client depend on — and
the harness now asserts the SHAPE rather than the strings, so the next re-word does not fail a test.

#### 3.13c ⚠️⚠️ The Mapmaker EW rules CORRECTED from the rulebook text — 2026-09-11

The user supplied the rulebook's own text and worked examples, and **D12 had been built on the
wrong weapon**. The DEW contest belongs to the Pulsar. The Array uses plain ship rules. §3.13b
item 2 (the no-lock exemption) was a patch over that mistake, so **it is reverted**.

- **Medium Lightning Array:** *"The flight may not use its offensive bonus. To-hit rolls are
  calculated using the MLA's fire control and the flight's OEW (range penalties doubled for lack of
  a lock-on as usual)."* Worked example: *"16 (defensive rating) −4 (DEW) +2 (OEW) −6 (range) +5
  (fire control) = 13"*. So the flight's OEW is added, and the target's DEW is subtracted as it would
  be against a ship.
- **Light Chromatic Pulsar:** *"It gains the bonus of its OEW minus the target's DEW (minimum bonus
  of 0), but never doubles the range for lack of a lock-on"*, on top of the offensive bonus. For
  example, 3 OEW against 5 DEW gives *"+0 (not −2)"*.

**As built.** One gated branch per weapon kind, in the same mirror pair as before:

| | Server: `Weapon::calculateHitBase` fighter branch | Client (`weaponManager.js`) |
|---|---|---|
| Pulsar (any non-`useFlightEW` fighter weapon) | `$oew = OB + max(0, flightOEW − (DEW+BDEW+SDEW))`, then all three are zeroed | `computeOEW` uses `defensiveEwBeforeWaiver` |
| Array (`useFlightEW`) | `$oew = flightOEW`, and DEW/BDEW/SDEW are **not** zeroed | `computeBaseDefenceBreakdown` skips the waiver |
| No-lock exemption | removed | removed from `computeJammerNoLock` |

The Pulsar's term costs nothing anywhere else, because every other flight in the game has OEW 0,
and `max(0, 0 − x)` is 0. The Pulsar still can never take a no-lock penalty: the bonus keeps its
`$oew` at 1 or above, as it does for every fighter.

**Game 4347 through the real pipeline** (Thentus, 7 DEW, range 3, flight OEW 1):

| Shot | Hit chance now | Before this fix |
|---|---|---|
| Array, with the OEW point | **45%** (OEW +1, DEW −7) | 75% |
| Array, OEW withdrawn | **35%** (no-lock penalty 0.99 applied) | — |
| Pulsar | **70%** (OB 8 + max(0, 1 − 7) = 8) | — |

**⚠️ Two decisions kept, for the user to confirm:**
- The Pulsar's subtraction still stacks BDEW and SDEW with the target's DEW (Q10). The rulebook
  line says only "the target's DEW".
- The fighter-only terms around the lock are untouched: the combat-pivot −1 and the range-0 jinking
  rule.

**Evidence:**
- Both PHP files lint clean.
- `tests/replay/walkersStage12ClientHarness.js`: 90/0. The OEW 0–3 × DEW 0–6 grid now asserts the
  lock **and** the profile total for both weapons, plus the rulebook examples.
- `tests/tmp/stage14_client.js`: 62/0. The no-lock group is inverted. One assertion was re-pointed
  at the user's reworded shortfall warning in `special.js`.
- `tests/tmp/stage14_server.php`: 99/0.
- `tests/tmp/stage14_ew_probe.php`: 15/0. It is rewritten for the corrected rule and gained a
  "withdraw the OEW row" pass.
- Replay: 114 passed / 8 failed. That is the same count as the recorded clean-tree state, and the
  four newer failures are snapshot-only critical/range diffs.
- ⚠️ **No Mapmaker game is in the replay corpus.** 4345 and 4347 exist locally but were never
  recorded. The corpus therefore proves only that nothing else moved, and the game-4347 probe is
  the real coverage.

The "still open" item at the end of §3.13b is closed by this. A 3-point pool against 7–16 DEW is a
to-hit penalty now, not a cancelled lock.

### 3.14 The Traveler's Docking Bay — **BUILT 2026-09-11 (Stage 16), §3.14a deferred (D35) — as built in §3.14b**

*"Aft hangar on the Traveler only should be relabelled as Docking Bay ... it can hold 2 Pathfinders
(12 spaces each), 6 Scribes (4 spaces) and/or up to 24 Mapmakers (1 space). It can also dock a
Waymarker (24 spaces) but the docking and launching procedure takes 2 turns instead of the usual
one."* (Corrected 2026-09-08; the first statement of this listed the Waymarker at 12 boxes.)

⭐ **THE NUMBERS FIT THE HULL EXACTLY, AND ALL FOUR FILL IT.** `Hangar`'s `$maxhealth` **is** its box
count — `HangarOps::effectiveCapacity()` returns `getRemainingHealth()`
([HangarOps.php:4559](source/server/model/systems/HangarOps.php#L4559)) — while `$output` is the
shared launch-plus-land budget per turn, not capacity. The Traveler's aft bay is already
`new Hangar(6, 24, 12)` ([Traveler.php:53](source/server/model/ships/walkers/Traveler.php#L53)):
**24 boxes**, 12 movements a turn.

| Craft | Class | Boxes each | Max in the bay | `unitSize` |
|---|---|---|---|---|
| Mapmaker Sensor Probe | `FighterFlight` | 1 | 24 | `1` |
| Scribe | `MediumShip` | 4 | 6 | `1/4` |
| Pathfinder | `MediumShip` | 12 | 2 | `1/12` |
| Waymarker | `HeavyCombatVessel` | 24 | 1 (**two-turn procedure**, D23) | `1/24` |

24 × 1, 6 × 4, 2 × 12 and 1 × 24 are all exactly 24, so the capacity rule needs no per-type cap at
all — the box arithmetic *is* the rule, which is what Q11 was worrying about and is no longer a
question. ⚠️ Note that box cost is **per phpclass, not per size class**: Pathfinder and Scribe are
both `MediumShip` and cost 12 and 4. `boxesPerCraftForClass` is keyed by phpclass, so this is free —
but any shortcut that infers cost from `shipSizeClass` would be wrong on this hull specifically.

`$this->fighters = array("Mapmaker Probes" => 36)` on the hull is 24 aft plus the two 6-box side
hangars, and stays as it is.

**A subclass, not a `displayName` (D17).** `class DockingBay extends Hangar`, in `baseSystems.php`
**below** the `Hangar` class — the file carries its own note about that
([baseSystems.php:5112](source/server/model/systems/baseSystems.php#L5112)) — alongside `Catapult`,
`FighterRail`, `ShadowHangar` and `DockingCollar`. It needs a class allow-list, multi-class box
costs and a per-turn type lock; none of those is a label.

⚠️ **THE HIT CHART.** The Traveler's aft row names `"Hangar"` at roll 11
([Traveler.php:96](source/server/model/ships/walkers/Traveler.php#L96)). Renaming the system without
moving the chart entry with it makes the bay **unhittable and silently rerouted to Structure** —
the exact failure `checkShipData.php` exists to catch, and the one Stage 1 already hit on five
rows. Change both, in the same edit, and let the validator confirm it.

**Box costs come free from `unitSize` (D18).** `HangarOps::boxesPerCraftForClass`
([HangarOps.php:1765](source/server/model/systems/HangarOps.php#L1765)) already returns
`ceil(1 / unitSize)` for a `unitSize < 1` craft — the Vorlon Assault Fighter's 2 boxes come from
`unitSize = 0.5` ([vorlonAssaultFighterFlight.php:40](source/server/model/ships/vorlons/vorlonAssaultFighterFlight.php#L40)).
So Pathfinder `1/12`, Scribe `1/4`, Mapmaker `1` and Waymarker `1/24` yield 12, 4, 1 and 24 with
**no new arithmetic anywhere**.

⚠️ **`unitSize` is not free on a non-flight hull, though.** The lobby's fleet check divides by it
([gamelobby.js:1392](source/public/client/gamelobby.js#L1392)) when a ship declares
`hangarRequired`, and the server's shuttle accounting divides by it too
([HangarOps.php:357](source/server/model/systems/HangarOps.php#L357)). Setting it on an HCV and a
MediumShip that have never carried one is the part of this stage most likely to move a number
somewhere else. The 2,500-hull corpus differential Stage 5 and Stage 8 both used is the guard.

**Docking whole SHIPS is already a solved problem, once.** `DockingCollar` (the LCV rail) holds one
LCV: `performLCVDock` sets `$lcv->removed = true; $lcv->removedTurn = $turn` and records the link in
a per-rail `lcvDocked` note ([HangarOps.php:1396](source/server/model/systems/HangarOps.php#L1396));
`performLCVLaunch` resurrects the ship at the carrier's hex, heading, facing and speed and re-inits
its weapon data ([1440](source/server/model/systems/HangarOps.php#L1440)); the state round-trips
through `generateIndividualNotes` / `onIndividualNotesLoaded`, which re-applies `$removed` on every
load ([baseSystems.php:3970](source/server/model/systems/baseSystems.php#L3970)).

⭐ **The Docking Bay is that rail with a LIST instead of one slot**, plus the box arithmetic above
and a class allow-list. Everything genuinely hard about docking a ship inside a ship — the removal,
the resurrection, the persistence, the carrier-destruction escape
([HangarOps.php:6867](source/server/model/systems/HangarOps.php#L6867)) — is written.

**The allow-list.** `hangarType = 'Mapmaker Probes'`, which is what `inferHangarType` would derive
from the hull's `$fighters` declaration anyway, plus an explicit `$allowedFighterClasses`-style list
extended to accept `Pathfinder`, `Scribe` and `Waymarker` **by phpclass**. The per-bay machinery is
`HangarOps::hangarAcceptsFighterClass` / `hangarAcceptsCategory`
([4676](source/server/model/systems/HangarOps.php#L4676) and
[4564](source/server/model/systems/HangarOps.php#L4564)), which today only ever answer about
`FighterFlight`s — teaching them to answer about a hull is the second half of the stage.

**One TYPE per turn (D17).** *"Can only launch/recover one TYPE of craft per turn, so can't mix
Scribes with Mapmakers."* The bay already tracks `launchedThisTurn` / `landedThisTurn`; add the
claimed type beside them and refuse a second kind. ⚠️ It persists through the existing
`hangarDockEvent` IndividualNote channel — **not** through Stage 8's
`ShipSystem::saveFirePhaseDeclaration()` hook, which is Fire-phase-only by construction and would
never see an Initial Orders or Movement hangar order.

⚠️ **No new shipid-keyed table.** If one is ever added it must go into **both** `deleteGames()` and
`leaveSlot()`, because leaving a slot deletes `tac_ship` alone and MariaDB recycles the id. Keeping
the whole link in notes avoids the question.

⚠️⚠️ **THREE OF THE FOUR DOCKABLE CRAFT ARE SHIPS, AND SHIPS MAY NOT DEPLOY ONTO AN OCCUPIED HEX.**
(User, 2026-09-09.) Only the Mapmaker Sensor Probes are a `FighterFlight`; Scribe, Pathfinder and
Waymarker are hulls, and the Deployment phase refuses to place a hull on a hex that already holds a
unit — `shipManager.getShipsInSameHex` ([ships.js:927](source/public/client/ships.js#L927)) collects
every non-destroyed unit whose position equals the candidate hex, and the Deployment strategy's
`isBlocked` rules turn a non-empty list into a refusal. So a fleet that wants to start the battle
with its Scribes already inside the Traveler cannot even be *placed*, quite apart from the docking
machinery.

⭐ **The exemption already exists in exactly the shape we need** — `getShipsInSameHex` skips
`ship2.pendingDeployDock` (Hangar Ops Stage 7) and `ship2.pendingLcvDeployDock` (LCV Rails), both
for the same reason: *"a unit queued for deployment-phase dock is logically inside a carrier's
hangar, not on the board."* A Docking Bay deploy-dock is the third instance of that idea, not a new
concept — so the restriction is loosened by making the bay's queued craft carry the same kind of
marker, **not** by weakening the hull-versus-hull occupancy rule for everyone.

- ⚠️ Skipping the craft is only half of it. The occupancy test is symmetric: the queued Scribe must
  also not be *refused* when the player drops it onto the Traveler's hex in the first place, which
  is the placement path rather than the collision list.
- ⚠️ Keep it to the deploy-dock queue. Two Scribes that are simply both on the board must still
  refuse to share a hex on turn 1 exactly as they do today; nothing here is a general permission for
  ships to stack during Deployment.
- ⚠️ The terrain branch must stay intact — a queued craft still cannot be dropped onto terrain, and
  the Huge/`hexOffsets` collision arms of the same function are untouched.

#### 3.14a The Waymarker's two-turn procedure — **BUILT 2026-09-12 (Stage 19)**; deferred from Stage 16 by D35, as built in §3.14e

Everything above is one-turn docking, which is what the bay already does and what the other three
craft need. The Waymarker needs an **intermediate state**: a turn in which it is neither on the
board under its own control nor inside the bay. That is the only genuinely new idea in this stage,
which is why it is severable — **build the bay first, prove it with the other three, then decide.**

**The user's suggestion is the right mechanism.** FV already has a "riding on a host, not inside it"
state: `attached`.

- `$rider->attached[$host->id] = $location` with the mirror
  `$host->hasAttached[$rider->id] = $location`, plus `attachedFacing` / `hasAttachedFacing` carrying
  an entry-side hex offset ([ShipClasses.php:202](source/server/model/ships/ShipClasses.php#L202)).
- It **persists with no schema change**, as `'Attached'` / `'Detached'` IndividualNotes read back in
  `CnC::onIndividualNotesLoaded` ([baseSystems.php:3246](source/server/model/systems/baseSystems.php#L3246)) —
  note value `shooterId=>location:facing`.
- Movement is **mirrored for free**: `MovementGamePhase::advance` duplicates the host's movement
  onto every attached unit as `'attached'`-type `MovementOrder`s
  ([MovementGamePhase.php:273](source/server/Phase/MovementGamePhase.php#L273)), with the facing
  offset and the rolled-host correction already handled.
- Attached units are **carried into hyperspace with their host** by `Movement::resolveJumpOuts`, are
  never rammed by it, and cannot exchange non-boarding fire with it — all of which is right for a
  Waymarker in the middle of a docking manoeuvre.

So the sequence is: **turn N** — the Waymarker is attached to the Traveler's aft location (2), moves
with it, and is not yet in the bay; **turn N+1** — it is removed and its 24 boxes are claimed.
Launching runs the same two steps backwards.

⚠️⚠️ **Attached mirror rows are ALL type `attached`, which makes an attached unit invisible to every
"entered a new hex" test in the codebase.** That is a known, load-bearing trap (it exists for
breaching pods) and it is exactly what a half-docked Waymarker wants — but anything the intermediate
state has to trigger (an EDF drain, a jump-out, a mine) has to be checked against that, not assumed.

⚠️ **The boxes must be reserved on turn N, not N+1.** Otherwise a player attaches a Waymarker and
fills the bay with Mapmakers in the same turn, and the arrival on N+1 has nowhere to go. Reserve at
declaration; release only if the manoeuvre is abandoned or the Waymarker dies.

⚠️ **`attached` is written today only by boarding-pod weapons**
([specialWeapons.php:8928](source/server/model/weapons/specialWeapons.php#L8928)). A hangar writing
it is new, and `weaponManager`'s attached-unit fire restrictions and `mathlib`'s attached-unit range
special cases ([mathlib.php:84](source/server/lib/mathlib.php#L84)) will all start applying to a
capital ship for the first time. Read each before assuming the state is inert.

**Exit criterion:** the bay accepts 24 Mapmakers, 6 Scribes, 2 Pathfinders or 1 Waymarker and
refuses the 25th, 7th, 3rd and 2nd; a mixed load fills to exactly 24 boxes; it refuses a Scribe on a
turn a Mapmaker moved; a docked Scribe survives a reload with its damage, power and notes intact;
the aft hit-chart row still finds the renamed system; the hull corpus differential shows no other
ship's hangar accounting moving. **If 3.14a is built:** a Waymarker rides attached for exactly one
turn each way, its boxes are reserved from declaration, and it moves with the Traveler while
attached.

#### 3.14b As built — Stage 16, 2026-09-11

Four rulings opened the stage (D33–D35) and one arrived with it (D36): ships dock and suffer damage on
the LCV-rail rules, a ship pays its BOXES against the launch rate, the Waymarker's two-turn procedure
is deferred, and bought ships count toward the Traveler's hangar requirement.

**The class.** `DockingBay extends Hangar` ([baseSystems.php](source/server/model/systems/baseSystems.php),
after `DockingCollar`). ⭐ **`$name` stays `'hangar'`** (trap 37): twenty-nine client sites gate the
fighter UI on the name and `SystemFactory` builds the client object from it, so the Mapmakers keep
every fighter path and the client carries the bay as a plain `Hangar` with `isDockingBay` set. The
hit chart matches `$displayName`, `"Docking Bay"`, and the Traveler's aft row moved with it. Constructor
`(armour, boxes, rate, direction, dockableShipClasses, fleetCheckCategory)`; the Traveler mounts
`new DockingBay(6, 24, 12, 0, ['Scribe','Pathfinder','Guideship','Waymarker'], 'Mapmaker Probes')` in
the Hangar's own position, and every system id is proved unchanged against the committed hull.
`hangarType` stays universal `'fighters'`: a typed bay would *reserve* the Mapmakers
(`bayReservesFlight`) and pull them in ahead of the side bays.

**The ship half is the LCV rail with a list**, in `HangarOps::*BayShip*` beside the LCV functions:
`$shipsDocked` `[{shipId, phpclass, boxes, dockTurn, deploy?}]`, one change-detected `bayShipsDocked`
note, `$removed` re-derived on every load; `bayShipDockOrder` / `bayShipLaunchOrder` notes resolved in
the crit phase, deploy-docks resolved at commit. Box costs are the hulls' `unitSize` (D18: 1/4, 1/12,
1/12, 1/24), inert everywhere else because the lobby reads a HULL's `unitSize` only when it sets
`hangarRequired` - proved on all four, and `ceil(1/(1/12))` is exactly 12 in PHP and in JS after a
JSON round trip.

**One pool.** Fighters see the ships through `HangarOps::effectiveCapacity` and
`HangarShared.effectiveHangarBoxes` - the two choke points - and two client dialog sites that had
recomputed `maxhealth` inline now call the latter (trap 38). Ships see the fighters through
`occupiedBoxes` / `hangarUsedBoxesOnBay`. Deploy-docks seed the POST-side bay from its DB twin and
resolve ships BEFORE the parent's fighter packer, so a Mapmaker flight packed in the same commit finds
the ships' boxes taken.

**One type per turn (D17), decided from the orders when the notes load** (trap 40). The bay's own
fighter orders win - *"it refuses a Scribe on a turn a Mapmaker moved"* - and the refused ship orders
are logged at resolution. Otherwise ship orders claim the bay and `effectiveCapacity` reads 0 for it
all turn, which is what keeps the carrier-level coalescer from routing Mapmakers in from a sibling bay;
the claim is LATCHED because the orders are consumed as they resolve. Damage eviction subtracts the
ships' boxes and never sees the lock. Between ship classes the lock is almost always the rate (D34).

**Dock, launch, lose.** `canBayShipDock` is `canLCVDock` plus box room, the box-priced rate and the
lock; `performBayShipLaunch` is `performLCVLaunch`'s placement and -50 initiative crit **without** its
re-init (trap 41). A ship cannot launch on the turn a Firing-phase dock brought it in; a deploy-dock
carries `deploy` and may. A destroyed bay or carrier forces every ship out with the bay's damage +
2d10 (`LCVRailFragments`, the rail's replay-safe clear-and-persist); a carrier that jumped away is
skipped, so its ships leave with it.

**Deployment (D30).** A queued ship carries the LCV marker, `pendingLcvDeployDock` with `bay: true`,
so every "inside a carrier, not on the board" test in the client applies unchanged - `getShipsInSameHex`
first among them - and `unqueueLcvDeployDock` hands a bay ship to its own release, which does not snap
it onto the carrier's hex the way an LCV is snapped. The server exempts it from the movement check via
`collectQueuedDeployStartFlightIds`. DOCK is offered from `SelectFromShips` and
`DeploymentPhaseStrategy.onShipClicked`; DEPLOY over an occupied hex stays refused.

**The rest of the wiring.** The bay's payload keys are stripped before the ordinary hangar parser runs
(trap 39); `shipsDocked` and the queued ship orders follow the enclosed-bay own-team mask, and
`hideDeploymentDocks` un-removes a same-turn deploy-docked ship for everyone else; the fleet list's
`isDepartedWithCarrier` walk knows a bay. UI: "Enter Hangar" on the ship (`confirm.bayShipDock`), and
Docking Bay sections in the carrier's Recover, Launch and Deploy dialogs, each refusing fighters and
ships through one bay in the same dialog.

**The Fleet Checker (D36).** Credit per category = min(box cost of every bought ship some bay in the
fleet lists, the fleet's total Docking Bay boxes), added in the live small-craft report loop and named in
the row ("incl. N Docking Bay boxes of ships"). Anchored on live-only lines: the archived
`checkChoices_LEGACY` block duplicates every other candidate.

**Verification.** 111 server checks (`tests/replay/walkersStage16Harness.php`) and 98 client checks
(`walkersStage16ClientHarness.js`, the real files under `vm`, the Walker blueprints read out of the
regenerated statics, the Fleet Checker driven through its live slices), plus the Stage 14 fleet-check
harness's 43 re-run as a regression - both new harnesses fail on the pre-stage tree. `checkShipData.php`
PASS, 0 new against 237; autoload +1 line. **Hull differential over 2,727 classes, 2,675 hangars, 7,090
facts: exactly five moved** - the four dockable hulls' box cost and the Traveler's aft system class -
and no capacity did. **Replay:** the corpus is 133/1 on a clean tree (4347, pre-existing); with the
stage the ten Traveler games (4329, 4331–4337, 4340, 4345) differ ONLY by four additive keys on the bay
- `isDockingBay`, `dockableShipClasses`, `deferredShipClasses`, `shipsDocked` - and need re-recording.

**Exit criterion.** 24 Mapmakers / 6 Scribes / 2 Pathfinders, and the 25th / 7th / 3rd refused ✓; the
Waymarker's 1-of-1 is deferred (D35); a mixed load is exactly 24 ✓; a Scribe refused on a turn a
Mapmaker moved ✓; the list and the removal round-trip a reload ✓ - the ship's own damage, power and
notes live in ship-keyed rows the dock never touches, which the user's live reload should confirm; the
renamed chart row ✓; no other hull's hangar accounting moved ✓; a queued ship placeable into the
Traveler's hex while two hulls still refuse to share one ✓.

**Not built, open for the user:** the Waymarker (§3.14a); a docked ship's weapons neither recharge nor
reset while it is aboard (the LCV re-init was not reused - trap 41); a ship launches on the Traveler's
own facing, not the bay's; and a dock order into a TEAMMATE's carrier sits on a system the player does
not submit - the LCV and fighter dialogs share that, so it is unchanged here.

**Revisions after review, 2026-09-11 (D37–D40).** ⚠️ The paragraph above about weapons was WRONG and
was never checked: `Manager`'s turn-advance sweep calls `onAdvancingGamedata` on every system of
every ship with no removed-ship filter, and `calculateLoadingFromLastTurn` tests only the weapon's own
destruction - so a docked ship's weapons always recharged, and an Energy Draining Mine restocks on its
cadence up to 3, exactly as the user asked. Proved now, not changed.
- **Rate (D37).** `$shipLaunchRates` (constructor map `class => per turn`), `$shipsMovedThisTurn`,
  `HangarOps::bayShipRateLeft` / `window.bayShipRateLeft`. Ships no longer touch the fighters'
  `launchedThisTurn` / `landedThisTurn`. `bayBudgetLeft` is gone from both sides.
- **Fill order (D38).** `bayFillRank` ranks reserved 0, ordinary 1, Docking Bay 2 - in
  `sortBaysReservedFirst`, `eligibleHangarsForLanding`, `legacyBerthFits`, `distributeFlightAcrossHangars`,
  `eligibleHangarsForFlight` and both fire-menu receiving-hangar lists. Every non-bay bay keeps the
  rank it had, so no other hull's order moves.
- **LCVs (D39).** `selIsLcvUnit` removed from `DeploymentPhaseStrategy` (both sites) and
  `SelectFromShips`; `unqueueLcvDeployDock` no longer snaps. FAQ updated.
- **Reinforcements (D40).** `JumpEngine::getLegacyRideHost` and its client mirror answer for any unit;
  `legacyBerthFits` fits a ship into a Docking Bay beside the fighters' promised boxes;
  `DeploymentDock.planFlightsIntoCarrier` packs ships and flights into one scratch map;
  `autoPlaceArrivingReinforcements` queues a ship's bay dock and flags it `forcedDeployDock`, which the
  bay's release path and un-dock section now honour.
- **Net preview.** `PhaseStrategy.buildEdfNetPreview` skips a unit on a deploy-dock marker or still on
  its `start` row, and answers an EMPTY preview (not `null`, which would fall back to the server's
  stale map) when Nets exist but none is on the board; `refreshDeploymentUIForDeployStart` re-syncs it
  on every dock and un-dock.
- Verified: server harness 131, client 118, Stage 14 fleet check 43, all 0 failed. Full `-Check`:
  autoload current, validator 0 new, replay 122 passed / 11 failed - the same ten Traveler games plus
  the pre-existing 4347, and outside 4347 the only differences are FIVE additive keys on the bay (the
  four above plus `shipLaunchRates`). 3676 skipped (the local game advanced a turn between runs).

**Revisions after the second review, 2026-09-11.**
- **Riders left on the map (game 4350).** `autoPlaceArrivingReinforcements` docks a legacy opener's
  riders AFTER `activate()` has run `consumeGamedata` - the only pass that applies `shouldBeHidden` to
  the icons - so they stood on their off-map `start` markers for the whole phase. It now re-runs
  `consumeGamedata`, `syncAllEdfFields` and `syncEdfNetPreview` when anything went aboard. Mapmaker
  riders had the same fault since Stage 15.
- **Fields drawn at start markers.** `TacGamedata::setEdfHexes` skips a unit whose LAST movement row is
  `start` - never placed; Generated Terrain (userid -5) exempt. It had been publishing Nets, and field
  hexes into the map the rules read, at the off-map markers of turn-1, late-slot and arriving units.
  Client twin `PhaseStrategy.isOffBoardForEdf` gates both overlays: the per-icon disc
  (`syncAllEdfFields`, now re-synced on every Deployment placement and dock) and the Net preview. The
  test is sound because a `start` row is only ever a unit's FIRST row: `submitMovement` never writes
  one, and the DB holds none after turn 1.
- **Greying instead of refusing (user request).** `confirm.bayShipBayProblem` is the one verdict -
  fighter claim, type, room, rate - behind both `refitBayShipRows` (greys each unticked row that could
  not be added, with a note after its label) and the OK-time guard. In the launch dialog a ticked ship
  caps every fighter row at the craft drained from its OTHER bays first (`refitBayConflicts`), and the
  fighters' live charges grey the ship rows. The recover dialog routes an auto-distributed flight round
  a ship-held bay, drops that bay from pick-lists, and greys what is left (`refitRecoverConflicts`).
  Queued fighter LAUNCHES no longer hide the bay's ships (`bayLaunchableShips`), and queued ship
  launches no longer hide its fighters (`hangarLaunch`): the dialog shows both. The recover dialog's
  auto-distribute also sorts by `bayFillRank` now.
- Verified: server harness 135 (the new group 17 fails 2 with the fix stashed), client 138, Stage 14
  fleet check 43, all 0 failed. Replay 122 passed / 11 failed, as before. A per-game tally: the ten
  Traveler games differ only by the five bay keys; 4347 by those plus its drift, byte-identical to a
  stashed tree; no `edf` line anywhere.
- **Two older faults, fixed at the user's request the same day.**
  - *The Deployment payload.* `setEdfHexes` runs in `onConstructed`, BEFORE `deleteHiddenData` strips
    an opponent's this-turn deploy rows, so a phase -1 payload's `edfHexes` / `edfNetHexes` carried the
    positions of Walkers the opponent had already committed. Nothing drew them, but the browser could
    read them. `deleteHiddenData` now rebuilds the map after its phase -1 masking, gated on there being
    a field. The masked unit is back on its `start` row, so it drops out.
  - *Late arrivals.* A late-slot unit places its entry hex the turn before it arrives, and it projected
    from there during that turn. `setEdfHexes` now skips `getTurnDeployed > turn`; the client twin
    `isOffBoardForEdf` makes the same test, which also keeps such a unit's Net out of the Movement
    preview (that preview walks hidden icons too).
  - ⚠️ **Side effect, intended:** `getTurnDeployed` answers 999 for a SURRENDERED slot, so a surrendered
    fleet stops projecting as well, like every other gate that treats it as gone. Seven surrendered
    replay games (4329, 4331–4334, 4337, 4345) differ by exactly that: the surrendered fleet's
    `edfHexes` (and one `edfNetHexes`), nothing else. Re-record those seven.
  - Verified: server harness 141 (groups 18 and 19 added), client 140, 0 failed.


---

#### 3.14c The hangar-manoeuvre label — **BUILT 2026-09-12 (Stage 19)**

*"We should add a new Docking with 'X' tooltip note, and a cyan status banner when ANY unit is
ordered to enter (or leave) a Hangar during the Firing phase. Then use this new tooltip note/status
banner to also indicate when a Waymarker is docking throughout the transitional docking/launching
turn, instead of the normal 'Attached' note and status banner."* (User, 2026-09-12.)

⭐ **ONE READER, TWO SURFACES.** `shipManager.getHangarManoeuvre(ship)` (ships.js) returns
`{ text, dir, riding, carrier }` or null, and is read by the map tooltip (ShipTooltip.js) and by the
ship window's banner stack (ShipWindow.js `getStatusBanners`). That is the contract
`getArrivalIniPenalty` already records: a figure the two surfaces must never disagree about gets one
function, not two.

**What it answers for.** Any unit named in a queued Firing-phase hangar order — a flight recovering
(`pendingDockOrders[].flightId`), a ship entering a Docking Bay (`pendingBayShipDockOrders[].shipId`),
an LCV coming back to its rail, and the two ship-shaped cases going out — plus a Waymarker riding a
Traveler's aft (`shipsAttaching`), which outranks a queued order on the same unit because it is the
thing that is actually happening rather than the thing that has been asked for. Wording is
**"Docking with &lt;carrier&gt;"** inbound and **"Launching from &lt;carrier&gt;"** outbound; the colour
is the cyan this tooltip already gives Hangar Operations, Just Launched and Arrival Scatter, and the
window already gives Deploying and Arrival Scatter — something benign the unit is *doing*, not damage.

⚠️⚠️ **A NEW FLIGHT LAUNCHING HAS NO UNIT TO LABEL.** A fighter launch order names a *phpclass and a
size*; the flight it creates does not exist until the order resolves, so there is nothing to hang a
banner on. The user ruled this out of scope rather than moving the banner to the carrier ("on the unit
only — skip new flights"), which keeps *"this label is about the unit wearing it"* true with no
exceptions. A relaunch of an already-docked flight is a launch order of the same shape, so it is not
covered either — the gap is the ORDER SHAPE, not the unit's existence, and a future fix belongs in the
order (a flight id), not in the reader.

⚠️ **IT REPLACES THE ATTACHED PAIR, IT DOES NOT SIT BESIDE THEM.** A riding Waymarker *is* `attached`,
so without suppression the tooltip reads "Attached to Traveler [Aft]" in boarding green and the
carrier reads "Ship is being Boarded!" in alert orange — a docking manoeuvre described as an enemy pod
on the hull. Both surfaces now gate the attached line on `!(manoeuvre && manoeuvre.riding)`, and
**both filter `hasAttached` through `shipManager.isDockingRider`** so a Traveler with a Waymarker on
its aft and a real pod on its bow still says it is being boarded.

**Masking is inherited, not re-implemented.** Queued orders ride the carrier's hangar system and are
own-team-only (`Hangar::stripForJson`), so an opponent simply finds none — an intention to dock is a
secret. `shipsAttaching` is published to EVERYONE, because the ride is on the map in plain sight and
its aft-hit redirect is something an attacker must be able to reason about before they shoot.

---

#### 3.14d What a stowed unit still projects — **BUILT 2026-09-12 (Stage 19)**

*"Energy Draining Fields are still operational for Docked craft in Traveler and docked ships with EW
Detectors still contribute their Saved EW to ships within 20 hexes."* (User, 2026-09-12.)

⭐⭐ **TWO RULES, ONE MISSING FACT.** Both sweeps — `TacGamedata::setEdfHexes` and
`EW::collectEwDetectors` — opened with the identical four exclusions (destroyed, still in hyperspace,
never placed, not arrived yet) written out twice, and `isDestroyed()` folds `removed` in, so a docked
unit was excluded by the FIRST of them. The fix is one shared reader,
**`HangarOps::projectionOriginFor($ship, $gamedata)`**, which returns the hex a unit projects FROM or
null: its own, or **its carrier's** when it is stowed. Client twin: `shipManager.getProjectionOrigin`.

⚠️ **THE CARRIER'S HEX, NEVER THE STOWED UNIT'S OWN.** A docked ship's last movement row is wherever it
happened to dock, and stops being true the moment the Traveler moves. This is why the rule could not be
a one-line relaxation of the `isDestroyed()` test: the exclusion and the position are the same problem.

**Three homes, one walk.** `HangarOps::stowedInCarrier` — the server twin of
`ajaxInterface.isDepartedWithCarrier` and of the client's `shipManager.carrierHolding` — finds a
Docking Bay's `shipsDocked`, a rail's one `lcvDocked`, and a hangar's stored FLIGHTS under
`hangarUsage[].dockedFlightId`.

⭐ **`BaseShip::isDestroyedByDamage()` is the server twin the client has had since Hangar Ops** — "the
same question asked of the damage alone". A stowed WRECK projects nothing, and `isDestroyed()` cannot
tell one from a unit parked inside a hangar. ⚠️ **NOT a change to `isDestroyed()`**, and it must never
become one; this is the third site in three stages to need that exact carve-out.

⭐⭐ **THE DISC HAS A MASKED INPUT, AND THAT IS A PUBLISHED-TWIN PROBLEM (the Stage 18 D47 shape
again).** A field projected from inside a hull has no icon of its own to draw a disc on, so the
CARRIER's icon draws it — but deriving the radius from the bay's ship list gives the opponent nothing,
because that list is masked, and they would then watch the drain apply over hexes with no disc on them.
(The hexes themselves are public in `gamedata.edfHexes`; only the SOURCE is hidden.) So the server also
publishes the finished number per hangar to everyone — `HangarOps::publishStowedEdfRadii` fills a
**protected** `Hangar::$stowedEdfRadius` during `setEdfHexes`, sent by `stripForJson` only when
non-zero — and `PhaseStrategy.getStowedEdfRadius` **maxes** it against the live walk. The owner gets a
figure that follows the power they are allocating this phase; the opponent gets the committed one;
neither can see a field that is not there. ⚠️ Protected rather than public on purpose: a public
property rides the static blueprint, which would put a live per-turn number into a cached per-CLASS
artefact.

⚠️ **The EW half is safe under masking for a reason that will not generalise.** `collectEwDetectors`
is mirrored on the client, and an enemy's docked list is masked — so their stowed detectors silently
drop out of the client's sweep. That is harmless HERE and only here: the allowance is filtered to the
viewer's own team (`countEwDetectorsCovering`), whose bays are disclosed to them. A future rule that
reads the detector list across teams would need the published-twin treatment the EDF disc got.

⚠️ **`EdfNetLinks::buildOccupancy` is deliberately NOT changed.** It counts units *standing in* a
corridor for a tie-break; a stowed unit is not standing anywhere of its own, and its carrier is already
counted.

---

#### 3.14e As built — Stage 19, 2026-09-12 — **STAGE COMPLETE** (signed off by the user after play testing, 2026-09-12; play-test fixes and refinements in §3.14f)

Four rulings opened the stage, all the same day: **least damaged = most structure boxes remaining**;
a riding Waymarker **can be shot, cannot shoot**; the leaving-side banner is **on the unit only, skip
new flights**; and in the fleet list **left-click is scroll only, right-click is the ship window, on
ALL docked units**.

**The state machine.** `DockingBay::$shipsAttaching` — `[{shipId, phpclass, boxes, startTurn, dir}]`,
`dir` being `'in'` or `'out'` — with its own change-detected `bayShipsAttaching` note beside
`bayShipsDocked`. Everything else is in `HangarOps`' two-turn section: `beginBayShipAttach`,
`releaseBayShipAttach`, `completeBayShipAttachments` and `releaseAllBayShipAttachments`.
`performBayShipDock` and `performBayShipLaunch` divert a two-turn class onto the ride and are
otherwise untouched, so a Scribe's path is byte-for-byte what it was.

⭐ **THE CLAMP LIVES IN THE CARRIER'S CnC NOTES, THE LIST ONLY SAYS WHY.** `Attached` / `Detached`
notes are what `CnC::onIndividualNotesLoaded` replays into `->attached` / `->hasAttached` on every
load, exactly as a breaching pod's are. Keeping the two halves separate is what lets a rider that
something ELSE detached — the CnC's own boarding sweep writes `Detached` when the host's structure at
that location dies — be noticed as an **ABORT** rather than completed: the entry is dropped, the boxes
released, and a Waymarker on its way IN stays on the board where it is.

⭐ **`attached` MEANS BOARDING EVERYWHERE ELSE IN THE TREE**, so no Stage 19 rule may read it directly.
`HangarOps::attachedBayShipFor` (carrier side), `bayCarrierAttachedTo` (rider side) and
`shipManager.isDockingRider` (client) are the discriminators, and each opens with the empty-`attached`
early-out so the whole question costs one array test in a game with no boarding in it.

**Free from the ride, and this is why it was the right mechanism:** movement is mirrored by
`MovementGamePhase::advance` and `Movement::setPreturnMovementStatusForShip`; the client already
refuses to plot a move for an attached unit (movement.js, five sites); the pair is never rammed by its
host; mathlib's same-hex bearing already knows the shape. Not one line of that was re-implemented.
Facing offset **0**, and that is a fact rather than a simplification — `canBayShipDock` requires the
docking ship to share the carrier's heading and `resurrectAtCarrier` puts a launch out on it.

**Boxes are reserved from declaration**, through the one choke point: `HangarOps::dockedShipBoxes`
counts `shipsAttaching` as well as `shipsDocked`, so `effectiveCapacity`, `bayFreeBoxesForShips` and
the client's `HangarShared.bayShipBoxesHeld` all reserve without knowing they do. The **type lock
lasts the whole manoeuvre**: `hasShipOrdersThisTurn()` answers true while anything is riding, which is
the existing one-type-per-turn rule simply lasting as long as the manoeuvre does.

**The -50 launch initiative is paid at SEPARATION, not at the attach.** An attached unit plots no
movement of its own and its initiative decides nothing, so spending it a turn early would have bought
nothing.

⭐⭐ **THE AFT-HIT REDIRECT IS SEVEN LINES, AND WHERE IT SITS IS THE WHOLE DESIGN.** The SHOT is never
redirected — only the hit that has already landed aft. Everything above the insertion point in
`Weapon::damageOneSheet` has run against the carrier exactly as it would with no rider at all: the
profile, the to-hit, `beforeDamage` and all 35 of its overrides, and the hit-location roll itself.
Placed AFTER `$tmpLocation` resolves and BEFORE the Piercing structure count, so the Piercing, Raking
and standard branches below are already talking about the Waymarker — that is what makes it seven lines
rather than a fork of the function. The `DamageEntry` follows `$target`
(`assignDamageReturnOverkill` files against `$target->id`), so the rows land on the Waymarker's own
sheet and persist there. ⚠️ `$forcePrimary` shots are exempt: they are internal effects aimed at the
carrier's Primary, not incoming fire that happened to strike the aft. ⚠️ A Piercing shot enters through
the chosen section (`$facingLocation = $tmpLocation`) rather than re-rolling the rider's facing; its
exit section is still derived from the bearing, so it behaves like a piercing shot once inside.

**Least damaged = MOST STRUCTURE BOXES REMAINING** (`HangarOps::leastDamagedFrontOrAft`). On a
Waymarker (Front 60, Aft 56) that means an undamaged hull takes the hit forward and swings aft only
once the bow is five boxes worse off; ties go FORWARD, the same side an unhurt hull picks, so the rule
never changes answer for an unhurt Waymarker. A destroyed section is never chosen.

**The fire withdrawal** is `Firing::withdrawFireFromDockingRiders`, modelled on
`withdrawFireFromJumpingUnits` directly above it down to the exclusions (a ram is a collision, a
hyperspace log order is not a shot, a selfIntercept marker is consent), called from both
`prepareFiring` and `preparePreFiring`. ⚠️ On the advance path, not in `validateFireOrders`: the ride
BEGINS in the previous turn's critical phase, so a POST-side ship reconstructed without its carrier's
notes cannot answer "am I riding anything". `weaponManager.selectWeapon` refuses the selection
client-side, with the same ram exemption.

**The fleet list (item 5).** `shipManager.carrierHolding` is now the ONE implementation on the client
— `fleetListManager.carrierHolding` is a one-line delegate — and it grew the `hangarUsage
.dockedFlightId` arm, so a docked FLIGHT resolves to its carrier like a docked hull. `isOffBoardButOurs`
lost its `removed && flight` clause, so that row falls THROUGH to the carrier-scroll branch.
⭐ **This withdraws Stage 17's own exception on purpose.** §3.15a argued a flight has no hex of its own
and should open its window; the user's answer is that its CARRIER does, and that is the honest reply to
"show me where this is". One list, one meaning per gesture: left-click scrolls, right-click and the ⓘ
affordance open the window, on all three kinds of stowed unit. What is left in `isOffBoardButOurs` is
the one state genuinely inside no hull at all — a reinforcement still in hyperspace.

**Abandonment.** A destroyed bay or a destroyed carrier simply lets the rider go — deliberately with
**no fragment damage**, unlike a ship forced out of the bay itself (`forceBayShipOut`): this one was
never inside, it was clamped to the outside of the hull and is already on the board at the carrier's
hex. There is no way to cancel a ride once it has begun; cancelling the ORDER before it resolves works
as it does for any other craft.

**Verification.** 123 server checks (`tests/replay/walkersStage19Harness.php`) - group 10 drives two whole
turns through the REAL `DockingBay::criticalPhaseEffects`, so the completion pass, the order pass, their
ORDER and the note round-trip are exercised rather than asserted from the source - and 71 client checks
(`tests/replay/walkersStage19ClientHarness.js`, the real `hangarShared.js` / `ships.js` / `ew.js` /
`fleetList.js` / `PhaseStrategy.js` under `vm`), both fatal on the pre-stage tree. `checkShipData.php`
PASS, 0 new against 237; autoload unchanged; **statics unchanged** (`TWO_TURN_SHIP_CLASSES` is a const and
`$stowedEdfRadius` is protected, so neither rides a blueprint, and `ShipCompactor` strips an empty
`shipsAttaching`). **Replay: 120 passed / 13 failed — the SAME 13 games and the same count as the
pre-stage tree**, so the stage adds no behavioural drift and no new failing game; the only Stage 19
lines in the diff are `deferredShipClasses: removed` / `twoTurnShipClasses: added` on the ten Traveler
games, beside Stage 17's `servicesDockedUnits` and Stage 18's `sharesDockedPower`. Re-record to accept.

**Exit criterion (3.14a as written), all met:** a Waymarker rides `attached` for exactly one turn each
way ✓; its boxes are reserved from declaration ✓; it moves with the Traveler while attached ✓ (through
the existing mirror, proved by the facing offset and the movement lock-out rather than re-implemented).

**Not built, open for the user:** a rider cannot be told to let go once the manoeuvre has begun; the
completion is unconditional on the carrier's speed (an attached unit moves with it, so nothing is
inconsistent, but the rules do not say either way); and a relaunching DOCKED flight still gets no
banner, because a fighter launch order carries no unit id — see the ⚠️⚠️ in §3.14c.

---


---

#### 3.14f Play-test fixes — game 4351, 2026-09-12 (same day)

**1. ⚠️⚠️ THE RIDER SNAPPED BACK TO THE CARRIER'S START HEX WHEN THE FIRING PHASE OPENED, and the
mirror had never run.** Both Waymarkers followed their Travelers on screen through the Movement
phase — the client mirrors a plotted move live — and then stood at the hex the Traveler had *begun*
the turn in. The DB says why: for turn 2 the Traveler had `move` + `end`, and the Waymarker had only
its preturn `sync` row plus the dummy `end` that `MovementGamePhase::advance` gives every ship at
the hex its last row names. No `attached` rows were ever written.

⭐ **THE CAUSE IS ONE LINE, AND IT IS A LESSON ABOUT REUSED STATE.**
`MovementGamePhase::process` built `$submittedShipIds` from *presence in the payload*:

    foreach ($ships as $s) $submittedShipIds[$s->id] = true;

and the mirror skips any attached unit in that set, so that a **detach** submission is not
overwritten. But `ajaxInterface` sends an entry for **every ship the player owns**, and for an
attached one it deliberately sends an **empty movement list** (the client refuses to plot a move for
a unit riding a host). So a rider read as "moved itself" and the mirror was skipped.

⭐⭐ **IT HAD NEVER SHOWN UP BECAUSE A BOARDING POD AND ITS HOST BELONG TO DIFFERENT PLAYERS** and are
therefore never in one submission. The Traveler and its Waymarker are the first attached pair in the
game on the **same side** — so reusing `attached` (trap 51) inherited a guard that had only ever
been exercised across the table. The fix is to count only ships that submitted actual movement
ROWS; an attached ship with a non-empty list is detaching, which is the case the skip exists for and
still reads true.

**2. EW is suspended on a rider, both ways** (user, same day). *"Waymarkers should also not use EW on
transition Docking/Launching turns, nor should ships have the opportunity to use any targeted EW on
it."*

⚠️ **IT KEEPS ITS DEW (D52).** Only ACTIVE allocations stop — OEW, CCEW, DIST, JAM, SOEW, SDEW, BDEW
and the two Detect types. Unspent points still fall into DEW through `convertUnusedToDEW` exactly as
they do for every ship, so a riding Waymarker is no easier to hit than usual; what it loses is the
ability to spend, and what its enemies lose is the lock. An attacker with no lock then takes the
ordinary doubled range penalty, which is the engine's standing rule and is deliberately untouched.

**Three layers, because EW has no server validation of its own.** `EW::validateEW()` returns true
unconditionally, so:
- `ew.isEwSuspended` gates `ew.AssignOEW` (first point, both ends) and `ew.assignEW` (the increment
  path and every self-EW type), with DEW exempt.
- The Initial Orders menu gains `sourceEwNotSuspended` / `targetEwNotSuspended` on **every EW row,
  `remove` included** — nine adds, ten removes — so the whole EW panel is withdrawn from a rider
  rather than half of it (trap 47). ⚠️ The first pass gated only the adds, on the grounds that a
  remove with nothing to remove is a harmless no-op; the user extended it the same day, and the
  wider version is both simpler to explain and impossible to strand a row with — a rider starts its
  ride with no active rows at all, because the ride begins at the end of the PREVIOUS turn's Firing
  phase, before the transitional turn's Initial Orders. ⚠️ `removeMultiOrder` lives in the same
  table and is deliberately NOT gated: it is a firing-order control, not an EW one.
- `EW::stripDockingRiderEw` runs beside `EW::clampFlightEw` in `InitialOrdersGamePhase::process` —
  the one place the server already clamps an EW submission. ⚠️⚠️ It takes `$gd`, the **reloaded**
  gamedata, because `$ship` there is the POST-side copy and has no attachment state at all
  (arch_post_side_ship_reconstruction).

⚠️ **`shipManager.isDockingRider` gained a load-bearing early-out.** It is now asked of both ends of
every EW button and on every weapon click, so the fleet walk inside `getHangarManoeuvre` would run
dozens of times per gesture. A rider is always `attached`, so one empty-object test rejects every
unit in every game with no boarding and no docking manoeuvre in it.

**3. ⚠️ THE FIRING MODE SELECTOR STILL OPENED ON A RIDER** (user report, same day). D49's "can be
shot, cannot shoot" was already enforced at both ends — `weaponManager.selectWeapon` refuses the
selection and `Firing::withdrawFireFromDockingRiders` drops anything past it — and every other
weapon control in `SystemInfoButtons` vanished on its own, because they all read
`hasFiringOrder` / `hasOrderForMode` and a rider holds none.

⭐ **The firing-mode selector is the ONE weapon control in that menu that never asks whether a fire
order exists.** It gates on the phase, the mode count and `hideFiringModeSelector` alone, so it
survived every other guard by construction. `canChangeFiringMode`, `canSelfIntercept` and
`canRemIntercept` now all refuse a rider (`isDockingRiderUnit`), which withdraws the whole
`<FiringModeSelector>` block — the two intercept buttons are its children.

⚠️ **THE SERVER HALF WENT WITH IT, and had to.** `Firing::automateIntercept` would otherwise have
handed a unit that may not fire a full set of intercept orders. It now treats a docking rider as
unarmed exactly as it treats a jumping Ancient (`isJumpingUnarmed`, one line away) — which is also
the precedent that settles whether interception counts as firing: it does.

**Verification.** The two harnesses grew to 142 server / 98 client, both still fatal on the pre-fix
tree; `checkShipData.php` PASS, 0 new; replay unchanged against the pre-fix tree (119 passed / 13
failed on both — games 4175, 4176 and 4350 SKIP for local database reasons that predate this work).

**The faction page** (`factions-tiers.php`) is updated in the same pass: the Docking Bay section now
lists the Waymarker among the bay's contents and in its launch rate, describes the two-turn ride and
everything true of a rider (moves with the carrier, may not fire or intercept, may be shot, the aft
redirect, no EW either way but keeps its DEW), notes that docked craft keep projecting their fields
and detectors from the carrier's hex, describes the "Docking with X" banner and the stowed-row
scroll — and the Waymarker is struck from the "not implemented yet" list, which now names only the
Extra-Dimensional Jump Drive.

---

### 3.15 The Traveler repairs what it carries — **BUILT 2026-09-12 (Stage 17), as built in §3.15a**

*"Traveler can use its SelfRepair to repair structure, CnC, Critical effects and SelfRepair systems
for any ship it's carrying. New parameter for Self-repair system maybe, again this is the only ship
in game that can do this so we need to gate behind a simple check for efficiency."*

**The gate has a precedent on the very class it goes on.** `SelfRepair` already carries
`$repairRestrictedTo`, `$outputDoubled` and `$linkedOrbital`
([baseSystems.php:11444](source/server/model/systems/baseSystems.php#L11444)) for the Kirishiac
Heavy Orbital — three properties whose whole job is "this one mount behaves differently, and every
other Self Repair in the game pays one null check". Add a fourth in the same block:
`public $servicesDockedUnits = false;`, set true only on the Traveler's mount.

**The behaviour has a precedent too, and it is close.** `CoopStructureSelfRepair`
([baseSystems.php:16666](source/server/model/systems/baseSystems.php#L16666)) already spends
leftover repair points on **other units'** structure: it tiers the recipients, sorts their damaged
blocks destroyed-first then most-damaged-first, and writes `DamageEntry` rows with a negative amount
and an `$undestroy` flag straight onto the other ship's systems
([16758](source/server/model/systems/baseSystems.php#L16758)). Three things differ here:

1. **No range test at all.** The recipients are inside the carrier; `COOP_RANGE` has no analogue.
2. **Four kinds of repair, not one.** Structure, C&C, critical effects and Self Repair systems —
   which means the docked unit's damaged systems and its criticals both enter the Traveler's own
   unified repair queue (`SelfRepair::criticalPhaseEffects`,
   [11562](source/server/model/systems/baseSystems.php#L11562)), rather than the structure-only
   block list the cooperative version builds.
3. ⚠️ **It is an explicit exception to this class's own standing rule.** `SelfRepair`'s tooltip says
   *"Cannot repair destroyed structure blocks or Self Repair systems"*
   ([11487](source/server/model/systems/baseSystems.php#L11487)). The Traveler repairing a docked
   ship's Self Repair is precisely the case that rule forbids, so the exception must be written
   where the rule is, gated on `servicesDockedUnits`, and the tooltip must say so.

⚠️⚠️ **A DOCKED UNIT IS `removed`, AND `removed` READS AS DESTROYED.** `BaseShip::isDestroyed()`
with no argument answers true for any unit whose `$removed` is set — the finding
`SpawnEnergyDrainingMine` carries in its own class comment. So the docked ship's own systems will
never be reached by the ordinary per-ship sweep in `Criticals::setCriticals`, and the Traveler's
Self Repair has to walk into them itself, exactly as `CoopStructureSelfRepair::repairBlocks` does.
Confirm on the way that `submitDamages` persists rows filed against a removed ship — if it does not,
the repair is applied in memory and lost on the next load, which looks like nothing happening.

⚠️ **Decide whether the docked ship's OWN Self Repair still runs**, and say so in the code. If
removed units are skipped by the sweep it does not, and the Traveler is its only source of repair;
if they are not, both run and the same damage can be paid for twice out of two different pools.
Whichever is true, it must be a decision rather than a discovery.

**Exit criterion:** a damaged Scribe docked in the Traveler is repaired out of the Traveler's pool
and not out of its own; the repair survives a reload; the Traveler's own systems still take
priority under the existing queue order; a Self Repair on the docked ship is repairable and a
Self Repair on any OTHER ship in the game still is not; and the 128-game replay corpus is unmoved.

---

### 3.15a As built — Stage 17, 2026-09-12

Built to §3.15 with three additions from the user's notes of the same day (D42–D44) and one
question the plan left open answered the other way from the plan's guess.

**D42 — the docked units share the Traveler's ONE list, marked by name.** *"Perhaps the best way to
add would be for damaged Structure, CnC, SelfRepair and Crits for docked craft to show up in
Traveler's own SelfRepairList, but be clearly marked as to what ship they belong to, and at a lower
priority than Traveler's own systems."* Built first as a **tier** — a field compared before priority
so a docked row could never outrank an own one — and **D45 withdrew that the same day** after the
user saw it:

> *"You have made it a separate list that is always repaired last, but this should really be the
> players choice, they may wish to prioritise repairing Docked ships. So can damage to dock ships
> not be treated as a separate list and instead combined with the ship's own selfRepairList. The
> cyan ship name should be enough for the player to distinguish between the two."*

⭐ **So there is no tier and no floor.** A docked unit's entries go into the same queue and are
sorted by **priority alone**, with the defaults doing the only separating they need (a docked C&C at
9 naturally sits above the Traveler's thrusters at 4, and the player can move either). Override,
+/−, drag and Move-to-Top all work across the whole list in both directions: a docked row can be
dragged above every one of ours, and the drop cascade rewrites a docked row's priority to make room
for one of ours exactly as it would for a sibling. The **cyan ship name** on the row (`docked: true`
→ `<OwnerTag>`) is the only thing that marks a docked entry out, which is precisely what the user
asked for. The one residue of ownership is the deterministic tiebreak at the bottom of both sorts —
`shipId`, with 0 for our own — and it only ever settles an exact tie.

⚠️ The lesson generalises past this menu: *"whose damage matters more"* is a player judgement, and
an engine that answers it for them reads as a bug even when it is defensible. Building the tier cost
a clamp on three separate UI gestures; deleting it deleted all three.

**D43 — a docked ship's own Self Repair keeps working.** §3.15 asked for this to be *decided*; the
user decided it runs. ⚠️⚠️ It cannot run by itself: a docked unit is `removed`, `removed` reads as
destroyed, and `Criticals::setCriticals` snapshots `$activeShips` with `isDestroyed()`, so nothing
on a docked hull is swept at all. `HangarOps::runDockedShipsSelfRepair($carrier, $gamedata)` drives
it, called from **two** places behind one transient guard (`DockingBay::$dockedSelfRepairDone`):
the carrier's own Self Repair (first thing it does, so the docked unit's own points are spent before
the Traveler's) and `DockingBay::criticalPhaseEffects` (which is what still runs when the carrier's
Self Repair is destroyed). ⚠️ It skips a ship whose `removedTurn` is the current turn unless the
entry is a `deploy` dock — a ship that flew in this turn WAS in the active snapshot and Pass 2 runs
its systems in their own right, so repairing it here as well pays for the same damage twice.

**D44 — reinforcement rows go cobalt.** `.fleetlistentry .hyperspace` is `#4a7fe0`, not the shared
`#00b8e6`. Docked rows keep the cyan. It is the one deliberate exception to the "do not introduce a
second blue" rule the other three stylesheets state, and each of them now says so.

**What is offered on a docked unit**, mirrored exactly in `SelfRepair::gatherDockedUnitRepairs` and
`SelfRepairList.getDockedRepairables`: damaged **Structure** (a destroyed block is still out of
reach), damaged **C&C**, damaged **Self Repair** — the exception the standing rule forbids — and
**every repairable critical on any system**, which is the literal reading of "Critical effects".
Nothing else: a docked hull's weapons, thrusters and sensors are its own business, and only its own
Self Repair can reach them.

⚠️ **THE OVERRIDE KEY IS COMPOSITE.** The overrides live on the CARRIER's Self Repair, so a docked
entry is keyed `d<shipid>:<sysid>` (and `d<shipid>:<sysid>-<critid>` for a critical) or two docked
hulls would collide on a shared system id — as would a docked hull and the carrier. The note
round-trip splits on `;` alone, so a `:` in the key is safe, and `notevalue` is varchar(4096). The
"fully repaired, drop the override" branch had to start reading the JOB's key rather than
`$systemToRepair->id`, or a docked Scribe's repair would clear the carrier's override of that id.

⚠️ **The DamageEntry's shipid must name the DOCKED ship.** Each job carries its owning ship;
`getNewDamages()`/`getUpdatedCriticals()` walk `$gamedata->ships` with no `removed` filter, so the
rows persist — but filed against the carrier they would be applied to the wrong hull and vanish.

⭐ **`servicesDockedUnits` travels on the WIRE as well as in the blueprint.** It is a public property
so it reaches the static blueprint (and is in ShipCompactor's `$falseKeys`, with
`dockedSelfRepairDone`, so nothing else pays for it), but `stripForJson` also sends it when true, so
the menu does not wait on a statics regeneration. The cost is the stage's only replay drift.

#### Play-test follow-ups — game 4350, same day

**⚠️⚠️ A DOCKED SHIP'S WHOLE WINDOW WAS INERT, and it was one line.** The list rendered correctly
(the user confirmed the docked Scribes' systems were showing on the Traveler), but clicking the
docked ship's OWN Self Repair in its OWN window did nothing — and neither did anything else in that
window. `SystemIcon.clickSystem`'s guard is
`if (!preBattleDamage && (shipManager.isDestroyed(ship) || …)) return;`, and `isDestroyed` folds
`removed` in, so every icon of every stowed unit had been dead since docking existed. The carve-out
is one predicate the codebase already had a name for: **`shipManager.isDestroyedByDamage`** — the
same question asked of the damage alone, whose whole purpose is telling "gone" from "parked out of
sight" — so `stowed = ship.removed && !isDestroyedByDamage(ship)`. ⚠️⚠️ Again NOT a change to
`isDestroyed` (§3.16's warning), and carved out at this one site only.
⭐ A stowed unit is then **diverted straight to the info menu** rather than let through the rest of
the handler: it may be managed (its repair queue now, its power in Stage 18) but it is inside a
hangar, so it must not reach weapon selection, called shots, a hangar-launch dialog or an LCV rail.
This is the prerequisite §3.16(a) predicted, arriving one stage early and from the other direction.
⚠️ Noted in passing, NOT changed: the second half of that guard,
`shipManager.isDestroyed(ship, system) && !system.clickableWhenDestroyed`, passes a system to a
one-argument function — it is `isDestroyed(ship)` again, so `clickableWhenDestroyed` has never done
anything there. Almost certainly meant to be `shipManager.systems.isDestroyed`.

**Left-click on a stowed ship's fleet row scrolls to its CARRIER** (user, 2026-09-12), which is
where the unit actually is — not to its own window, which was the first build's answer and made
left-click mean two different things in one list. Right-click and the ⓘ affordance remain the
window. `fleetListManager.carrierHolding(ship)` is the finder, deliberately the same walk as
`ajaxInterface.isDepartedWithCarrier` (a rail's `lcvDocked`, a bay's `shipsDocked`, ids parsed
because a spawned unit's id is a STRING), so it covers a rail-parked LCV as well. ⚠️ It sits ABOVE
the `shouldBeHidden` guard, which reads every removed unit as destroyed and would otherwise make the
branch unreachable. A docked FLIGHT is deliberately excluded: it has no hex of its own and its row
has opened its window since Hangar Ops Stage 9.1.

**Proof.** `tests/replay/walkersStage17Harness.php` (70 checks) + `walkersStage17ClientHarness.js`
(57, including the real `SystemIcon.clickSystem` and `carrierHolding` lifted out of the live file by
a source marker), both **fatal** on a stashed pre-stage tree; `checkShipData.php` PASS, 0 new
against 237; statics and both client bundles regenerated. ⚠️ **Replay corpus: 135/0 clean, 121/14
with the stage, and every one of the 14 diffs is the same single additive key**
(`/ships/N/systems/N/servicesDockedUnits: added (true)`) — no damage, critical, movement, to-hit or
masking drift anywhere. Re-record to accept it.

---

### 3.16 Docked units share power with the Traveler — **BUILT 2026-09-12 (Stage 18) — see §3.16a**

*"Docked ships can share power with Traveler on a 1 power per 4 shared basis. We can access their
SCS via the fleetList menu, but are not able to manage power at the moment during Initial Orders."*

**Two halves, and the first is the prerequisite.**

**(a) Managing a docked unit's power.** The block is `shipManager.isDestroyed(ship)` guarding every
power mutation — `setOffline` refuses on it at
[power.js:1167](source/public/client/power.js#L1167) and its siblings do the same — and a docked
unit is `removed`, which that predicate reports as destroyed. Relax it through **one named
predicate** (`shipManager.power.isPowerManageable(ship)`) used by the power paths only.

⚠️⚠️ **NEVER BY CHANGING `isDestroyed`.** The same short-circuit stands in front of
`shouldBeHidden`, the fleet list, the icon, the movement sequence and every "is this unit on the
board" test in the client. Widening it would put a docked ship back on the map.

⚠️ **The server half is the real blocker.** `InitialOrdersGamePhase::process` and `submitPower` have
to accept power rows for a removed ship. Check whether the phase's ship loop skips removed units
before assuming this is a client change.

**(b) The transfer.** The Docking Bay asks each docked **ship** (never a flight) for its reactor
surplus, sums them, floors the total over 4, and adds that to the Traveler's own available power.

⚠️⚠️ **THERE IS NO SERVER TWIN OF `getReactorPower`.** The whole power balance is computed in the
client ([power.js:493](source/public/client/power.js#L493)) and the server trusts the power entries
it is sent; `Reactor::getOutput` answers only for one reactor on one hull. So the shared figure is a
**client** number unless a server-side validator is written for it, and a plan that does not say so
is a plan that ships an unvalidated power grant. Decide which, explicitly, and if the answer is
"client only" then say in the tooltip that it is advisory.

⚠️ **The dependency runs both ways.** A docked ship's surplus depends on what its owner has powered
down, and the Traveler's budget depends on the sum — so the carrier's power display must recompute
whenever any docked ship's allocation changes. One event in the shape of `ShipEwChanged`
([ew.js:457](source/public/client/ew.js#L457)), never a poll.

⚠️ **Trap 23 applies directly.** *Anything in `TacGamedata::onConstructed()` that reads a number an
enhancement can move must run below the per-ship loop* — and "the sum of every docked reactor's
output" is exactly such a number, because `BaseShip::onConstructed()` inside that loop is what
applies enhancements. This is the third system in this plan to meet that trap; `setEdfHexes()`
published a refitted field at its unenhanced radius for a whole stage before it was found.

**Exit criterion:** a docked Scribe can be powered down during Initial Orders and the change
persists across the commit; four points of docked surplus give the Traveler one and three give it
none; a docked **flight** contributes nothing; and the figure recomputes live as the docked ship's
allocation changes.

---

### 3.16a As built — Stage 18, 2026-09-12

Built to §3.16, both halves, plus the explicit decision the exit criterion demanded.

**D46 — THE GRANT IS CLIENT-COMPUTED AND ADVISORY, and that is a decision, not an omission.**
§3.16 required this to be written down either way. It is advisory, because the alternative is not
"add a validator" but "give Fiery Void a server-side power model it has never had":

* There is **no server twin of `getReactorPower`** anywhere in the tree. `Reactor::getOutput`
  answers for one reactor on one hull; nothing sums a ship's draws, boosts and overloads.
* `DBManager::submitPower` validates **nothing** — it normalises, de-duplicates on
  `shipid-systemid-type-turn` and inserts. `InitialOrdersGamePhase::process` merges every
  system's `->power` and hands it straight over.
* So **every power figure in the game is already a client number**. A server check on this one
  grant would be the only power validation in the codebase, and it would still be reading a
  balance it cannot compute.

What follows from that: the flag `DockingBay::$sharesDockedPower` publishes the **rule** and not a
figure — nothing on the server reads it. ⚠️ §3.16 asked for the tooltip to say the number is
advisory; it was written that way and the user trimmed that tail the same day (see “Where the
number shows” below), so the caveat lives in this record and nowhere on screen. If the balance is
ever to be enforced, that is a cross-cutting project (one server-side
`getReactorPower`, then the commit gate moves behind it) and not a Walkers stage.

**(a) Managing a docked unit's power — one predicate, four call sites.**
`shipManager.power.isPowerManageable(ship)` is `!shipManager.isDestroyedByDamage(ship)`, and it
replaced `shipManager.isDestroyed(ship)` in `onOfflineClicked`, `onOnlineClicked`,
`onOverloadClicked` and `onStopOverloadClicked`. On a unit that is on the board the two answer
identically; on a `removed` one — a ship in the Docking Bay, a rail-parked LCV, a docked flight —
the old guard said "destroyed" and returned. ⚠️⚠️ **Not a change to `isDestroyed`**, for every
reason §3.16 gives; it is the same carve-out, with the same existing predicate, that Stage 17 made
in `SystemIcon.clickSystem` (§3.15a).

⭐ **The user's report named the symptom precisely and was worth believing literally:** *"I can
click on a system in the docked ship's shipWindow and bring up the systemPowerSettings menu, but
clicking its buttons does nothing."* Both halves were true and for different reasons. The MENU
opens because none of `SystemInfoButtons`'s six gates (`canOffline`, `canOnline`, `canBoost`,
`canDeBoost`, `canOverload`, `canStopOverload`) asks about the SHIP at all — they test the phase,
the system and the player. The BUTTONS did nothing because the four mutations behind them each
opened with the ship-level guard. ⭐ And **boost and unboost already worked**: `clickPlus` /
`clickMinus` never had the guard, which is exactly what made the menu look half-broken rather than
switched off.

⚠️ **THE SERVER HALF NEEDED NOTHING, and that was worth checking rather than assuming** (§3.16 said
so). `InitialOrdersGamePhase::process` loops `$ships` with no `removed` filter; `construcGamedata`
loops `gamedata.ships` with no `removed` filter either, so a docked ship's systems are in the POST
already; and `submitPower` inserts what it is given. A docked ship's power has therefore persisted
correctly for as long as the Docking Bay has existed — the only thing missing was the ability to
set it.

⚠️ **What is still refused on a docked hull, deliberately:** ownership (`gamedata.isMyShip` and
`ship.userid != gamedata.thisplayer` are separate guards and untouched, so an enemy's docked ship
stays read-only), the phase (Initial Orders only), a cooldown-forced offline, a vortex-locked
offline, `powerLocked`, and a system with a firing order. A WRECK is still refused everywhere.

⚠️ **Found in passing and NOT fixed** (same reasoning as the sibling finding in §3.15a):
`onStopOverloadClicked`'s guard read `shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship,
system)` — and `shipManager.isDestroyed` takes ONE argument, so the second clause has always been
the first one again and has never asked anything about the system. Only the ship-level half was
relaxed; turning the dead clause into a real system test would be a rules change on every hull in
the game, and it is flagged here rather than folded in.

**The commit gate was deliberately left alone.** `getShipsNegativePower` still skips removed units,
so a docked hull cannot block a commit. It cannot inflate the grant either — a negative surplus is
clamped to 0 per ship before the sum — and a docked ship has nothing a boost could spend power on
(it cannot move, fire or hold EW). Adding it would be a new way to block a commit for no gain.

**(b) The transfer — `shipManager.power.getDockedPowerSummary(carrier)`.**
Returns `{donors, surplus, shared}`: every Docking Bay on the hull with `sharesDockedPower`, every
SHIP in its `shipsDocked`, each one's own `getReactorPower`, surpluses summed and then
`Math.floor(total / 4)`. `getDockedPowerShared` is the number alone, and `getReactorPower` adds it
to the carrier's balance as the **last** thing it does — a grant from elsewhere is not a system's
draw and must not go into the per-system loop.

Five rules in the sum, each with a reason:

| Rule | Why |
|---|---|
| SHIPS only, never a flight | D20. Docked fighters are not in `shipsDocked` at all (they ride `hangarUsage`), so the `flight` test is belt and braces. |
| The donor is **not charged** | D20 is a transfer at a quarter rate, not a spend. Deducting the 4 would drop the donor's surplus and the next recompute would take the grant away again, oscillating. What the donor really pays is the powering-down its owner must do to have a surplus — which is why (a) is the same stage. |
| A negative surplus contributes **0** | Clamped per ship, BEFORE the sum, so one over-boosted docked hull cannot drain the Traveler. |
| A wreck, a dead reactor, a destroyed bay or an already-launched ship contribute nothing | A destroyed bay has already put its ships back on the board (`HangarOps::onDockingBayDestroyed`), and the `removed` test catches a unit that left this turn. |
| An **enemy viewer computes 0**, and is meant to | `DockingBay::stripForJson` masks `shipsDocked` to `[]` outside the owning team, so the grant is invisible rather than leaked. For a number nothing enforces, that is the safe direction. |

⚠️ **Trap 23 does NOT bite here, and that is why the figure is a function rather than a map.** §3.16
warned that "the sum of every docked reactor's output" is exactly the kind of number that must not
be computed in `TacGamedata::onConstructed()` above the per-ship enhancement loop. It is never
computed on the server at all, and on the client it is derived live at every read — so there is no
snapshot to be taken at the wrong moment.

⚠️ **THE LIVE RECOMPUTE NEEDED NO NEW EVENT.** §3.16 called for "one event in the shape of
`ShipEwChanged`". There already is one: every power mutation raises `SystemDataChanged`, whose
handler ends in `shipWindowManager.update()`, which re-renders **every** open ship window — so
powering a docked Scribe down moves the Traveler's reactor figure on the same click. Adding a
second event would have been duplicate plumbing.

⚠️ **A re-entrancy latch, for a cycle that should not exist.** `getReactorPower(donor)` calls back
into the walk for the DONOR's own bays. A docked ship cannot itself hold docked ships today, so
`dockedPowerWalk` can only ever break a cycle that is already a bug — but a stack overflow is not
the way to find out that one has appeared.

⚠️ **THE LOBBY HAS A DIFFERENT `gamedata.getShip`.** On `gamelobby.php` there is no `gamedata.js` at
all: `gamelobby.js` defines its own `getShip(phpclass, faction)`, which answers with a BLUEPRINT.
`power.js` is loaded on both pages, so the walk early-outs on `gamedata.gamephase === -2` as well as
on an empty `shipsDocked` — either alone would do, and both are cheap.

**Where the number shows.** The Traveler's reactor icon already reads `getReactorPower`, so the
grant lands in the figure the player looks at with no display work at all. The explanation is a
client-computed line on the **Reactor** tooltip — *"Shared by docked ships: +1 of 4 pooled from 1
ship"* — modelled on `shadowBombAvailable`,
which exists for the same reason (`system.data` is built server-side per blueprint and cannot carry
a figure that moves per click). ⭐ It is drawn whenever there is a donor **even when the grant is
0**: "3 pooled, +0" is precisely what a player who has powered one system down needs to see.
⚠️ It first carried the tail *"(4 shared = 1 gained; advisory, not server-checked)"*, which §3.16
asked for and **the user trimmed the same day** — so the rule is stated on the Docking Bay's own
`Special` text and on the faction page, and the advisory caveat is stated only here. The bay's
`Special` text can stay server-side because, unlike the figure, the rule does not move.

**The play-test pass — two findings, 2026-09-12. The first was FIXED (§3.16b); the second is
recorded and deliberately not built.**

**(1) The OPPONENT did not see the grant, and it was a masking consequence rather than a bug.**
`DockingBay::stripForJson` masks `shipsDocked` to `[]` for anyone outside the owning team —
`isDisclosedToCurrentViewer`, the **private-logistics** gate that also hides ammo loads and hangar
contents, and which does not open with age (only with the post-mortem). So the opponent's client
finds no donors, contributes 0, and renders the Traveler's balance without the grant while the owner
renders it with. Everything else lines up: the docked ship's own row IS in the opponent's payload
(`removed: true` is published unconditionally; only hyperspace reinforcements are dropped from the
list), and its power rows only reach the database at commit — so an opponent's view of any enemy's
power is inherently post-commit, which is why the discrepancy shows up exactly when the user saw it.

⭐ **THE WIDER FACT, which is the reason this is worth writing down:** every derived power figure in
Fiery Void is computed by the VIEWER'S OWN CLIENT from the data that viewer is allowed to see. Until
now every input to that computation was public, so the answer was the same for everybody. This is the
first power figure with a MASKED input, and nothing in the code warns that masking an input silently
changes a number two players are meant to agree on. Any future figure derived from private logistics
has the same property.

The option space is exactly two, and neither is free:

| Fix | Cost |
|---|---|
| Publish the docked ship **ids** to every viewer, keeping `boxes`/`dockTurn`/`phpclass` masked (~3 lines in `stripForJson`). The opponent's client then runs the *same* `getDockedPowerSummary` and gets the *same* number — no duplication, no drift, and correctly post-commit for free. | It discloses the ASSOCIATION and the bay's occupancy count. The opponent already has the docked unit's full sheet and knows it is `removed`; what they gain is *which* hull holds it (nothing, against a fleet with one Traveler; something against two) and therefore the bay's remaining capacity. That is a deliberate Stage 16 information rule, so it is the user's call and not a refactor. |
| Compute the grant **server-side** and publish the integer. | A second implementation of the power balance. ⚠️ `EdfExposure::getMaxAvailablePower` is already a partial mirror ("the server-side mirror of the client's `getReactorPower` at maximum shed — keep the two in step"), but it is a CEILING: it ignores per-turn offline rows, boost cost and overload draw, so it cannot answer this. A full mirror would then have to agree with the owner's live client figure at every moment, or the owner sees one number before commit and another after. |

**The user chose the first (2026-09-12) — built as §3.16b below.**

**(2) NOT BUILT — power management for a unit still in HYPERSPACE is one step away, and the step is
not in `power.js`.** `isPowerManageable` already answers true for a reinforcement that has not arrived (it
is not a wreck), and driving `SystemPowerSettings`'s handlers against one switches its systems off
correctly. Two things stop the click reaching them:

* `PhaseStrategy.onSystemClicked` opens with `if (shipManager.getTurnDeployed(ship) > gamedata.turn)
  return;` — and `getTurnDeployed` is the 999 sentinel for a unit in hyperspace, so the system info
  menu never opens at all. ⚠️ It is also the SURRENDER test (999 again), so relaxing it needs the
  narrower predicate, not a widened comparison.
* `SystemIcon.clickSystem`'s `stowed` divert is `ship.removed && !isDestroyedByDamage(ship)`, and a
  hyperspace unit is not `removed` — so it would fall through into the select/target workflow it has
  no business in, exactly what the divert exists to prevent for a docked ship.

The window itself already opens (`fleetListManager.isOffBoardButOurs` returns true for a hyperspace
reinforcement), so the feature is: one named "off-board but ours" predicate shared by those two
sites. Not built — the user asked for it "at a later point" — and recorded here because Stage 18 is
what made the power half of it free.

---

### 3.16b As built — the opponent's view of the grant (Stage 18 follow-up, 2026-09-12)

**D47 — the bay discloses the IDS of the ships aboard, and only the ids, to a viewer outside the
owning team.** The user's ruling on the choice above. It is the option that cannot drift: the
opponent's client runs the SAME `getDockedPowerSummary` on the SAME docked ships and reaches the
SAME number, which no server-side recomputation could promise.

⚠️⚠️ **A SEPARATE KEY, NOT A PRUNED `shipsDocked`, and that is the whole safety of the change.**
Until now an outside viewer's `shipsDocked` was ALWAYS `[]`, so no client consumer has ever met a
partial entry — and `HangarShared`'s capacity maths, the fire-menu dock dialogs, `SelfRepairList`
and `fleetListManager.carrierHolding` all read `boxes`, `phpclass` or `dockTurn` off these rows.
Handing them id-only entries would have been a silent `NaN` in four places. So the ids ride
`sharesDockedPowerIds`, a bare integer list that exactly one function reads;
`getDockedPowerSummary` prefers the real list whenever it has one, so an owner can never
double-count.

⚠️ **Only on a bay that actually shares power.** An ordinary Docking Bay fitted to some other hull
later stays fully masked — the disclosure is bought by the rule that needs it and by nothing else.

⚠️ **`hideDeploymentDocks` still drops anything that docked THIS turn**, and must: concealing the
dock EVENT is a stronger mask than this one (it is *where a unit went*, not what a reactor reads).
⭐ In practice it costs nothing, and the reason is a timing fact worth keeping: the dock resolves in
the Critical phase, AFTER that turn's Initial Orders — so by the next turn's orders, which is when
the figure is actually managed, `dockTurn` is in the past and the entry is disclosed here. The only
window where the two players can still differ is the back half of the docking turn itself, when
nobody is allocating power.

⭐ **What the opponent gains, precisely:** the association and the bay's occupancy count. They
already had the docked unit's full sheet (only hyperspace reinforcements are dropped from the ship
list) and already knew it was `removed`. The post-mortem was already total disclosure, and stays so
— with the game over, `isDisclosedToCurrentViewer` returns the real list and the id key is not
emitted at all.

**Verification.** 19 checks in a server harness over the REAL `Traveler` and its REAL bay, fatal on
the pre-change tree: the owner and a teammate get the full list and no id key; the opponent gets an
EMPTY `shipsDocked` plus bare integer ids, no `dockTurn` and no per-entry `boxes` anywhere in the
payload, and no queued dock/launch orders; a non-sharing bay discloses neither ids nor flag; an
empty sharing bay emits no key; the post-mortem hands over the real list; and a build with NO viewer
context — static ship generation — emits no id key, which is what keeps it out of the blueprints.
Plus 9 client checks: the opponent reaching the owner's figure from the id list, two donors still
summed-then-floored, the owner preferring `shipsDocked` and never counting a ship twice, and every
per-ship exclusion (flight, unresolvable id, destroyed bay, missing flag) still applying to an entry
that arrived as an id. ⭐ Replay corpus unchanged at 121/13 with the same two additive keys — the new
one never appears, because the harness has no outside viewer.

**Files:** `baseSystems.php` (`DockingBay::stripForJson`), `power.js` (`getDockedPowerSummary`
reads either list).

---

**Files (Stage 18 proper):** `power.js` (the predicate, the four guards, the summary, the hook in `getReactorPower`),
`SystemInfo.js` (the tooltip line), `baseSystems.php` (`DockingBay::$sharesDockedPower`, its
`stripForJson` and its `Special` line), `ShipCompactor.php` (`$falseKeys`), `Traveler.php` (the flag
on the instance — the bay moved to a local variable, so no system id moved).

**Verification.** 134 checks green across three harnesses — 67 in a server-free harness over the REAL
`power.js`, 48 in a React harness, and 19 in a server harness over the real `Traveler` (§3.16b). The
React one bundles the whole `reactJs` tree, evaluates it at module scope, renders `SystemInfo` to
static markup and drives `SystemPowerSettings`'s own handlers. All three are fatal on the tree they
were written against (21/37, 23/9 and 14/5). ⭐ The React run reproduces the user's report exactly: on
the old tree *"the menu OPENS for a DOCKED hull"* passes while *"Off actually switches it off"*
fails. ⭐ A second play-test pass the same day added 16 more: the WHOLE click path for a stowed
WEAPON (a real `SystemIcon.clickSystem` call relaying exactly one `SystemClicked` and no
targeting event, then overcharge and stop-overcharge taken through the menu), the right-click
“all systems of this name” pair on a docked hull, and the REINFORCEMENT boundary — a unit still in
hyperspace is ALREADY power-manageable, so what stops it is the click path and not the power model
(see finding (2) of the play-test pass below). `checkShipData.php` PASS, 0 new against 237. A **2,727-hull differential** over 58,548 facts
(every system's `sharesDockedPower`, `isDockingBay`, `powerReq`, `output`, `outputMod` and
`boostable`, plus every hangar's `Special` text) moved exactly **two lines**, both `Traveler|sys11`:
the flag and the added sentence. Replay corpus 121/13 against the un-re-recorded baseline, every
diff one of two additive keys (`servicesDockedUnits` from Stage 17, `sharesDockedPower` from this
one) and no behavioural drift; autoload unchanged.

### 3.17 The Walker jump drive — leaving slowly — **BUILT 2026-09-11 (Stage 15), rules 1 and 2 WITHDRAWN the same day — see §3.17b**

*(Written 2026-09-08 as the "Traveler" drive and Stage 18. Promoted to Stage 15 on 2026-09-11 and
widened to every Walker hull by D32. The design below is what was built, except where §3.17a says
otherwise.)*

> *"Some jump drives are even more advanced. In addition to the effects of advanced jump drives,
> traveler drives deliver a coruscating field of crackling lightning, fading away into the center of
> the resulting jump point. This operates as another advanced jump drive, except that the ship is
> permitted to fire weapons on the same turn that it departs the map."*

~~`class TravelerJumpDrive extends JumpEngine`. A new phpclass, never a flag on `JumpEngine` — system
ids are construction order and a variant needs its own class (trap 7).~~ **SUPERSEDED by D32
(2026-09-11): a flag, `JumpEngine::markWalker()`, on every Walker hull's engine**, the way a Scanner
is marked Advanced. The trap-7 worry was misplaced: trap 7 is about REORDERING a constructor, and a
flag set after construction reorders nothing, so no system id moves. `markLegacy()` and `markGate()`
had already set the precedent. See §3.17a.

Three rules, and each has exactly one seam.

**1. The unit stays until the end of the turn.** Today `Movement::resolveJumpOuts` runs as the
**first** statement of `MovementGamePhase::advance`
([MovementGamePhase.php:23](source/server/Phase/MovementGamePhase.php#L23)) and destroys the primary
structure there and then — and the comment says why it is first: *"a unit that has left then reads
isDestroyed() for the rest of advance(), so it gets no dummy 'end' move, no post-move stealth check
and holds no Pre-Firing slot open"*.

A Walker unit needs the **opposite** of all three. So the deferral is a positive decision taken at
that call site, not an omission: `resolveJumpOuts` skips a unit whose jump engine is a Traveler
drive, records the pending departure, and `applyJumpOut` runs for it at the **end of the Firing
phase** instead — where `JumpEngine::doHyperspaceJump` already runs
([firing.php:2116](source/server/handlers/firing.php#L2116)), which puts both departure paths in the
same place and keeps the combat log's ordering sane.

⚠️ `applyJumpOut` is *"`doHyperspaceJump` MINUS THE FAILURE ROLL — the risk was taken when the
vortex was opened, not when it is used"*
([movement.php:461](source/server/handlers/movement.php#L461)). That stays true; rule 3 below is
about the OPENER's roll, not the user's.

**2. Enemies cannot target it during Pre-Firing or Firing.** This is §3.10c's `isTargetableBy` hook,
with one ⭐ difference: **this one needs a real server refusal.** The Moon rule is client-only —
`weaponManager` refuses the click and the server never asks — which is fine for a wasted shot at an
indestructible probe and is not fine for a rule a player will plan around. A fire order naming a
departing Walker is rejected in the submit path and in `Firing::prepareFiring`.

- ⚠️ It must **not** stop the Walker shooting; that is the whole point of the rule.
- ⚠️ It must **not** stop collateral, area effects or ramming reaching the hex it is standing in.
- The departing state is **public** — the enemy has to be able to see why the click is refused — so
  it rides `stripForJson` as a plain flag, not a per-viewer one. That is a deliberate call and the
  opposite of the SCT's `hideNotesFromEnemies`.

**3. No destruction roll while the drive is in use.**

> *"Additionally, the ship suffers no chance of being destroyed if its jump system is damaged during
> a turn that the drive is in use, though jump-out will be cancelled if the drive is completely
> destroyed as normal."*

⭐ **The hook already exists in exactly the right shape, facing the other way.**
`JumpEngine::getCertainJumpFailureNote($ship, $gamedata)`
([baseSystems.php:8198](source/server/model/systems/baseSystems.php#L8198)) returns a log line when
a jump is doomed regardless of the dice and null otherwise — the Shadow Phasing Drive is its only
user. Add the twin, `isJumpFailureImmune($ship, $gamedata)`, asked in the same place.

⚠️⚠️ **STILL CONSUME THE d100.** The existing hook is asked *before* the roll rather than instead of
it, and the comment says why: *"Dice draws are part of the game's random sequence and a rule that
silently skipped one would make otherwise-identical games diverge."* An immunity that returns early
would break the replay harness on every unrelated game in the corpus that happens to contain a
Walker.

⚠️⚠️ **THERE ARE THREE FAILURE SITES, NOT ONE.**

| Site | What it does |
|---|---|
| `doHyperspaceJump` ([8095](source/server/model/systems/baseSystems.php#L8095)) | the legacy boost-to-jump path |
| `rollVortexJumpFailure` ([8325](source/server/model/systems/baseSystems.php#L8325)) | the per-turn roll while a vortex is open, from `criticalPhaseEffects` |
| `openVortex` ([7460](source/server/model/systems/baseSystems.php#L7460)) | computes the same percentage **for the log line only** |

The immunity has to reach all three or it is half-applied and the log will contradict the outcome.
Note that Ancients already halve the chance at two of them (`if ($ship->factionAge >= 3)`), so the
Walker rule is "halved, then zeroed while in use", not a first exception.

*"Cancelled if the drive is completely destroyed as normal"* is already the
`getRemainingHealth() <= 0` early return at
[8111](source/server/model/systems/baseSystems.php#L8111). Nothing to add.

**Exit criterion:** a Traveler that enters a vortex is still on the board through Pre-Firing and
Firing, fires normally, and is gone at the end of the turn; every enemy fire order naming it is
refused on both sides of the wire; its drive rolls no failure while in use but a completely
destroyed drive still cancels the jump; the d100 is drawn either way; and the replay corpus is
byte-identical on games without a Walker.

### 3.17b REWORKED — Stage 15, 2026-09-11 (supersedes §3.17 rules 1–2 and all of §3.17a)

The user found the first build rested on a misreading of the Ancient jump rules. The rule as it
actually stands (B5W):

> *"Each Ancient One has its own method for traveling into hyperspace, but these are all listed on the
> control sheet as a 'special jump drive' for consistency. The drive affects only the Ancient's ship
> and nothing else (unless otherwise noted). As with a phasing drive, the jump drive is initiated at the
> start of the turn and takes the ship out of (or into) the scenario by the turn's end, though the
> vessel will be vulnerable to weapons fire in the interim. Except as noted, the ship may not fire
> weapons while jumping into/out of a scenario. If the jump drive itself is damaged while the ship is
> departing/arriving, it has only half the usual chance of detonating. Ancient jump drives cannot be
> affected by vortex disruptors."*

User rulings (2026-09-11): Kirishiac, Mindriders, Torvalus, Triad, **Thirdspace** and Walkers get
legacy drives like the Shadow Phasing Drive, since they form no jump points; all of them (and the
Shadows) are immune to the Vortex Disruptor, which now reaches only the **Vorlons and The System**; none
may fire on the turn they jump — **except the Walkers, whose whole benefit is that they CAN, plus zero
chance of drive failure**. The **Mapmaker probes go legacy too** (reversing §3.12's "works normally").
There is no deferred departure and no untargetability: a jumping Ancient is on the board, and can be
shot, until it leaves at the end of Firing like any boost-jumper.

**REVERTED** (backup patch in the session scratchpad, `stage15_backup/stage15_full.patch`):
`Movement::getDeferredJumpOutVortex` / `isOutOfReach` / `resolveDeferredJumpOuts` / `departWithAttached`
/ `writeCancelledJumpOutLog`, the `resolveJumpOuts` skip, `JumpEngine::getUnitWalkerDrive`,
`BaseShip::isTargetableBy`'s third argument, `Firing::isOrderAtUntargetableUnit` /
`withdrawFireAtDepartingUnits` (⚠️ which also takes the Energy Draining Mine orb's server-side refusal
back to client-only, as it was before Stage 15), the Disruptor's "waiting Walker" branch,
`TacGamedata::hasLeftThroughVortex`'s Walker exemption, and every client twin (`carriesWalkerJumpDrive`,
`isDeferredJumpOut`, the shooter-aware `isTargetable`, and the fleet-list / ajaxInterface / banner /
`shouldBeHidden` sites). A committed `jumpout` row means "gone at the end of Movement" again.

**BUILT:**

- **`JumpEngine::markAncient()`** = `markLegacy()` + protected `$ancientJump`. One-liner
  `(new JumpEngine(..))->markAncient()` in 36 ship files (Kirishiac 7, Mindriders 4, Torvalus 6, Triad
  11, Thirdspace 8); `PhasingDrive`'s constructor calls it instead of `markLegacy()`. **`markWalker()`**
  now = `markAncient()` + `$walkerJump`, on the six hulls and on every Mapmaker probe. A flag set after
  construction moves no positional system id. `stripForJson` sends `ancientJump` / `walkerJump` only
  when set; the legacy tooltip gained Ancient and Walker variants.
- **No fire on the jump turn** (`JumpEngine::forbidsFireWhileJumping` = Ancient and not Walker).
  Enforced at RESOLUTION by `Firing::withdrawFireFromJumpingUnits` (preparePreFiring + prepareFiring)
  and an `isJumpingUnarmed` skip in `automateIntercept`, which runs after prepareFiring. ⚠️ Not in
  `validateFireOrders`: the boost and the orders arrive in the same Initial Orders POST, and that path
  judges them against the DB copy, which has no power rows for this turn yet. Rams, log orders and the
  selfIntercept marker are left alone. Client: `JumpEngine.onBoostIncrease` (called by
  `power.clickPlus`) withdraws the unit's orders when the jump is set, and `weaponManager.selectWeapon`
  refuses new ones via `shipManager.movement.isJumpFireForbidden`.
- ⭐ **`JumpEngine::getUnitJumpingEngine($unit, $turn)`** — the one "who is boosting to jump" reader,
  shared by the end-of-Fire boost sweep and the withdrawal, and it **descends into every craft of a
  flight** (the boost sits on whichever probe the player clicked). The sweep used to walk
  `$ship->systems` and never saw a flight at all.
- **`doHyperspaceJump` on a flight** — it had a null-structure fatal; now a flight leaves craft by craft
  (Movement::applyJumpOut's shape) with the CV note on the sample fighter.
- ⚠️ **The half-chance was missing on the boost path.** `openVortex` and `rollVortexJumpFailure` have
  always halved for factionAge 3+; `doHyperspaceJump` never did. It does now, before the Walker zeroing
  and with no return before the d100. This also changes the % a Shadow boost-jump quotes, which is the rule.
- **Vortex Disruptor:** `isImmuneToDisruption` = carries an Ancient drive, or is factionAge 3+ and not in
  `DISRUPTABLE_ANCIENT_FACTIONS` ('Vorlon Empire', 'The System'). Spared before the escape roll (no die
  drawn). A doorway an Ancient drive holds does not collapse; a phase-in one reads as an empty-hex shot
  so the log cannot confirm a hidden arrival.

Harnesses rewritten: `tests/replay/walkersStage15Harness.php` (73) and
`walkersStage15ClientHarness.js` (24), each group with a control that fails the other way. Gate:
autoload current (no new class), `checkShipData.php` PASS with 0 new findings (237 baselined). Replay
harness 80 passed / 42 failed: 34 of them differ ONLY by `ancientJump: added (true)` on an Ancient
drive's payload - the intended additive key - and the other 8 (3671, 3676, 4249, 4256, 4297, 4303,
4325, 4328) fail IDENTICALLY with `source/` stashed once those lines are removed, so they are
pre-existing drift, not this change.

**Open for the user:** arriving is also "jumping into a scenario", and whether an arriving Ancient may
fire on its arrival turn was not touched. The Initial Orders commit summary lists ships jumping to
hyperspace but still skips flights, so a jumping Mapmaker flight is not named there.

#### 3.17c Refinements after play (2026-09-11, games 4347 and 4348)

1. **A Mapmaker flight's drives boost as one** (4347: one probe boosted, the others read unboosted).
   Client `JumpEngine.onBoostIncrease` / `onBoostDecrease` → `mirrorFlightBoost`, the Stiletto Shading
   Field's "set one, set all" shape: a real type-2 power row on every live sibling engine, so each
   travels with the ordinary submit, and "No" on any probe clears all of them. The server needed
   nothing - `getUnitJumpingEngine` already takes the flight off any boosted craft.
2. **A jumping non-Walker Ancient's Initial Orders are never written.**
   `InitialOrdersGamePhase::dropFireOfJumpingShip` filters them out of `process()` before
   `validateFireOrders`, reading the POST-side ship's own boost rows (the DB copy has none yet). So no
   ballistic shows through Movement and Firing. Rams, log orders and older turns are kept;
   `withdrawFireFromJumpingUnits` stays as the resolution-time backstop.
3. **The Energy Draining Field no longer drains terrain** - a `isTerrain()` skip in
   `EdfExposure::resolve`, the only site that acts on the unit standing in the field (the hit penalty
   and the dampened-explosion rules are properties of the HEX).
4. ⭐ **A legacy exit declaration vanished in the Firing phase** - `TacGamedata::hideSystemFireOrders`
   strips every current-turn phase-3 order whose weapon has `$ballistic == false`, and `markLegacy()`
   sets exactly that on every Shadow / Ancient / BSG / Star Wars drive, so the owner's `jumpexit` order
   was taken for a direct-fire order. `jumpexit` is now exempt. (An ordinary exit survived only because
   its engine is still `ballistic`.) The enemy's copy was never affected: `republishFormingExits` runs
   before the strip.
5. **One blue "Jump Point" per hex.** The exit marker says "Jump Point" in blue for every drive (no
   more "Jump Point Forming" / "Reinforcements"), the pre-placed wave's marker is blue text too, and
   `generateExitHexes` and `generateReinforcementHexes` share ONE claim set with the exit sweep first,
   so a replay no longer stacks two labels on one hex. `formingExits[].phase` is still published but
   nothing reads it.
6. **"Jumping to Hyperspace"** tooltip line and ship-window banner now also show for any LEGACY drive set
   to jump this turn (`shipManager.isJumpingToHyperspace`, via `getJumpingOutEngine` +
   `isLegacyJumpEngine`), until the ship has gone. No leak: an enemy's power rows are stripped during
   Initial Orders and public from Movement on.

Harnesses: server 81, client 37 (both extended for all six). Existing harnesses green: reinforcements
6/7/8/9 (server and client), legacy recharge, Vortex Disruptor (both), Stage 6.

7. ⚠️ **Item 1 was still broken in play (4347), by TWO bugs, neither in the mirror itself.**
   (a) Client `JumpEngine.getOwningUnit` asked for `gamedata.getShipById` behind a typeof guard - the
   CLIENT HAS NO SUCH METHOD (only `getShip`; `getShipById` is the server's name) - so it fell back to
   the craft on every real client, and every flight rule keyed off the owning unit, Stage 13's included,
   answered "no". The Stage 15 client harness had stubbed `getShipById`, which is what hid it; it now
   stubs `getShip` only. (b) `Manager::getShipsFromJSON` handed each FIGHTER system its whole power LIST
   as one `setPower()` entry, in a duplicated block, so a POST-side fighter's `$power` held nested arrays.
   `DBManager::submitPower` flattened them quietly; `dropFireOfJumpingShip` → `isOverloading` fataled
   ("property turn on array"). Now one entry per call.
8. **A legacy-drive opener's manifest** (Ancient drives, Phasing Drive, BSG / Star Wars / Trek drives -
   they open no jump point, so nothing rides THROUGH them) **holds only fighter flights that fit its
   hangars, and they arrive docked** (user ruling 2026-09-11).
   - Client: `shipManager.movement.isLegacyOpener` / `getLegacyRideHost`;
     `DeploymentDock.planFlightsIntoCarrier` (the real packer, non-mutating, via an `extraReserved` box
     map); the manifest dialog lists only fighters that fit and greys rows as the hangars fill;
     `autoPlaceArrivingReinforcements` queues the deploy-start dock instead of placing, and
     `forcedDeployDock` stops the dock dialog, the DOCK button and un-queue undoing it.
   - Server: `JumpEngine::isLegacyOpener` / `getLegacyRideHost`; `InitialOrdersGamePhase::legacyBerthFits`
     (cumulative across the pass, same bay rules as HangarOps) in `persistManifest`; and
     `validateReinforcementArrival` refuses a map position for such a flight. The Deployment dock is still
     the authority - a flight that fails it stays unplaced and goes back to hyperspace, nothing spent.
   - ⚠️ Harness trap: `ReflectionMethod::invoke` passes by VALUE, so a `&$reserved` argument never filled
     and "20 flights fit in 36 boxes"; use `invokeArgs` with a real reference.

Harnesses after 7-8: server 92, client 49. Replay 134/1 (4347 only, identical on a clean tree).

### 3.17a As built — Stage 15, 2026-09-11 — ⚠️ SUPERSEDED by §3.17b; kept as the record of the first build

Harnesses: `tests/replay/walkersStage15Harness.php` (73) and
`tests/replay/walkersStage15ClientHarness.js` (34), both **fatal on the pre-edit tree**.
`checkShipData.php` PASS, 0 new findings (237 baselined), identical to a stashed tree; autoload
unchanged (no new class). Replay harness 114 passed / 8 failed, **byte-identical with timings
normalised** to the same run with `source/` stashed. The 8 are the Stage 14 set, and the corpus holds
no Walker jump-out, so that run proves only that nothing ELSE moved.

**THE MARK (D32).** `JumpEngine::markWalker()` sets a protected `$walkerJump`, called in the six Walker
hull files straight after construction (Traveler, Wanderer, Waymarker, Pathfinder, Guideship, Scribe),
exactly as `$scanner->markAdvanced()` is two lines above it. Protected, so no static blueprint grows a
key; `stripForJson` sends `walkerJump: true` on a Walker drive only; `setSystemDataWindow` swaps the
failure sentence for the Walker rule. **The Mapmaker flight is NOT marked** - §3.12's ruling is that its
engine *"works normally"* - but every helper below goes through `getUnitJumpEngines` (and the client's
walks descend into craft), so marking it later is one line in `MapmakerProbes::populate()`.

**ONE PREDICATE, AND NO STORED STATE.** `Movement::getDeferredJumpOutVortex($ship, $gamedata)` - "this
unit entered a jump point this turn, legally, carrying a working Walker drive" - mirrored by
`shipManager.movement.isDeferredJumpOut(ship)`. No note and no flag: the persisted `jumpout` movement
order IS the record, so the answer is re-derived identically on every load, across the double gamedata
load and in replay. "Working" is `JumpEngine::getUnitWalkerDrive()`, read off CURRENT state on purpose:
- a Walker whose drive was lost on an EARLIER turn is not deferred - it leaves at the end of Movement
  like any engineless ship (§2.5 of the jump-points plan: any unit may use any open vortex);
- one whose drive is destroyed WHILE it waits stops answering, becomes an ordinary target for the rest
  of the turn, and has its jump cancelled at the end of Firing - *"cancelled if the drive is completely
  destroyed as normal"*.

**RULE 1 - THE DEFERRAL.** `Movement::resolveJumpOuts` skips a unit `getUnitWalkerDrive` answers for, so
it keeps its dummy `end` move, its post-move checks and its Pre-Firing slot. `Movement::resolveDeferredJumpOuts`
runs at the end of `Firing::fireWeapons` (after `createFailedAttachRamOrders`, before the boost sweep)
and takes it out through the same `applyJumpOut` - shared with its attached units through the new
`departWithAttached` - so the records are the same three: the `HyperspaceJump` log order, the `jumped`
CV note, the structure destroyed as a jump. It runs before `Criticals::setCriticals`, so a departed
unit rolls no criticals and no jump failure. A cancelled jump writes a `JumpVortex` log line.

**RULE 2 - THE REFUSAL, ON BOTH SIDES OF THE WIRE.** `BaseShip::isTargetableBy($shooter, $turn, $gamedata)`
gained the third argument; given the board it asks `Movement::isOutOfReach`, which refuses the ENEMY
only - the Walker's own side may still support it - and treats "no shooter" as refused.
- **Submit:** `Firing::validateFireOrders` rejects and detaches any order naming an untargetable unit,
  which also closes §3.10c's owed server half for the Energy Draining Mine orb.
- **Resolution:** `Firing::withdrawFireAtDepartingUnits`, from `preparePreFiring` and `prepareFiring`. It
  catches what the submit path cannot see coming - a ballistic declared in Initial Orders, before
  anyone knew the unit would be in a jump point - and wastes it, exactly as a ballistic at any departed
  ship is wasted. Gated on a departing set built from one movement scan per ship, empty in virtually
  every game.
- **Exempt everywhere:** intercept orders (their `targetid` names a fire order), hex-targeted orders
  (area effects on the hex stand), log-only orders, and RAMS - §3.17's *"collisions reach whatever is in
  the hex"*. ⚠️ The client refuses a deliberate ram CLICK like any other targeting, because
  `weaponManager.targetShip` asks before it knows the weapon; the server lets ram orders through so
  automatic collisions still resolve.
- **Client:** `shipManager.isTargetable(ship, shooter)` at all four call sites - weaponManager's tooltip
  line and `targetShip`, and both tooltip menus (the EW one is also the late-EW window).

**RULE 3 - NO FAILURE ROLL.** `JumpEngine::isJumpFailureImmune($ship, $gamedata)` (protected, answers
`$walkerJump`) at all three sites - `doHyperspaceJump`, `rollVortexJumpFailure`, `openVortex` (trap 33).
Each ZEROES its percentage rather than returning, so the log quotes the 0% that applied and every d100
is still drawn (trap 34; trap 35 for how that was proved). A roll of 1-100 against 0 always holds.
⚠️ The EDJD must override the hook (§3.18).

⚠️ **FOUR FINDINGS WORTH CARRYING.**

1. **A COMMITTED JUMP-OUT MEANT "GONE" TO FOUR CLIENT SITES** (trap 36): `shouldBeHidden`, the fleet row,
   `getJumpedDockedFlightIds` and ajaxInterface's docked-LCV walk - plus their SERVER twin,
   `TacGamedata::hasLeftThroughVortex`, which would have painted a waiting Walker carrier's docked
   flights `jumpedWithCarrier`. Left alone, the enemy would have stared at an empty hex that was
   still shooting at them. ⭐ The fix is TWO predicates, not one:
   `carriesWalkerJumpDrive` (any Walker drive, working or not) for everything PRESENTATIONAL, because
   the server keeps such a unit on the board after its commit either way - waiting, or cancelled - and
   `isDeferredJumpOut` (committed + working drive) for TARGETING and the banner. With one predicate, a
   drive destroyed mid-turn would have hidden a ship the server was keeping.
2. **THE VORTEX DISRUPTOR WOULD HAVE BEEN DODGED.** `getDeparturesThrough` searched wreckage only
   (`isDestroyed && hasJumpedToHyperspace`), and a waiting Walker is neither - so the collapse would
   have skipped it and it would then have left anyway at the end of Firing, immune to the one weapon
   built to stop a jump. `hasLeftThroughVortex` now counts a deferred departer as inside the rift, and
   its attached units with it. The Ancient escape roll applies as to anyone.
3. **TRAP 34's "SAME RANDOM SEQUENCE" IS NOT THIS CODEBASE'S DICE** (trap 35): `Dice::d` is `random_int`.
   The draw is kept by construction and asserted by the shape of the two method bodies.
4. **THE TRAP-7 OBJECTION TO A FLAG WAS WRONG** - a flag set after construction reorders nothing - so
   the user's mark-it-like-a-Scanner instinct was also the cheaper build: no class, no autoload
   regeneration (which on live needs the maintenance gate), no id shift, and `phpclass` stays
   `JumpEngine` for every game already in flight.

**Rulings taken without asking, to confirm:** the Mapmaker stays unmarked; an Initial Orders ballistic
at a departing Walker is wasted; a deliberate ram click at one is refused while server-side ram orders
pass.

**§3.18's "plain traveler drive" (the ½ power-turn contributor that may not initiate) now means ANY
Walker drive** - `isWalkerJump()` - since D32 made that the whole population.

---

### 3.18 Extra-Dimensional Jump Drive — abduction — **BUILT 2026-09-12 (Stage 20), as built in §3.18a** (renumbered 2026-09-12 when the Waymarker's two-turn procedure landed as Stage 19)

⚠️ The design below predates §3.17b, which made every Walker drive a LEGACY boost-to-jump drive. Where
the two disagree - the subclass, power-turns as boost levels, friendly use, the `isJumpFailureImmune`
override - §3.18a and D53-D56 are what was built.

`class ExtraDimensionalJumpDrive extends JumpEngine`, calling `markWalker()` in its constructor (D32 -
there is no `TravelerJumpDrive` to extend, and a subclass is right here because the EDJD genuinely adds
behaviour, not just a flag). Every §3.17 rule applies unchanged,
plus the ability to drag an enemy unit into hyperspace over several consecutive turns. **This is the
largest single item left in the plan** and it should land last.

**The state, and why it needs no schema change (D22).** An abduction is a running total of
*power-turns* against one named target, which must be **consecutive**. That is one
`IndividualNote` per turn on the EDJD: `notekey = 'EDJD'`, value `<targetId>:<powerTurns>:<cost>`.
"Consecutive" is then *"is there a note for turn N−1 naming the same target?"* — and a gap needs no
cleanup at all, because the chain is rebuilt from the notes on every load. Same discipline as the
Energy Draining Mine's lifetime, and the same reason: nothing to persist, nothing to get wrong on a
reload.

⚠️ `notekey` and `notekey_human` are `varchar(40)` and an overflow is a fatal that aborts the whole
submission (trap 4). The wide column is the **value**; keep the keys short.

**The two conditions, checked every turn.**

**1. The target ended its movement in an Energy Draining Field connected to the ship's own.**
`TacGamedata::$edfHexes` is already keyed `'q,r' => ['teams' => [teamId => true]]`, published to the
client and rebuilt every load (§2.1) — so "is the target standing in a field of my team" is one
lookup, and *"extended through ED Mines or other ships"* comes free, because the EDN's corridors and
filled areas and the EDM orbs' seven-hex discs are already IN that map (Stages 6 and 7).

⚠️ **"Connected" is the half the map cannot answer.** Overlapping fields collapse into one hex entry
deliberately — that IS the overlap rule — so the map does not record which source covers a hex. Two
options:

  - **(a)** flood-fill the team's own hexes outward from the EDJD ship's own field and require the
    target's hex to be in that connected component. Pure post-processing of the published map, no
    new publication, and it mirrors trivially on the client.
  - **(b)** record a source id per hex, widening `$edfHexes` and everything that reads it.

**Take (a).** It costs one traversal of a map that already exists, and (b) would change a payload
five things consume.

**2. More OEW at the target than the target's DEW,** *"including defensive but not offensive ELINT
support"* — so the comparison is `$shooter->getOEW($target, $turn)` against
`$target->getDEW($turn) + EW::getSupportedDEW(...) + EW::getBlanketDEW(...)`.
⚠️ **Not** `+ EW::getSupportedOEW(...)` on the attacker's side; the rules exclude it by name and the
helper sits two lines away from the ones that are included
([EW.php:97](source/server/handlers/EW.php#L97)).

**Power-turns.** *"A single power-turn is achieved by applying normal jump engine power (over the
standard norm) for an entire turn. Two power-turns are achieved by applying normal jump engine power
for two turns or by applying double power for a single turn."*

⭐ That is boost levels, and **`boostEfficiency` is the EXTRA power one level costs, not a flag**
(trap 19) — the exact correction Stage 5 had to make to the variable EDF, which had shipped its
boost for free. Set it from `$powerReq` so double power really is double; one boost level is one
power-turn per turn, two levels are two, and `maxBoostLevel` is the ceiling.

**The cost.**

| Target | Power-turns required |
|---|---|
| Any unit (shuttle up to Enormous) | `ceil(rammingFactor / 50)` |
| …with advanced armour or better | `ceil(rammingFactor / 10)` |
| Asteroid / Moon / Planetoid | `10 × radius³` — **never built**. Terrain became abductable on 2026-09-20 (D66, §3.18b) and pays the ordinary `ceil(rammingFactor / 50)` instead; FV terrain has no radius property to put in this formula. |

`BaseShip::getRammingFactor()` ([ShipClasses.php:4324](source/server/model/ships/ShipClasses.php#L4324))
and `$this->advancedArmor` answer both rows. Docked or otherwise connected units add their ramming
factors together — `$ship->attached` is the accessor, and the attached-movement mirror rows are all
type `attached`, so do not try to find them by movement.

⚠️⚠️ **THE RAMMING FACTOR MOVES WHILE THE ABDUCTION RUNS.** `getRammingFactor()` sums structure
**as of the previous turn** and shrinks as the target is shot, so a target that is being abducted
*and* shot gets cheaper every turn — the required total would change under the player mid-chain,
which is unexplainable at the table and unstable in a replay. **Lock the cost at the first
power-turn** and carry it in the note (that is the third field above). Q12 confirmed 2026-09-08: **locked**.

**Contributors.** *"Multiple vessels may contribute ... as long as at least one has been affecting
the target for the duration"*, and a plain traveler drive contributes at most **½ a power-turn** for
double power in a turn and **may not initiate**. So the note records the initiator, and a
contribution from a non-EDJD drive is worth 0.5 with no increase for more power.

**Completion.** `Movement::applyJumpOut($target, $gamedata, $pubNotes)` — ⭐ it already handles
hulls, flights and attached units, writes the `'jumped'` note with the combat value snapshotted
*before* the structure is destroyed, and files the `RammingAttack` log order. *"Removed to
Hyperspace for all intents and purposes as if it had left the game of its own accord via a jump
point"* is that function's exact contract. Nothing new.

**Friendly use.** *"Can also be used on friendly ships by allocating 1 EW point to a friendly, and
automatically jump them to Hyperspace."* One OEW point at a friendly unit, same call, no
power-turns, no chain.

⚠️⚠️⚠️ **THE ONE PLACE §3.17 AND §3.18 CONTRADICT EACH OTHER.**

> *"If the EDJD is damaged, critical rolls are performed every turn that the engine is active. The
> EDJD must check for jump engine detonation as any other damaged jump drive would. Note that the
> check must be performed every turn that the EDJD is active."*

A Traveler drive suffers **no** destruction chance while in use (§3.17 rule 3). An EDJD suffers one
**every turn** it is active. So `isJumpFailureImmune()` must return **false** while an abduction is
running, and that is the single most important interaction between the two stages — the hook,
`JumpEngine::isJumpFailureImmune()`, landed at Stage 15 answering `$walkerJump` - so §3.18's override is
owed the day the EDJD is built, or the drive that is supposed to be riskiest becomes
the safest in the game.

**Exit criterion:** an abduction accumulates only while both conditions hold, resets on a gap,
completes at exactly the locked cost, and removes the target through `applyJumpOut` with a `jumped`
record rather than a kill; a second EDJD can contribute and a plain traveler drive can contribute
½ and cannot initiate; a friendly jumps on one EW point; a damaged EDJD rolls for detonation on
every active turn while the same hull's ordinary jump-out does not; and the whole chain survives a
mid-abduction reload with no note sweep.

### 3.18a As built — Stage 20, 2026-09-12

**Who.** `JumpEngine::markExtraDimensional()` (= `markWalker()` + protected `$extraDimensional`) on the
Wanderer, Traveler, Waymarker and Guideship. A flag, not the subclass above, for D32's reasons.
`canJoinAbduction()` = any Walker drive on a HULL; a Pathfinder/Scribe contributes, a Mapmaker probe
cannot (no power allocation). The payload carries `extraDimensional` and `abductionMaxPower` (4 on an
EDJD, 2 on a supporting drive) only when they apply.

**The order (D53).** Type `ballistic`, damageclass `abduction`, firing mode = power level. Client:
`JumpEngine.canSelectForAbduction` lets `weaponManager.selectWeapon` past `autoFireOnly` and
`SystemIcon.clickSystem` past its not-ballistic Initial Orders clause; the drive sets
`hasSpecialTargeting` PER INSTANCE (keyed off `abductionMaxPower`), so `targetShip` diverts to
`doSpecialTargeting`, which refuses friendly and terrain, replaces a previous abduction, and starts an
EDJD at level 1 (a supporting drive at 2). `JumpEngineMenu` gained an Abduction panel: target, the
−/+ level, what it costs, the published cost or progress, and CANCEL. Server:
`Firing::getVortexDeclarationBlock` takes an `abduction` branch FIRST (the legacy refusal below it
would drop every Walker order) → `EdjdAbduction::getDeclarationBlock`: Walker hull drive, ballistic,
unit on board, drive intact/online/charged, enemy target on the board and targetable, level 1-4 (EDJD)
or exactly 2 (supporting), one abduction per unit per turn. The two CONDITIONS are not judged at submit.

**Power.** `JumpEngine.getAbductionPowerDraw` = level × `powerReq`, subtracted inside the online branch
of `shipManager.power.getReactorPower`, so the commit gate refuses a deficit. Walker reactors read 0
surplus, so paying means shutting weapons, fields or detectors down. Advisory, as D46.

**Resolution** - `EdjdAbduction::resolve`, at the end of `Firing::fireWeapons`, before the boost-jump
sweep, behind `TacGamedata::$abductionCapable`. Per target: every declaration on a WORKING drive (ship
on board, drive intact this turn, online, unit not jumping) contributes - 2 halves per level for an
EDJD, 1 half for a supporting drive. An EDJD QUALIFIES when (1) `isInConnectedField` - flood fill over
the team's `edfHexes`, SEEDED from the hexes `edfSources` credits to that ship (option (a); a Walker
whose own field is down is never connected) - and (2) `getOEW > getDEW + getSupportedDEW +
getBlanketDEW`. The chain continues if a prior anchor qualifies again (D54), otherwise restarts when
anything qualifies, otherwise nothing happens and the attempt is logged with its reason. At
`halves >= 2 × cost` the target leaves through `Movement::applyJumpOut` (made public), attached units
with it.

**State (D22).** One `EDJD` note per working declaration per turn:
`<targetId>:<halves>:<cost>:<since>:<anchor>`; a no-hold attempt writes `<targetId>:0:0:0:0`, which is
also what makes a second run of the same turn a no-op. `JumpEngine::onIndividualNotesLoaded` claims
them before the `jumped` fall-through and sets `TacGamedata::$abductionPresent` (reset in
`DBManager::getSystemDataForShips`). `EdjdAbduction::getChains` rebuilds total, locked cost, start turn
and the anchor intersection.

**Cost.** `ceil(RF / 50)`, `/ 10` for advanced armour, plus the RF of ATTACHED units only - **D57**:
nothing a target carries inside it counts; a flight is its live craft × per-craft RF. ⚠️ `getRammingFactor`
sums the MAX structure of sections standing as of last turn, so it moves only when a section is lost -
§3.18's "shrinks as it is shot" overstated it; the lock stays regardless.

**Detonation (D56).** `JumpEngine::rollAbductionJumpFailure` from `criticalPhaseEffects`, on an EDJD
with a declaration on a working drive: % boxes lost, halved for factionAge 3+, `JumpFailure` path.
`isJumpFailureImmune` is not overridden.

**Publication.** `TacGamedata::$abductions = {costs, chains}` (objects, never `[]`), copied by name in
`gamedata.js`. `costs` is exactly the figure a chain would lock that turn (D57 removed the bay contents
that had made it a preview).
`hideSystemFireOrders` exempts `abduction` from the Firing-phase strip (a legacy drive is not
ballistic - the `jumpexit` shape again). The map marker is purple "Abduction"; the target's tooltip
shows "Being abducted: x/y power-turns"; the order is dropped from the INCOMING list; the per-turn log
row is `Abduction` in `doShortLogText`; `JumpEngine.isSpentLocked` dims the drive and hides the
remove button outside Initial Orders.

**Verification.** `tests/replay/walkersStage20Harness.php` **89/0** and
`walkersStage20ClientHarness.js` **57/0**, each fatal on its stashed pre-stage tree. The server
harness drives the real `Firing::validateFireOrders`, the real resolve, a reload from the stored notes
alone, and the detonation roll statistically (287/600 at a 45% chance, 530/600 with the halving off,
0 on every control). `checkShipData.php` PASS, 0 new against 237; autoload +1 line (`EdjdAbduction`).
Replay 116 passed / 16 failed against 119 / 13 on a stashed tree: every added line is one of three
ADDITIVE keys (`abductions`, `extraDimensional`, `abductionMaxPower`) on Walker games, no movement,
to-hit, damage or masking drift - re-record to accept.

**Review revisions (user, 2026-09-13) - D57-D60.** The three observations the first build left open, and
the cost, were all ruled on the next day: bay contents out of the cost (D57); deactivating the drive
cancels the order on the client, and a destroyed or offline drive's declaration is logged as
CANCELLED at resolution (D58); no line of sight (D59); a ship-window banner (D60). Server harness
**96/0**, client **65/0** - each new client check proved by deleting its one edit and watching it fail.
Replay unchanged: 116/16, the same three additive keys.

**Play-test refinements (game 4352, user, 2026-09-13).** Six items, all UI except D61:
(1) the CANCEL button sizes to its label (`$wide`) instead of the 24px square of a −/+ step; (2) the
drive's icon lights ORANGE in Initial Orders while it holds an abduction, like a jump point
declaration - `SystemIcon.isFiring` asks `getAbductionOrder` because `hasFiringOrder` cannot see the
order on a legacy drive (trap 58 again); (3) the Abduction panel uses the Hyach purple palette, as its
own components in `JumpEngineMenu.js` rather than overrides of `activationMenu.js`'s blue ones; (4) the
panel shares the Power Settings panel's width - it was a fixed 190px, and is now `width: 100%` +
`min-width: 190px` + `contain: inline-size`, so it asks the shrink-to-fit tooltip for 190px, adds
nothing from its own text, and stretches to whatever the menu is (the vortex Maintain panel gets the
same fix); (5) **D61**, fighter flights refused on both sides; (6) the system info tooltip lists
"Abduction target: <name>" off the order itself (`SystemInfo.js`). Server harness **99/0**, client
**66/0** - the flight checks fail with the block removed (97/2, 65/1); the menu and tooltip rendered to
static markup, with the no-order case showing no line. No serialised field, so no replay run.
**Second pass, same day:** CANCEL left the purple marker and ballistic line on the map, because
`PhaseStrategy.onSystemDataChanged` (and `onShipTargeted`) only redraw the ballistic layer for a
`ballistic` / `hextarget` / `canSplitShots` system - a Walker drive is none of them (trap 58 once more);
both gates now also admit `abductionMaxPower`. And **D62**, a held abduction continues by itself.
Server harness **104/0**, client **81/0**, each new check failing with its line undone; replay
116/16, unchanged, `abductionLastHold` in no diff.
**Third pass, same day - D63, the cooldown.** ⚠️ Two client gates had to learn the continuation
exemption as well as `canSelectForAbduction`: `weaponManager.selectWeapon` and `weaponManager.targetShip`
each test `isLoaded` themselves, so a recharging drive could be offered by the icon and then refused
silently - the harness's `target()` helper sets the selection by hand and missed the selectWeapon half
until a check through the real `selectWeapon` was added. Server **125/0**, client **92/0**; each of the
seven edits undone on its own turns at least one check red; replay output byte-identical with timings
stripped.
**Fourth pass, same day - D64, the first turn only takes hold.** Seventeen server and eight client
checks encoded power on the declaration turn and were rewritten to the new rule (completion now takes
at least two turns; the anchor-swap restart lands at 0 halves). Server **135/0**, client **103/0**; each
of the seven D64 edits undone on its own turns at least one check red; the menu rendered to static markup
in all three states (EDJD targeting: no Power row + targeting note; EDJD next turn: stepper; Scribe
joining: "Double power"); replay unchanged. `factions-tiers.php` rewritten for the targeting turn, the
supporting-drive rule and the D63 cooldown.
**Fifth pass, same day - D65, conditions for taking hold only.** Eight server checks encoded a per-turn
condition test and were rewritten: the mid-chain collapse is now "the holding EDJD stops and a newcomer
fails the conditions", the anchor swap now needs the old anchor to STOP, and two new checks prove a
mid-chain target out of every field with OEW 1 < DEW 5 still counts (with a probe showing
`getConditionBlock` would refuse that exact position, so the check is not vacuous). Server **138/0**, client
**104/0**; re-checking the conditions on continuing turns turns four checks red; replay unchanged.
`factions-tiers.php` updated.

### 3.18b Abducting TERRAIN — **BUILT 2026-09-20**, awaiting play test (D66)

Stage 20 shipped with Q13 answered *"out of scope for now"*. Reopened at the user's request: *"one part
of the EDJB stage was that we consciously decided to skip being able to abduct Terrain units into
Hyperspace. I'd now like to add that."* Four rulings settled it (D66) and the build is small, because
almost nothing about an abduction cares what its target is.

**What was actually in the way** — three refusals and one geometry rule:

| Site | Was | Now |
|---|---|---|
| `EdjdAbduction::isOnBoard` | `if ($unit->isTerrain()) return false;` — terrain was invisible to the resolver AND to the published cost list | the terrain line is gone; `isOnBoard` answers only "is it a unit standing on the map" |
| `EdjdAbduction::getDeclarationBlock` | `$target->team == $shooter->team` → "only an enemy unit" | that test is skipped for terrain (**terrain belongs to nobody**), and a new refusal names a jump point |
| `weaponManager.targetShip` / `targetingTooltip` | `if (ship.Huge > 0) return;` — a blanket refusal of multi-hex terrain to every weapon | a **per-weapon** skip: an abduction-capable Walker drive is let through, everything else still skips silently. The tooltip gains an "Abduction" line so it no longer says "Cannot Target" on a target the click is about to accept |
| `JumpEngine.doSpecialTargeting` | `"Terrain cannot be abducted."` | replaced by `JumpEngine.isAbductableTarget`, the client mirror of the server's three refusals |

⭐⭐ **THE ONE RULE THAT CHANGES SHAPE IS THE FIELD.** *"The target ended its movement in an Energy
Draining Field"* is one hex for anything with a crew and **nineteen for a medium moon**, so
`isInConnectedField` no longer stops at the first goal: it takes the whole footprint as a key set,
crosses each hex off as the flood fill pops it, and succeeds only when the set is empty. For a
single-hex target that is the same answer as before — the old early exit was an optimisation, not a
rule — and a footprint hex sitting in a *disconnected* field of the same team is still a miss, because
only reachable hexes are ever popped. The footprint itself is `RammingAttack::getTerrainOccupiedHexes`,
the one place in the game that knows a terrain shape (⚠️ hexOffsets and `Huge` are **alternatives**
there, not additive — `asteroidTwoHex` declares `Huge = 1` *and* an offset list, and occupies the two
offset hexes, not a disc).

**The cost is the ordinary one.** `ceil(RF / 50)`, which for terrain is `maxStructure × 1.1`: small
asteroid **7**, medium **14**, large **20**, oblong **27**, triangular **33**, small moon **55**,
medium moon **110**, large moon **165**. An EDJD at power 4 takes a small asteroid in three turns
(one to take hold, two of power) and will never take a moon, which is the right answer for a rule
whose own table says *"Planet or larger: Unknown"*. The rules' `10 × radius³` row stays unbuilt — FV
terrain has no radius property, and `Huge` is 0 on all three round asteroids, so the formula cannot be
expressed at all (D66).

**Scope.** `EdjdAbduction::isAbductableTerrain` holds both exclusions: `SpawnJumpPoint` (and so the
exit and the phase-in doorway) because a jump point is a hole in space rather than an object, and
anything `unTargetable`, which is the Energy Draining Mine's orb. Everything else that answers
`isTerrain()` is fair game — asteroids, moons, **fixed jump gates and shipyards**. The vortices were
already out of the client's click sweep (`getInterestingStuffInPosition`), so the client refusal is
belt-and-braces with a message.

**Publication.** `abductions.costs` now carries a key per terrain unit. That is **additive**: every key
that was published before is still published and unchanged, and the whole payload is still behind
`TacGamedata::$abductionCapable`, so a game with no Walker in it sees nothing.

**Everything else was already general.** The chain, the notes, the lock, the cooldown, D64's targeting
turn, D65's conditions-on-taking-hold-only, the detonation roll, the log, the map marker, the "Being
abducted" tooltip and ship-window banner, and `Movement::applyJumpOut` — all read `$target->id` and
needed no edit. A terrain unit has a primary `Structure` and a `RammingAttack` (every hull that calls
`addPrimarySystem(new Structure(...))` gets one), which is exactly what `applyJumpOut` writes its three
records against, and a destroyed terrain unit already drops out of `setBlockedHexes`, so an abducted
asteroid stops blocking line of sight by itself.

**UI wording.** `JumpEngineMenu`'s targeting note says *"takes hold if every hex it occupies is inside
your connected field"* for multi-hex terrain and *"if it is inside"* for one-hex terrain, mirroring the
two messages `getConditionBlock` now writes to the combat log.

**Verification.** `walkersStage20ClientHarness.js` **109/0** (was 104/0): the group that asserted
*"CONTROL: terrain is refused, with a message"* now asserts the opposite, plus a moon declaring, a gun
selected alongside still refused, a jump point refused by name and the orb refused silently.
`walkersStage20Harness.php` **162/0** (was 138/0), with a new group 14 of **22 checks** — the six
declaration verdicts, the wire path through the real `Firing::validateFireOrders` both ways, the
footprint geometry on a two-hex asteroid and on a moon (each proved in both directions by widening the
field), the cost, the published preview with its two exclusions, and an end-to-end run that takes hold
on turn one and drags a small asteroid out in exactly `ceil(7 / 4)` powered turns.

⚠️ **One check in group 8 was stale, not broken by this work.** *"... and the log says the abduction is
complete"* had been red since commit `985f9c7b7` (2026-09-13) removed that suffix as *"Unnecesary
info"*; confirmed by stash-and-compare, and rewritten to assert the shipped progress line instead
(user ruling 2026-09-20). That is why the count is 162 and not 161/1.

`checkShipData.php` PASS, 0 new against 237; autoload map up to date; `yarn build` ran.
**Replay: 122 passed / 3 failed**, which is the clean-tree line for HEAD `81818b3d2` (3671, 4297,
4349 — all pre-existing, proved by stashing `EdjdAbduction.php` and re-running the whole gate). The one
game this change touched was **4329**, whose entire diff was five added `abductions/costs` keys (five
large asteroids at 20 power-turns each) identical for both viewers, with no movement, to-hit, damage or
masking line moved — accepted by the documented merge re-record (manifest 128 → 1 → 128, then a full
check reproducing the clean-tree line exactly).

**Play-test fix (game 4371, user 2026-09-20) — the CANCEL MOVE icon vanished.** Commit Initial Orders
with an abduction standing, move in the Movement phase, and the Traveler could not undo the move; with
no abduction it worked. **Trap 58 (d)**, and nothing to do with terrain: `UI.shipMovement` gates that
icon on `weaponManager.canCombatTurn`, which refuses while the ship holds a live order this turn whose
WEAPON is not `ballistic` — and `markLegacy()` clears exactly that flag, so the type-`ballistic`
abduction read as declared direct fire. Confirmed against the game's own row (`tac_fireorder` 497507,
`type='ballistic' damageclass='abduction' rolled=0`) before any code was touched.

⭐ **`jumpexit` had the identical bug and is fixed in the same line** — a Shadow or Ancient hull
declaring a hyperspace exit and then moving lost the icon too. Both damageclasses are now skipped in
`canCombatTurn`, which is the **same pair, for the same reason**, that
`TacGamedata::hideSystemFireOrders` already exempts; the comment at each site points at the other, and
an ordinary drive's `jumppoint` still needs no exemption because its engine stays ballistic. That
asymmetry — vortex fine, abduction broken — is what identified the line.

`canCombatTurn`'s only other caller is `movement.canPivot`'s flight-in-Firing-phase branch, which a
flight can never reach with either damageclass, so the blast radius is the icon alone. Client harness
**114/0** (+5: the no-order control, the standing abduction, ⭐ the same order re-labelled `Standard`
refusing so the check is not vacuous, and the `jumpexit` twin); server **162/0** unchanged; replay
unchanged at 122/3 — `canCombatTurn` is client-only and serialises nothing.

---

## 4. Stages & exit criteria

Ordered so that each stage is independently shippable and the risky shared-path work lands last.

| # | Stage | Exit criterion |
|---|---|---|
| **0** ✅ | `HexZone` extraction (§2.3) — **DONE 2026-09-03** | Replay harness identical before/after; 13,504-case differential test against the pre-move bodies, zero mismatches. |
| **1** ✅ | Faction skeleton — directory, tier line, and the **user's Walker test hull** (D4) — **DONE 2026-09-03** | `Traveler` generates into `Walkers of Sigma-957.json`; `checkShipData.php` clean (0 new findings, down from 5). |
| **2** ✅ | Lightning Array + Medium Lightning Array — **DONE 2026-09-03; targeting REVISED twice the same day after play testing** | 157 checks green on the first build, +138 across the revisions, incl. a JS-vs-PHP comparison of all six tables and both mode constants, and the intercept gun accounting verified against the REAL `Firing::isValidInterceptor` over ten scenarios. Two firing modes (Combined / Single), now `multiModeSplit` so both are usable in one turn; both weapons intercept. Revisions: **no allocation dialog** — one click = one discharge, repeat clicks on one target fuse; count rides in `->shots`; `'Sweeping'` + `"Split"` so the shot shows in the shooter's INCOMING list, which counts DISCHARGES not orders; withdrawing PEELS one discharge off a combined shot; Medium starts 1/2, not 0/2. |
| **3** ✅ | Chromatic Pulse Driver — **DONE 2026-09-03; CLOSED 2026-09-04** | 186 + 56 server checks and 35 client checks green (2026-09-04: the client half was DEAD until gamedata.js was taught to copy `cpdAdaptation` off the payload). Two firing modes (Pulse / Scanning), the Pulse profile keyed by turns charged; a Scanning hit reduces the target race's shields fleet-wide from the **next** turn, survives a reload, and the double-load trap is demonstrated BOTH ways. ⭐ Publication changed from the plan: the reduction lands on the AGGREGATED defensive bucket in `BaseShip::getHitChanceMod`/`getDamageMod` + FighterFlight's two, not inside each of the nine shield classes — four edits instead of eighteen, once-only by construction, no-op when no CPD is in the game. It lives in `pulse.php`/`pulse.js`, not the Walker files, because `special.js` loads BEFORE `pulse.js`. ⭐ Closed 2026-09-04 with the CAPACITY-POOL half: Thirdspace, Thought and both Trek shield projections hold a pool instead of a modifier, so the bucket reduction reached nothing on them; they now lose the same points off the pool via `Shield::getCapacityAgainstShooter()`, and `doesProtectFromDamage` gained an optional `$shooter`. |
| **4** ✅ | EDF + Variable EDF (§2.1 + §2.2) — **DONE 2026-09-04** | 71 + 105 server checks and 32 client checks green. The field (fixed and variable, both crit ladders, boost, deactivation) on the Traveler with its chart row restored; `TacGamedata::$edfHexes` published hex-keyed so overlap collapse and own-fleet immunity are structural; the targeting penalty server↔client, agreeing over a 4,000-line / 128,745-hex differential corpus; the drain landing as six one-turn param criticals read at four choke-points; and the map overlay. ⭐⭐ Two findings worth carrying: the client parity needed a port of PHP's `round()`, not `Math.round` (half-away-from-zero AND PHP's pre-rounding, worth 843 and 13 wrong lines), and the drain needs NO replay roll-note because the RESULT is the record — every roll lands in a persisted crit's param and an `EdfExposed` marker makes a reload idempotent. ⭐ The full rules text arrived the same day and corrected three things: the EW drain is **d6** not d10; a fully drained reactor blacks out every powered system, reusing `Reactor::addCritical`'s existing cascade; and flash/proximity weapons lose their collateral and their blast radius inside a field. Nothing in Stage 4 is inferred any more. |
| **5** ✅ | EDF criticals + EDF Range enhancement + the `factionAge` gate fix (§3.3) — **DONE 2026-09-04; the refit's RANGE fixed 2026-09-05 after play testing** | 98 server checks and 50 client checks green, plus a **2,573-hull corpus differential** on the offer tuples: the Traveler is the ONLY hull whose offers changed, and only by gaining `EDF_RANGE` + the user's `WalkerShip` set. The crit ladders were already built in Stage 4 and are confirmed against the rules text, which arrived afterwards and matches them exactly (fixed 21+, floor 1; variable 20+, boost first then a hex each, floor 0). ⭐ The refit went **ship-level** (`EDF_RANGE`), not per-system — see §3.5 — and its variable-field rule is implemented by **spending `boostRadiusBonus` down as `radius` goes up**, so the double-power radius is unmoved by construction. ⚠️⚠️ Two findings worth carrying: rewriting the Ancient-weapon gate as a bare `factionAge > $ship->factionAge` silently stripped every refit from the ~40 **middleborn** (`factionAge = 2`) weapon classes on young hulls — the corpus differential was the only thing that caught it, and the test needs BOTH halves (§3.3); and Stage 4 had shipped the variable field's **boost for free** (`boostEfficiency = 0`, which is the EXTRA power a boost level costs, not a flag), now set from `$powerReq` so double power really is double. |
| **6** ✅ | Energy Draining Mine — **DONE 2026-09-05; three play-test revisions the same day** | 215 server checks and 53 client checks green, including a 40,000-shot resolution run measuring 74.55% / 15.22% / 10.23% against the rules' 75 / 15 / 10 and a flat d5 scatter. Stores 3, begins with 1, launches any number at any hexes, spawns a 7-hex field that expires after one turn. ⭐ Two departures from the plan, both simplifications: the orb carries an **ordinary `EnergyDrainingField`** rather than a bespoke `EdfSource` (so the drain, the penalty and the map overlay needed no new code at all), and its one-turn life is **derived from the ship's name on every load** instead of a `generateIndividualNotes` cleanup sweep — which means it still expires on time when the launcher that fired it is dead. `IncreasedRecharge1` covered the crit ladder as-is. ⚠️⚠️ Two findings worth carrying: `turnsloaded` is the MINE COUNT, so `loadingtime` must stay 1 or a crippled launcher needs two mines in store to count as loaded at all — the slowed reload is a separate `turn % interval` cadence, deliberately NOT the `overloading` slot, because `weaponManager.isLoaded` also tests that and would call an empty launcher loaded; and `$removed` has to be re-decided on every load, because `isDestroyed()` with no argument is true for ANY removed unit whatever `removedTurn` says. **Play-test revisions (game 4337):** turn 1 must not reload — the reload branch fires when the game leaves DEPLOYMENT, which on turn 1 is before the player's first Initial Orders, so the launcher opened at 2/3; the map marker is now a PURPLE seven-hex disc labelled "Energy Drain Mine" rather than the default red hex; and ⭐⭐ **the field now drains on the turn it LANDS as well as the turn after**, via a queue flushed through the new `TacGamedata::registerEdfField()` at the top of `Criticals::setCriticals` — ⚠️ additive and never a `setEdfHexes()` rebuild (which would stop a Walker shot down in that same Firing step from draining on the turn it died), flushed at the Critical Hit step rather than from `fire()` (so a probe cannot contain an enemy proximity blast declared later in the same step, which would be resolution-order dependent), and placed BEFORE the `$edfPresent` gate because `registerEdfField()` is what sets it. Zero replay drift across the 128-game corpus. |
| **7** ✅ | Energy Draining Net — **DONE 2026-09-05; play-test fix + live preview the same day** | **Play-test revision (game 4338):** the fill treated any *connected* group of 3+ Nets as a closed area, so three Waymarkers in a **chain** (#1–#2 at 2 hexes, #2–#3 at 3, #1–#3 at 5) filled hexes beside the chain that nothing enclosed — the user reported (1,0). *"Form a closed area"* needs a **cycle**, and the fix is the group's **2-core**: iteratively drop every Net with fewer than two links. A chain erases itself, a ring survives whole, a ring with a trailer keeps the ring. **Live preview added the same day:** deployment and movement now recompute the field client-side from PLOTTED positions (`model/EdfNetLinks.js`, a ported resolver) so the corridors and the filled area form as the ship is dragged; **advisory only** — nothing but the overlay reads it. Proven by a **12,000-board differential** against the PHP across three seeds, hex, team and attribution, zero mismatches, with the generator taught to emit rings deliberately after the coverage guard caught that 13,000 random boards had produced a single cap refusal between them. 68 server checks, 16 preview checks and 7 clustering checks green. **As first built:** replay harness 128 passed / 1 failed, byte-identical to the same run on a stashed tree (game 4325, the known clean-tree failure), so zero drift. `checkShipData.php`: 0 new errors. Pairwise links at 1/2/3 hexes and not at 4; the closed-area fill capped at `2N−1` with the corridors isolated out first so the refusal is provable as the *empty set* rather than "fewer hexes"; three collinear Nets still link. ⭐⭐ **Two departures from §3.7, both corrections.** Linking runs in `setEdfHexes()`, not in the §2.2 resolver — the section predates Stage 4's map, and a corridor computed at the Critical Hit step would have drained units while being invisible to the targeting penalty, the client's mirror and the overlay. And `HexZone::line()` is the wrong tool: it answers ONE line including both endpoints, while the rule hands the player a CHOICE between corridors, so all shortest paths are enumerated instead. ⚠️⚠️ Three findings worth carrying: **`Debug::log` cannot be used anywhere `setEdfHexes()` reaches** — it dumps `$_REQUEST` and `$_SESSION` to disk per call, and a fleet parked in an over-cap formation is a persistent state polled every couple of seconds, so the refusal is an in-memory array instead; **the map overlay must be split into connected clusters** because `HexRegion.buildRegionFromHexes` sizes its sweep from the farthest hex, and two lone Nets at opposite corners of the board would sweep 14,641 hexes to draw two; and **an over-cap area needs more Nets, not more spread** — three Nets at maximum spread have corridors that swallow their own interior, leaving 3 fill hexes against a cap of 5, so the refusal test had to go to a five-Net arc. ⚠️⚠️ And one bug caught by a smoke test rather than by any of the 51 checks that preceded it: **`powerReq` is a BLUEPRINT field that rides the per-class static bundle, not the poll payload** — so the crit-escalated requirement never reached the client until `stripForJson()` republished it per instance. Applies to any system whose criticals move a blueprint number. |
| **8** ✅ | Wide-Beam enhancements — **DONE 2026-09-06; reworked twice the same day** | 143 server checks and 84 client checks green, plus a **2,578-hull corpus differential** on the offer tuples in which exactly TWO lines changed (Traveler gains one `SYS_WBLA` + one `SYS_WBMLA`; Waymarker gains one `SYS_WBMLA` per Medium array), and a replay-harness run of 127 passed / 1 failed that is byte-identical to the same run on a stashed tree (game 4325, the known clean-tree failure). `checkShipData.php` PASS, with the same 3 pre-existing warnings on both trees. Two registry entries at 300 / 200, `ages => array(3)`, `limit` 1; the per-die floor; the 50% / 25% collateral; the one-turn cooldown. ⭐⭐ **Reworked TWICE on the user's rulings.** It shipped as one extra firing mode; the user pointed out that the rules' *"in all modes"* means *whatever the discharge count*, so it became **four** modes (Combined / Single × normal / wide); then the four-entry selector was judged too clunky — *"mechanically that all seems to work perfectly, however the UI is a little bit clunky"* — and it is now **two firing modes plus a per-turn "Wide Beam" toggle** in the array's `<SystemActivation>` box, which is what the rules describe anyway (*"the lightning array may be configured to fire a wide beam"*). ⚠️⚠️ **Six findings worth carrying**, all in §3.3: **the Fire phase has never run the generic `generateIndividualNotes` sweep and must not start** — 34 of the ~80 overrides carry no phase guard at all — so the toggle's write is a new narrow `ShipSystem::saveFirePhaseDeclaration()` hook that does nothing by default; **that write cannot be gated on the refit**, because a POST-side ship is rebuilt without enhancements, so it writes unconditionally and the refit is re-checked at read time; **both toggle states are written and the highest note id wins**, since the load query cannot promise an order within a phase and writing only on arm strands a re-commit on a stale 1; the four-mode detour exposed a REAL bug — `getCombinableOrder` matched *"not Single Shots"* rather than *"this mode"*, so with two fusing modes a wide click silently converted a standing ordinary shot, cooldown and all, and the equality fix is kept; the cooldown had to zero **`overloadturns` as well as `turnsloaded`**, because `calculateLoading` increments `overloadturns` at every turn advance for EVERY weapon and `weaponManager.isLoaded` is an OR of the two; and **nothing on the server refuses an offensive order from an unloaded weapon at all**, so the cooldown needed a server half of its own. Plus two smaller ones: **`MediumLightningArray extends LightningArray`**, so the full array's refit needs an explicit subclass exclusion or every Medium is offered both at once on one mount; and **50% collateral must be computed from the damage**, never by doubling the 25% figure. |
| **9** ✅ | Sensor Charge Transceiver — **DONE 2026-09-06** | 72 checks green in one harness covering both ends, plus a replay run of 127 passed / 1 failed that is **identical to the same run on a stashed clean tree** (game 4325, the known clean-tree failure) with timings normalised, and `checkShipData.php` PASS with the same 3 pre-existing warnings. The harness proves four things nothing else would catch: a **1,080-case geometry differential** on the three new `mathlib` helpers, PHP against the JS mirror, with 495 of the pairs on a hex axis and turn costs 0–3 all present; the resolver over a **12-course corpus** written as (bearing, length) legs rather than hexes; `beforeFiringOrderResolution` end to end against a real `TacGamedata` with a stand-in DBManager — every shot claiming its own database id, the informational row at `rolled 1 / shots 0`, the receiver's `DamageEntry` filed against that row and visible to `isDamagedOnTurn`; and the client's `measureLeg` accepting **exactly** what the server's resolver accepts, over the same corpus. ⭐ Every group asserts its own non-vacuity. ⚠️⚠️ **The test found one real bug that no amount of reading would have**: `calculateLoading` asked `getChargeOutcome` AFTER `parent::calculateLoading`, which calls `setLoading()` and writes `turnsloaded` back to 0 — so `isReadyToFire()` read 0, every charge looked as though an unloaded transceiver had sent it, and the fast recharge could never fire. The outcome is now taken before the parent runs. ⚠️ The icon is a **placeholder** (a copy of `sensorSpike.png`) until real art lands. **Play-test revisions 2026-09-06 (§3.9):** the reachable fan is HEXES rather than six lines; the course carries direction chevrons and the waypoint orders are suppressed from the ballistic layer (they were drawing a red hex and a white arrow each); green markers at the manoeuvre points, the head and any hex where a unit was named; a two-row spent-of-total budget label at the head while the weapon is selected; and the **choice between units sharing a hex is now the player's**, carried as `SCT|w:<n>|t:<id>` from a "Target Ship" tooltip button, advisory-only at resolution, with a new opt-in `Weapon::$hideNotesFromEnemies` so `hidetarget` blanks the token along with the x/y it already blanked. 68 client + 47 server checks green in a throwaway harness covering both ends, and a replay-harness run of 127 passed / 1 failed that is **byte-identical to the same run on a stashed clean tree** (game 4325, the known clean-tree failure) with timings normalised; `checkShipData.php` 0 new errors, the same 3 pre-existing warnings. **Play-test revisions 2026-09-07 (§3.9), client-only:** a refused hex click is now **silent** — `isHexOnFiringArc` owns both geometry refusals (off-axis, and an off-arc launch), measured from the **head** rather than the origin, and `measureLeg` keeps them with `reason` null so a replayed course still truncates identically; and **contact with a receiver finishes the course**, unselecting the weapon and zeroing its shots read-out through one shared `isCourseFinished` predicate. 49 checks green in a throwaway harness, which **fails 11 of them on the pre-edit bodies** — all 11 exactly the changed behaviours, with the "ran out of hexes" branch passing both ways. No server change, no serialised field, so no replay-harness run. |
| **10A** ✅ | EW Detector, the allowance (§3.8) — **DONE 2026-09-09** | 77 server checks and 90 client checks green in a throwaway harness covering both ends, incl. a **41-count ladder differential** in which the JS reads back the table the PHP wrote (so both are compared over the same inputs, with the run asserting its own non-vacuity), the stage's 1/4/5/8/9 tuple on both sides, inclusive-at-exactly-range geometry, per-system ranges, and the ladder exercised **end to end through the real sweep** at 13 counts. `checkShipData.php` PASS, 0 new findings against the same 237 baseline; replay harness 123 passed / 4 failed, **byte-identical** to the same run on a tree with the three server files stashed (4325 is the known clean-tree failure; 3676 / 4249 / 4297 are pre-existing, moved by the *Elite crew* and *Kelly Phaser* commits and never re-recorded). ⭐⭐ **The one EW sweep in the game that is MIRRORED on the client**, because "in range both before and after movement" asks about a plotted, uncommitted position the server cannot have. ⭐ The ladder is counted in **quarters, as integers**, and the rounding rule collapses to `(quarters + 1) intdiv 4`. ⚠️ The allowance is **own-side only** in the UI, and per SHIP rather than a pooled fleet budget — see §3.8 for the reading. |
| **10B** ✅ | EW Detector, late allocation (§3.8) — **DONE 2026-09-10** | **210 checks green across three throwaway harnesses** — 50 server, 135 client, 25 tooltip-menu — covering the pool and its two sources, the clamp, the phase window, the diff in all six refusal modes, `submitLateEw` end to end against a recording DBManager (budget clamp, all-or-nothing Disruption, idempotence, wrong phase, another player's ship, and the no-detector fast path proving it never loads gamedata at all), the derived-bookkeeping invariant, both gates with Initial Orders asserted unchanged, the real `AssignOEW`/`assignEW`/`deassignEW`/`removeEW` paths end to end, and the menu split proved lossless **by object identity** with the null-selection case asserted. `checkShipData.php` PASS, 0 new findings; replay harness 119 passed / 4 failed, **byte-identical with timings normalised** to the same run with the six server files stashed — zero drift on all five checks, `masking` and `snapshot` included. ⭐⭐ **User ruling R1 deleted the hard half of this stage**: "end of the movement segment" is the start of Pre-Firing (or of Firing), so there is nothing to DECLARE — the allowance is simply recomputed at the post-movement hex and a ship that drifted out of range finds it is zero. ⭐⭐ **The bookkeeping is one derived number and two bounds** — `spent = pool − getEWLeft()`, upper bound the budget, lower bound what stops an Initial Orders allocation being taken back — no snapshot, no per-entry marking. ⭐ **User ruling R2**: the point comes out of the unspent DEW pool alone, so the allowance is `min(ladder, pool)` and a ship that spent everything on non-DEW types saves nothing. ⚠️ The write is **additive by the shape of the diff**, raises existing rows rather than duplicating them (`getEWbyType` reads the first, `getOEW` sums), and is idempotent. ⚠️ The EW buttons are **reused verbatim** from the Initial Orders menu behind ONE menu-level gate. ⚠️ Masking verdict and its one residual (a deliberate mid-phase page reload) recorded in §3.8.
| **11** ✅ | Housekeeping (§3.10) — 50% deployment bracket · SCT green name removed · Energy Draining Mine untargetable · the faction entry in `factions-tiers.php` · the Wanderer starts fully charged — **DONE 2026-09-10** | All five, each proved on its own: **54 checks green** across four throwaway harnesses (14 bracket, 15 SCT marker, 11 client + 14 server untargetable) plus the seeding demonstrated on real hulls both ways. `checkShipData.php` unchanged — 238 findings, 237 baselined, and the **1 new error is pre-existing on a stashed tree** (`Wanderer :: location 1, roll 9` names `"EW Detector"` where the class is `"Electronic Warfare Detector"`, so the system can never be hit; it came in with the hull and the fix is one string in [Wanderer.php:94](source/server/model/ships/walkers/Wanderer.php#L94)). Replay harness **119 passed / 4 failed**, exactly the documented clean-tree failures (3676, 4249, 4297, 4325), byte-identical to a stashed-tree run. ⚠️⚠️ **And the harness caught a real regression that every unit test of the feature missed**: §3.10e's hull list as a `public static` on `Weapon` broke every missile-armed ship in the game, because `MissileRack::stripForJson` walks its ammo with `ReflectionObject::getProperties(IS_PUBLIC)` — which lists public STATICS — and then reads each name as `$missile->$key`. It is a `const` now; see the head of §3.10. ⭐ Two smaller findings: **the fleet checker no longer exists twice** (the second copy is `checkChoices_LEGACY`, inside a block comment since the Item-5 simplification, and a test asserts it did not grow a 50% bracket), and **the untargetable flag is a ship PROPERTY declared only on the orb**, so it rides the static blueprint verbatim and the server method and client mirror read one fact. |
| **12** ✅ | Mapmaker Electronic Warfare (§3.11, as built §3.11a; saved EW extended to Walker flights 2026-09-10) | 3 points per flight across OEW and DEW and no more; an ordinary flight's mine-detection allowance byte-identical before and after; hit chance agreeing server↔client over an OEW 0–3 × DEW 0–6 differential; enemy allocation invisible during Initial Orders; flight-window EW block rendering without breaking the scale-to-fit budget or the resize grip. |
| **13** ✅ | Mapmaker Jump Engine (§3.12) + the Fleet Checker hangar exemption — **DONE 2026-09-10** | **94 checks green** across two throwaway harnesses — 59 server, 35 client — covering the 10-turn recharge read from `$delay`, the one-engine-per-flight accessor, the charge mirrored onto all six craft, the declaration normalised at the wire and the second one refused *with the existing reason string*, `hasVortexDeclaration` reaching a flight at all, and the blueprint scan; every group asserts its own non-vacuity and both harnesses **fatal on the pre-edit tree**. `checkShipData.php` PASS, 0 new findings against the same 237 baseline; replay harness 115 passed / 8 failed, **byte-identical with timings normalised** to the same run with `source/` stashed. ⭐⭐ **THE PLAN WAS RIGHT THAT "ONE JUMP POINT PER FLIGHT" NEEDED NO NEW RULE AND WRONG ABOUT WHY IT WORKED**: `Firing::getVortexDeclarationBlock`'s one-vortex-per-**shooter** loop does catch it — but only once both orders name the SAME engine, so the rule that makes D13 true is a two-line **weaponid normalisation** in `validateVortexDeclaration`, not the loop itself. ⭐⭐ **AND THE REAL WORK WAS NOT THE CLIENT** (§3.12 called it "the whole stage"): five server sweeps walked `$ship->systems` looking for a `JumpEngine` and found NOTHING on a flight, because a flight's systems are *craft*. They all go through the new `JumpEngine::getUnitJumpEngines()` now. ⚠️ Three findings worth carrying, all in §3.12. |
| **14** ✅ | `MedLightningArrayFtr` (§3.13, as built §3.13a, play-test fixes §3.13b, EW rules corrected §3.13c) + the Mapmaker hangar rule (D31) — **COMPLETE 2026-09-11** (built 2026-09-10) | ⚠️⚠️ **2026-09-11: the Mapmaker EW rules were CORRECTED from the rulebook text (§3.13c)** — the Array uses plain ship EW rules (OEW added, target DEW/BDEW/SDEW as the ordinary to-hit penalty, no OB, no OEW = doubled range penalty as usual) and the Pulsar gets OB + max(0, OEW − defensive EW). That **reverted** the §3.13b no-lock exemption described below. Re-proved with the Stage 12 client grid (90/0), the Stage 14 client (62/0) and server (99/0) harnesses, and the game-4347 probe (15/0); replay 114 / 8, unchanged, though no Mapmaker game is in the corpus. | **215 checks green** across four throwaway harnesses — 99 server, 62 client, 43 fleet-check and an 11-check live-game probe — covering 3 and 6 combining while 1/2/4/5 go technical, two 3-groups at two targets, buckets split by target / called id / mode, a damaged probe excluded on `getRemainingHealth() >= maxhealth` (and asserted NOT destroyed, so the obvious shortcut is proved wrong), an uncharged array refused server-side, D16 resolved in both directions with the loser named in the log and proved to ignore intercept orders, the turn-1 full charge proved as an ABSENT override, `edfSuppressesCollateral` proved inherited (and `LightningArray`'s proved overridden, which is why this class must not extend it), and the fleet check driven through the REAL `gamelobby.js` slices for Mapmakers, Stilettos and Fighter Squadrons alike. Every harness fails on a stashed tree, and each play-test fix fails in isolation when its own line is reverted. `checkShipData.php` PASS, 0 new findings against the same 237 baseline; replay harness 114 passed / 8 failed, **byte-identical with timings normalised** to the same run with `source/` stashed — including after the `weapon.php` no-lock change, which every weapon in the game runs through. ⭐ **THE STATS NEEDED NO CLIENT MIRROR** — both modes differ only in fire control, range penalty and damage span, and all three already travel as the engine's generic per-mode arrays, so the client half is the group rule and nothing else. ⭐⭐ **THE REAL WORK WAS D16, NOT THE COMBINING**: the combining is `HyperplasmaMatrix`'s pattern, but every exclusivity mechanism in the tree is per-CRAFT (`checkConflictingFireOrder` narrows to `getFighterBySystem` before it looks), so flight-wide exclusivity needed a new predicate on both sheets keyed on a new `flightExclusiveGroup` string. ⚠️⚠️ **AND THE HANGAR FIX FOUND THAT THE TWO HALVES OF THE RULE HAD NEVER MET**: the four Walker hulls declare `"Mapmaker Probes"` capacity while the flight left `hangarRequired` at `'fighters'` and classified itself as an ordinary MEDIUM fighter, so nothing could ever fill it. ⚠️⚠️ **PLAY TEST 4347 THEN FOUND TWO MORE THAT NO UNIT TEST COULD**: an undamaged craft has NO `damage` key at all (ShipCompactor strips empty arrays) so `getRemainingHealth` threw and the weapon could not be targeted, and a `useFlightEW` shot was taking a no-lock penalty D12 forbids — see §3.13b. ⚠️ Four findings worth carrying in §3.13a, three more in §3.13b. |
| **15** ✅ | Walker jump drive (§3.17, as built §3.17a) — **DONE 2026-09-11**, promoted from Stage 18 the same day; a `markWalker()` flag on every Walker hull (D32) | **107 checks green** across two harnesses — 73 server, 34 client — both fatal on the pre-edit tree: the mark on all six hulls and on nothing else; the deferral at the end of Movement, against an ordinary hull on the identical legal path which still leaves; the refusal at submit and at resolution (which also withdraws an Initial Orders ballistic), with the identical orders accepted at a Walker that is not leaving, and all four client call sites passing the shooter; departure at the end of Firing with its attached unit, and a cancellation when the drive dies while it waits; zero failures in 300 rolls from an engine that fails at once with the flag off; and the Vortex Disruptor catching the waiting Walker. `checkShipData.php` PASS, 0 new against 237; autoload unchanged; replay 114 / 8 **byte-identical with timings normalised** to a stashed tree. ⚠️ Four findings in §3.17a, and traps 35–36. |
| **16** ✅ | The Traveler's Docking Bay (§3.14, as built §3.14b) — **DONE 2026-09-11**; the Waymarker's two-turn procedure (§3.14a) DEFERRED (D35) | **292 checks green after the review revisions (D37–D40)** - 131 server, 118 client, and the Stage 14 fleet-check harness's 43 as a regression - with both new harnesses failing on the pre-stage tree; `checkShipData.php` PASS, 0 new against 237; a **2,727-hull differential** in which exactly five facts moved (the four dockable hulls' box cost, the Traveler's aft system class) and no capacity did; replay corpus 133/1 on a clean tree, and with the stage the ten Traveler games differ ONLY by four additive keys. ⚠️ Five traps, 37–41. Criterion as written: 24 Mapmakers **or** 6 Scribes **or** 2 Pathfinders, with the 25th/7th/3rd refused and a mixed load filling to exactly 24 boxes; one craft type per turn; a docked Scribe surviving a reload with damage, power and notes intact; the aft hit-chart row still finding the renamed system (`checkShipData.php` clean); no other hull's hangar accounting moving in the corpus differential; a Scribe, Pathfinder or Waymarker queued for a deployment-phase dock placeable ON the Traveler's hex while two ordinary hulls still refuse to share one. **If §3.14a lands:** a Waymarker rides `attached` for exactly one turn each way with its 24 boxes reserved from declaration. |
| **17** ✅ | Traveler Self Repair serves docked units (§3.15, as built §3.15a) — **DONE 2026-09-12**, two play-test follow-ups the same day | **127 checks green** — 70 server, 57 client — both harnesses fatal on a stashed pre-stage tree; `checkShipData.php` PASS, 0 new against 237. Criterion as written, all met: a damaged docked Scribe repaired out of the Traveler's pool (and its Thruster, which the Traveler may not touch, out of its own); every healing row filed against the DOCKED ship's id and marked updated, so it persists; the Traveler's own queue order unchanged and a priority of 99 on a docked row still beaten by an own row of 4; a docked Self Repair repaired and every other Self Repair in the game still refused. ⭐ Three additions from the user's notes the same day: **D42** one list, the docked rows marked by their ship name in cyan - first built with a TIER pinning them below every own row, which **D45 withdrew the same day**, so priority alone now decides and the player may put a docked hull first, **D43** a docked unit's OWN Self Repair keeps running — which needs driving, because `removed` reads as destroyed and `Criticals::setCriticals` never reaches it — and **D44** reinforcement fleet-list rows go cobalt so they cannot be read as docked. ⭐ Play-test (game 4350) then found the other half of D43: **a docked ship's whole ship window was inert**, because `SystemIcon.clickSystem`'s guard is `shipManager.isDestroyed(ship)` and that folds `removed` in — carved out with the existing `isDestroyedByDamage` predicate and diverted straight to the info menu, which is also §3.16(a)'s prerequisite arriving a stage early; and left-click on a stowed ship's fleet row now scrolls to its **carrier** rather than opening its window (right-click still does that). ⚠️ Replay corpus 135/0 clean vs 121/14 with the stage, **every diff the same single additive key** `servicesDockedUnits: added (true)` and nothing else — re-record to accept. |
| **18** ✅ | Docked power sharing (§3.16, as built §3.16a, opponent view §3.16b) — **DONE 2026-09-12**, one play-test follow-up the same day | **134 checks green** — 67 server-free over the REAL `power.js`, 48 in a React harness, 19 in a server harness over the real `Traveler` that bundles the whole `reactJs` tree, evaluates it at module scope, renders `SystemInfo` to static markup and drives `SystemPowerSettings`'s own handlers — each fatal on the tree it was written against (21/37, 23/9, 14/5); `checkShipData.php` PASS, 0 new against 237; a **2,727-hull / 58,548-fact differential** in which exactly TWO lines moved, both `Traveler|sys11` (the flag and one tooltip sentence); replay 121/13 with every diff one of two ADDITIVE keys and no behavioural drift; autoload unchanged. Criterion as written, all met: a docked Scribe's power manageable during Initial Orders and persisted through the commit (the server half needed nothing — no `removed` filter in `InitialOrdersGamePhase::process`, `construcGamedata` or `submitPower`); four points of docked surplus giving the Traveler one and three giving none; flights contributing nothing; the figure recomputing live (on the existing `SystemDataChanged` → `shipWindowManager.update()`, no new event); and the decision written down as **D46 — client-computed and ADVISORY**. ⭐ Play-test follow-up: the OPPONENT saw the Traveler's balance WITHOUT the grant, because the grant is computed per viewer and `shipsDocked` is masked under the private-logistics gate — fixed by disclosing the bay's ship IDS on a separate key (**D47**, §3.16b), so both clients run one function and cannot drift. ⚠️ Three traps, 47–49, plus 50 on the masked-input fact; one adjacent defect flagged but deliberately not fixed; and power management for a unit still in HYPERSPACE left unbuilt but mapped. |
| **19** ✅ | The Waymarker's two-turn procedure, the aft-hit redirect, the hangar-manoeuvre label and what a stowed unit projects (§3.14a / §3.14c / §3.14d, as built §3.14e) — **DONE 2026-09-12** | **240 checks green after the play-test fixes (§3.14f)** — 142 server (group 10 drives two whole turns through the real `criticalPhaseEffects`), 98 client over the real `hangarShared.js` / `ships.js` / `ew.js` / `fleetList.js` / `PhaseStrategy.js` under `vm` — both fatal on the pre-stage tree; `checkShipData.php` PASS, 0 new against 237; autoload and **statics both unchanged** (the class list is a const and `$stowedEdfRadius` is protected, so neither rides a blueprint); **replay 120 passed / 13 failed, the SAME 13 games and the same count as the pre-stage tree**, so the stage adds no behavioural drift and no new failing game — its only lines in the diff are `deferredShipClasses: removed` / `twoTurnShipClasses: added` on the ten Traveler games. Criterion as written (§3.14a), all met: a Waymarker rides `attached` for exactly one turn each way; its boxes are reserved from declaration through the single `dockedShipBoxes` choke point; it moves with the Traveler while attached, through the existing mirror rather than a re-implementation. ⭐ Four rulings the same day — **D48** least damaged = most boxes remaining, **D49** can be shot / cannot shoot, **D50** the banner goes on the unit and a new flight gets none, **D51** left-click scrolls on ALL docked units, which withdraws a Stage 17 exception. ⭐ Play-test (game 4351) then found two things the same day (§3.14f): ⚠️⚠️ **the movement mirror had never run for a rider**, because `MovementGamePhase::process` counted PRESENCE in the payload rather than submitted movement rows and the client sends every own ship with an empty list - invisible until now because a boarding pod and its host are never on the same side; EW is suspended on a rider both ways, with **D52** ruling that it KEEPS its DEW; and the FIRING MODE SELECTOR was the one weapon control in the menu that never asks whether a fire order exists, so it alone survived every other guard - withdrawn now along with the intercept pair it parents, and `automateIntercept` matched on the server. The faction page (`factions-tiers.php`) is updated and the Waymarker is struck from its not-implemented list. ⚠️ Six traps, 51–56. |
| **20** ✅ | Extra-Dimensional Jump Drive (§3.18, as built §3.18a) — **BUILT 2026-09-12**, awaiting play test | **146 checks green** — 89 server, 57 client — each fatal on its stashed pre-stage tree; `checkShipData.php` PASS, 0 new against 237; autoload +1 (`EdjdAbduction`); replay 116/16 vs 119/13 clean, every added line one of three additive keys and no behavioural drift. Criterion as written: power-turns accumulate only while an initiating EDJD meets both conditions and restart after a gap; the cost is locked on the first turn (proved against a section destroyed mid-chain); completion at exactly the cost through `Movement::applyJumpOut`, attached units taken; a second EDJD and a Scribe contribute, the Scribe alone cannot carry it; a damaged EDJD rolls every abducting turn at half while the hull's own jump-out stays immune; the chain rebuilds from the stored notes alone. **Changed by ruling:** D53 a declaration with a power level instead of boost levels, D54 only the initiator meets the conditions, **D55 friendly use NOT built**, D56 detonation halved; review revisions 2026-09-13: D57 bay contents out of the cost, D58 deactivation/destruction cancels, D59 no line of sight, D60 a ship-window banner (harnesses now 96 + 65). The faction page gained its own section and the "not implemented" line is gone. ⚠️ Traps 57–60. **Extended 2026-09-20 (D66, §3.18b): TERRAIN can be abducted** — asteroids, moons, jump gates and shipyards, at the ordinary `ceil(RF / 50)`, with a multi-hex unit needing its whole footprint in the field; server harness **162/0** (+22 checks, and a stale group-8 assertion rewritten), client **109/0**, `checkShipData.php` PASS, replay clean-tree line restored after a merge re-record of game 4329 (five additive `abductions/costs` keys). Awaiting play test. |

**Every stage:** run `fvbuild.ps1 -Check` (ship-data validator + replay harness). ⚠️ The baseline
drifts on a clean tree — never read a pre-existing FAIL as your regression, and **never
blind-re-record**. The method that works: capture the check output, `git stash push -- source/`,
re-run, `git stash pop`, diff with timings normalised.

Stages 4, 5 and 10 each change a serialised property or a shared payload, so the harness `check`
is mandatory rather than advisory on those. ⚠️ **Stage 3 turned out to be one of them too** — it
added `TacGamedata->cpdAdaptation` and put a line into the four defensive-mod aggregators every
faction in the game runs through. It came out clean (130/1, the known 4325 failure), but the rule
should be read as "any stage that touches a shared path", not as a fixed list.

⭐⭐ **AND FROM STAGE 11 ON, EVERY STAGE UPDATES THE FACTION ENTRY** in `factions-tiers.php`
(§3.10d). A faction page written once and never revised is worse than none — players read it as
authoritative and it silently describes a game that no longer exists.

⚠️ **The second wave is unusually shared-path heavy.** Stage 12 changes EW for a unit class that has
never had it, Stage 16 changes `unitSize` and hangar accounting for hulls that have never used
either, Stage 18 touches the power balance, and Stage 15 (built) changed when a unit leaves the board.
Every one of those is "any stage that touches a shared path", so the harness `check` is mandatory on
all of them — plus a `masking` pass on Stage 12 and a 2,500-hull corpus differential on Stage 16.

**Each stage opens on a control sheet (D4)** — the user lands a Walker hull carrying a basic version
of that stage's system, and the stats are read out of the hull file. Stages **0 and 1 need nothing**:
Stage 0 is a pure code move and Stage 1 *is* the first test hull.

⭐ **Stage 14's arrived on 2026-09-08** and is folded into §3.13 as a table rather than a hull, the
way Stage 3's did. Everything still outstanding is listed in Q5.

---

## 5. Trap register

Collected from the survey; each one has bitten this codebase before.

1. **The double gamedata load.** `Manager::advanceGameState` loads, then the phase's `advance()`
   loads again — in one request. Any per-load static (§2.1, §3.4) must be reset in
   `DBManager::getSystemDataForShips` before the `onIndividualNotesLoaded` sweep.
2. **POST-side ships have no enhancements and no loaded notes.** Anything reading a Wide-Beam
   purchase or a CPD scan note inside `generateIndividualNotes` / `process()` reads a class default
   and fails **silently, forever**. Server-authoritative checks belong in `advance()`.
3. **`advance()` has already set the next phase** before its ship loop. Never branch on
   `$gamedata->phase` there; pass an explicit checkpoint.
4. **`notekey` / `notekey_human` are `varchar(40)`** and overflow is a fatal that aborts the whole
   submission. `substr($x, 0, 40)`.
5. **FV initiative is d100** — every modifier is 5× tabletop. The EDF's `−20` cap is `−100` here.
6. **Client system fields are shared by reference** across same-phpclass instances. Any per-instance
   mutation (a modified `data` tooltip, a per-array Wide-Beam flag) needs `isModified` handling or
   it bleeds onto every sibling array on the ship.
7. **Positional system ids.** Ids are construction order; a variant needs a **new phpclass**, never
   a reordered constructor.
8. **`ShipCompactor` strips blueprint keys.** Adding a public property to a system is not free —
   check whether the compactor's `$falseKeys` / `$emptyArrayKeys` / `$deadSystemKeys` would remove
   it, and whether every client read behaves identically when it is `undefined`
   (`=== false` does **not**).
9. **`empty array()` encodes as JSON `[]`, not `{}`** — the `$edfHexes` map must never be emitted
   empty as an array where the client expects an object.
10. **Render-loop idle gating** — any scene mutation outside the animation list needs
    `requestRender()`. The SCT arcs will silently not draw.
11. **`splitShots` `$guns` padding** must skip manual `intercept` orders (Slicer, game 4306).
12. **Filename must equal class name** or the ship silently does not exist.
13. **Regenerate autoload** (`fvbuild.ps1 -Autoload`) after every new class; never hand-edit
    `source/autoload.php`. Regenerate **statics** whenever something reaches the client through the
    blueprint rather than gamedata.
14. **Never commit the two `.legacy.bundle.js` files.**
15. **PHP's `round()` is not JS's `Math.round()` — in TWO ways.** Half away from zero
    (`round(-2.5) === -3`), *and* a pre-round to `14 - floor(log10|v|)` decimal places that turns
    `-20.49999999999999644` into `-20.5`. Any JS port of a PHP geometry routine needs both;
    `mathlib.phpRound` (Stage 4) is the reference, and the only way to know is a differential
    corpus generated by the PHP side.
16. **A new gamedata-LEVEL field reaches the client ONLY if `gamedata.js parseServerData()`
    copies it BY NAME.** Publishing it from `stripForJson` is half the job. It cost the CPD's
    whole client half a day (§3.4); `edfHexes` carries the same line for the same reason.
17. **A `oneturn` critical must NOT carry an `$outputMod`.** `ShipSystem::effectCriticals()` sums
    `outputMod` across every critical with **no turn filter** (it is built for permanent
    `OutputReduced*` crits and runs once from `onConstructed`), so a one-turn crit would apply
    from the turn it was rolled and then for ever. Read a per-turn magnitude through
    `sumCriticalParam()` in the system's own `getOutput()` instead — Stage 4b.
18. **`sumCriticalParam($type, $turn = false)` defaults on `=== false`, NOT on null.** A method
    with a `$turn = null` parameter that passes it straight through makes every turn comparison
    fail and reads 0, silently — while the same method called with an explicit turn is correct.
19. **`boostEfficiency` is the EXTRA POWER one boost level costs, not a flag and not an
    efficiency.** `power.js countBoostReqPower` / `countBoostPowerUsed` multiply by it, so `0`
    means the boost is **free**. A system that wants "double power" must set it to its own
    `$powerReq` — and since that is normally a constructor argument, it cannot be a declared
    property (Stage 5, found on the EDF).
20. **An enhancement that must not change a DERIVED number should spend the derived number's own
    input down, not clamp the result.** `EDF_RANGE` raises `radius` and lowers `boostRadiusBonus`
    by the same amount, so "the double-power radius does not change" holds with no second stored
    value and nothing left to keep in step (Stage 5, §3.5).
21. **A per-system enhancement's numbers must be kept OUT of prose `data` entries.** The lobby has
    no server round trip and has to rewrite `data` itself; one short numeric key per number is a
    mirror that survives, a number inside a paragraph is one that rots (Stage 5).
22. **A hull-age comparison needs a floor as well as a direction.** `>= 3 && > $ship->factionAge`,
    never one or the other — the ~40 `factionAge = 2` weapon classes make the difference invisible
    in every unit test and visible only in a corpus differential (Stage 5, §3.3).
23. ⭐⭐ **ANYTHING IN `TacGamedata::onConstructed()` THAT READS A NUMBER AN ENHANCEMENT CAN MOVE
    MUST RUN BELOW THE PER-SHIP LOOP.** `BaseShip::onConstructed()` — called *inside* that loop —
    is what applies enhancements, so the whole first half of the method sees blueprint values.
    `setBlockedHexes()` is safe up there because it reads only where ships ARE; `setEdfHexes()`
    was not, and published a refitted field at its unenhanced radius for a whole stage (§3.5).
    The comment on `markUnavailableSetMarkers()` already said this for the Chameleon gate.
24. **A stage whose numbers are equal by default cannot be tested by a unit test alone.** Without
    the refit, blueprint radius == effective radius, so every check passed on both sides of trap
    23. A recorded corpus game with the refit actually bought is the only guard — and the replay
    snapshot IS `stripForJson()`, so it pins published maps like `edfHexes` for free.
25. ⚠️⚠️ **`replayHarness.php record --games=<id>` REWRITES `manifest.json` FROM SCRATCH** with
    only the games it just recorded — it does not merge. The other ~130 baseline directories stay
    on disk but drop out of `check` silently, and `tests/replay/baseline/` is gitignored, so there
    is no copy to restore. It IS reconstructible without re-recording: each `snapshot_p*.json` is
    `stripForJson()` output and carries the game's `turn` and `status` **as recorded**, which with
    the on-disk report list is the whole manifest entry. Intersect with `discoverGames()`'s query
    (~90 of the directories are dead games that were never in the manifest), and fall back to the
    DB row for a baseline that recorded a `HARNESS-ERROR` and so has no snapshot to read.
26. ⚠️⚠️ **THE FLEET CHECK EXISTS TWICE IN `gamelobby.js`.** The bracket accumulator, the bracket
    report and the hangar tallies all appear once around lines 1028–1930 and again around
    4754–5710, near-identical but not identical — the second copy still consults the dead
    `oneOverAllowed` flag. Any fleet-legality change has to land in both, or the lobby's live check
    and the standalone Fleet Checker disagree with nothing on screen to say why (§3.10a).
27. ⚠️ **A CUSTOM HANGAR CATEGORY HAS NO MINIMUM** — and for the Torvalus Stiletto that is still the
    feature. `totalFtrH/M/L` feed `minFtrRequired = ceil(totalHangarAvailable / 2)` — the 50%
    full-hangar rule — while a craft with its own `hangarRequired` string lands in `totalFtrOther`
    and is reported "allowed up to N". Inventing a new size band instead of a new category would
    silently re-impose the rule.
    ⚠️⚠️ **BUT IT IS NO LONGER TRUE OF THE MAPMAKER (D31, 2026-09-10)**, and reading it that way is
    what let the bug live: `'Mapmaker Probes'` now carries a 50% minimum *and* no maximum, and both
    facts are read off one array, `noHangarMaxCraftTypes` (§3.13a). ⭐ **A MINIMUM SEEDED FROM A
    DECLARED LIST, NOT DERIVED FROM THE FLEET** — the rule has to bite on an EMPTY carrier, which is
    exactly the case where a set built from the craft actually bought is empty.
28. ⚠️ **`ew.getScannerOutput()` ALREADY ANSWERS A DIFFERENT QUESTION FOR A FLIGHT.** It returns the
    MINE-DETECTION allowance (`floor(offensivebonus / 2)`), and `getEWLeft` — which every assign
    path measures against — is built on it. Adding a flight's OEW/DEW pool to that one number lets
    an ordinary fighter spend mine detection on OEW and a Mapmaker spend OEW on mine detection, in
    both directions, silently. Two pools, two functions (§3.11).
29. ⚠️ **`Hangar`'s `$maxhealth` IS ITS CAPACITY IN BOXES; `$output` IS THE PER-TURN LAUNCH+LAND
    BUDGET.** `HangarOps::effectiveCapacity()` returns `getRemainingHealth()`, so a damaged bay
    holds less. Reading `$output` as capacity — the natural assumption from the constructor's
    argument order — is off by a factor of two on the Traveler and by more elsewhere (§3.14).
30. ⚠️ **`unitSize` IS READ BY THE FLEET CHECK AS WELL AS BY THE HANGAR.** Putting one on a hull
    that has never carried one (an HCV, a MediumShip) changes `1 / lship.unitSize` in the lobby's
    small-craft tally and `flightSize / unitSize` in the server's shuttle accounting at the same
    time. The hull-corpus differential is the only guard (§3.14).
31. ⚠️⚠️ **A DOCKED UNIT IS `removed`, AND `removed` READS AS DESTROYED EVERYWHERE.**
    `BaseShip::isDestroyed()` with no argument is true for any removed unit, and the client's
    `shipManager.isDestroyed` sits in front of the power paths, `shouldBeHidden`, the fleet list,
    the icon and the movement sequence. Anything that has to reach a docked unit — repair, power,
    a shared reactor sum — needs its own narrow predicate, never a change to `isDestroyed`
    (§3.15, §3.16).
32. ⚠️⚠️ **THERE IS NO SERVER TWIN OF `getReactorPower`.** The power balance is computed entirely in
    `power.js`; the server trusts the entries it is sent, and `Reactor::getOutput` answers only for
    one reactor on one hull. Any rule that GRANTS power — the docked-ship share, and anything like
    it — is unvalidated unless a server-side check is written for it on purpose (§3.16).
33. ⚠️⚠️ **A JUMP-FAILURE RULE HAS THREE SITES, AND ONE OF THEM ONLY WRITES THE LOG.**
    `doHyperspaceJump` (the boost path), `rollVortexJumpFailure` (the per-turn roll while a vortex
    is open) and `openVortex` (which recomputes the percentage purely for its log line). A change
    applied to two of the three produces an outcome that contradicts what the combat log said would
    happen (§3.17).
34. ⚠️⚠️ **AN IMMUNITY MUST STILL CONSUME ITS DIE.** `getCertainJumpFailureNote` is asked BEFORE the
    d100 rather than instead of it, on purpose: dice draws are part of the game's random sequence,
    and skipping one makes otherwise-identical games diverge. Every new "this unit never fails"
    rule inherits that constraint, or it breaks the replay harness on games that have nothing to do
    with the feature (§3.17).
35. ⭐ **TRAP 34'S RATIONALE IS WEAKER THAN IT READS, SO PROVE IT BY SHAPE.** `Dice::d` is `random_int`,
    unseeded, so live play has no reproducible sequence to protect; the replay harness declares its
    OWN seeded `Dice` ahead of autoload and re-seeds at fixed points. Keep the draw anyway (it is
    free), but no test can show "the die is still drawn" by replaying a sequence. Assert instead that
    the immunity only ZEROES the percentage and that no `return` sits between it and the draw (§3.17a).
36. ⚠️⚠️ **A COMMITTED JUMP-OUT NO LONGER MEANS "GONE AT THE END OF MOVEMENT".** A Walker drive keeps
    its unit on the board until the end of Firing (§3.17). Every site that read a committed
    `jumpout` order as "it has left" had to learn otherwise: the client's sprite, fleet row,
    docked-flight walk and docked-LCV walk (`carriesWalkerJumpDrive`), and the Vortex Disruptor,
    which searched only wreckage. A new "has it left?" test must ask the REMOVAL (`isDestroyed` +
    `hasJumpedToHyperspace`), or ask both questions.
37. ⚠️ **A HANGAR SUBCLASS THAT MUST STILL CARRY FIGHTERS KEEPS `$name = 'hangar'`.** Twenty-nine
    client sites gate the fighter launch/dock/recover UI on the name, and `SystemFactory` builds the
    client object from it (`window[Capitalised(name)]`), so a new name silently takes the fighters'
    UI away. Keep the name, add a discriminator flag, gate new client behaviour on the flag - the
    ShadowHangar's precedent and now the Docking Bay's. The hit chart matches `$displayName`, which is
    free to change and must move with the chart row in the same edit (§3.14b).
38. ⚠️⚠️ **NOT EVERY FIGHTER CAPACITY SITE WENT THROUGH THE CHOKE POINT.** `HangarOps::effectiveCapacity`
    and `HangarShared.effectiveHangarBoxes` are the two, but `shipTooltipFireMenu.js`'s dock AND
    recover eligibility each recomputed `maxhealth - damage` inline and would never have seen a docked
    ship. Both call `HangarShared` now. Grep for `maxhealth` before assuming a capacity rule reaches
    every dialog (§3.14b).
39. ⚠️⚠️ **THE ORDINARY HANGAR PARSER READS A PAYLOAD WITHOUT ITS OWN KEYS AS A LEGACY LAUNCH LIST**
    and writes an EMPTY fighter launch order - which, latest note winning, cancels a real one. A Hangar
    subclass that adds payload keys must take them out before calling the parent
    (`DockingBay::doIndividualNotesTransfer`). The server harness proves the misfire on an ordinary
    hangar as its control (§3.14b).
40. ⭐ **A "ONE X PER TURN" LOCK MUST BE DECIDED FROM THE ORDERS AT LOAD, NOT BY WHOEVER RESOLVES
    FIRST.** Hangar crit hooks run per bay in system order and the fighter coalescer runs once per
    carrier from whichever bay reaches it first, so "first come" would be iteration order. And the lock
    must be LATCHED: the orders are consumed as they resolve, and a lock that reads the live orders
    reopens itself halfway through the pass. ⚠️ Keep it OUT of damage eviction, which reads boxes - a
    lock expressed as "capacity 0" would there evict every craft aboard (§3.14b).
41. ⚠️ **`performLCVLaunch`'s RE-INIT IS NOT SAFE FOR EVERY HULL.** It tops every Weapon to
    `loadingtime`, which on a Walker hull would reset an Energy Draining Mine's STORE (its
    `turnsloaded` is the mine count, Stage 6). System data loads as the latest row at or before the
    turn, so a docked ship that is simply resurrected keeps exactly the state it docked with - which is
    what the Docking Bay does (§3.14b).
42. ⚠️⚠️ **NOTHING AT ALL RUNS ON A `removed` UNIT DURING THE CRITICAL PHASE.**
    `Criticals::setCriticals` snapshots `$activeShips` through `isDestroyed()`, which answers true
    for anything removed, so a docked ship's `testCritical`, `criticalPhaseEffects` and everything
    hung off them stop the moment it docks. (`onAdvancingGamedata` is the opposite case and DOES
    still run on it - §3.14b, group 16 - so "docked units are frozen" is false in general and true
    for exactly this phase.) Anything a docked unit must keep doing needs a named driver on the
    CARRIER, and that driver needs a guard for the ship that docked THIS turn: it WAS in the
    snapshot, so it is about to be processed in its own right and doing it twice pays for the same
    thing out of two pools. Stage 17's is `HangarOps::runDockedShipsSelfRepair`.
43. ⚠️ **A cross-ship priority/override map needs a COMPOSITE key, and every read site must use the
    job's key rather than the system's id.** `SelfRepair::$priorityChanges` was keyed by system id
    alone; with docked units in the same list that collides between two docked hulls and between a
    docked hull and the carrier. The subtle half is the WRITE side - the "fully repaired, drop the
    override" branch read `$systemToRepair->id`, which would have cleared the carrier's own override
    of that number. Carry the key in the job. (Format `d<shipid>:<sysid>[-<critid>]`; the note
    round-trip splits on `;` only, and `notevalue` is varchar(4096).)
44. ⚠️ **DO NOT RANK ANOTHER UNIT'S DAMAGE FOR THE PLAYER - BUILT AND WITHDRAWN THE SAME DAY (D42 →
    D45).** Stage 17 first put the docked units in their own TIER, compared before priority so they
    were always repaired last, which is what the brief literally said. Seeing it, the user withdrew
    it: *"this should really be the player's choice, they may wish to prioritise repairing Docked
    ships."* ⭐ Two things to carry: **"whose damage matters more" is a player judgement**, and an
    engine that answers it reads as a bug even when the brief asked for it; and **a tier is far more
    expensive than the three lines it looks like**, because every UI gesture that writes a priority
    then has to be clamped to it - the drag's drop index, the drag's upward cascade, AND
    Move-to-Top's "what is the maximum" - or the player sets a number and watches nothing move.
    Deleting the tier deleted all three clamps. What replaced it is one list, one sort by priority,
    and a per-row LABEL (the owning ship's name in cyan), which is all the separation a mixed list
    of other people's systems actually needed.
45. ⚠️⚠️ **A STOWED UNIT'S SHIP WINDOW IS DEAD TO THE TOUCH, and it is one line in
    `SystemIcon.clickSystem`.** Its opening guard is `shipManager.isDestroyed(ship)`, which folds
    `removed` in - so no icon of a docked ship, a docked flight or a rail-parked LCV has ever
    responded to a click. The fix is NOT to widen `isDestroyed` (§3.16's ⚠️⚠️) but to use the
    predicate the client already has for exactly this distinction, `shipManager.isDestroyedByDamage`
    ("gone" vs "parked out of sight"), at that ONE site - and then to divert the stowed unit
    straight to the info menu rather than let it fall through into weapon selection, called shots,
    hangar dialogs and LCV rails, none of which mean anything from inside a hangar.
46. ⭐ **"Off the board" is not one behaviour.** A fleet-list row for a docked FLIGHT opens its
    window (it has no hex of its own); a row for a docked SHIP scrolls to its CARRIER (it does have
    one - its carrier's); a row for a hyperspace reinforcement opens its window (there is no hex
    yet). Left-click meaning "show me where this is" everywhere and right-click meaning "open it"
    everywhere is what keeps the list legible; a state that quietly swaps the two reads as a bug.

47. ⚠️⚠️ **A MENU WHOSE GATES DO NOT ASK ABOUT THE SHIP WILL OPEN ON A UNIT ITS OWN HANDLERS
    REFUSE, and the player then reports "the buttons do nothing".** All six
    `SystemInfoButtons` power gates (`canOffline`, `canOnline`, `canBoost`, `canDeBoost`,
    `canOverload`, `canStopOverload`) test the PHASE, the SYSTEM and the PLAYER and never the
    ship's state - while four of the mutations they lead to opened with
    `shipManager.isDestroyed(ship)`. On a docked ship the menu therefore drew every button and
    nothing happened when they were clicked (§3.16a). ⭐ Two generalising halves. First, when a
    gate and its action disagree about eligibility, the SYMPTOM is always this one, so
    "the menu appears but does nothing" should send you looking for a guard inside the handler,
    not a missing one in the gate. Second, **a partial refusal is worse than a total one**: boost
    and unboost worked all along because `clickPlus`/`clickMinus` never had the guard, which made
    the panel look broken rather than switched off and cost a play-test to pin down.
    ⚠️ The fix is a NAMED predicate used by the affected paths only
    (`shipManager.power.isPowerManageable`), never a widening of `isDestroyed` - trap 45 and
    §3.16 both say why, and this is now the second site to need the same carve-out.

48. ⭐ **A DERIVED NUMBER THAT NOTHING PERSISTS IS IMMUNE TO TRAP 23, AND THAT IS A DESIGN OPTION.**
    Trap 23 is about a number read in `TacGamedata::onConstructed()` above the per-ship
    enhancement loop. "The sum of every docked reactor's surplus" is exactly that kind of
    number - and §3.16 flagged it as the third system in this plan to meet the trap. It does not,
    because it is never stored: it is a function evaluated at every read, on the client, from
    live objects. ⚠️ The price is that it can only be as authoritative as the place it is
    computed, which for power in Fiery Void is the CLIENT (D46: `submitPower` validates nothing
    and there is no server twin of `getReactorPower`). ⭐ So the real ruling is: when a figure
    has no server-side owner, choose between a cached snapshot that can be stale and a live
    derivation that can only be advisory - and say in the tooltip which one the player is
    looking at.

49. ⚠️ **A "SHARED RESOURCE" MUST SAY WHETHER THE DONOR IS CHARGED, OR IT WILL OSCILLATE.** The
    docked-power grant reads each donor's surplus and does NOT deduct it. Deducting would drop
    the donor's surplus to 0, the next recompute would take the grant away, and the figure would
    flip on every render - a live-derived number cannot spend from the source it is derived from.
    ⚠️ Clamp each contributor at 0 BEFORE summing, too: without that, one over-boosted donor
    silently taxes the recipient, which is the opposite of what "sharing" means. Both facts
    generalise to any pooled figure computed from its contributors rather than stored.

50. ⭐⭐ **EVERY DERIVED FIGURE IN THE CLIENT IS COMPUTED BY THE VIEWER FROM WHAT THE VIEWER MAY SEE,
    SO A MASKED INPUT SILENTLY MAKES TWO PLAYERS DISAGREE - AND NOTHING WARNS YOU.** The
    docked-power grant is the first POWER figure in Fiery Void with a masked input: the owner's
    client summed the docked ships and the opponent's found none, so the same reactor read 7 and 6
    on the two screens (user report 2026-09-12, §3.16b). Until then every input to a power figure
    was public, so the question had never arisen.
    ⭐ **The general rule this produced:** when a derived number is added, ask *who computes it and
    what can they see* - and if any input is masked, the figure needs either a disclosure or an
    explicit statement that it is owner-only. Masking has a direction here that is unlike every
    other mask in the tree: the usual failure is showing too much, but a derived figure fails by
    showing a DIFFERENT ANSWER, which reads as a bug rather than as concealment.
    ⭐ **And the fix has a shape:** disclose the minimum INPUT and let both clients run the one
    function, rather than recomputing the figure on the server. A server number would have to agree
    with the owner's live client figure at every moment; one shared function cannot drift by
    construction. ⚠️ `EdfExposure::getMaxAvailablePower` is a standing warning about the other
    choice - it is a partial server mirror of `getReactorPower` whose own comment says "keep the two
    in step", and it is a CEILING at maximum shed, blind to per-turn offline rows, boost cost and
    overload draw, so it could not have answered this even though it looks as though it should.
    ⚠️⚠️ **When you widen a mask, add a NEW key rather than pruning the masked one.** An outside
    viewer's `shipsDocked` had ALWAYS been `[]`, so no client consumer had ever met a partial entry -
    and four of them (`HangarShared` capacity, the fire-menu dock dialogs, `SelfRepairList`,
    `fleetListManager.carrierHolding`) read `boxes`, `phpclass` or `dockTurn` off those rows. Pruning
    would have been a silent `NaN` in all four. A separate key that exactly one function reads has
    no blast radius at all.

51. ⚠️⚠️ **`attached` MEANS BOARDING EVERYWHERE ELSE IN THE TREE.** A docking Waymarker and a
    breaching pod are the same state to the movement mirror, to mathlib's same-hex bearing, to the
    CnC's detach/destroy sweep, to Firing's spill-to-host rule and to the map tooltip - which read
    "Ship is being Boarded!" off a docking manoeuvre until Stage 19 filtered it. **No Stage 19 rule
    may key off `attached` alone**: the bay's own `shipsAttaching` list is the discriminator
    (`HangarOps::attachedBayShipFor` / `bayCarrierAttachedTo`, `shipManager.isDockingRider`), and
    each opens with the empty-`attached` early-out so the question is free in a game with no
    boarding in it.
    ⭐ **The corollary is what made the feature cheap:** movement mirroring, the client's five
    movement lock-out sites and the never-rammed-by-your-host rule all came free BECAUSE it is the
    same state. Reusing a state means inheriting every consumer of it - read them all first, then
    decide which ones need the discriminator.

52. ⭐ **A TWO-PART STATE WANTS ITS TWO HALVES IN DIFFERENT PLACES.** The `attached` clamp
    round-trips through the CARRIER's CnC `Attached` / `Detached` notes; the bay's
    `shipsAttaching` list only records that the ride is a docking one and whose boxes it holds.
    Because they are separate, a rider that something ELSE detached - the CnC's boarding sweep
    writes `Detached` the moment the host's structure at that location dies - is noticed as an
    **ABORT** (entry dropped, boxes released, ship left on the board) instead of being completed
    blind. A single fused record would have finished a dock for a ship attached to nothing.

53. ⚠️⚠️ **`isDestroyed()` FOLDS `removed` IN, SO EVERY "OUT OF PLAY" SWEEP SILENTLY EXCLUDES A
    DOCKED UNIT** - and the two that had to stop doing so (`TacGamedata::setEdfHexes`,
    `EW::collectEwDetectors`) carried the identical four exclusions written out twice.
    ⭐ **The fix is one shared reader that answers "where does this unit project FROM, or null"**
    (`HangarOps::projectionOriginFor`, client twin `shipManager.getProjectionOrigin`), because the
    exclusion and the POSITION are the same problem: a stowed unit's own last movement row is
    wherever it happened to dock and stops being true the moment its carrier moves. Anything that
    measures hexes from a unit must ask it.
    ⚠️ `BaseShip::isDestroyedByDamage()` is now the server twin of a client predicate that has
    existed since Hangar Ops - the THIRD site in three stages to need that exact carve-out (Stage
    17's ship window, Stage 18's power menu, this) - and still never a change to `isDestroyed()`.
    ⚠️ `EdfNetLinks::buildOccupancy` deliberately does NOT get the new reader: it counts units
    *standing in* a corridor, a stowed unit stands nowhere of its own, and its carrier is already
    counted.

54. ⭐⭐ **A DERIVED FIGURE WITH A MASKED INPUT NEEDS A PUBLISHED TWIN, NOT A WIDER MASK** - trap 50's
    shape, hit again in the very next stage and in a different subsystem, which is what makes it a
    pattern rather than an incident. A field projected from INSIDE a hull has no icon of its own, so
    the carrier's icon draws the disc - but the radius can only be derived from the bay's ship list,
    which is masked, so the opponent would have watched the drain apply over hexes with no disc on
    them. (The hexes are already public in `edfHexes`; only the SOURCE is hidden, which is exactly
    why publishing the radius discloses nothing new.) The server publishes the finished number per
    hangar to everyone and the client **MAXes** it against its own live walk: the owner gets a figure
    that follows the power they are allocating this phase, the opponent gets the committed one.
    ⚠️ `Hangar::$stowedEdfRadius` is **PROTECTED**. A public property rides the static blueprint,
    which would put a live per-turn number into a cached per-CLASS artefact and add a key to every
    hangar in the game; `stripForJson` reflects IS_PUBLIC only, so a protected field is invisible to
    it and is copied by hand.
    ⚠️ The EW half of the same ruling needed no twin, and the reason will not generalise: that
    allowance is filtered to the viewer's own team, whose bays are disclosed to them. A rule that
    read the detector list ACROSS teams would need the same treatment.

55. ⚠️⚠️ **"IS THIS SHIP IN THE SUBMISSION" IS NOT "DID THIS SHIP MOVE".** `ajaxInterface` sends an
    entry for EVERY ship the player owns, and for an ATTACHED one it deliberately sends an EMPTY
    movement list - the client refuses to plot a move for a unit riding a host. So
    `MovementGamePhase::process`'s `$submittedShipIds`, built from presence alone, read a rider as
    having moved itself and skipped the mirror that copies the host's path onto it. The rider then
    sat on its preturn `sync` row at the host's START hex for the whole Firing phase (game 4351).
    ⭐⭐ **THE REASON IT HAD SURVIVED FOR YEARS IS THE REAL LESSON:** the guard exists so a DETACH
    submission is not overwritten, and until Stage 19 every attached pair in the game was a boarding
    pod and its victim - **different players, never in one submission**. Reusing a state (trap 51)
    inherits every guard written for it, including the ones whose assumptions were never stated.
    When you put an existing mechanism to a new use, list its consumers AND the conditions each of
    them has silently been relying on.

56. ⭐ **A PREDICATE THAT MOVES ONTO A HOT PATH NEEDS AN EARLY-OUT, AND THE RIGHT ONE IS A FACT ABOUT
    THE RULE.** `shipManager.isDockingRider` was written for a banner (a handful of calls) and then
    became the gate on both ends of every EW button in the Initial Orders menu and on every weapon
    click - dozens of fleet walks per gesture. The fix is not a cache: a rider is ALWAYS `attached`,
    so one empty-object test rejects every unit in every game with no boarding and no docking
    manoeuvre in it, and it cannot go stale because it is the same fact the rule is made of.
    ⚠️ The same shape appears in `EW::stripDockingRiderEw` (collect the rider ids ONCE per
    submitting ship, not per EW row) and in `setEdfHexes` / `collectEwDetectors` (the `instanceof`
    sweep comes first and every other question is deferred behind it).
57. ⚠️⚠️ **A PLAN WRITTEN BEFORE A REWORK MUST BE RE-READ AGAINST IT.** §3.18 said "power-turns are
    boost levels" - true of the vortex-era drive it was written for, and wrong the day §3.17b made
    every Walker drive a legacy one whose ONLY boost level is the jump itself. Found by reading
    `markLegacy()` before designing, not by reading the plan.
58. ⚠️⚠️ **A LEGACY DRIVE IS INVISIBLE TO EVERY "IS THIS A BALLISTIC ORDER" TEST THAT READS THE WEAPON.**
    `markLegacy()` sets `$ballistic = false`, so a type-`ballistic` order on one is (a) stripped from
    every Firing-phase payload by `hideSystemFireOrders` - the `jumpexit` bug again, fixed the same way;
    (b) never counted by `weaponManager.hasFiringOrder` in Initial Orders, so there is no generic remove
    button and `canOffline` does not block it; (c) never selected by `SystemIcon`'s Initial Orders
    clause; (d) **taken for DECLARED DIRECT FIRE by `weaponManager.canCombatTurn`**, which is what gates
    the CANCEL MOVE icon (`UI.shipMovement`) - so a ship with a standing abduction could not undo a
    plotted move for the whole Movement phase (user report 2026-09-20, game 4371; `jumpexit` had the
    identical bug and was fixed with it). Ask the ORDER's type or damageclass, or give the system a
    predicate of its own. ⭐ **The tell is always the same**: the ordinary-drive version of the gesture
    (a `jumppoint` order, on a still-ballistic engine) behaves correctly, and only the legacy one
    misbehaves. When a Walker-only symptom has an ordinary-drive twin that works, look here first.
59. ⭐ **`hasSpecialTargeting` ON A PROTOTYPE CHANGES EVERY INSTANCE'S ICON CLICK.** `SystemIcon` treats
    such a weapon's existing order as editable rather than committed. Set it per INSTANCE, keyed off a
    payload field only the right instances carry (`abductionMaxPower`).
60. ⭐ **WHEN A RULE'S INPUT IS MASKED, ASK WHETHER THE INPUT BELONGS IN THE RULE AT ALL.** The first
    build summed docked units' ramming factors into the abduction cost, found that bay contents are
    own-team-only, and answered with TWO figures - a published preview without them and a locked cost
    with them. The user's ruling (D57) removed the input instead, and the two figures became one. Before
    engineering around a masked input (trap 54's published twin, or a split figure), put the question
    to the user.

---

## 6. Open questions — ALL RESOLVED (Q1–Q15)

Kept as the record of what was asked and why; the rulings are D5–D10 (Q1–Q7) and D11–D26 (Q8–Q15)
in §0, and are folded into the sections they affect. Nothing in this plan is waiting on an answer.

**Q1 — EDF and concealment. → D5, out of scope.** The Walkers have no stealth function, so the
question is academic. §2.1 records what would make it live again and where the guard would go.

**Q2 — Wide-Beam declaration timing. → D6, firing mode in the Fire phase.** §3.3.

**Q3 — EDN corridor choice. → D8, deterministic with a player-preferring tie-break.** Prefer the
corridor containing an enemy unit, then the one adding the most new field, then lowest `(q, r)`
for stability. §3.7.

**Q4 — CPD adaptation scope. → D7, the raw `faction` string.** No faction families. §3.4.

**Q5 — Control sheets. → D4, supplied as a Walker test ship per system.** Each stage opens when the
user lands a hull carrying a basic version of that stage's system; the stats are then read out of
the hull file rather than transcribed. Stage 3 varied it — the sheet arrived as a per-firing-mode
stat table rather than a hull, which worked just as well. Still needed across the whole plan:
damage / range / fire-control / power / RoF for the remaining weapons, radii and power for the three
field systems, EW-Detector range, and point costs throughout.

**Q6 — CPD adaptation vs the Torvalus Shading Field (asked 2026-09-03). → D9.** Adaptation eats into
the field's whole defensive contribution, shaded doubling included, and never touches its
stealth/detection mechanics. §3.4 — the bucket already does exactly this, so no code was needed.

**Q7 — What does this cost every other game? (raised 2026-09-03). → D10.** One static boolean and no
autoload. §3.4 carries the table of what an ordinary game actually pays, and the gate has its own
load-bearing test.
**Q8 — Is the Mapmaker EW pool 3 per FLIGHT or 3 per CRAFT? (asked 2026-09-08). → ANSWERED: 3 per
flight.** The hull's comment (*"Advanced Sensors w/ 3 EW"* on each `Fighter`,
[MapmakerProbes.php:58](source/server/model/ships/walkers/MapmakerProbes.php#L58)) would have made a
full flight 18; it does not. D11 stands as written, and the pool does not scale with surviving
craft. §3.11.

**Q9 — Does the SCT keep its green HEX at a named-unit waypoint? (asked 2026-09-08). → ANSWERED:
yes.** *"Keep hex but remove green text."* §3.10b as written — the marker survives, only
`marker.text` and `SCT_MARKER_TEXT_COLOUR` go.

**Q10 — Do BDEW and SDEW join the Mapmaker's DEW subtraction? (asked 2026-09-08). → ANSWERED: yes.**
*"BDEW and SDEW are separate EW functions so would stack with the fighter's own DEW."* The
subtraction is `OEW − (DEW + BDEW + SDEW)`, floored at 0, and all three are then zeroed. D12 is
extended accordingly, and it now agrees with the EDJD's own condition (§3.18) rather than differing
from it. §3.11.

**Q11 — Why only ONE Waymarker in a 24-box bay? (asked 2026-09-08). → ANSWERED: the 12 was a slip.**
A Waymarker costs **24 boxes**, and the bay also holds **2 Pathfinders** at 12 each — which the
first statement of the brief omitted. Every load now fills the 24-box bay exactly and no per-type
cap is needed at all. The Waymarker's real condition is a **two-turn** dock/launch procedure (D23,
§3.14a), optional within Stage 16.

**Q12 — Is an abduction's cost locked or live? (asked 2026-09-08). → ANSWERED: locked.** Fixed at the
first power-turn and carried in the note, so a target that is abducted *and* shot does not get
cheaper as its ramming factor falls. §3.18.

**Q13 — Do the terrain rows of the EDJD table matter? (asked 2026-09-08). → ANSWERED twice.**
*First, 2026-09-08: out of scope for now.* *"Asteroid, Moon, Planetoid: 10 × radius³"* and *"Planet or
larger: Unknown"* are not built. Terrain has no radius property in FV, `moonNew`'s ramming factor is
5,500, and nothing in the codebase would let a moon leave the board.
**REOPENED AND ANSWERED 2026-09-20 (D66): terrain CAN be abducted, and the `10 × radius³` row still is
not built.** The two halves of the first answer came apart: *"nothing would let a moon leave the board"*
was wrong — `Movement::applyJumpOut` works on any unit with a primary Structure, which terrain has —
while *"terrain has no radius property"* was right and is exactly why the formula stays unbuilt. Terrain
pays the ordinary `ceil(RF / 50)`, which leaves `moonNew` at 110 power-turns, so the practical answer to
*"can you abduct a moon"* is still no. §3.18b.

**Q14 — Is the fighter Medium Lightning Array's reload 2 turns or 4? (raised 2026-09-08).
→ ANSWERED: 4.** The brief said *"a recharge rate of 2 turns"* and the control sheet said
**1 per 4 turns**; the sheet is right (*"Ah yes, loading time is 4, my mistake"*). `loadingtime = 4`.
§3.13.

**Q15 — Does anything else read *"Flight-Level combat"*? (asked 2026-09-08). → ANSWERED: no, the
term has no FV meaning.** The sheet's *"Does not use Flight-Level combat or Offensive Bonus"* is one
exclusion, not two: use flight EW instead of the offensive bonus, and change nothing else in the
`instanceof FighterFlight` branch. D25. §3.13.

---

## 7. What this plan deliberately does not do

- **No database schema change.** Everything persists through `tac_critical.param`,
  `tac_individual_notes` and `tac_systemdata`, all of which already exist.
- **No new phase**, no change to the turn loop.
- **No new plotting engine** — D2's design reuses `doMultipleHexFireOrders`.
- **No rewrite of the Gravitic Mine geometry** — Stage 0 moves it unchanged and proves it with the
  harness.
- **No blanket removal of the `factionAge` enhancement gate** — §3.3 narrows it instead, so no
  existing Ancient hull gains an enhancement it does not have today.
- **No new hangar engine** — §3.14's Docking Bay is `DockingCollar`'s LCV dock with a list instead
  of one slot; the removal, the resurrection and the persistence are already written.
- **No new departure mechanism** — §3.17 does not add a phase or a turn step. It DEFERS an existing
  removal from the top of `MovementGamePhase::advance` to the end of the Firing phase, where the
  other departure path already runs.
