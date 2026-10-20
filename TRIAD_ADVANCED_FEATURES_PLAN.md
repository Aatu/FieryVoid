# Triad Advanced Features — Hyperplasma Cutter defence, Jealous ELINT, Command Node

Status: **BUILT 2026-10-08/09 — uncommitted; play-test rounds 1 (§8) and 2 (§9) addressed, round 2 not yet
re-tested in game.** Three features for The Triad, asked for together and built in three stages (§5). §6 is the
build record; §7 the play-test checklist.

Sources: the user's request (2026-10-08) and two tabletop rule extracts it attached (*Command Node*,
*Jealous ELINT*), transcribed in §1.2 and §1.3.

---

## 0. Rulings (user, 2026-10-08)

| # | Question | Ruling |
|---|---|---|
| H1 | Cutter defensive dialog | Slicer-style rows (~~one per cutter~~ - one per SHIP since H3). A defensive shot is a **block of N dice = -5N% against one incoming shot** ("9 dice could be used as a single 45% intercept, or 3 15% intercepts"), so each row is *shots x dice per shot* |
| H2 | Manual interception (INCOMING list) | **"Just 5% per die added with a click"** - each click puts one more die on that shot |
| J1 | "One Jealous ELINT vessel per every four" | **Round up**: 1-4 → 1 may act as ELINT each turn, 5-8 → 2 |
| J2 | Which ships | **Jealous ELINT: Chaos Demon, Chaos Fiend, Lesser Triumviron, Neutrality Wraith.** The **Archangel is a normal ELINT** ship (neither Constrained nor Jealous). **Constrained EW is removed from every Triad ship** - it was a stop-gap for the missing Jealous ELINT |
| C1 | Command Node UI and timing | User design: ship-tooltip buttons in Initial Orders; with the Command Node selected, clicking another friendly Triad capital keeps the selection (as an ELINT does) and offers *Swap Initiative*. Client remembers the choice; the server adjusts `tac_iniative` after Initial Orders are committed. Code lives on `CnC`, switched on per hull by `addTriad()` |
| C2 | The old "+1 initiative" stand-in | **Removed from the Archangel, Demon and Wraith** (`3 *5` → `2 *5`). Triumvirons unchanged at `2 *5` |
| H3 | (play-test, game 4453) Do a ship's cutters share dice for defence? | **Yes** - "the shots from the 3 Cutters ... should share their pool of dice, even for intercept": defence from any cutter can spend all 30. Supersedes H1's per-cutter rows (§8) |
| C3 | (play-test) When does the player see the node's effect? | **Straight away**: the tooltip shows the projected initiative during Initial Orders (§8) |
| H4 | (play-test round 2) Shape of Commit to interception | **One row per intercept shot**, each its own dice - "one 9-dice intercept shot, a 5-dice intercept shot and a 3-dice intercept shot (or any combination using available dice)". Replaces H1's *shots x dice* (§9) |
| H5 | (game 4455 check) Which block does the automation spend first? | **The largest** - "so the largest intercept goes first", against the most dangerous shot. Entry order and dialog count no longer matter (§9) |
| C4 | (play-test round 2) Who keeps the +10 when the node swaps? | **The node, always**: "only the base initiative rolls [are] swapped", the +10 is added after. The DB row records it - classic movement writes the node's ini bonus (`unmodified_iniative`) up by 10 as well (§9) |

Defaults taken without asking (say the word to change any):

| # | Default |
|---|---|
| D1 | Jealous ELINT is designated with a ship-tooltip button (self-click, Initial Orders), the same surface as the Command Node. Standing a ship down clears its ELINT EW for the turn. Not sticky - designate again each turn |
| D2 | Quota and Command Node are per **fleet = slot** (one player may hold several slots in a game) |
| D3 | The swap partner must be in the **same fleet**, alive and on the board |
| D4 | ~~One die from each selected cutter per click~~ - **superseded by H3**: one die per click from the ship's pool, however many cutters are selected |
| D5 | A manual click draws on the ship's **uncommitted** dice. Dice already committed to blocks are not raided; withdraw a block (ship window) to free them |

---

## 1. The rules

### 1.1 Hyperplasma Cutter (FV docs, `docs/factions-tiers.html`)
All cutters on a ship are one weapon; each contributes 10d10. Defensively, each d10 gives -1 (-5%) to
hit against one incoming shot, cumulatively. Sustained mode blocks defensive fire.

### 1.2 Jealous ELINT
> If a ship is designated as a Jealous ELINT vessel, it has a sensor suite with the capabilities to
> function as an ELINT ship, but it will not do so during every turn. Only one Jealous ELINT vessel per
> every four may provide ELINT support to a fleet during any given turn. The decision as to which
> vessel will be an ELINT ship must be made during the EW Determination segment of the Combat
> Sequence. Triad vessels noted as being regular ELINT ships may do so without any effects, and do not
> count towards the Jealous ELINT limitations in any way.

### 1.3 Command Node
> Every turn, the Tri controlling the fleet concentrates more effort on a particular vessel. Once a
> turn, during the Initiative segment, nominate one Triad capital ship to be the Command Node for that
> turn. That vessel gains a +2 initiative modifier, and may swap initiative totals with any other
> friendly Triad capital ship (after all rolls are made).

FV rolls initiative at the turn change, before Initial Orders, so the Initiative segment's choice is
made during Initial Orders and applied when the phase closes (C1). +2 tabletop is **+10** here: FV
initiative is d100 and every modifier is five times its tabletop value.

---

## 2. Hyperplasma Cutter

### 2.1 What was wrong before
`HyperplasmaCutter` (contributed 2026-08-30) had lost the `usesCustomInterceptAllocation` flag, so
it fell into the generic per-gun manual path. Its `getInterceptionMod` indexed the selfIntercept
queue by "how many intercept orders exist" - and every manual order already exists when the first
one is credited, so a manual order was paired with the wrong block, or worth 0.

### 2.2 Currency
Two kinds of defensive order, both priced at **dice x 5%** against ONE shot:

* **Block** - `selfIntercept`, notes `HPC-intercept`, `shots` = dice. Made by the dialog (H1); the
  automation assigns each block to one shot, as before.
* **Manual** - `intercept`, notes `HPC-intercept`, `shots` = 1, naming the shot. One per click per
  selected cutter (H2). No marker behind it: the die rides on the order.

### 2.3 Server (`plasma.php`)
* `getInterceptionMod` credits in sequence: the cutter's manual orders first (in array order - the
  order `automateIntercept`'s totals loop reaches them), then its blocks. Position comes from
  `Weapon::$firedDefensivelyAlready`, which `addToInterceptionTotal` → `fireDefensively` bumps once per
  credited interception, manual or automated. A rejected manual order is detached before it is credited,
  so the list and the counter stay aligned. A running cap of `maxDice` bounds the total.
* `beforeFiringOrderResolution`: `guns` = (every non-selfIntercept order, subordinates and manual
  orders included) + (number of blocks). That makes `automateIntercept`'s split-weapon budget
  `guns - count(orders) + selfIntercepts` come out at exactly the block count, keeps
  `validateManualIntercept`'s gun cap clear of the manual orders, and keeps `isValidInterceptor`
  true only while a block is left. (The old sum double-counted blocks and skipped subordinate
  orders, which could starve a cutter that had lent dice to another cutter's shot.)

### 2.4 Client (`plasma.js`)
* `usesCustomInterceptAllocation`, `canDeclareManualIntercept` (not sustained, dice left),
  `declareManualIntercept` (pushes the 1-die order). No `getSpareInterceptCapacity` - nothing is paired.
* `getRemainingDice` also counts `intercept` orders.
* The dialog: ~~`multiplier` on, so a row reads *[shots] shots [dice] dice* - one row per SHIP since H3 (§8)~~ -
  since H4, `confirm.hyperplasmaIntercept`: one row per intercept shot, each its own dice from the ship's pool (§9).

---

## 3. Jealous ELINT

### 3.1 Server (`ElintScanner`, `baseSystems.php`)
* `markJealous()` adds `JealousELINT`. Jealous hulls call `markAdvanced(); markJealous();`.
* `getSpecialAbilityList` leaves **ELINT** out unless the array is designated for the current turn.
  It runs from `BaseShip::onConstructed`, which `TacGamedata::onConstructed` calls AFTER the note
  sweep, so `hasSpecialAbility("ELINT")` / `isElint()` - and with them SOEW, SDEW, BDEW, DIST, JAM,
  Detect Stealth, the x3 stealth detection range and the jump-point opening bonus - all follow the
  designation with no call-site changes.
* `generateIndividualNotes` (Initial Orders): writes a `JealousELINT` note for a designated array,
  owner only, at most `ceil(N/4)` per fleet per request (N counted on the server-side load).
* `onIndividualNotesLoaded` reads the latest note's turn; `stripForJson` sends `jealousElintTurn` for
  the current turn to the owner/team, and to everyone once Initial Orders have closed (masked in
  phase 1 like EW).

### 3.2 Client
* `ShipSystem.isSpecialAbilityActive(ability)` (default true), asked by
  `shipManager.getSpecialAbilitySystem`; `ElintScanner` answers false for ELINT when jealous and not
  designated. Same single choke point as the server.
* `ElintScanner` statics: `getJealousArray`, `getJealousQuota`, `countJealousDesignated`,
  `canDesignateJealous`, `designateJealous`, `standDownJealous`.
* Tooltip buttons `designateJealousElint` / `standDownJealousElint`; transfer `[1|0]`; Save Orders key
  `jealousElintTurn` (phase 1).

---

## 4. Command Node

### 4.1 Server (`CnC`, `baseSystems.php`)
* `addTriad()` adds `CommandNode`; called on the C&C of the Triumviron, Lesser Triumviron, Archangel,
  Demon and Wraith (every Triad `shipSizeClass` 3).
* `generateIndividualNotes` (Initial Orders): a `CommandNode` note, value = swap partner id or -1, owner
  only, one per fleet per request.
* `CnC::applyTriadCommandNodes($gamedata, $dbManager)` - called FIRST in
  `InitialOrdersGamePhase::advance`, before the first active ship is chosen. Per fleet: swap the ROLLED
  totals with a valid partner, then +10 to the node (C4 - the bonus never travels), then write
  `tac_iniative` (`DBManager::updateIniative`) and re-sort. Simultaneous movement: the raw totals swap,
  the node's raw total moves and the category is re-derived (`SimultaneousMovementRule::getIniativeCategory`).
  Classic movement: the node's total and its ini bonus (`unmodified_iniative`) both move by 10, and an
  exact tie created here is broken by +1 on the adjusted ship (node first), as `Manager::generateIniative`
  breaks ties.
* `stripForJson` sends `commandNodeTurn` / `commandNodeSwap` for the current turn, masked from enemies
  in phase 1.

### 4.2 Client
* `CnC` statics: `getTriadNode`, `canNominate`, `getFleetCommandNode`, `nominate`, `withdraw`,
  `canSwapWith`, `setSwap`, `clearSwap`, `getTooltipLine`. Transfer `[1, swapId]`; Save Orders keys.
* Tooltip buttons `setCommandNode` / `cancelCommandNode` (self) and `swapInitiative` /
  `cancelSwapInitiative` (partner). `InitialPhaseStrategy.selectShip` keeps the selection when the
  Command Node is selected and the clicked ship is a valid partner.
* `ShipTooltip`: the Ini line carries the (projected) total; the node / swap text is a yellow notice in
  the status-effects line (§9).

---

## 5. Stages

1. Cutter (§2). 2. Jealous ELINT + hull changes (§3, J2). 3. Command Node + initiative (§4, C2).
4. Icons, docs (`docs/factions-tiers.html` Triad section), statics, bundles, harness.

## 6. Build record (2026-10-08/09)

### What landed, as §2-§4 describe
* **Cutter** - `plasma.php` (`getInterceptionMod` + `getDefensiveDiceSequence`, `beforeFiringOrderResolution`
  gun count, system-window text); `plasma.js` (pool counts `intercept` orders, `getDefensiveDice`, the
  per-cutter dialog, `addSelfInterceptOrders`, the two manual hooks, "Defensive Dice" line). Comment-only
  updates in `weaponManager.js` and `molecular.js`, which still called the Cutter "out of scope".
* **Jealous ELINT** - `ElintScanner` in both `baseSystems.php` and `baseSystems.js`;
  `ShipSystem.prototype.isSpecialAbilityActive` (`shipSystem.js`) asked by
  `shipManager.getSpecialAbilitySystem` (`ships.js`); auto ship-notes line in `ShipClasses.php`.
* **Command Node** - `CnC` in both `baseSystems` files; `DBManager::updateIniative`;
  `SimultaneousMovementRule::getIniativeCategory` (extracted from `generateIniative`, same behaviour);
  first line of `InitialOrdersGamePhase::advance`; `InitialPhaseStrategy.selectShip`; `ShipTooltip`'s line.
* **Both**: six buttons in `shipTooltipInitialOrdersMenu.js`, CSS in `shipTooltip.css`, three new 40x40
  icons (`img/setCommandNode.png`, `img/swapInitiative.png`, `img/designateJealousElint.png`; the three
  withdrawals reuse `cancel.png`).
* **Hulls** - Archangel `markMindrider` → `markAdvanced` (plain ELINT); Demon, Wraith, Lesser Triumviron,
  Fiend → `markAdvanced(); markJealous();`; `addTriad()` on the C&C of all five capitals; Archangel, Demon,
  Wraith `3 *5` → `2 *5`. No Triad hull is Constrained any more.
* **Docs** - `docs/factions-tiers.html` (Command Node, Jealous ELINT, both Cutter defence bullets) and the
  interception note in `docs/faq.html`.

### Deviations / additions
* The swap partner is looked up with a LOOSE id match (`findShipLoosely`): `TacGamedata::getShipById`
  compares with `===`, and the note stores an int.
* No DB schema change: the choices ride `tac_individual_notes` (`JealousELINT`, `CommandNode`), the effect
  is an UPDATE of the turn's `tac_iniative` rows.

### Verification
* `php -l` clean on every touched PHP file; `node --check` clean on every touched JS file and both bundles.
* Server harness (real classes, recording DB stub, E_ALL): **84/84** - Cutter pricing order, pool cap,
  budget arithmetic (incl. the subordinate case), Jealous gating per turn, quota per fleet, note writing and
  masking, Command Node +10 / swap / tie / five illegal partners / stale / one-per-fleet / simultaneous
  categories, notes lines.
* Client harness (real sources in a `vm`): **79/79** - Cutter hooks through `weaponManager`, the dialog's
  rows and results, Jealous designate/stand-down (EW stripping), Command Node helpers and tooltip lines,
  every new button condition incl. a null selection.
* `fvbuild -Statics` (Triad blueprints checked), legacy bundles rebuilt (minified, as they were),
  `fvbuild -Sync`.
* `fvbuild -Check`: autoload up to date, ship-data validator PASS, replay harness **125 passed, 0 failed**.
  Game 4348 (the corpus's only Triad game) failed first; compared by ship id its ONLY differences were the
  Demon's C&C gaining `CommandNode`, its scanner's `ConstrainedEW` becoming `JealousELINT`, its notes line,
  and the ship order moving with its lower ini bonus (C2). Accepted with the merge recipe
  (`record --games=4348` + manifest merge); old baseline kept in the session scratchpad.

### Deploy notes
* Statics must be regenerated (the deploy ritual does it).
* A game that is MID-TURN at deploy, with a Demon/Fiend/Lesser Triumviron/Wraith that already committed ELINT
  EW this turn, has no designation note for it: that ship's ELINT EW is ignored for that one turn. Deploying
  between turns avoids it. Cutter blocks committed before the deploy keep working unchanged.

### Not done / observed
* Not play-tested - §7.
* Pre-existing, untouched: `EW::getBlanketDEW` applies the Constrained 0.2 factor when the protected
  TARGET is ConstrainedEW, while the client (`ew.js`, `ShipWindowEw.js`) keys it on the ELINT ship. Moot for
  the Triad now; still live for Mindriders.

## 8. Play-test round 1 - game 4453 (2026-10-09)

Four reports, all addressed. Server harness 91/91, client harness 99/99, `fvbuild -Check` green (replay
125/0), statics and bundles rebuilt, served copy synced. Not yet re-tested in game.

1. **Show the node's effect during Initial Orders.** `CnC.getIniativeProjection` (client) replays
   `applyTriadCommandNodes` on the loaded totals - per fleet one node, +10 (raw total + re-categorised under
   simultaneous movement, via `CnC.getIniativeCategory`, the client copy of the server's), the swap, the
   classic +1 tie nudge - and `ShipTooltip`'s Ini line ranks EVERY ship on it (`getProjectedIniativeOrder`),
   so ships the node jumps over show their new order too. Initial Orders only: afterwards the loaded totals
   already carry it. The node line now reads "Command Node: +10 Ini (rolled 60), swapped with X"; the partner's
   "Swapped Ini with Command Node X (rolled 50)". Only nominations this client can see are projected (own and
   team), so the opponent's tooltips are unchanged until the phase closes.
2. **Icons.** `cancelCommandNode.png` = the star in red with a "-2" badge; `standDownJealousElint.png` = the mast
   in red with a "-" badge. Cancel Swap keeps `cancel.png` (superseded in round 2: red swap arrows, §9).
3. **Hover glitch on the left edge of the star / swap buttons.** Not a re-render: the tooltip is placed once,
   centred on the ship, and keeps its LEFT edge while its width is shrink-to-fit. A long hover text widened it,
   the centred buttons slid right by half the growth, the pointer on a button's left edge fell off it, the text
   cleared, the box shrank - a mouseover/mouseout loop. Fixed at the source for every button:
   `.shipNameContainer .buttons .menu .info { width: 0; min-width: 100% }` takes the line out of the width
   calculation, so long text wraps. The Triad hover texts were shortened as well. Diagnosed from the code (not
   reproduced in a browser).
4. **One dice pool for defence (H3).** The pool is the SHIP's: all intact cutters' dice less everything spent
   this turn, counted exactly (`getShipPoolRemaining` - offensive subordinate copies skipped, a block may exceed
   its own cutter). The dialog is one row per ship offering the whole pool; its blocks go on the cutter defence
   was opened from (its arc - identical on every Triumviron cutter; on the Fiend open defence from the cutter
   facing the threat). Manual clicks: `getInterceptPoolKey` makes `weaponManager.getSelectedInterceptorsFor` keep
   one cutter per pool, so one click = one die however many cutters are selected; the pool's last die unselects
   every cutter. Offence is capped by the pool as well (`getShipRemainingDice`). Server: the defensive cap is the
   ship's pool (`getDefensiveDiceSequence` - every cutter's dice less each primary shot's `damageDice`, shared out
   in cutter-id order), so a block bigger than one cutter is honoured and a doctored one cannot exceed the ship.
   Deliberately NOT done: splitting one block's dice across cutters as separate orders - a cutter's own "remove
   orders" button would then half-delete it.

## 7. Play-test checklist

| Case | Expect |
|---|---|
| Cutter, right-click shield, three rows of 3 dice | 3 blocks; automation puts each on one shot at -15% |
| Triumviron, shield on ONE cutter | the green dialog offers all 30 dice; a single 25-die row is accepted and worth -125% on one shot |
| Rows 9 / 5 / 3 ("+ Add another intercept shot" twice) | three blocks, -45/-25/-15%, in that order in `tac_fireorder` |
| Blocks entered small first (3 / 5 / 9, or across two dialogs) | still spent 9, then 5, then 3 - largest first, each on the shot it stops the most damage on (H5) |
| Cutter targeting a ship | the "Allocate d10s" dialog is green, not purple |
| Triumviron fires two 15-die shots at one target | the target's INCOMING list: ONE row "2x Hyperplasma Cutter (Normal) (30d10)"; expanded, two "(15d10)" sub-rows; no A/B/C |
| All three cutters selected, click a missile's hit chance | ONE die per click; the last die of the pool unselects all three |
| Cutter selected, click a missile's hit chance 3 times | 3 one-die orders, row shows -15%; DB `tac_fireorder` 3 `intercept` rows, `shots` 1, notes `HPC-intercept` |
| Manual + blocks on one cutter | manual dice credited first, blocks after; combat log lists both |
| Sustained cutter | no manual intercept offered |
| Fleet with 3 Jealous ships | only one may be designated; the others show no ELINT buttons |
| Designated, then stood down | its SOEW/SDEW/BDEW/DIST/JAM/Detect Stealth for the turn are cleared |
| Enemy view during Initial Orders | no designation / Command Node visible until the phase closes |
| Command Node, still in Initial Orders | your tooltip's Ini Order and total already show +10 (and any swap); other ships it passes are re-ranked; a yellow "Command Node: ..." notice sits with the status notices; the opponent sees nothing yet |
| Command Node only | +10 on the node's `tac_iniative` row after Initial Orders close (classic: `iniative` AND `unmodified_iniative`) |
| Command Node + swap | node = partner's roll + 10, partner = node's roll (C4); classic: only the node's `unmodified_iniative` gains 10 |
| Cancel a swap | the button is the red swap arrows with a minus badge |
| Simultaneous movement game | category re-derived from the adjusted raw total |

## 9. Play-test round 2 (2026-10-09)

Six requests, all addressed. Server harness 92/92, client harness 103/103, two headless-Chrome harnesses over the
real sources (dialog + INCOMING list 29/29 with real clicks and typing; real `ShipTooltip` 10/10), legacy bundles
rebuilt (`yarn build:legacy`, minified as they were), statics regenerated, served copy synced. Not yet re-tested in game.

1. **Green cutter dialogs.** The default multi-value skin is purple, which is the Molecular Slicer's.
   `confirm.askForMultipleValues` takes an optional 4th `options` (`cssClass`); the cutter's "Allocate d10s" dialog
   passes `hpcConfirm`, and `.confirm.multi-value-confirm.hpcConfirm` in `confirm.css` paints it green (one class
   above the base rules, so source order does not matter). The Slicer's dialogs are untouched.
2. **INCOMING list.** Two opt-in hooks read by `ShipTooltipBallisticsMenu`: `getIncomingDisplayName` (the name a row
   prints AND groups on - the cutter answers `stripPairingSuffix(displayName)`, so A/B/C shots of one ship collapse
   into "2x Hyperplasma Cutter") and `getIncomingDiceText` (the cutter's "(15d10)", summed over the row's own shots,
   like the Slicer's "(3d + 12)"; empty once an order is resolved, because the server then leaves `shots` = 1).
   The group key still includes shooter, mode, hit chance and launch hex.
3. **Commit to interception = one row per shot (H4).** New `confirm.hyperplasmaIntercept(shipName, pool, cb)`: each
   row one intercept shot with its own dice and its price ("-45%"), "+ Add another intercept shot" while a die is
   unspent, ✕ to drop a row, a "left: N / pool" readout. Only the row being edited is clamped (to what the others
   leave), so typing never pulls another row down. The themed number stepper moved out of `askForMultipleValues` to
   `confirm.attachStepper` so both dialogs share it. `HyperplasmaCutter.addSelfInterceptOrders(ship, blocks)` now
   takes the list (kept in row order at the head of `fireOrders`, for the ship window only).
   **Block order (H5, after the game 4455 check):** with blocks of different sizes order matters - the automation
   gives each block in turn the shot it stops the most expected damage on (`Firing::getBestInterception`). 4455
   turn 2 stored 5 / 3 / 9 and spent the 5 and the 3 on two 8% missiles; the 9 was left over. Now
   `hyperplasmaCutter::getOwnDefensiveDice` `rsort`s the blocks (manual orders still first, in their own order),
   so the largest is always spent first, on the most dangerous shot.
4. **Command Node in the tooltip.** The white row is gone; `CnC.getTooltipLine`'s text (unchanged, the user's own
   wording) is a yellow (`#e1b000`, the line's existing yellow) notice in the status-effects line, and the Ini line
   carries the total.
5. **Cancel Initiative Swap icon** = `img/cancelSwapInitiative.png`: the swap arrows in the cancel-icon red
   (#E04838) with the red minus badge of `standDownJealousElint.png` (script: this session's scratchpad
   `cancelSwapIcon.ps1`, built on round 1's `triadCancelIcons.ps1`).
6. **The +10 stays with the node (C4).** `applyTriadCommandNodes` and the client projection now swap the ROLLED totals
   first and add the +10 to the node after. Game 4455 showed the old order: the Archangel rolled 13, took +10 (23)
   and swapped with the Wraith's 29 - the node's row ended without its bonus. Classic movement also writes the node's
   `unmodified_iniative` (its ini bonus there) up by 10, so the row records the bonus; the next turn's row is written
   fresh by `submitIniative` from `iniativebonus`, so nothing carries over. Worked example: node 60 / partner 50
   swapped gives 50 + 10 = 60 / 60, and the classic tie nudge (node first) makes it 61 / 60.
