# Mine Detection at Every Hex

Today a unit checks for mines only at the end of its movement. Under the tabletop rule it checks every
time it moves:

> Detection EW remains active throughout the turn, and works throughout a unit's movement. Each time a
> unit moves, check its detection EW against any mines in the area, and reveal any that have been
> detected.

This plan makes every hex count, and shows what a unit finds while its move is still being plotted, so
the unit can react to it - and so can the rest of its simultaneous group.

Status: **Stage 0 (the replay leaks) BUILT 2026-10-03 and committed. Stages 1, 2 and 3 BUILT 2026-10-04,
uncommitted, verified short of play-testing - §6.3-§6.6.** Stage 1: the server walks every unit's whole
path - at each commit, when Movement begins, and after the server moves units itself. Stage 2: live
sweeping, OPT-IN per unit through a purple Minesweeping Mode icon (D14, §6.8) - as a unit in that mode
enters a hex, the client asks the server, the hex becomes final, and a find shows at once and stops the
move there (D10-D13). Stage 3: the FAQ and the starter guide. What is left is the play-test half of §4, which is the user's (create a fresh game per test).
⚠️ **Deploy: apply `db/mineSweep.sql` to the live database BEFORE uploading the PHP** (T16; the PHP
survives a missing table, but nothing can be swept until it is there).
**Off switch** (user request, 2026-10-04): `MineSweep::$liveSweeping` turns live sweeping off everywhere
and keeps Stage 1 - §6.7.

## Where it stands

- **The parameters changed (D9).** The earlier drafts kept the client out of detection, so a find could
  only show once a move was committed. The user ruled that this will not play well: detection has to
  happen as the unit moves. The client may take part, gated and coded efficiently, with the server
  verifying - and an unfound mine's position must still be unreadable to the opponent.
- **Encryption cannot hide data the client has to use (§0.3).** Whatever lets the client decide "would
  this hex find a mine?" lets a player decide it for every hex: run the same check over every hex in
  reach and see where the answer is yes. A key the client holds is a key the player holds, and a list of
  hashed hexes can be tested one hex at a time. So the client still holds nothing about an unfound mine.
- **Instead, the client drives and the server decides (§1.4).** As a unit with a Detect Mines rating
  enters a hex, the client asks the server about that hex. The server answers from a per-turn cache with
  a few small queries, records any find, and the mine appears on the player's map at once. A hex the
  server has answered for is final for that unit, whether or not anything was found - that is what
  stops a player probing a path and taking it back (D10; §0.7 covers how other games handle the same
  problem).
- **What it replaces.** A scout's finds now show while the rest of its group is still being plotted, so
  the group commits together as it does today (the one-unit-at-a-time proposal is withdrawn, §1.7).
  Stage 1 stays underneath as the authority: at the commit it walks each unit's whole path, which covers
  the last hex, units the client never asked about, and a tampered client.

## Rulings (user, 2026-10-03)

| # | Question | Ruling |
|---|---|---|
| D1 | Path detection first, reveal after the commit, no interrupt? | Interrupting and re-plotting is rejected: resubmitting the same unit, possibly several times, plays badly. Path detection itself is kept (Stage 1). |
| D2 | Where a lock would live. | A new table is acceptable. |
| D3 | Simultaneous movement: interrupt only the detecting unit? | Rejected, and with it the detect-at-commit-then-replot approach as a whole. A player will scout with a fast unit to find a clear path for the rest of the same group; if everything is committed together, the others cannot react to the find. |
| D4 | Check every unit's starting hex when Movement begins? | Yes. |
| D5 | A find on the last hex of a plot? | Show what was found at the end of a unit's movement (no interrupts at all). |
| D6 | The mine's owner sees "Detected" before the mover's path is public. | Accept for now. |
| D7 | Fix the replay leak of unfound mine positions? | Yes - built as Stage 0. |
| D8 | Units the server moves (Uncontrolled HKs): detection along their path? | Yes. |
| D9 | Second review: can detection stay server-only, with finds shown after a commit? | No - it will not play well. The client may take part in detection as a unit moves, gated and coded efficiently, with the server verifying. Mine positions must stay unintelligible to the opponent. |
| D10 | Q4: who decides whether a hex finds a mine? | The server, asked by the client as the unit moves. Concern raised: a sweeper could test a direction, cancel back and test another until it finds a mine. A final hex is what prevents that. The server never answers about a hex the unit can still back out of, so a scout sweeps only the path it actually flies (§0.7). |
| D11 | Q5: when does a swept hex become final? | When the unit ENTERS it, because that is the moment the tabletop detects a mine. (The earlier recommendation, "when the unit moves on from it", is just as hard to probe, because the hex that is checked is the hex that is locked, at the same moment. But under it a find arrives one step late, and seeing what a hex reveals before turning there takes a step out and an undo.) The cost: a sweeping unit cannot take back a step once it has been asked about, a mis-click included (§1.4.1). |
| D12 | Q6: which units sweep live? | Every unit with a Detect Mines rating above 0, as recommended. On the user's request, the reading "only units with some Detect Mines EW" was checked (§1.4.1). A ship's Minesweeper Bonus detects on its own, with no EW assigned, so the gate is the rating, not the EW. |
| D13 | A multi-hex move (a right-click on Move) passes a find partway. Does the unit stop there? | Yes. The move is interrupted at the hex the mine is detected from. Every step plotted after it is taken back, and the unit stands on that hex with its remaining movement still to plot. That hex and every step before it are final, as usual (D11). |
| D14 | (2026-10-04, after Stage 2 was built) Q7 - should a sweeping unit be able to keep its undo? | Yes, by OPTING IN: a purple Minesweeping Mode icon (the MDEW art) in the movement UI of a unit with a Detect Mines rating above 0. Off (the default), the unit plots as before and its finds show at the commit; on, it sweeps live. Once on and a hex has been swept, it cannot be turned off. "Lock only after a find" was weighed and not chosen: it leaks clear corridors (T20). A global off switch was added the same day (§6.7). |

## Open questions

| # | Question | Recommendation |
|---|---|---|
| Q4-Q6 | Who decides; when a swept hex is final; which units sweep live. | **Ruled 2026-10-03** - D10-D12. |
| Q7 | A per-unit switch to hold sweeping: the unit plots with free undo, and its finds show at the commit (like Battle for Wesnoth's "Delay Shroud Updates", §0.7). | **Ruled and BUILT 2026-10-04 (D14)** - as an opt-in Minesweeping Mode, §6.8. |
| Q1, Q2 | Commit a simultaneous group one unit at a time; a "Move this unit now" action. | **Withdrawn** - superseded by live sweeping (§1.7). |
| Q3 | Laid mines (§0.2): is a launched mine's landing hex meant to stay secret once its turn is over? | **Ruled yes (2026-10-03)** - built into Stage 0 (§6.2). |

## 0. Findings

### 0.1 How detection works today
- `MineStealth::isMineDetectedMovement` (baseSystems.php): for each enemy unit (not a mine, not terrain,
  not destroyed), a detection rating. A ship that is not disabled rates its Detect Mines EW - only while
  it has a working, online scanner - plus its `minesweeperbonus`; a flight rates its Detect Mines EW.
  The mine is detected when rating > distance + the mine's signature and terrain does not block line of
  sight (`Mathlib::isLoSBlocked` against `$gamedata->blockedHexes`).
- The distance is to `$otherShip->getHexPos()` - the unit's **last** movement row. So only where the
  unit ends up (or, before it moves, where it starts) counts. A unit that passes within range and ends
  out of range never detects the mine.
- It runs from `MovementGamePhase::process`, after an activation's moves are stored: every mine is
  checked against every enemy unit's current position. Only when the COMMITTING player has enemy mines -
  `TacGamedata::$areMinesPresent` is per viewer.
- Nothing runs when the Movement phase starts. The first commit of the phase checks everyone's start
  position.
- A find is a `detected` individual note (value = the finding team), written with phase 2.
  `MineStealth::onIndividualNotesLoaded` builds `$detected` from those notes on every load.

### 0.2 What the client knows about mines
- An enemy mine the viewer's team has not found has its movement replaced by the off-map sentinel
  (`TacGamedata::hideStealthShipMovement`, from `deleteHiddenData`). **Its hex is the only thing
  withheld.** The unit itself is in the payload with its `phpclass`, and game.php inlines that class's
  blueprint, signature included (`captorMineC1`: 4). The UI calls it "Mine" until its type is revealed,
  but the data is there. (The earlier draft said the client lacked the signature; it does not. Noted
  only - not in scope.)
- The client knows its own units' detection rating: `ew.getDetectMEW` (Detect Mines EW plus the
  minesweeper bonus), drawn as the purple MDEW overlay (`ShipIcon.showMDEW`). It also has the terrain
  that blocks line of sight (`gamedata.blockedHexes`).
- **The replay leak (fixed by Stage 0):** `replay.php` → `Manager::getReplayGameData` calls
  `prepareForPlayer(true)` for any PAST turn of a game in progress, which skipped `deleteHiddenData` and
  with it the mine mask. Every past-turn load includes each mine's deploy row, so the replay of any
  earlier turn carried the real hex of every enemy mine, found or not. Confirmed on games 4247 and 4255
  (§6).
- **Laid mines (fixed by Stage 0, Q3).** `BallisticMineLauncher` (and `ChoukaMineLauncher`, which
  extends it) fires at a hex. When nothing is in range of the landing hex, it leaves a loitering captor
  mine there (`createLoiteringMine`), deployed at the order's x/y. `hidetarget` blanks that x/y only
  while the turn is current, so the replay payload of the launch turn carried it. The client already
  drew nothing for an enemy's lone launch in replay (the launcher's `alwaysHideFireOrders`). But after a
  deviation, the PRINTED combat log - which reads past turns through `replay.php` - showed "deviation
  from X Y to X' Y'" from the launch's `pubnotes`. Confirmed on game 4252 (§6.2).

### 0.3 Why the client cannot hold the answer, even encrypted
To answer "would this hex find a mine?", the client needs something that maps a hex to yes or no. A
player can put every hex through it - the client's own code will do it for them - and the "yes" hexes
form a disc around each mine (rating > distance + signature), whose centre is the mine. Every way of
packaging the data runs into this:
- **Encrypted:** the client must decrypt to use it, so the key is on the player's machine.
- **Hashed hexes:** a map is a few thousand hexes; hashing each one and comparing takes moments. A
  deliberately slow hash does not help when a turn lasts hours.
- **Only what is in reach this turn:** shrinks the leak but does not close it. A speed-8 scout with
  Detect Mines 6 finds a signature-3 mine from 2 hexes; testing the 217 hexes it could reach locates
  every unfound mine within 10 hexes of where it starts.
- **Probing is free and invisible:** the client answers "nothing here" for any path the player plots
  and takes back, and the server never hears of it.
- Cryptography that really does compute on one side's secret without showing it to the other
  (zero-knowledge proofs, multi-party computation) needs the mine's owner online during the move, or a
  trusted third party. FV already has the trusted party: the server.

The most a client-side scheme can offer is obfuscation. That stops a glance at the network tab, not a
player with developer tools.

### 0.4 Asking the server as the unit moves
The first draft rejected this on two grounds. Both were overstated:
- **Cost.** It assumed each question loads the gamedata. It need not: everything an answer needs is
  fixed for the turn once Initial Orders resolve - each mine's hex and signature, each unit's Detect
  Mines rating, the blocked hexes. Cached once per game per turn, a question costs a session check, a
  cache read and a handful of small indexed queries (§1.4.3). And only units that can detect anything
  ask, only when they enter a hex, and only while their team has a mine left to find.
- **Probing.** An answer about a hex the player then takes back is information the tabletop never gives.
  The answer is to make an answered hex final - D2 already accepts a table for that kind of lock. The
  first draft dismissed this as ending undo "in any game with mines"; in fact only sweeping units are
  affected, and only for steps that have already been answered.

### 0.5 Where the authoritative check belongs
At the commit. Movement already commits a unit's whole plot, and `MovementGamePhase::process` already
walks every committed path to re-validate it (`validateExtendedTurn`, `validateThrustPayment`,
`validateJumpOutSubmission`) before storing it. Detection is one more pass over the validated path, at
a cost of about (hexes entered) × (mines the team has not found) distance checks, with a line-of-sight
test only for a mine in range - tiny next to the gamedata load the commit already does. Live sweeping
(§1.4) does not replace it: the commit walk is what makes every find certain.

### 0.6 Other facts the design rests on
- **Mine attacks already work hex by hex.** `CaptorMine::checkForValidTargets` (AoE.php) tests a unit's
  start hex and then every hex it entered by `move`, `slipleft` or `slipright`, by initiative. Live
  sweeping uses the same idea of a path.
- **An active player's client does not poll.** Only `WaitingPhaseStrategy` starts polling, and a
  refresh would replace the rows being plotted anyway. So a find has to come back in the reply to the
  client's own question and be drawn locally (§1.4.5).
- **Which units may move now is one cheap read.** `tac_game.activeship` is JSON - one id in sequential
  movement, the group's ids under simultaneous movement - and `isMovementAlreadySubmitted` says whether
  a unit has committed. No gamedata load is needed to refuse a question about the wrong unit.
- **One client seam.** Every plotted action goes through `ShipMovementCallbacks` →
  `onShipMovementChanged` (`MovementPhaseStrategy` overrides it).
- **Restoring plotted rows is solved.** Save Orders Stage 7 rebuilds them from JSON in
  `savedOrders.applyPlottedMoves`, sparse `assignedThrust` included (its T16).
- **A newly visible unit draws itself.** `IdleAnimationStrategy.update` shows or hides every icon from
  `shipManager.shouldBeHidden`, which for an enemy mine asks `MineStealth.isDetectedMine` - true once
  `detected` includes the viewer's team. So a found mine needs its row and its team, then an update.

### 0.7 How other games handle undo and hidden information (D10)
Games that offer undo alongside hidden information all settle on the same rule: **an action cannot be
taken back once it has revealed something.** They differ only in what counts as "revealed".
- **Battle for Wesnoth:** an action that clears fog from any hex cannot be undone, nor can anything
  before it, whether or not an enemy was there. Its "Delay Shroud Updates" option holds the clearing
  back, so moves stay undoable, until the player picks "Update Shroud Now" or does something that cannot
  be undone ([wiki](https://wiki.wesnoth.org/Fog)).
- **Panzer Corps:** a move can be undone unless it revealed a hidden unit
  ([Giant Bomb](https://giantbomb.com/wiki/Games/Panzer_Corps)). A move into empty fog can be taken
  back, so empty fog is scouted for free. That is tolerable when the enemy moves every turn, but not for
  mines, which stay put all game. "Nothing here" is exactly the answer a sweeper is looking for, so every
  hex walked becomes final, found or not (T20).
- **Board Game Arena**, as a policy for every game on the site: no undo back past a point where hidden
  information was revealed ([BGA undo policy](https://en.doc.boardgamearena.com/BGA_Undo_policy)).
- **Magic tournament rules (MTR 4.8):** a judge may let a player reverse a decision only if they have
  gained no information since. If the judge cannot be sure, the decision stands
  ([MTR 4.8](https://blogs.magicjudges.org/rules/mtr4-8/)).
- **Advance Wars:** a unit that runs into an enemy hidden in fog stops and can take no more orders that
  turn ([wiki](https://advancewars.fandom.com/wiki/Fog_of_War)).

The options this leaves for FV:
1. **Lock whatever the server has answered.** Chosen (D10, D11).
2. **A per-unit switch to hold sweeping**, as Wesnoth does: free undo, with finds at the commit. Q7, not
   for now.
3. **Show finds only after the commit.** This is today's behaviour, and Stage 1. Ruled out by D9.
4. **End the unit's move at a find**, as Advance Wars does, with no further orders. A B5W ship has to
   fly its full speed, and D1/D3 ruled out stopping a commit. What D13 does instead is interrupt the
   plot at the find and leave the rest of the movement to be plotted.
5. **Allow the undo but log it, charge for it or ration it.** The player keeps the information either
   way.
6. **Detect in the client.** Ruled out in §0.3 and §1.6.

## 1. Design

### 1.1 Detection along a path (Stage 1)
Three helpers, so the existing check and the path walk share one rule:
- `MineStealth::getDetectionRating($unit, $gamedata)` - the rating from §0.1, lifted out of
  `isMineDetectedMovement` unchanged; 0 for anything that cannot detect.
- `MineStealth::canDetectFrom($mine, $pos, $rating, $gamedata)` - rating > distance + signature, and no
  terrain blocking the line. Distance first, line of sight only when in range.
- `Movement::getHexesEntered($unit, $turn)` - the start hex, then every hex entered by a `move`,
  `slipleft` or `slipright` row this turn, in order.

`isMineDetectedMovement` then loops over each unit's hexes instead of `getHexPos()`. Nothing else
changes: same notes, same call site. In sequential movement this already gives D5: each activation is
one unit, and what it found along its path is revealed the moment it commits, before anyone else moves.
Stage 2 reuses all three helpers.

### 1.2 Start of Movement (Stage 1, D4)
In `InitialOrdersGamePhase::advance`, once the phase is 2: if any mine is on the board, run the mines'
phase-2 check so that every unit's starting hex is tested before anyone plots. `advanceGameState` loads
fresh gamedata from the database before advancing, so every player's Detect Mines EW is there. Gate on
"is there a mine at all", not on `$areMinesPresent`, which is only about the advancing player's enemies.
Stage 2 primes its turn context here too (§1.4.3).

### 1.3 Units the server moves (Stage 1, D8)
Uncontrolled HKs get their moves from `AutomatedMovement`, as stored rows like any other, so the path
walk covers them. But the moves come from two places. One is `process()`, when their owner commits
their group, and the check runs after it. The other is the fallback in `MovementGamePhase::advance`,
after which no mine check runs today. Stage 1 must run the walk there too, or a unit moved by the
fallback never detects anything.

### 1.4 Live sweeping (Stage 2 - ruled D10-D12)

#### 1.4.1 The rule
A unit **sweeps** while its Detect Mines rating is above 0 and its team still has an enemy mine to find
(D12). For a sweeping unit:
- When the player plots a step INTO a hex (`move`, `slipleft`, `slipright`), the client asks the server
  about that hex. (The start hex was checked when Movement began - D4.)
- The server checks the hex, records anything found, and the hex becomes **final**, found or not (T20):
  the unit's committed path must pass through it, in the same order.
- So a find shows the moment the unit enters the hex it is made from, which is when the tabletop would
  announce it.
- **A find interrupts the move there (D13).** Anything plotted beyond that hex is taken back
  automatically: the rest of a right-click "move fully", and any steps plotted while the reply was on
  its way. The unit stands on the hex with its remaining movement still to plot, and the player decides
  what to do there with the mine in view: turn, pivot, slip or carry on. A right-click on Move carries
  on until the next find.
- A step that has been asked about cannot be undone, a mis-click included. A step plotted during the
  short debounce before its request leaves has not been asked about, so it can be. The commit sends any
  such steps and waits for the answer first (§1.4.2), so the commit's own walk (Stage 1) only meets
  unasked steps when a request failed. A find on one of those shows at the end of the move (D5).
- Turns, pivots, rolls, speed changes and jinks do not change the hex, so they never ask. What is final
  is the sequence of hexes, and manoeuvres plotted after the last final hex was entered stay free.
  Speed changes must come before any other move in a turn (`canChangeSpeed`), so a sweeping unit's
  first step also fixes its speed for the turn. The docs should say so (Stage 3).

Which units have a rating (D12, checked 2026-10-03 at the user's request):
- **A ship:** its Detect Mines EW, which counts only while it has a working, online scanner and is not
  disabled, plus its Minesweeper Bonus. **The bonus counts on its own**, with no Detect Mines EW
  assigned and even with the scanner down, so a minesweeper detects with no EW at all. 23 ship classes
  have a bonus, of 2 to 4. That is today's rule and the FAQ states it. Whether the bonus ought to need
  EW is a separate rules question.
- **A flight or shuttle:** its Detect Mines EW only, bought with Offensive Bonus at 2 OB a point. A
  normal flight can put in up to half its OB. A flight flagged as a minesweeper can put in all of it:
  the Minesweeping Shuttles, and any shuttle with the Minesweeper Conversion enhancement.
- **No floor.** Mine signatures run from 0 to 8, and an activated DEW mine's can drop to -3, so any
  rating above 0 can find something. The gate is a rating above 0, not Detect Mines EW above 0. Gating
  on the EW would leave a minesweeper with no EW sweeping only at the commit.

Alternatives considered (Q5):
- **Final on moving on** (the earlier recommendation): a hex is asked about, and locked, when the unit
  steps out of it, so the latest step can always be undone. It is just as hard to probe: the hex that
  is checked is the hex that is locked, at the same moment, so a find never comes from a hex the unit
  can back out of. Not chosen (D11): a find arrives one step after the tabletop moment, and seeing what
  a hex reveals before turning there takes a step out and an undo.
- **Never final** - free undo, but a player can sweep a corridor, take it back and go another way, so
  one scout sweeps several paths a turn. Detection in the client (§1.6) has the same hole and cannot
  close it.

#### 1.4.2 Client
- **Gate - public data only, so the gate itself reveals nothing.** Movement phase, not replay; the
  player's own active unit, not riding a host; `ew.getDetectMEW(ship) > 0` (a flight: its Detect Mines
  EW); and at least one enemy mine, deployed and not destroyed, that the player's team has not found. A
  unit that fails the gate never asks and plots exactly as today.
- **Trigger.** After an action that ADDS a hex-changing row (`move`, `slipleft`, `slipright`), the hexes
  the unit has entered that are not yet final go in one request, after a short debounce, so a run of
  clicks or a right-click "move fully" is one request. Undo never sends anything. A step undone before
  its request leaves was never asked about, so the debounce also gives a moment's grace for a
  mis-click.
- **One request at a time.** Steps plotted while a request is on its way wait for its reply. If the
  reply found nothing, they are sent at once. If it brought a find, they are never sent: they are taken
  back with everything else after the find hex (D13, T21).
- **Lock.** From the moment a request is sent, every row up to and including the one that entered the
  last hex in it is locked, because the server may already have made those hexes final. The lock is a
  PREFIX of the turn's rows (a count), not a mark on single rows (T13). `deleteMove` (the undo button
  and its right-click loop) and `deleteSpeedChange` refuse locked rows, and `hasDeletableMovements`
  agrees. That also hides the cancel-movement button (`UI/shipMovement.js`) once only final steps
  remain. When the reply comes back, the lock shrinks to the rows up to the last hex the server made
  final.
- **Reply.** How many hexes are now final, and the mines found. Each find is drawn at once (§1.4.5).
  The server stops at the first hex with a find, and the client **interrupts the move there (D13)**: it
  takes back every row after that hex, last first, through the same `deleteMove` that right-click
  Cancel uses, until it reaches the locked prefix (T21, T23). A passive notice says the move stopped
  because a mine was detected. The unit's movement is then redrawn through `onShipMovementChanged`,
  whether or not it is the selected unit.
- **Commit** first sends any steps still waiting out the debounce, and waits until nothing is in
  flight. If a reply brought a find, the commit stops there with the notice, so the player can react.
  That includes a find on the last hex, where the unit can still turn or pivot. Otherwise the commit
  sends the whole plot as today.

#### 1.4.3 Server
- **Endpoint** `mineSweep.php`, shaped like `saveOrders.php`: the player from the session only, the
  plotted rows as a JSON string in the body (T12). POST {gameid, turn, shipid, moves} →
  `Manager::sweepMines`.
- **Refuses** unless the game is in Movement on this turn, the player owns the unit, the unit is in
  `tac_game.activeship` (T11), and it has not committed. Small indexed reads - no gamedata load.
- **Path.** The hexes come from the posted rows through `Movement::getHexesEntered`, read defensively
  (T4). The first must be the unit's start hex, each next one adjacent, and there may be no more of them
  than the unit could cover this turn (a generous bound - full legality is judged at the commit, as
  today; T22). The path must begin with the hexes already final; if not, the client is out of step (a second
  tab, say) and is told to reload (§1.4.6).
- **Turn context**, cached in APCu per game and turn: each mine's id, team, hex and signature; each
  unit's rating (`MineStealth::getDetectionRating`) and start hex; the blocked hexes. Built from one
  gamedata load - primed where Movement begins (§1.2 already has the fresh load), and rebuilt under an
  `apcu_add` lock on a miss (after a deploy, or with APCu off). Which mines a team has found is read
  fresh on every request - one query on `tac_individual_notes` - because finds land from several places
  during the phase.
- **Walk.** Each hex not yet final, in order, against every unfound enemy mine (`canDetectFrom`:
  distance first, line of sight only when in range). Stop after the first hex with a find. Every hex
  walked becomes final, found or not (T20).
- **No rating, nothing to bind.** If the unit's rating is 0 on the server (its scanner is destroyed or
  offline), the reply says so, nothing becomes final, and the client stops asking for that unit - an
  answer that can never be "found" teaches nothing. A rating above 0 that cannot beat any remaining
  signature is NOT short-circuited: saying so would leak the mines' types.
- **Write.** The final hexes, and the rows up to them, into `tac_minesweep` - one row per game, turn and
  unit. A `detected` note per find, straight to the database (T1). `touchGame` only when something was
  found, so waiting players refresh.
- **Reply** {final: n, found: [{id, q, r}]} - only the mines just found. The turn context itself never
  leaves the server (T15).

#### 1.4.4 At the commit (the verification)
- `MovementGamePhase::process`: for a unit with final hexes, the submitted path must begin with them, in
  order - checked before the existing validators. If it does not, the commit is refused with an error.
  Stage 1's walk then runs over the whole path as usual.
- The game's `tac_minesweep` rows are deleted when Movement advances, and by `deleteGames()` and
  `leaveSlot()` (T16).

#### 1.4.5 Drawing a find without a reload
The client already has the mine - its sentinel row and its blueprint. For each mine in the reply:
replace its movement with a `deploy` row at the reply's hex, add `gamedata.getPlayerTeam()` to its
`mineStealth.detected` (the value `isDetectedMine` compares against), reposition its icon, and run the
animation strategy's `update` so it is shown (§0.6). Refresh its fleet-list row. The next full load says
the same, because the mine mask lifts for a team in `detected`.

#### 1.4.6 Reloading mid-move
game.php inlines the player's `tac_minesweep` rows for this turn, the way it inlines
`window.fvSavedOrders`. When Movement activates, the client puts them back with
`savedOrders.applyPlottedMoves` and locks them. A Save Orders draft restored for the same unit is kept
only if its rows begin with the final hexes; otherwise the final rows replace it (T14).

### 1.5 What other players see (D6)
- Opponents see nothing of a unit's path until its group is over: final hexes live in `tac_minesweep`,
  not `tac_shipmovement`, and committed rows stay hidden by `hideActiveShipMovement` as today.
- The mine's owner sees "Detected" as soon as the note is written - now mid-plot rather than at the
  commit. The same information D6 accepted, a little earlier.
- A teammate plotting at the same time sees the find when their page next loads (an active client does
  not poll); a waiting teammate sees it on the next poll. If that proves annoying, the reply could later
  carry finds by teammates too.

### 1.6 Considered, not recommended: detection in the client (D10)
The server would send, per sweeping unit, its detection zones within reach, encoded; the client would
test each hex locally and post a find to be recorded; the commit walk would verify. Not recommended:
- it leaks (§0.3) every unfound mine within reach plus detection range of every sweeping unit, to anyone
  who runs the check over those hexes;
- probing is free, and the server never sees it;
- it saves little server work: a find must still be posted and recorded, final hexes must still be
  locked, and the zones must still be worked out per unit per turn.

Its one advantage is no request per step.

### 1.7 Withdrawn - recorded so they are not proposed again
- **Stop the commit at the first find (first draft).** The commit walked the plot, stopped at the first
  find, locked the moves up to it (`tac_movementlock`), reloaded the page and had the player re-plot.
  Rejected (D1, D3): resubmitting the same unit, possibly several times; and a simultaneous group could
  not react. The finding that a Save Orders draft cannot itself be a lock still holds - the draft is the
  player's own discardable copy, and the server never reads it.
- **Commit a simultaneous group one unit at a time (second draft, old Q1/Q2).** A "Move this unit now"
  action committed a scout early; a Stage 7 save and a reload carried the other units' plots across.
  Superseded by live sweeping: the scout's finds reach the player while the group is still being
  plotted, so the group commits together. Its traps (old T7-T9: taking units off the active list,
  `waiting` flips that `DiscordNotifier` turns into notifications, Stage 7's activation key) no longer
  apply.

## 2. Stages

**Stage 0 - the replay leak (D7). BUILT 2026-10-03 - §6.**

**Stages 1-3 BUILT 2026-10-04 - §6.3-§6.6.** What follows is the plan as written.

**Stage 1 - every hex counts at the commit (D4, D5, D8).** §1.1-§1.3. Server only, no new state.
Unchanged by D9, and the base Stage 2 builds on: it is the authority, and it provides the helpers.
Verify with scratch PHP on games 4247 and 4255 (ongoing, with unfound mines) - a unit that passes in
range and ends out of range must now find the mine. The replay harness does not cover mine detection, so
its baseline should not move; run it anyway.

**Stage 2 - live sweeping (D10-D12).** §1.4.
- 2a, server: `tac_minesweep` and its migration, `mineSweep.php`, `Manager::sweepMines`, the turn
  context, the commit check, the clean-up. Verify with scratch PHP against 4247/4255 before any client
  work: a request walking toward an unfound mine returns it and makes the hexes final; a commit that
  skips a final hex is refused; requests for the wrong unit, turn or phase are refused.
- 2b, client: `mineSweep.js` (gate, trigger, lock, reveal, interrupt, restore), the delete paths in
  `movement.js`, the commit flush-and-wait, the inline restore data in game.php.

**Stage 3 - docs and play-test.** The FAQ's mines section and the starter guide: every hex counts; a unit
with a Detect Mines rating sweeps as it moves, and each hex it enters is final, so set its speed before
its first step; a find stops a multi-hex move at the hex it was made from; a minesweeper sweeps even with
no Detect Mines EW; a scout's finds show while the rest of its group is plotted. The FAQ paragraph that says detection happens only at the end of a committed
Movement segment, "so that players cannot detect a mine and then take their moves back", is replaced.
Then §4.

**Stage 4 (optional) - D6 masking.**

## 3. Traps

- **T1. `MineStealth::generateIndividualNotes` starts by calling `onIndividualNotesLoaded`, which reads
  `$this->individualNotes` into `$detected` and then CLEARS the list.** A note queued earlier in the same
  request but not yet saved is silently lost. Write any new find straight to the database, or queue it
  only after that call.
- **T2. `MineStealth::markDetected()` inserts nothing:** its `$newindividualNotes` is never filled. Do not
  build on it.
- **T3. `$areMinesPresent` is per viewer** - "the advancing or committing player has enemy mines". Right
  for a commit (it is the committer's units that moved); wrong as the gate for the start-of-Movement
  check.
- **T4. Walk the VALIDATED rows.** `validateThrustPayment` can drop rows from a tampered plot. Read
  positions defensively (`new OffsetCoordinate($move->position)`); POSTed rows can arrive as plain
  objects or arrays (the hydration block in `process()`; `project_replay_harness` hit the same thing).
- **T5. A riding pod's rows are all type `attached`,** so "entered a hex" tests never see them
  (`arch_attached_movement_mirror`). The host's walk covers where the formation goes; a pod's own Detect
  Mines EW is not used while it rides. The same is true today. **⚠️ Corrected in the build (§6.3):** it
  was not true - the old end-of-move check read the pod's LAST row, i.e. the host's END hex, so a pod's
  own rating did count there. The walk counts an `attached` row that lands in a new hex as entering it.
- **T6. The `advance()` fallback moves units with no mine check after it** (§1.3).
- T7-T9 belonged to the withdrawn one-unit-at-a-time proposal (§1.7).
- **T10. An active client does not poll, and a refresh would replace the rows being plotted.** A find
  comes back in the reply and is drawn locally (§1.4.5); never force a gamedata reload to show it.
- **T11. `tac_game.activeship` is JSON:** one id in sequential movement, an array under simultaneous
  movement. Test membership for both shapes.
- **T12. Post the rows as a JSON string and decode them in the endpoint, as `saveOrders.php` does.** A
  body decoded as an associative array turns `{}` into `[]` (`arch_php_empty_array_json`); positions
  arrive as plain arrays or objects (T4). `tac_minesweep` stores the client's string as sent, so a
  restore gives back exactly what was plotted.
- **T13. Every client path that removes a plotted row must respect the lock.** `deleteMove` and
  `deleteSpeedChange` refuse, and `hasDeletableMovements` must say no when only locked rows remain - or
  the right-click loop in `ShipMovementCallbacks.cancelCallback` (`while (hasDeletableMovements)
  deleteMove`) never ends. `deleteSpeedChange` removes the LAST row when it finds a matching speed
  change anywhere this turn, so test the row it actually removes. `movement.js` has seven sites that
  remove rows (`splice`/`pop`); check each. **Lock a prefix (a row count), never single rows:**
  `hasDeletableMovements` says yes if ANY row this turn is deletable, while `deleteMove` removes only
  the LAST row. With a lock on just the hex-entering rows, an unlocked turn between two of them keeps
  the first saying yes while the second refuses, and the loop spins forever. Since D13, the same loop
  runs inside a reply handler. Test the last row against the prefix in both, and make every loop stop
  if `deleteMove` removed nothing. **As built (§6.5):** `hasDeletableMovements` is NOT changed - it also
  feeds the initiative list's "moved" styling and `canDetach`. The Cancel button and the right-click
  loop ask a new `canCancelMove` instead.
- **T14. Stage 7 restores first and REPLACES this turn's rows; the final rows are reconciled after it**
  (§1.4.6). Restored rows carry sparse `assignedThrust` - go through `applyPlottedMoves` (Stage 7 T16).
- **T15. The turn context holds every mine's real hex.** Server only: never in a reply, an error
  message, or anything a player can read.
- **T16. `tac_minesweep` is keyed by ship id:** delete its rows in `deleteGames()` AND `leaveSlot()`
  (`arch_orphan_shipid_rows_recycled_ids`). `deleteGames` runs on every game.php load, so guard the
  delete against a server where the migration has not been applied yet
  (`arch_deletegames_every_page_load`). Add the table to `db/emptyDatabase.sql` and a migration; apply
  the migration before uploading the PHP.
- **T17. A request in flight at the commit.** The commit waits for it. If one arrives after the commit
  anyway, it is refused (the unit has moved), which is harmless: the commit walk records any find, and
  it shows at the end of the move.
- **T18. A riding pod never asks** (T5); a pod that detaches plots its own rows and sweeps like any
  unit.
- **T19. The cached blocked hexes assume no terrain moves during Movement.** Confirm that before relying
  on the cache; the commit walk loads fresh data either way. **Checked (§6.4): it does not hold.** Terrain
  stays put, but an Enormous SHIP blocks line of sight too and moves (the Explorer, the Kraken...). Every
  Movement commit bumps a generation counter, and a context built before it is rebuilt.
- **T20. Every hex walked becomes final, even when nothing is found** - that is the rule that stops
  probing. Do not "optimise" it into marking only the hexes with a find.
- **T21. A reply that brings a find takes back every step after the find hex, including the steps
  queued behind it, which are never sent (D13).** They were plotted before the player saw the find.
  Sending them would lock the unit in before it could react, and sending them before the take-back
  would turn a free step into a final one.
- **T22. The sweep endpoint checks only that a path is connected and not too long.** Legality is the
  commit's job, and `Movement::$enforceThrustValidation` is still false (log-only), so a forged request
  can sweep a path the unit could not legally fly. It still cannot take the path back, because the
  commit must begin with those hexes. So the exposure is one that already exists today: committing an
  illegal move.
- **T23. The interrupt can land while the thrust panel is open on a row it has to take back** - for
  example, a turn plotted after the request left (`MovementPhaseStrategy.shipThrustUIState.ship` is the
  unit). Cancel that assignment first, with `cancelAssignThrustEvent`, as the panel's own Cancel does.
  That removes the row and closes the panel, and the take-back then carries on. The panel's row is
  always the unit's last row, because `doneAssignThrust` and `cancelAssignThrust` both assume it is. A
  panel open on a different unit is left alone.

## 4. Test matrix (create a fresh game per test)

1. **Replay leak (Stage 0 - done, §6):** replaying an earlier turn of a game in progress shows no unfound
   enemy mine in the payload; the owner still sees theirs; after the game ends, everything shows.
2. **Passing in range (Stage 1):** a ship passes within detection range of an enemy mine and ends out of
   range. The mine is detected (today it is not).
3. **Start of Movement (Stage 1):** a ship that starts the turn in range sees the mine as Movement begins,
   before anyone commits.
4. **Sequential movement (Stage 1):** after one ship's activation finds a mine, the player's next ship
   (and the opponent) see it before plotting.
5. **An Uncontrolled HK passing a mine (Stage 1):** detected - including when the fallback in `advance()`
   moved it.
6. **A live find (Stage 2):** a unit with Detect Mines EW plots toward an unfound enemy mine. As it
   enters the first hex in range, the mine appears. That step cannot be undone, and the unit can turn
   away from there.
7. **Final hexes (Stage 2):** undo stops at the last final hex and the cancel button disappears; the
   right-click "undo all" stops there too and does not hang; a unit without a Detect Mines rating in
   the same game undoes freely and sends no requests; a minesweeper with no Detect Mines EW sweeps
   (D12).
8. **"Move fully" past a mine (Stage 2, D13):** the move stops at the hex the find was made from, the
   steps after it are taken back, and the unit cannot be cancelled back past that hex. A second
   right-click on Move carries on to the next find or to the end of its movement.
9. **Scouting in a simultaneous group (Stage 2):** a scout sweeps ahead and finds a mine; the rest of the
   group is plotted around it; the group commits together. Opponents in the same group see neither the
   path nor the mine until the group is over.
10. **Reload mid-move (Stage 2):** the final steps come back, locked; with a Save Orders draft that
    extends them, the draft's extra steps come back too; with one that contradicts them, the final steps
    win.
11. **Tampering (Stage 2):** a commit whose path skips a final hex is refused; requests for another
    player's unit, a unit outside the active group, a past turn, or a path that jumps a hex are refused.
12. **Cost (Stage 2):** a game without mines, and a unit without a Detect Mines rating, send no requests
    (the browser's network tab).
13. **A step plotted while a find is on its way (Stage 2, T21, T23):** throttle the network, plot a step
    that finds a mine, and plot another step after its request has left but before the reply. The
    second step is taken back by the interrupt and never sent. With the thrust panel open on a turn
    plotted in that gap, the panel closes and the turn goes too.
14. **Commit straight after a step (Stage 2, D13):** plot a last step into detection range and commit
    within the debounce. The commit waits for the reply, the find stops it, and nothing is committed
    until the player commits again.

## 5. Files

As built (2026-10-04). Departures from the planned list are marked *.

| File | Stage | Change |
|---|---|---|
| `source/server/model/TacGamedata.php` | 0 | mine and laid-mine launch masking on the history path |
| `source/server/model/systems/baseSystems.php` | 1 | `MineStealth::getDetectionRating`, `canDetectFrom`/`canDetectAt`, `getMinePosition`, `checkMovementDetection`, `anyMineOnBoard`; path loop in `isMineDetectedMovement` |
| `source/server/handlers/movement.php` | 1, 2 | `Movement::getHexesEntered` / `getHexesEnteredFrom`, `toOffset`, `rowField`; `readSweepPath` (the endpoint's strict reading) |
| `source/server/Phase/InitialOrdersGamePhase.php` | 1, 2 | start-of-Movement check; prime the turn context |
| `source/server/Phase/MovementGamePhase.php` | 1, 2 | the walk on the fresh load in `process()`; the final-hex check; the walk after the `advance()` fallback; clear `tac_minesweep` on advance |
| `source/server/handlers/MineSweep.php` * | 2 | new: the turn context (build, prime, cache, generation), the walk, the commit check, the clean-up - kept out of Manager |
| `source/public/mineSweep.php` | 2 | new endpoint |
| `source/server/controller/Manager.php` | 2 | `sweepMines`, `getMineSweepJSON` (inline restore data); the generation bump after a Movement commit * |
| `source/server/controller/DBManager.php` | 2 | `tac_minesweep` reads, writes and deletes; `getMinesFoundByTeam`; `getShipOwner`; `deleteGames`/`leaveSlot` |
| `source/autoload.php` * | 2 | regenerated (`fvbuild -Autoload`) for `MineSweep` |
| `db/mineSweep.sql`, `db/emptyDatabase.sql` | 2 | the table |
| `source/public/game.php` | 2 | inline restore data; script tag for `mineSweep.js` |
| `source/public/client/mineSweep.js` | 2 | new: gate, trigger, lock, reveal, interrupt, commit wait, restore |
| `source/public/client/movement.js` | 2 | `isLastMoveFinal`, `canCancelMove`; `deleteMove`, `deleteSpeedChange`, `canChangeSpeed` respect the lock |
| `source/public/client/ShipMovementCallbacks.js` * | 2 | right-click undo-all asks `canCancelMove` and stops when nothing was removed; `minesweepCallback` (D14) |
| `source/public/client/UI/shipMovement.js` * | 2 | the Cancel icon asks `canCancelMove`; the Minesweeping Mode icon (D14) |
| `source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js` | 2 | hook in `onShipMovementChanged` |
| `source/public/client/renderer/PhaseDirector.js` * | 2 | the restore hook, after Save Orders' |
| `source/public/client/gamedata.js` | 2 | `onCommitClicked` (Movement) waits for every answer |
| `source/public/docs/faq.html`, `starter-guide.html` | 3 | text |

## 6. Build notes

### 6.1 Stage 0 - the replay leak (built 2026-10-03)
- `TacGamedata::prepareForPlayer($all)`: on the history path (`$all` true - the replay of a past turn;
  `Manager::getReplayGameData` is its only caller) it now runs `hideStealthShipMovement(true)` unless
  the game is over (`TacGamedata::$currentGameFinished`, i.e. FINISHED or SURRENDERED - a post-mortem
  still discloses everything). `hideStealthShipMovement` gained a `$minesOnly` flag that skips every
  unit that is not a mine; with it, the live path's own rule decides, unchanged.
- What decides "found" is the mine's `detected` list as loaded for the replayed turn. Notes load with
  `turn <= T`, so a mine found - or that fired - during turn T is visible in that turn's replay.
- Mines only, on purpose: the same hole for stealth SHIPS is the reverted 2026-07-24 rework's territory
  and needs its own ruling (`arch_info_bleed_masking`).
- The sentinel row is stamped with the game's live turn (the replay calls `setTurn` after
  `prepareForPlayer`). Harmless: the client hides an unfound enemy stealth unit in replay
  (`shipManager.shouldBeHidden`), the replay builds no animation for one that did not fire, and every
  `ShipIcon` position lookup falls back to its default position, which is the sentinel.
- **Verified** against the local database, read-only, through the real `Manager::getReplayGameData`:
  every past turn of every local game with mines (4247, 4255 ongoing; 4252, 4239, 4238, 4337
  surrendered), as every player - 68 mine views. Before the change, 17 were wrong: in both ongoing
  games the enemy received every mine's real hex in every past turn. After it, 0 wrong. In-memory
  checks of the branches the data does not reach: a mine the viewer's team has found stays visible;
  five stealth ships in 4255 are untouched on this path; the live path still hides all three mines;
  the owner sees their own. Replay harness: 132 passed, 0 failed. `php -l` clean.

### 6.2 Stage 0 - where a launched mine came to rest (built 2026-10-03, Q3 ruled yes)
- `TacGamedata::hideLaidMineLaunches()` runs on the history path right after the mine mask, under the
  same gate (not game over). It does NOT scan ships and systems: `onConstructed()`'s existing fire-order
  loop, which already resolves every loaded order's weapon, also files the orders of a weapon with
  `getAlwaysHideFireOrders()` - the Ballistic Mine Launcher family - into a private
  `$mineLaunchOrders` list (a `ballistic` property read rules out almost every order first). The mask
  returns at once when the list is empty, which is nearly every game. For each listed order from a
  unit not owned by the viewer or their team, a hex-aimed order (`targetid` -1) with NO `SecondAttack`
  beside it that turn has its x/y set to `"null"` (the
  hidetarget convention every client reader accepts), and its `pubnotes` lose the "deviation from X Y
  to X' Y'." fragment. "Shot deviates N hexes" and "Mine launched, but no valid target" stay: they say a
  mine was laid, not where.
- A launch WITH a SecondAttack is left alone: no mine stays behind, the attack is public, and the
  explosion is drawn from that hex. That is the same test the client's replay uses to skip a lone launch
  (`weaponManager.getAllHexTargetedBallistics`, `BallisticIconContainer`), so the client already drew
  nothing for what this blanks.
- `$this->ballistics` needed nothing: it is built only for the live turn and is never sent to the client.
- **Verified** on game 4252, the local game with real launches: four lone launches, two of them
  deviated, one launcher ship per player. The game is surrendered, so the game-over flag was cleared in
  memory. Results:
  - each viewer keeps their own launches' hexes and deviation text, and gets neither for the
    opponent's;
  - with the real status, the post-mortem shows everything;
  - an in-memory SecondAttack keeps its launch's hex, while a lone launch on the same ship is still
    masked;
  - the mine-position check (§6.1) still reads 68 views, 0 wrong; replay harness 132 passed, 0 failed;
    `php -l` clean.

### 6.3 Stage 1 - every hex counts (built 2026-10-04)
- **The rule, in one place.** `MineStealth::getDetectionRating` is the old inline rating, unchanged but
  for one line: a unit not on the board yet (`getTurnDeployed > turn`) rates 0. The old end-of-move
  check tested such a unit at its `start` or early-placed deploy row, so it could find mines from a hex it
  had never reached. `canDetectFrom` / `canDetectAt` hold "rating > distance + signature, line of sight
  clear" (distance first). `isMineDetectedMovement` skips a rating of 0: below 1 the inequality needs a
  negative signature, which only an activated DEW mine has, and a DEW mine activates by firing, which
  has already marked it detected by every enemy team - so this changes nothing.
- **The path.** `Movement::getHexesEntered` = the start hex (`getLastTurnMovement`, so a unit deployed
  this turn starts on its deploy row and a unit not on the board has none) plus every hex entered this
  turn. ⚠️ `start` rows are skipped, as `getLastTurnMovement` skips them: every unit has one dated TURN 1
  at the centre of its deployment box (686 of the corpus's turn-1 unit-turns carry one), so on turn 1 the
  walk would otherwise trace a trip out to the box and back - and the sweep's strict reader would refuse
  every turn-1 request. Found against real rows; no synthetic test had one. Two departures from §1.1,
  both found in the code:
  - **D8's drift is one `end` row `speed` hexes away** (`AutomatedMovement::buildDriftMove` writes no
    `move` rows), so any other row that lands somewhere new counts the line of travel - along its
    heading when that arrives, the straight hex line otherwise.
  - **T5 was wrong about today:** the old check read a riding pod's LAST row - its host's END hex - so the
    pod's own rating did count there. An `attached` row that lands in a new hex now counts as entering
    it, so a pod's rating applies along its host's whole path. Live sweeping is unaffected: a rider
    never asks (T18).
- **The commit walk runs on `process()`'s FRESH load, before its hydration loop.** That loop cuts the
  ships' movement down to this turn's rows, which loses the start hex; the old mine loop ran on
  `$gameData` after it and so only ever saw where each unit ended. The loop itself is untouched.
- **D4:** `InitialOrdersGamePhase::advance` runs the mines' phase-2 check (`checkMovementDetection`)
  when `anyMineOnBoard`, after the Chameleon checkpoint and before the vortex sweeps. Mine systems write
  no other notes in phase 2 (checked: CaptorMine, ProximityMine and MineControllerDEW act in -1 / 1).
- **D8:** `MovementGamePhase::advance` walks again only when its fallback actually moved a unit
  (`generateAndSubmit` puts the rows on the in-memory ship too, so no reload is needed).
- **Verified** (scratch PHP on game 4255, in memory only, 31 checks): the old end-hex rule misses a unit
  that passes beside a mine and ends out of range, the walk finds it; own team never; start hex in range
  is found with nothing plotted; a 6-hex drift and an off-heading jump become connected lines; `attached`
  rows count; a `start`-only unit has no hexes; ratings for D12 (a bonus of 3 with no EW rates 3). No
  unit in the local corpus has a Detect Mines rating, so every rated case is synthetic.
- **Verified on real rows:** every turn of every corpus game, loaded AS OF that turn (⚠️ `getMovesForShips`
  loads only turn 1, the previous turn and the current one - a walk of an older turn from a later load
  reads its start from turn 1, which is a test artefact, not a live case): 140 games, 392 loads, 2,237
  unit-turns, 215 with moves - every walk starts on the unit's real start hex and steps one hex at a
  time, and the strict sweep reader accepts all 215 recorded paths as the client would post them.

### 6.4 Stage 2a - live sweeping, server (built 2026-10-04)
- **`MineSweep` (handlers/MineSweep.php)** holds the turn context, the walk, the commit check and the
  clean-up; `Manager::sweepMines` is the façade (validation, lock, transaction, writes, reply). The plan
  put the context in Manager; a handler keeps Manager lean, at the cost of an autoload regeneration.
- **T19 does not hold** - Enormous ships move. Every Movement commit bumps a generation key in APCu
  (`MineSweep::onMovementCommitted`, from `Manager::submitTacGamedata` AFTER its transaction), and a
  context stored under an older generation is rebuilt. Built under an `apcu_add` lock; a request that
  finds the lock taken waits up to 3s for the builder, then builds its own without storing it.
- **The sweep takes the player's commit lock** (`getPlayerSubmitLock`), so a sweep and a commit by the
  same player never interleave; a request refused for it answers `busy`.
- **A lost reply is given again.** The client posts `known` (its count of final hexes). `tac_minesweep`
  has a `found` column - the mines found from the LAST final hex - and when `known` is behind the server
  and that list is not empty, the reply repeats the find without walking further (T21). Without it, a
  find whose reply was lost would never be drawn: the mine is in `detected` by then, so no later walk
  reports it again.
- **Refusals** carry a flag the client acts on: `stale` (wrong game state, turn, unit, owner, or already
  moved - stop asking), `busy`, `reload` (the posted path does not begin with the final hexes), and
  `noRating`. The path is read STRICTLY (`Movement::readSweepPath`): from the context's start hex, every
  hex a neighbour entered by a move or slip, nothing else changing the hex. Bound (T22): no more hexes
  than the start speed plus one per posted speed change. Server exceptions answer a fixed message (T15).
- **The commit check** (`process()`, before every validator) refuses a path that does not begin with the
  unit's final hexes, using the authoritative ship's start hex - its stored movement is still intact at
  that point. `getFinalHexesForCommit` never throws, so a database without the table cannot cost a
  commit.
- **Verified** against game 4255 through `Manager::sweepMines` with a test-mode DBManager, everything in
  one rolled-back transaction (33 checks; row counts identical before and after): refusals for another
  player's unit, a past turn, a unit not moving, unreadable rows; `noRating` with nothing stored; a path
  too long or not starting at the start hex; a find writes the row, the notes (team, phase 2) and the
  reply; a lost reply is resent; nothing new; out of step -> reload; a speed change allows a second hex;
  the commit check passes and refuses; the inline restore data; a sweep after the unit moved is refused;
  `forgetGame`. Replay harness: 127 passed, 5 failed - exactly the known Kelly Phaser five
  (`arch_replay_corpus_known_failures`), every diff a min/maxDamage key.

### 6.5 Stage 2b - live sweeping, client (built 2026-10-04)
- **`hasDeletableMovements` is unchanged** (T13 as planned would have changed it): it also answers "has
  this unit plotted anything" for `drawIniGUI`'s moved styling and for `canDetach` - with it false, a
  detached pod whose steps were all swept would offer Detach again. `movement.isLastMoveFinal` and
  `canCancelMove` carry the lock instead: `deleteMove` and `deleteSpeedChange` refuse a final last row,
  the Cancel icon and the right-click loop ask `canCancelMove`, and the loop breaks when nothing was
  removed. `canChangeSpeed` refuses once anything is locked.
- **The lock is a hex COUNT** (final, or sent while a request is out) turned into a prefix on demand - the
  row that entered that hex and everything before it. `doJink(-1)` and `doContraction(-1)` splice rows
  out of the middle; a stored row count would have drifted, a hex count cannot. Off outside phase 2, so
  Fire-phase combat pivots are untouched.
- **An uncommitted slip has not been entered yet** - its thrust panel can still cancel it. It is asked
  about once its thrust is confirmed.
- **One request at a time for ALL the player's units**, not per unit: the server serialises them on the
  player's lock anyway.
- **Failures** keep the lock where the request left it (the server may have made those hexes final) and
  retry - after 2.5s up to three times, then with the next step or the commit. A commit never waits on a
  failure: it goes ahead, and its walk reports what it finds at the end of the move (D5).
- **The reveal assigns a FRESH `detected` array.** The server sends `detected` only when it is
  non-empty, so an unfound mine's is the static blueprint's, shared by every mine of its class
  (`arch_client_system_shared_reference`) - a push would have shown them all.
- The passive notice and the commit overlay reuse Save Orders' (`savedOrders.showNotice` /
  `showOverlay`, "SWEEPING FOR MINES...").
- **Verified:** a vm harness with the real model, `movement.js`, `ew.js`, `savedOrders.js` and
  `ShipMovementCallbacks.js` (71 checks: gate incl. D12 and own-team mines; debounce, one request, the
  lock in flight and after; right-click undo-all stops and does not spin; a turn after the lock stays
  free; move-fully interrupted at the find; the reveal, and the shared array untouched; T21 and T23 with
  the thrust panel open; a step queued behind a clean reply sent at once; the commit wait, a find on the
  last hex stopping it, a failure letting it through; retries; noRating; stale; reload; a jink removed
  inside the prefix; the restore with no draft, an extending draft, a contradicting draft, stale data;
  no lock in phase 3). Five injected regressions each caught. Then end to end on the real local
  `game.php` (game 4255, player 211, every commit POST blocked): one real round trip to `mineSweep.php`
  answered `noRating` and wrote nothing; canned replies then drove a move-fully interrupted at the find
  hex, the mine drawn at its hex while its sister mine stayed hidden, the next step asked with
  `known=1`, the commit gate, and the restore - no page errors, database row counts unchanged.

### 6.6 Stage 3 - docs (built 2026-10-04)
- FAQ, Mines: the "detection happens at the END of each committed Movement segment" paragraph is
  replaced (every hex counts; line of sight; the Minesweeper Bonus counts on its own), and a new
  "Sweeping as you move" subsection (`data-anchor="minesweeping"`): finds show at once and while the group
  is plotted, swept steps are final, set speed first, a find stops the move, the commit waits.
- Starter guide, Movement: a bullet beside Cancel Move linking `faq.php#minesweeping`, and a sentence
  under Adjusting speed.
- Not changed, noted: the FAQ says flights buy Detect Mines at "10 OB per point"; the client charges 2
  OB per point (1 for a minesweeper flight).

### 6.7 The off switch (user request, 2026-10-04)
Asked for in case play-testers dislike what live sweeping costs a sweeping unit (no undo of swept steps,
speed fixed by the first one).
- **`MineSweep::$liveSweeping`** (source/server/handlers/MineSweep.php, default true), the same kind of
  switch as `Movement::$enforceThrustValidation`. Off:
  - `mineSweep.php` answers `{"disabled": true}` before touching the database;
  - the commit check (§1.4.4) binds nobody - `getFinalHexesForCommit` returns nothing, so a hex swept
    before the flip cannot refuse a commit;
  - game.php inlines no restore data and `window.fvLiveMineSweeping = false`, so the client never asks,
    locks or restores - units plot exactly as before Stage 2;
  - Movement does not prime a context.
  **Stage 1 stays on** (every hex of the committed path counts, the start-of-Movement check): it changes no
  controls, only what is found, and finds show when a unit commits (D5).
- **Flipping it needs one PHP file uploaded and no client rebuild.** A page opened before the flip learns
  at its next sweep request: it unlocks everything, redraws the player's units (the Cancel icon comes
  back), lets a waiting commit through, and shows a passive notice.
- ⚠️ The FAQ's "Sweeping as you move" section and the starter guide's Movement bullet describe live
  sweeping - edit them out while it is off.
- **Verified:** vm harness 84 checks (13 for the switch: off at load - no requests, free undo, no restore;
  flipped mid-page - unlocked, commit released, notice, redraw, no further requests; a regression that
  ignores the switch fails 9 of them); scratch PHP on 4255, rolled back (disabled reply, no commit check,
  no restore data); the real game.php in both modes, row counts unchanged.

### 6.8 Minesweeping Mode - Q7 ruled (D14, built 2026-10-04)
Live sweeping is now OPT-IN, per unit and per turn. All client; the server is unchanged, because it only
ever answers what it is asked.
- **The icon.** `#minesweep` in game.php's movement UI, drawn by `UI.shipMovement.drawMinesweepIcon`:
  the `img/mineIcon.png` art, FIRST ASTERN (user 2026-10-05): straight behind the unit at 84px, the slot
  the roll icon (ships) or the jink stack (flights) used to start in. When it is drawn everything astern
  steps back - a ship's roll from 84 to 136px, a flight's jink + from 98 to 124px, i.e. clear of its 40px
  hit box plus `minesweepGap` (12px, a constant beside the others) - and the jink stack and Cancel follow
  through the shared `dis` chain. Its canvas is counter-rotated in `reposition` so the
  art stays upright. Faint while off, a purple glow while on; the art, the faint opacity and the glow
  colour are constants at the top of UI/shipMovement.js (`minesweepIcon`, `minesweepOffOpacity`,
  `minesweepGlowColour`). Hover labels: "Start Minesweeping Mode", "Stop Minesweeping Mode",
  "Minesweeping - Each move is final" (set through `.data()` as well as the attribute - moveTooltip
  reads `.data()`, which caches). (First built stern-right at 140 degrees / 105px, clear of the stack.)
- **The movement UI follows the lock (`mineSweep.redrawUI`, fixed 2026-10-05).** The lock moves with no
  row changing - when a request leaves (`send`) and when its answer lands (`onReply`) - so no
  ShipMovementChanged fired, and the Cancel icon drawn during the 0.4s delay stayed on screen over a
  step that had just become final: clicking it did nothing (deleteMove refuses). `redrawUI` calls the
  strategy's `redrawMovementUI` at both points, which redraws the selected unit's ring only. ⚠️ Never
  under an open thrust panel / an uncommitted row (the earlier hide dropped `strategy.movementUI`, so a
  redraw would put the ring over the panel) or a confirm dialog (drawShipMovementUI refuses and the
  strategy loses track of the ring) - closing either redraws anyway.
- **No Cancel on a sweeping unit's hex step, ever (user 2026-10-05).** Even inside the 0.4s delay, before
  the request leaves - a Cancel that worked for a moment and then didn't read as misleading. `canCancelMove`
  also asks `mineSweep.isLastRowSweptStep` (the unit is a sweeper and its last row entered a hex), which
  covers the icon and the right-click undo-all. ⚠️ The BUTTON only: `deleteMove` / `isLastRowLocked`
  still take an unsent step, because the find interrupt (D13) takes back steps plotted behind a find that
  were never sent (T21) - folding it into the lock breaks that (harness MUTATE=lockunsent). A turn or
  pivot after the step still cancels; a unit not in the mode (or whose request was refused) undoes freely.
  The FAQ's "a step taken back within a moment has not been checked" sentence is gone.
- **Who is offered it** - `mineSweep.canSweep`, the old gate: own active unit, not riding, Detect Mines
  rating above 0 (`ew.getDetectMEW`, the figure the purple MDEW overlay draws - a minesweeper's bonus
  with no EW counts, D12), an enemy mine still unfound, live sweeping switched on. `isSweeper` is now
  `canSweep` AND the unit's mode.
- **A unit moves wholly in the mode or wholly out of it (user 2026-10-05).** The icon is clickable - on
  and off - only while the unit has no step INTO a hex this turn (`hexRows` empty); speed changes, turns,
  pivots and the like first are fine, the same line Cancel draws. The first hex step fixes it for the
  move: on -> locked at once (before its request leaves - this also closed the 0.4s window in which on
  could still be turned off), off -> `unavailable`. A unit that moved with it off can cancel back to its
  starting hex and turn it on then. So `toggleMode` never has plotted steps to sweep or unsent ones to
  drop. (Replaces "turning it on part-way through a move sweeps the hexes already plotted".)
- **States** (`mineSweep.getModeState`): off (default, faint, clickable) / on (glowing, clickable) /
  locked (glowing, cursor default - on with a hex plotted or asked about) / unavailable (fainter still,
  `minesweepUnavailableOpacity` 0.35, cursor default, label "Minesweeping - Before first move only" - off
  with a hex plotted). Locked stays even once nothing is left to find, so the player can see why those
  steps will not undo. A reload that restores swept steps restores the mode on, locked. A Save Orders
  draft does not carry the mode: a draft with hex steps comes back unavailable.
- **Off, the unit is exactly the old behaviour:** finds at the commit (Stage 1's walk); every unit's
  starting hex is still checked when Movement begins (D4).
- Docs: the FAQ section is now "Minesweeping Mode" (same `data-anchor="minesweeping"`), and the starter
  guide's Movement notes say the mode is opt-in.
- **Verified:** vm harness 101 checks (17 for the mode: default off with free undo and no requests; on
  then off inside the delay sends nothing; the icon's callback; on part-way sweeps the plotted hexes;
  locked refuses off; the locked icon outlives the last mine; restore comes back locked; the off switch
  hides it), every injected regression caught (two new: the mode ignored, the mode turned off after a
  sweep). On the real game.php with REAL mouse clicks on the icon (game 4255, commits blocked, sweeps
  answered in the driver): hidden without a rating, faint when offered, glowing when on, off again, a
  move-fully interrupted at the find with the icon locked, a click on the locked icon changes nothing,
  the hover label follows each click; no page errors, database row counts unchanged. Screenshots:
  mode_off / mode_on / mode_locked.png in session e8454487's scratchpad.
  2026-10-05 refinements: harness 112 checks (redrawUI - no Cancel in the delay, none after the request
  leaves or the answer, back after a refusal; no redraw under a thrust panel, an uncommitted row or a
  confirm; a turn after an unsent step cancels, then Cancel goes; deleteMove still takes an unsent step;
  a unit not in the mode is offered Cancel), MUTATE=noredraw / unguardedredraw / delaycancel /
  lockunsent each caught. Real page: with the mode off a move offers Cancel and a real click on it
  takes the move back; with it on, no Cancel 0.1s after the move or after the sweep.
  Whole-move rule: harness 118 checks (on/off freely after a speed change and a turn, no request with no
  hex; locked the moment the first hex is plotted; unavailable after a hex with it off, the icon's
  callback refused, offered again only back at the starting hex, then swept), MUTATE=midmove (the old
  getModeState) fails 11. Real page with real clicks: moved off -> faded, click ignored, Cancel to start
  -> clickable -> on -> moved -> locked at once, click ignored, swept once. Real game.php 4447 (read-only, sweeps answered in the driver) as both players:
  Seltat ship and Sentri / Koist flights at the new spot, Cancel visible in the delay and gone after
  the sweep, the Kuach's restored swept hexes locked with no Cancel; no page errors.
