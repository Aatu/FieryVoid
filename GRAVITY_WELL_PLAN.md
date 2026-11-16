# Gravity Well — terrain plan

User request 2026-11-07. A new Create Game terrain feature (one random well, strength 1-3, placed near the map
centre) plus a Terrain-faction unit players can buy and place themselves. Same unit, same rules, both ways.

## 1. The rule (user's text, B5W)

> All units are drawn towards the gravity well's hex when all other movement is complete (so between Movement and
> Pre-Firing phase), a number of hexes equal to the strength of the gravity well. The first priority is to move it
> into the hex closest to the [gravity well]. If there is more than one choice, move the unit in the direction of its
> motion (the heading it is moving at the end of the turn, but if this does not resolve the question, use the last
> appropriate direction). A unit will not be pulled farther than the center hex of the gravity source. Any unit
> that enters such a hex would be destroyed. Advanced races treat gravity sources as 1 point less than they actually are.

User notes: one well per map from Create Game, strength 1-3, placed in roughly the 25% dust area; no dragging past the
well hex; dragged in = destroyed; bases, OSATs and Huge terrain immune; Enormous, blocks LoS; Singularity.png; rare,
so gate efficiently.

## 2. Rulings (2026-11-07)

| # | Question | Ruling |
|---|---|---|
| R1 | How is strength picked when buying? | **Three variants**: `GravityWell` (1), `GravityWell2`, `GravityWell3` - 2 and 3 nested under 1 in the lobby. No enhancement code. |
| R2 | Pulled into solid terrain / an Enormous unit? | **Enter and collide** - the pull's rows count as hexes entered, so the ordinary terrain collision (asteroid, moon crash, dust, meteors) or auto-ram follows in Pre-Firing. |
| R3 | Two neighbours both one hex nearer the well? | **Heading decides** (literal - B5W counts distance in hexes): the one nearer the unit's heading. Adjacent hex directions never tie against an integer heading, so the "last appropriate direction" fallback can never be needed. |
| R4 | When is the random well's hex chosen? | **At game start** (BuyingGamePhase::advance) with the other random terrain. Not drawn on the Map Preview; the legend and scenario card name it. |

Settled from the code, not asked:
- "Advanced race" = `factionAge >= 3`, the convention the Nebula and EM Storm already use. Strength 1 does not move them.
- "Closest to the map edge" in the rule text is read as closest to the well (the tabletop rule was written for a planet's edge).
- Immune: terrain (all of it - every terrain unit is a base anyway), bases, OSATs (mines included), undeployed and destroyed units.
- A unit entering the well hex by ITS OWN movement is destroyed too (the rule says "any unit that enters").
- Attached riders (pods, grapples) are not pulled on their own: they get their host's path.
- Several wells (bought ones beside the random one) each pull in turn, lowest id first. A unit already in any well hex is not pulled further.

## 3. Shape

- **Unit**: `ships/terrain/GravityWell.php` (+ `GravityWell2`, `GravityWell3`). Terrain, base, Enormous (blocks LoS - the
  existing Enormous rule), Huge 0, `$gravityStrength`. Systems: `GravityWellRammingAttack` (autoFireOnly), OSATCnC, Structure.
- **Rule**: `GravityWellRule` - rules key `gravityWell`: the strength, 1..3. Absent in every other game.
  BuyingGamePhase::process (slot 1) submits one unit of the matching class (userid -5); ::advance places it FIRST among
  the random terrain, inside the central ellipse (semi-axes width/4 x height/4 - the 25% clump's ellipse), clear of a
  template's terrain, and registers it as a moon so moons keep 7 hexes and asteroids 4.
- **Pull**: `handlers/GravityWells.php` - `GravityWells::resolvePull($gamedata, $dbManager)`, called once from
  MovementGamePhase::advance after the ship loop (every 'end' row written) and before ELINT / missile detection read
  positions. Gate = one `instanceof GravityWell` scan of the ship list; nothing else runs in any other game.
  Per pulled unit: ONE log order (`GravityWellPull`, prefiring, well -> unit, rolled 1, shots/shotshit 0) inserted at
  once for its id, then one movement row per hex of type **`gravity`**, value = that order's id (replay pairing).
- **Destruction**: `GravityWellRammingAttack` (specialWeapons.php). Its Pre-Firing sweep is the parent's (units that
  entered the hex) plus any unit standing in the hex with no order yet; damageclass `GravityWellCollision`, auto-hit,
  beforeDamage destroys outright (primary structure, or every craft of a flight), no return damage on the well.

### Why a new movement type
Every existing forced shift ('prefire': Gravitic Mine, Gravity Net, Transverse Drive, Singularity) is deliberately NOT
a hex entered by the collision sweep (`RammingAttack::isHexTransit`). R2 needs the pull to be one, and changing that
for 'prefire' would change four existing weapons. So the pull writes `gravity` rows: a forced shift everywhere
'prefire' is one (bearing walk-back, replay), AND a hex transit.

## 4. Every site

Server
- `model/GravityWellRule.php` (new), `GameRules::getGravityWellRules`.
- `ships/terrain/GravityWell.php`, `GravityWell2.php`, `GravityWell3.php` (new).
- `handlers/GravityWells.php` (new).
- `weapons/specialWeapons.php`: `GravityWellRammingAttack`; `RammingAttack::isHexTransit` counts `gravity` rows.
- `Phase/MovementGamePhase.php`: the call.
- `Phase/BuyingGamePhase.php`: process + advance placement.
- `ships/ShipClasses.php` `getPreviousCoPos`: `gravity` is a forced shift.
- `handlers/firing.php` `isHyperspaceLogOrder`: `GravityWellPull` is a log line, not a shot; `validateFireOrders`
  rejects a re-posted one (the Homing Missile re-attack convention).

Client
- `model/weapon/special.js`: `GravityWellRammingAttack` stub.
- `movement.js` `getPreviousLocation`, `renderer/icon/ShipIcon.js`: `gravity` is a forced shift (twins of the server).
- `weaponManager.doShortLogText`, `combatLog.js` (STANDALONE kind + "GRAVITY WELL:" header), `declarations.js` (skip the pull line).
- `creategame.php` row, `UI/createGame.js` (rules + legend), `UI/mapPreview.js` (`GRAVITY_WELL_SWATCH`),
  `UI/scenarioCard.js` chip, `gamelobby.js` legend.
- Replay: `ReplayAnimationStrategy.animateWeaponPreFire` **Pass 0** plays every pull (line, then drag) before any
  Pre-Firing weapon, and Pass 2 draws a pulled unit's hits where the pull left it (`shiftedTo`); see section 7.

Build: autoload (new classes), statics (Terrain.json), legacy bundles.

## 5. Traps

- ⚠️ The pull runs in Movement's ADVANCE, on `$latestgameData` (a real load) - never in process() (POST-side ships).
- ⚠️ Pull log orders keep shots/shotshit 0: `DBManager::submitDamages` back-fills an unknown fireorderid by
  (shooter, weapon, target, shotshit > 0), and the collision order on the same weapon and target deals real damage.
- ⚠️ A log order needs rolled > 0 or the printed log drops it; and `Firing::fire` skips any order already rolled.
- The 'end' rows are written to the DB but NOT added to the in-memory ships; the gravity rows are inserted after them
  (higher ids), so the DB order is move ... end, gravity ... and the in-memory last row is the pulled position.
- ⚠️ The pull order's x/y stay 0. An order's x/y is not masked per viewer, so the end hex there would give away a
  stealthed unit's position; `hideStealthShipMovement` masks the gravity ROWS with the rest of its movement.
- ⚠️ (corrected 2026-11-07) The phase-5 strip does NOT touch these orders: it keys on `$weapon->preFires`, and
  RammingAttack's is false. So the pull lines are in every Pre-Firing payload, the owner of a bought well re-posts them
  on the Pre-Firing commit, and the `validateFireOrders` reject is the live guard, not a backstop. It was the PHASE-3
  strip (non-preFires, non-ballistic, current turn) that removed them - see section 7.

## 6. As built / verify (2026-11-07, uncommitted)

All of section 4 built. `fvbuild -Server` (autoload: exactly the 6 new classes; Terrain.json: the 3 units), legacy
bundles rebuilt with `node scripts/bundle-legacy.js`, `fvbuild -Sync`.

Verify kit: session scratchpad `gravitywell_test.php` (piped on stdin into the php container, read-only - a
recording stub stands in for the DB writes). 5,245 checks: every step from a 15x15 patch x 6 headings closes one hex
and takes the heading-nearer neighbour (never a tie); path stops in the well; rule round-trip and bad strengths; the
random hex always inside the ellipse and 3+ from template terrain; on game 4524 with a strength-3 well injected -
immune units untouched, every other unit min(strength, distance) rows, advanced race one fewer, a rider carried hex for
hex with one line, pull orders log-only and rejected when re-posted, the two units dragged in get exactly the two
collision orders and are destroyed (no return damage), a flight placed on a well loses every craft, a unit dragged
through an asteroid hex gets a TerrainCollision while the same drag written as 'prefire' still does not.

Not verified: a live play-test (create a game with the option, buy one from the Terrain faction, watch a replay).

Observations, not changed:
- Hyach/Torvalus post-move stealth checks (`checkStealth`, Chameleon reveal) run inside Movement's ship loop, BEFORE
  the pull, so they judge pre-pull positions.
- A pull line names the unit it moved, stealthed or not (as terrain collision lines already do).

## 7. Play-test fixes (games 4548 / 4549, 2026-11-07, uncommitted)

1. **FAQ** (`docs/faq.html`, Terrain): every terrain type is now an `<h3>` with a `data-anchor` (asteroids, moons,
   dust, meteors, nebula, emstorm, gravitywell, placingterrain), so each is listed under Terrain in the viewer's left
   list; the meteor table moved under Meteor Swarms; new Gravity Well entry.
2. **Current-turn replay in the Firing phase showed no drag.** `TacGamedata::hideSystemFireOrders`' phase-3 strip
   removed the GravityWellPull / GravityWellCollision orders (RammingAttack is not preFires - the TerrainCollision
   whitelist exists for the same reason), so no order paired with the 'gravity' rows. Both classes added to that
   whitelist. Safe to re-post: `submitFireorders` writes 'prefiring' only in phase 5. Ordinary Pre-Firing shifts were
   never affected (their weapons are preFires, exempt from that strip). Verified on 4549 turn 2: 0 -> 3 orders.
3. **A unit dragged in was struck, and exploded, one hex short of the well.** Replay positions come from the movement
   animation, which stops where the unit's own movement ended. Now: Pass 0 (pull line, then the drag, for every
   pulled unit, before any Pre-Firing weapon), `pulledTo` as the start of any later forced move in Passes 1-2, Pass 2's
   hits drawn there (`AllWeaponFireAgainstShipAnimation` opt `shiftedTo`), and `ShipDestroyedAnimation` explodes a
   ship at its last forced-move row (it plays after all firing) - which fixes the same for every Pre-Firing shift.
   Verify kit: session scratchpad `gw_replay_order_test.js` (the real strategy + destroyed animation on stubs shaped
   like 4548 turn 4): 10 checks pass; the HEAD files fail 4 of them.

4. **A dragged ship jumping out opened its vortex where its own movement ended** (game 4550: a Jupiter's legacy FTL
   drive boosted, dragged (1,12) -> (3,9), vortex at (1,12)). `ShipJumpAnimation` now uses the last forced-move row,
   the same rule as `ShipDestroyedAnimation`. Only boosted drives can show it: a ship leaving through a jump point goes
   in Movement's advance (`resolveJumpOuts`), before the pull. Kit: scratchpad `gw_jump_test.js`, 4 checks; HEAD fails 3.

5. **Every shot origin from the hex the ship is actually in** (user request after 4550/4551 - "just in case").
   Audit: two origin sites. `FireAnimationHelper.getShipPositionForFiring` (hex-targeted shots) started a moved ship's
   shot where its own movement ended - invisible on `noProjectile` weapons (Plasma Web, Transverse Drive "passed" in
   4550 for that reason), and on the Shredder's AIM shot in 4551 (the bolts the user saw were its per-target orders,
   already right). `AllWeaponFireAgainstShipAnimation` drew a BALLISTIC from the post-forced-move hex, because its
   forced-move override ignores the launch time - so a pulled ship's missiles left the dragged-to hex (and, after
   `shiftedTo` above, its Pre-Firing ballistics too). The rule now, at both sites:
   - ballistic: the launch hex (start of the turn's movement) - before any movement or forced move;
   - Pre-Firing: end of own movement, then the pull (last 'gravity' row) - pulls play before any Pre-Firing;
   - Firing: after every forced move (last 'prefire'/'gravity' row).
   Kit: scratchpad `gw_origin_test.js` (the three real files on stubs), 14 checks; HEAD fails 7.

6. **Ballistic icon launch hex / line start** (4550: the Transverse Drive's yellow launch hex sat on the pre-drag hex).
   `BallisticIconContainer` took a 'normal' / 'prefiring' order's launch hex from `shooterIcon.getLastMovement()`,
   which a replay builds WITHOUT the turn's 'prefire' / 'gravity' rows and the live view WITH them: the Dark Knife's
   launch read (1,6) in the replay (pre-drag) and (1,7) live (its own jump DESTINATION). New `getOrderFiringHex`
   reads the ship's raw rows instead - Pre-Firing: last row of the turn that is not 'prefire'; Firing: last row - and
   both the launch sprite and the ballistic line use it. Now (2,4) in both. Ballistic-type orders keep the start-of-turn
   hex. Kit: scratchpad `gw_launch_hex_test.js` (the function lifted from the file, on 4550's real replay payload).

Not covered: a ship shifted by one Pre-Firing effect and firing ANOTHER Pre-Firing weapon - its origin ignores the
shift, matching the replay, which plays the hex-targeted Pre-Firing pass before the per-target one.
