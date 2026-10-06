# Extended Turns — paying for a turn across two game turns

A ship that cannot pay for a whole turn this turn (or should not, because it would overthrust) may
**begin an extended turn**. It pays at least a quarter of the turn cost now and names the side, keeps
flying straight, and **completes the turn at the very start of its next movement**, paying the rest.

The same project restyles the Apply Thrust panel and the movement-icon hover labels into the unified
look. That restyle comes first (Stage 0) because both extended-turn panels are built on top of it.

Status: **ALL STAGES BUILT. Stages 0-2 are committed (16a7be9cc). Stages 3-6 were built 2026-10-01 and are
uncommitted.** The user played the first extended turn in game 4430, which is now in the replay corpus.
The rest of the test matrix (§9) is still the user's to play. Decisions ruled by the user on 2026-10-01
(see Rulings). D12 is open but does not block anything. As-built notes and deviations: §8.1 (Stages 0-2),
§8.2 (Stages 3-4), §8.3 (Stages 5-6).

## The rule (B5W)

> "If a ship (not fighters) is moving extremely fast, or has suffered damage to critical thrusters, it
> might wish to extend its thrust expense into the next combat turn. This option is occasionally seen if
> a fast-moving ship wishes to avoid overthrusting.
> To make an extended turn, the player announces that he is paying some fraction of the required thrust
> (but at least 25% of the turn cost) to make an extended turn. He must also announce the direction of
> the turn. The ship, however, doesn't actually change facing yet, but continues moving as before, and
> may not make any other maneuver (including accelerations or decelerations) until the extended turn is
> finished. On the ship's next combat turn, it must pay the remainder of the turn immediately, before
> moving even a single hex on the map. The extended turn is completed at this point, and the ship
> changes facing and direction. (Note that the ship may not accelerate or decelerate on this combat
> turn, because accelerations and decelerations must be done before any other maneuvers.)
> The turn delay count begins at this point, not when the extended turn begins! If for some reason the
> ship cannot pay the thrust requirement (such as one or more required thrusters being blown off the ship
> in the preceding turn), the entire maneuver is canceled. Note that cancellation cannot be done
> voluntarily — it is permitted only if it is physically impossible (due to thruster or other
> restrictions) to complete it. If canceled, the ship is still not permitted to accelerate or
> decelerate, and any thrust spent previously is lost. Extended turns may be shortened as described
> earlier. The extra cost is paid in the second game turn in which thrust is applied."

### In FV terms: what the code has to enforce

| # | Rule |
|---|---|
| R1 | **Ships only.** Not flights (fighters, shuttles, MicroSATs). Also not OSATs, bases, terrain or mines, none of which turn by spending thrust. |
| R2 | **Turn N (the begin):** pay at least ⌈25%⌉ of the turn cost through the turn's thrusters, and name the side. Facing and heading do **not** change. For the rest of turn N the ship may not turn, pivot or roll (D4). Speed changes are already impossible by then, because a speed change must come before any other order. |
| R3 | **Turn N+1 (the completion):** before any other order, and before moving a single hex, pay the remainder. Facing and heading change **at that point**, in the hex where the ship starts N+1. |
| R4 | **No acceleration or deceleration at any point in turn N+1**, whether the turn was completed or cancelled. |
| R5 | **Turn delay counts from the completion**, not from the begin. Thrust spent to shorten the turn is paid on N+1, as part of the completion. |
| R6 | **Cancellation happens only when completion is physically impossible:** required thrusters lost or crippled, not enough thrust, or the engine shorted (D9). Once committed, the player cannot cancel by choice. The thrust spent on turn N is lost. |

## Rulings (user, 2026-10-01)

| # | Question | Ruling | Effect on the design |
|---|---|---|---|
| D1 | Where is "Begin Extended Turn" offered? | **A+C.** A map icon sits in the turn arrow's place when the normal turn costs more engine thrust than is left but at least 25% is affordable. In addition, a **Make Extended Turn** box appears on a normal turn's thrust panel. | §3.3, §4.3, §5 |
| D2 | What is the most that may be paid on turn N? | **Cost − 1.** While **Make Extended Turn** is ticked, the panel refuses the last point. Unticking the box turns the row back into a normal turn. | `assignThrust` refuses the last point on a begin row (§3.4); the box converts the row both ways (§3.3) |
| D3 | How is the 25% minimum rounded? | **⌈cost/4⌉.** Not offered when the cost is below 2. | none |
| D4 | What is blocked between the begin and the completion? | **A manoeuvre is a turn, a pivot or a roll.** Everything else stays allowed. The list must be easy to change. | One list of blocked row types, mirrored on client and server and checked by every gate (§2.2, §3.2). ⚠️ *My reading:* the list governs turn N after the begin. On N+1 the completion still comes first, as your original sketch said ("only show the turn icon until the turn is made") and as the rule says ("immediately"). So on N+1 nothing else is offered until the turn is completed or cancelled. |
| D5 | How does a cancellation happen? | **Automatically, with no pop-up.** A note goes in the ship window's status banner (the bottom strip that carries ROLLED). | §3.3, §6 |
| D6 | What may the ship do after completion or cancellation on N+1? | Manoeuvre normally, but no speed changes. | §3.2 (R4) |
| D7 | Agile snap turns | A snap turn cannot lead into an extended turn: **an extended turn is a single 60° turn.** | The begin needs a turn delay of 0. ⚠️ *My reading of "single 60° turn":* a snap turn cannot follow straight on from the completion either; the next turn waits out the completion's delay as normal. This is one condition in `canTurn`; drop it if you meant otherwise. |
| D8 | Who can see an extended turn? | **A public, green "Making Extended Turn" note in the ship tooltip.** Stealth ships stay hidden, because their tooltip is hidden. | §6. ⚠️ I add the side, as in "Making Extended Turn (starboard)", because the rule makes the direction part of the announcement. Drop it if you'd rather not. |
| D9 | What happens if the engine shorts before completion? | **The extended turn is cancelled**, because the engine is offline. | An explicit cancellation reason, checked before the payability test (§3.3). The server accepts it as a justified cancellation (§2.1 h) |
| D10 | Do gravitic hulls keep their turning exceptions? | **Normal turn rules apply.** Gravitic ships keep `canTurn`'s exceptions, so they may begin while rolling or pivoting. | The amount owed is stored **by role** (aft / side / either) and mapped onto physical thrusters only at completion. That way a roll or pivot that completes between the two turns cannot leave it naming the wrong thrusters (§1.4, T10) |
| D11 | How far does the restyle reach? | **The thrust panel and the movement-icon hover labels.** | Stage 0 now includes `moveTooltip.js` (§4.6) |
| D12 | **New, open, and not blocking.** Engine boost is chosen in Initial Orders, before the completion. A player who owes an extended turn could leave the boost off, or let power run short, and so make completion impossible on purpose: a voluntary cancellation by the back door. (The engines themselves cannot be switched off: they have `powerReq` 0 and no `canOffLine`.) | Recommended: **no extra rule.** The server's tripwire (§2.1 h) logs it, and the player still loses the thrust and the speed change. | none |

---

## 0. What this builds on

### 0.1 How a turn works today
- **Gate:** `canTurn` ([movement.js:2700](source/public/client/movement.js#L2700)) checks the structural conditions, then thrust: remaining engine thrust must be at least ⌈speed × turn cost⌉. The cost has an LCV-rail surcharge, and a reversing submarine pays ×1.33 ([2743-2752](source/public/client/movement.js#L2743)).
- **Requirement:** `calculateRequiredThrust` ([3004](source/public/client/movement.js#L3004)) splits cost C into ⌊C/2⌋ side + ⌊C/2⌋ aft + (C mod 2) "either". `thrusterDirectionRequired` then maps these to physical thruster directions, allowing for rolled, backwards and gravitic ships ([3041-3048](source/public/client/movement.js#L3041)). On a Mindrider, all of C goes to the side.
- **Row:** `doNormalTurn` ([2784](source/public/client/movement.js#L2784)) appends an uncommitted `turnleft`/`turnright` row. `autoAssignThrust` ([2897](source/public/client/movement.js#L2897)) fills thrusters without overthrusting. `updateAssignThrust` ([2510](source/public/client/movement.js#L2510)) then fires the `AssignThrust` event, which runs `MovementPhaseStrategy.onAssignThrust` ([MovementPhaseStrategy.js:210](source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js#L210)), then `UIManager.showShipThrustUI`, then `ShipThrust.js`.
- **Commit:** `doneAssignThrust` ([2521](source/public/client/movement.js#L2521)) commits the row once every slot of `calculateThrustStillReq` is ≤ 0.
- **Overpaying:** `assignThrust` ([2557](source/public/client/movement.js#L2557)) refuses to overpay a non-turn row ([2570](source/public/client/movement.js#L2570)). It lets a turn row be overpaid while the delay stays at 1 or more ([2573](source/public/client/movement.js#L2573)); that overpayment is how a turn is shortened.

### 0.2 Turn delay
`calculateTurndelayAtMove` ([3140](source/public/client/movement.js#L3140)) walks back from the current move to the last `turnleft`/`turnright` row, counting moves and slips. Overpayment on that row shortens the delay (`calculateExtraThrustSpent`, [3256](source/public/client/movement.js#L3256)). The server's twins are `Movement::getTurnDelay` and `calculateTurndelay` ([movement.php:730](source/server/handlers/movement.php#L730), [762](source/server/handlers/movement.php#L762)), which feed `TacGamedata::calculateTurndelays`. **Both sides look only at those two turn types.** §1.3 depends on this.

### 0.3 Thrust is accounted per turn
`getRemainingEngineThrust` ([2133](source/public/client/movement.js#L2133)) sums the current turn's `assignedThrust`. Overthrust criticals test each thruster's channelled thrust per turn ([criticals.php:88](source/server/handlers/criticals.php#L88)). Spreading a turn over two game turns therefore genuinely halves the load each turn, which is the reason the rule exists, and it needs no new accounting.

### 0.4 The server trusts movement, with two backstops
`MovementGamePhase::process` ([169](source/server/Phase/MovementGamePhase.php#L169)) runs two checks:
- `Movement::validateThrustPayment` ([movement.php:100](source/server/handlers/movement.php#L100)) requires every committed turn, pivot, roll and slip to be fully paid. It is **log-only** while `$enforceThrustValidation` is false ([16](source/server/handlers/movement.php#L16)).
- `validateJumpOutSubmission` ([385](source/server/handlers/movement.php#L385)) checks the submitted moves against the authoritative history. This plan reuses that pattern.

Nothing recomputes the turn *cost*: `requiredThrust` always comes from the client.

### 0.5 Persistence needs no migration
`tac_shipmovement.type` is `varchar(45)` and `value` is `varchar(100)` ([emptyDatabase.sql:442/454](db/emptyDatabase.sql#L442)). `getMovesForShips` ([DBManager.php:3156](source/server/controller/DBManager.php#L3156)) loads turn 1, **turn T−1** and turn T. So turn N's rows are available on both client and server at N+1, and are no longer loaded at N+2.

### 0.6 Precedents this follows
- **Gate threading:** `hasJumpedOut` sits at the top of every `canX` gate. The extended turn copies that pattern.
- **A turn variant marked in `value`:** a turn into a pivot is a `turnleft`/`turnright` row with `value: 'turnIntoPivot'`.
- **Rows the client forces when a ship activates:** `doForcedPivot`, called through `doForcedMovementForActiveShip` ([MovementPhaseStrategy.js:251](source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js#L251)). It is idempotent, sets `forced: true`, and the row cannot be deleted.
- **A recoloured movement icon:** `#detach` in `game.php` is the existing art with a CSS `filter`; no new PNG was needed.
- **Multi-turn state markers:** `isPivoting*` and `isRolling`, written by `setPreturnMovementStatusForShip` ([924](source/server/handlers/movement.php#L924)). This plan deliberately does **not** use them (§1.4).

---

## 1. Design

### 1.1 Three movement rows, no new columns

| Row | When | `type` | facing / heading | `requiredThrust` | `assignedThrust` | `value` |
|---|---|---|---|---|---|---|
| **Begin** | turn N, made by the player | `extendTurnLeft` / `extendTurnRight` | unchanged | the **full** turn requirement: exactly what a normal turn would ask for now | what is paid now | **the amount owed, by role**, as JSON written at CONFIRM: `{"any":0,"rear":2,"side":5}` |
| **Completion** | N+1, made by the player as the first order | `turnleft` / `turnright` (same side as the begin) | rotated, as for any turn | the amount owed, mapped onto the thrusters doing each role at N+1 | what is paid now; overpaying shortens the delay | `'extendedTurn'` |
| **Cancellation** | N+1, forced by the client | `extendTurnCancel` | unchanged | nulls | none | reason: `'thrusters'`, `'thrust'` or `'engineShorted'` |

Position, speed and `at_initiative` are set as for any other row. The begin and the cancellation change nothing geometric. The JSON-object form is deliberate: it reads plainly in a Workbench export.

### 1.2 Life cycle
```
turn N    ...moves... ─► [Begin, uncommitted] ─panel CONFIRM─► [Begin, committed] ─► no turn/pivot/roll (D4) ─► commit
             ▲ until the player commits the phase, deleting the move restores normal play
turn N+1  owed ─┬─ payable ────────────────► only the completion icon ─► [Completion] ─► normal play, no speed change
                └─ unpayable/engine shorted ─► [Cancel, forced] + banner note ─────────► normal play, no speed change
```

### 1.3 Why the completion is an ordinary turn row and the begin is not
Making the completion an ordinary `turnleft`/`turnright` row gets the following without any new code:
- the turn delay starts at the completion (R5), on both client and server;
- shortening works by overpaying, which is R5's "extra cost paid in the second game turn";
- `validateThrustPayment` validates the payment;
- replay animates it as a turn;
- `canChangeSpeed`'s "speed changes come first" loop ([1990](source/public/client/movement.js#L1990)) locks out acceleration after it.

A new row type would have to be taught to every one of those, on both client and server: roughly a dozen sites that mirror each other.

The begin must **not** be a turn type, for two reasons. The turn delay would start at the begin, which is wrong under R5. And `validateThrustPayment` would reject it as an underpaid turn.

### 1.4 Why the amount owed is a snapshot stored by role, and why there is no turn-start marker
- **Why a snapshot:** the client's `calculateAssignedThrust` ([3054](source/public/client/movement.js#L3054)) reads criticals as they are *now*, and FirstThrustIgnored reads *this turn's* channelled totals. Recomputing turn N's payment at N+1 therefore gives a different answer whenever a thruster took a HalfEfficiency or FirstThrustIgnored critical in turn N's fire phase. That is exactly the damaged-thruster case extended turns exist for. So at CONFIRM the client writes the amount still owed into `value`, and N+1 reads it back.
- **Why by role, not by physical thruster (D10):** `requiredThrust` names physical thruster sets (1 front, 2 aft, 3 port, 4 starboard). `thrusterDirectionRequired` picks them from the ship's orientation *at that moment*: a rolled ship swaps port and starboard, and a reversing ship swaps front and aft. Under D10 a gravitic ship may begin while rolling or pivoting, and that roll or pivot can complete between the two turns. A snapshot of physical directions would then ask for the wrong thrusters.
  - So the snapshot records **roles**: the aft role is slots 1+2, the side role is slots 3+4, and "either" is slot 0. A turn's requirement only ever uses one slot of each pair.
  - The completion maps the roles back through `thrusterDirectionRequired` using N+1's orientation, exactly as `calculateRequiredThrust` does for a fresh turn.
  - The pair sums need no mapping at all, which also keeps the server checks simple (§2.1 d, f).
- **Why no marker:** the obvious alternative is a preturn `isExtendingTurn*` row written by `setPreturnMovementStatusForShip`, as pivots do. It is not needed: at N+1 the begin row is already loaded on both sides (the T−1 window). A marker would mean a second source of truth, a generation hook, and a new entry in `hideActiveShipMovement`'s marker whitelist ([TacGamedata.php:1932](source/server/model/TacGamedata.php#L1932)). It is rejected (§12), and stays the fallback if the T−1 load window ever shrinks.

### 1.5 How the payment splits
The begin uses the normal turn requirement, so the player assigns thrust exactly as for a normal turn, only less of it: aft, plus the side opposite the turn, with the "either" slot absorbing any overflow. Any split is legal.

Example: speed 12, turn cost 1, turning right. The requirement is Aft 6 · Port 6, so C = 12 and the minimum is 3. Paying Aft 4 · Port 1 now stores `{"any":0,"rear":2,"side":5}`. Next turn the completion asks for Aft 2 · Port 5. If a gravitic ship's roll has completed in between, it asks for Aft 2 · **Starboard** 5 instead, because the starboard thrusters are now doing the side's job.

The begin cannot be overpaid on turn N. It is not a turn type, so `assignThrust`'s rule for non-turn rows ([2570](source/public/client/movement.js#L2570)) already refuses overpayment, which is what R5 requires. D2 also refuses the last point (§3.4).

---

## 2. Server — Stage 1

### 2.1 `Movement::validateExtendedTurn($activeShip, $submitted, $history, $turn)`
A new function in `movement.php`, next to `validateJumpOutSubmission`. It returns the movement array. It uses the same log-only switch as `validateThrustPayment`: while `$enforceThrustValidation` is false, it logs `validateExtendedTurn[LOG-ONLY]: WOULD …` and returns `$submitted` unchanged. Preturn and forced rows are skipped by every check.

| # | Check | Enforce action |
|---|---|---|
| a | At most one begin row this turn | keep the first one, drop the rest |
| b | After a begin, no row whose type is in `Movement::$extendedTurnBlockedTypes` (§2.2) | truncate at the first such row and rebuild straight moves to full speed (shared helper, §2.4) |
| c | Raw thrust paid ≥ ⌈C/4⌉, where C = Σ requiredThrust and C ≥ 2 | drop **only** the begin row. It changed nothing geometric, so every later move is still valid |
| d | The snapshot is not smaller, role by role, than the server's own raw remainder (slot 0 / slots 1+2 / slots 3+4 of `calculateThrustStillReq`) | overwrite `value` with the larger figure for each role |
| e | If `$history` holds a begin from turn T−1, the first submitted row is either the completion (same side, `value` `'extendedTurn'`) or a cancellation | none: treat it as cancelled and log it (the server cannot pay for the player) |
| f | The completion's `requiredThrust` matches the snapshot role by role: slot 0 = either, slots 1+2 = the aft role, slots 3+4 = the side role, with null counting as 0. This needs no server copy of `thrusterDirectionRequired` | drop the completion and everything after it, then rebuild straight. The ship is treated exactly as `validateThrustPayment` treats an underpaid turn |
| g | When a turn is owed: no non-preturn `speedchange` row anywhere in turn T | truncate at it and rebuild straight moves at the old speed |
| h | Tripwire for cancellations. A cancellation counts as justified when Engine Shorted fired this turn (a preturn `speedchange`, D9), or when a thruster or engine was destroyed or took a critical on turn N. Otherwise log "cancelled with nothing changed since the begin" (this catches D12) | **log-only, permanently.** The server's thrust maths ignores criticals (T3), so this check can only ever be a tripwire |

The server checks **lower bounds only** (T3). The server's `calculateAssignedThrust` ([movement.php:867](source/server/handlers/movement.php#L867)) counts raw thrust, while the client counts effective thrust after HalfEfficiency and FirstThrustIgnored. Raw is always at least effective, so "≥ 25%" and "snapshot ≥ raw remainder" can never flag an honest client. An upper bound (≤ C−1) could, and paying too much is not a way to cheat anyway.

**Deliberately not checked: the turn cost itself.** The server does not recompute any turn's cost today (§0.4), so a client that understates C can already understate an ordinary turn. Extended turns inherit the same trust and open no new hole.

### 2.2 The blocked list (D4): one list, mirrored on both sides
```php
/* EXTENDED_TURNS_PLAN.md D4 (user ruling 2026-10-01): the row types a ship may NOT add between
   beginning an extended turn and the end of that turn. "A manoeuvre is a turn, pivot or roll."
   Edit this list to change the ruling.
   ⚠️ MIRROR PAIR with shipManager.movement.extendedTurnBlockedTypes (movement.js). */
public static $extendedTurnBlockedTypes = array(
    'turnleft', 'turnright', 'pivotleft', 'pivotright', 'roll', 'extendTurnLeft', 'extendTurnRight'
);
```
- The client has the identical array, and every client gate asks it (§3.2). Changing the ruling therefore means editing these two lines.
  - Adding `'slipleft', 'slipright'` blocks slips.
  - Adding `'jink'` blocks jinking.
  - Adding `'halfPhase'` or `'contract'` blocks those.
- **`'move'` must never be listed:** the whole point is that the ship keeps moving.
- **`'speedchange'` need not be listed:** after any other order a speed change is impossible anyway (R2), and R4 covers N+1 separately.
- **The extended-turn types are listed** so that a ship can begin only one extended turn at a time.

### 2.3 Call site
In `MovementGamePhase::process` ([169](source/server/Phase/MovementGamePhase.php#L169)), inside the existing backup/swap block ([212-223](source/server/Phase/MovementGamePhase.php#L212)). **The order matters:** `validateExtendedTurn`, then `validateThrustPayment`, then `validateJumpOutSubmission`. That way the extended-turn checks run on the movement exactly as the client submitted it, and `validateThrustPayment` then judges payment on what survives.

### 2.4 Shared helper: rebuild the rest of the turn as straight moves
Move `validateThrustPayment`'s rebuild code ([movement.php:183-215](source/server/handlers/movement.php#L183)) into `private static function rebuildStraightTail(...)`, and have both validators call it. This is a pure extraction. The replay harness's movement check already runs `validateThrustPayment` with enforcement on across the whole corpus, so a byte-identical `check --checks=movement` proves the extraction changed nothing.

### 2.5 What needs no server change

| Area | Why |
|---|---|
| Turn delay (`getTurnDelay`, `calculateTurndelay`) | the completion is a `turnleft`/`turnright` row |
| Overthrust criticals | thrust is totalled per turn (§0.3) |
| Persistence (`submitMovement`) | `type` and `value` are free text. The forced cancellation row is saved, because only forced jinks are skipped ([DBManager.php:2327](source/server/controller/DBManager.php#L2327)) |
| `hideActiveShipMovement` | Turn N: the begin is masked like any other move until its bracket resolves. N+1: the begin is a T−1 row, which this function never masks |
| Stealth | an undetected ship's movement is replaced wholesale |
| Attached mirror | pods copy the host's rows as `attached` rows, taking positions and facings only |
| Chameleon plausibility | it checks speed changes only ([baseSystems.php:2723](source/server/model/systems/baseSystems.php#L2723)) |
| Jump out | `resolveJumpOuts` ignores the begin row, and a ship that leaves owes nothing |
| `setPreturnMovementStatusForShip` | there is no marker (§1.4) |

### 2.6 Replay harness
Write a `validateExtendedTurn` line to `movement.txt` **only** for ship-turns that carry a begin, completion or cancellation row. No corpus game has one, so the baseline does not change. After the Stage 6 playtest, add that game to the corpus with the merge recipe (see [[project_replay_harness]]; never use `record --games=`).

### 2.7 Server testing
Use scratch-PHP synthetic movement arrays, as was done for `validateThrustPayment`'s five synthetic cases. Cover:
- **Begin:** a legal begin; one paid below 25%; a listed manoeuvre after it; an unlisted one (a slip) after it, which must pass; a tampered snapshot.
- **Completion:** a legal completion; a missing one; one with the wrong requirement; one whose roles were remapped after a roll, which must pass.
- **N+1:** acceleration on N+1.
- **Cancellation:** after Engine Shorted; after a thruster was destroyed; with nothing changed.

Run each in log-only mode and with enforcement switched on in memory. Then run a full replay `check`, which must be byte-identical.

---

## 3. Client engine — Stage 2 (`movement.js`; nothing reaches it until Stage 4 adds the icons)

### 3.1 State
- `extendedTurnBlockedTypes`: the D4 list, the mirror of §2.2.
- `isExtendedTurnStart(move)`: the row's type is `extendTurnLeft` or `extendTurnRight`.
- `getExtendedTurnStart(ship)`: this turn's begin row, or null.
- `getOwedExtendedTurn(ship)`: last turn's begin row, or null.
- `getExtendedTurnCancel(ship)`: this turn's cancellation row, or null.
- `isExtendedTurnOutstanding(ship)`: a turn is owed, and this turn has neither a committed completion nor a cancellation.
- **`extendedTurnForbids(ship, type)`: the one predicate every gate asks.** It returns true when the turn is outstanding (any type: on N+1 nothing comes before the completion), or when a begin exists this turn and `type` is in the list.
- `getExtendedTurnRemainder(begin)`: returns `{any, rear, side}` from `JSON.parse(begin.value)`. If `value` is unreadable, it recomputes from `requiredThrust`/`assignedThrust` as pair sums; the server has already bounded the figure (§2.1 d).
- `getExtendedTurnCost(req)` and `getExtendedTurnMinimum(cost)`.

### 3.2 Gate threading: every gate asks the list, using the row type it would create

| Gate | Asks | Today's result after a begin |
|---|---|---|
| `canTurn`, `canTurnIntoPivot`, `canGraviticTurn` | `'turnleft'` / `'turnright'` (by side) | blocked |
| `canPivot` | `'pivotleft'` / `'pivotright'` | blocked |
| `canRoll`, `canEmergencyRoll` | `'roll'` | blocked |
| `canBeginExtendedTurn` | `'extendTurnLeft'` / `'extendTurnRight'` | blocked |
| `canSlip` | `'slipleft'` / `'slipright'` | allowed |
| `canJink` | `'jink'` | allowed |
| `canHalfPhase` | `'halfPhase'` | allowed |
| `canContract` | `'contract'` | allowed |
| `canJumpOut` | `'jumpout'` | allowed |
| `canMove` | `'move'` | allowed; never listed, so only the outstanding state blocks it |
| `canChangeSpeed` | not list-based: `if (getOwedExtendedTurn(ship)) return false;` for the whole of N+1 (R4, D6). On turn N the begin row already trips the "speed changes come first" loop ([1990](source/public/client/movement.js#L1990)). ⚠️ That loop skips forced rows, so a forced cancellation row does not block acceleration by itself; this explicit test is what does (T5) | blocked |

The gates that are "allowed" today still ask the list. That is what makes D4 a one-line change.

Two changes to `canTurn` itself:
- a third parameter, `ignoreThrustCost = false`. Existing callers are unchanged; this follows the pattern of `calculateAssignedThrust`'s `overthrustCheck` parameter. It lets the begin reuse `canTurn`'s structural conditions instead of copying them;
- its agile snap exception ([2738](source/public/client/movement.js#L2738)) no longer applies when the previous row is a completion (`value === 'extendedTurn'`), per the D7 reading.

### 3.3 Actions
- **`canBeginExtendedTurn(ship, right)`** returns true when all of these hold:
  - it is phase 2, and the unit is not a flight, OSAT, base, terrain or mine;
  - `canTurn(ship, right, true)` is true. This means `canTurn`'s own rolling, pivoting and alignment rules apply, including the gravitic exceptions (D10);
  - `!extendedTurnForbids(ship, type)`;
  - the turn delay is 0, with no agile exception (D7);
  - C ≥ 2, and engine thrust left ≥ ⌈C/4⌉;
  - D1-A: `!canTurn(ship, right)`, meaning the normal turn is unaffordable.
- **`doBeginExtendedTurn(ship, right)`** appends the begin row (uncommitted, with `requiredThrust` from `calculateRequiredThrust`), auto-assigns thrust capped at min(engine thrust left, C − 1), and opens the panel. Paying as much as possible by default is deliberate: thrust left over on turn N has nothing else to buy except slips and jinks.
- **The Make Extended Turn box** (D1-C, D2): `canToggleExtendedTurn(ship, move)` and `setExtendedTurn(ship, on)`.
  - **On a plain turn row:** the box appears, unticked, on an uncommitted plain `turnleft`/`turnright` row (`value` 0) when the ship would qualify for a begin apart from D1-A's thrust test. Ticking it changes the row's type and restores the previous facing and heading. If the full cost was already assigned, it takes one point back from the thruster carrying the most thrust. That keeps the player's hand-made split, which matters because avoiding overthrust is exactly the case where they set it by hand.
  - **On a begin row:** the box appears ticked. Unticking it turns the row back into a normal turn, with facing and heading rotated. It is enabled only when a normal turn is affordable (engine thrust available to this turn ≥ C); otherwise it is locked, with the hint "Not enough thrust for a normal turn". So a begin made from the map icon is always locked, because that is why its icon appeared.
- **`canCompleteExtendedTurn(ship)`** returns true when the turn is outstanding, there is no uncommitted row, there is no Engine Shorted this turn, and the remainder is payable. It deliberately does **not** call `canTurn`: the turn-delay and thrust gates belonged to the begin.
- **`doCompleteExtendedTurn(ship)`** builds the row with `doNormalTurn`'s geometry for the begin's side and `value: 'extendedTurn'`. It maps the role snapshot onto N+1's thrusters:
  - slot 0 gets the "either" amount;
  - `thrusterDirectionRequired(ship, 'main', false, true)` gets the aft role;
  - `thrusterDirectionRequired(ship, right ? 'port' : 'stbd', false, true)` gets the side role.

  These are the same two calls `calculateRequiredThrust` makes ([3041-3048](source/public/client/movement.js#L3041)). It then auto-assigns thrust and opens the panel.
- **`canPayRequirement(ship, req)`** is the payability test. It is **a dry run of the real `assignThrust`**, not a capacity formula. It pushes a scratch row, assigns greedily through the required directions and then "either", reads `calculateThrustStillReq`, and splices the scratch row out in a `finally`. The test therefore faces exactly the same limits as the player: the overthrust ceiling (2× rating), critical steps and the engine limit, with no second copy of the rules. Cleaning up only needs the splice, because `assignThrust` does not touch `thruster.channeled` (only `autoAssignThrust` does).
- **`doForcedExtendedTurnCancel(ship)`** is idempotent, like `doForcedPivot`. It picks the reason in this order:
  - `'engineShorted'` if this turn has a preturn `speedchange`, which is the row `doStuckEngine` writes (D9);
  - otherwise, if `!canPayRequirement`: `'thrust'` when engine thrust left is less than the total owed, and `'thrusters'` otherwise.

  It appends the cancellation row with `forced: true`. There is **no pop-up** (D5); the reason shows in the ship window banner (§6). It must run **after** `doForcedPivot` (T19).

### 3.4 Thrust-panel plumbing
- **`assignThrust`:** on a begin row, refuse a point that would leave nothing owed (D2): if the sum of positive stillReq slots is ≤ 1, return false. Each click adds exactly one effective point (HalfEfficiency and FirstThrustIgnored step the raw count instead), so "owed ≤ 1" is exact.
- **`doneAssignThrust`:** for a begin row, commit only when ⌈C/4⌉ ≤ paid ≤ C−1, where paid = C − Σ positive stillReq slots. This is the backstop for D2. Write the role snapshot into `value` first. Other rows are unchanged.
- **`autoAssignThrust(ship, maxTotal)`:** add an optional cap; leaving it undefined behaves as today. The cap is needed because `autoAssignThrust` never checks engine thrust (for normal turns, `canTurn` already guaranteed enough of it). See T6.
- **`unAssignThrust`, `calculateThrustStillReq`:** unchanged.

---

## 4. Thrust panel and hover labels — Stage 0 (restyle) and Stage 3 (extended-turn modes)

### 4.1 What is wrong with the panel today
- **Old skin:** the info box is the shared hover `Tooltip`: rounded, 65% black, headed "ASSIGN THRUST". It is the only chrome on the board that was never converted to the SCS skin.
- **No per-direction view:** it shows lines such as "3 thrust to aft thrusters", not paid/required per direction.
- **Destroyed thrusters look like live ones in the panel.** A tile picks its art from criticals only ([ShipThrust.js:26-37](source/public/client/UI/reactJs/shipThrust/ShipThrust.js#L26)), and `getThrusters` ([systems.js:1094](source/public/client/systems.js#L1094)) returns destroyed thrusters too. Your July steer, "let them SEE why via the greyed-out thruster icons", is therefore only met in the ship window, not on the panel the player is actually clicking. An extended turn's cancellation is exactly that case.
- **Emoji buttons:** ✔ and 🛇 render differently on each platform, and 🛇 has no glyph in several Windows fonts.
- **Mismatched `getThrusters` signature:** it is declared ([251](source/public/client/UI/reactJs/shipThrust/ShipThrust.js#L251)) as `(ship, direction, totalRequired, movement)` but called with five arguments. As a result `movement` holds the stillReq array, and `movement.type !== 'roll'` is always true. Rolls only work because their requirement uses 0 rather than null.
- **Dead code:** `ThrustUIContainer` paints `background-color: blue` on a zero-size box, and 55 lines of commented-out legacy code sit at the bottom of the file.
- **Off-screen on phones:** the panel is anchored 125px below the ship and never clamped, so it goes off-screen at phone sizes.

### 4.2 New layout
The thruster ring stays as it is (rotation, positions, art). The info box becomes an SCS panel:
```
                 [2/3]
        [0/2]    ▲      [3/3]          thruster ring, rotated with the ship
                 [1/2]
 ┌─────────────────────────────────────────────┐
 │ BEGIN EXTENDED TURN · STARBOARD             │  section bar
 │ Turn cost                 12                │
 │ Paying now (min 3)         5  ████▌·|·····  │  meter; tick at 25%
 │ Owed next turn             7  Aft 2 · Port 5│
 │ Engine thrust left         0                │
 │ Delay counts from completion next turn      │
 │ [■] Make Extended Turn   (locked: not enough│  the D1-C / D2 box
 │                           thrust for a turn)│
 │ [AUTO] [RESET]            [CANCEL] [CONFIRM]│
 └─────────────────────────────────────────────┘
```
- **Panel:** square corners, `theme.colors.windowBg` fill and a 1px `line` border. The title bar copies ShipNotesPanel's `BlockTitle`. The label/value rows copy its `StatRow`, `StatLabel` and `StatValue`: Consolas numerals, with value text in `textAccent`.
- **Requirement rows:** one row per direction for every manoeuvre, such as `Aft 2/3`, `Port 3/3` and `Either 0/1` (`Any` for rolls). Paid rows use `statusOk`; open rows use `warning`.
- **Thruster tiles:** keep their PNGs and critical variants, and add:
  - a Consolas `channeled/output` badge, in `healthCrit` when overthrusting;
  - an accent outline on directions that still need thrust;
  - dimming and a ✕ on destroyed thrusters.
- **Buttons:** text buttons replace the emoji. CONFIRM is the primary button; it stays disabled until the payment is valid, and says why on hover. CANCEL, AUTO and RESET sit beside it. The **Make Extended Turn** box appears on qualifying turns and on every begin (§3.3).
- **Translucency:** set it in the fill's alpha only, never with element opacity (see the alpha-compounding trap in [[project_visual_unification]]).

### 4.3 Modes
The component works out its mode from the row itself:
- `isExtendedTurnStart(movement)` means begin mode;
- `movement.value === 'extendedTurn'` means completion mode;
- anything else is normal mode.

The `AssignThrust` event payload and `MovementPhaseStrategy.onAssignThrust` do not change.

| Mode | Title | Extra rows | Make Extended Turn box | CONFIRM is enabled when |
|---|---|---|---|---|
| Normal | TURN STARBOARD / PIVOT PORT / ROLL / ACCELERATE … | turn delay (turns only) | unticked, on qualifying turns only | every slot is paid |
| Begin | BEGIN EXTENDED TURN · side | cost, paying now (with the minimum), meter, amount owed per direction | ticked; locked when a normal turn is unaffordable | ⌈C/4⌉ ≤ paid ≤ C−1 (the last point cannot be assigned at all, D2) |
| Completion | COMPLETE EXTENDED TURN · side | amount owed (from the snapshot, mapped onto today's thrusters), turn delay including shortening | none | every slot is paid |

### 4.4 Input behaviour that stays the same (see [[feedback_preserve_input_affordances]])
- Left-click assigns thrust; right-click unassigns it.
- On touch, a long press unassigns, because it fires `contextmenu` by itself.
- AUTO and RESET call the same functions as today.
- CANCEL removes the row.

### 4.5 Phones
On `(pointer: coarse)` screens, or below about 600px wide, the panel docks to the bottom of the viewport, which is what `starter-guide.html` already describes; the thruster ring stays on the ship. On desktop the panel sits under the ship, clamped inside the viewport. Buttons and the box are at least 32px on touch screens.

### 4.6 Movement-icon hover labels (D11)
`moveTooltip.js` currently styles its label in two layers of inline styles: `#2c3e50` with a 5px radius and 16px bold text in the template, which `.css()` then overrides with `#333`, a 4px radius and 12px text, at z-index 1000. Replace both layers with one class in `tactical.css` built on the map-tooltip tokens:
- fill `--fv-overlay-soft`;
- text `--fv-text-bright`, Arial 12px;
- corners `--fv-radius-tooltip` (tooltips are the standing 7px rounded exception in [[project_visual_unification]]).

The label then reads as the same family as the ship tooltip it sits beside.

What stays the same:
- The "Stop Pivoting" and "Stop Rolling" relabelling stays.
- The four new icons label themselves through their own `data-movement-type` (T11).
- `fadeIn` animates opacity up to 1, so the alpha-compounding trap does not apply.

---

## 5. Movement icons — Stage 4 (the feature goes live)

### 5.1 Markup (in `game.php`, inside `#shipMovementUI`)
Add four `.movement-icon` divs. They are separate elements, following the `pivotLeftActive`/`rollActive` precedent:
- `extendTurnLeft` / `extendTurnRight`, labelled "Begin Extended Turn";
- `completeExtendTurnLeft` / `completeExtendTurnRight`, labelled "Complete Extended Turn".

**Art:** the existing `turnleft`/`turnright` PNGs, recoloured with a CSS `filter` class in `tactical.css` next to the `#shipMovementUI` rules (the `#detach` precedent). The begin is green, matching the "Making Extended Turn" note (D8); the completion is cyan (`statusPending`).

⚠️ **Use separate elements rather than relabelling one.** `moveTooltip.js` reads `.data('movement-type')`, and jQuery caches that value after the first read, so changing the attribute later is silently ignored.

### 5.2 `drawShipMovementUI` in `shipMovement.js`
- In the turn-left block ([351](source/public/client/UI/shipMovement.js#L351)) and the turn-right block ([390](source/public/client/UI/shipMovement.js#L390)): draw the normal arrow if `canTurn` is true; otherwise draw the begin icon if `canBeginExtendedTurn` is true. Both use the same spot (angle ∓60, distance 60); under D1-A they can never both apply.
- Draw the completion icon in the same spot when `canCompleteExtendedTurn` is true. Because §3.2's `extendedTurnForbids` blocks everything while a turn is outstanding, the completion icon is the only one on screen until the completion is confirmed. That gives you "only show the turn icon".

### 5.3 Callbacks
`ShipMovementCallbacks` gains `extendTurn` and `completeExtendTurn` callbacks next to `turnCallback` ([155](source/public/client/ShipMovementCallbacks.js#L155)), and `shipMovement.js` gains the matching element bindings.

### 5.4 Automatic cancellation
`doForcedMovementForActiveShip` ([MovementPhaseStrategy.js:251](source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js#L251)) calls `doForcedExtendedTurnCancel` for each active ship, **after** `doForcedPivot` (T19). The call runs on every update, so it must be idempotent. There is no pop-up (D5): the ship window banner carries the note (§6).

### 5.5 Commit dialog
The phase-2 branch of `gamedata.js` ([1463](source/public/client/gamedata.js#L1463)) gains a list next to "will LEAVE THE BATTLE": "beginning an EXTENDED TURN — must be completed at the start of next turn's movement". This is a line in the existing commit confirmation, not a new pop-up. It covers the one commitment the player cannot take back next turn.

---

## 6. Showing the state to players — Stage 5
- **Map tooltip** (the state line in `ShipTooltip.js`, [295-311](source/public/client/UI/ShipTooltip.js#L295)): a green note, **Making Extended Turn (starboard)**, from the begin until the completion or cancellation (D8). It uses `limegreen`, like the tooltip's other green notes. Opponents see it once the move is revealed. Undetected stealth ships show nothing, because their tooltip is hidden.
- **Ship window status banners** (`getStatusBanners` in `ShipWindow.js`, [2225](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L2225)):
  - while the turn is in progress, the same green line, in `statusOk` (the banners already mirror the tooltip's notes in its colours);
  - after a cancellation, an amber (`statusAlert`) **Extended Turn Cancelled — {thrusters lost | not enough thrust | engine shorted} · No speed change this turn**, for the rest of N+1 (D5).
- **Replay:** nothing to build. `ShipMovementAnimation` drops rows that change nothing, so the begin and the cancellation do not animate, and the completion animates as a turn at N+1's starting hex. Verify this; do not change it.
- **Docs:**
  - [faq.html:76](source/public/docs/faq.html#L76) says "Extended turns … are not implemented"; replace it with the rule.
  - The "Turning" section of `starter-guide.html` ([388](source/public/docs/starter-guide.html#L388)) gains a bullet. If a new subsection is added, it must use `data-anchor`, never `id=` (see [[project_document_viewer]]).
  - `img/docViewer/movement-arrows.jpg` shows the old panel and needs re-shooting after Stage 0.

---

## 7. Traps

| # | Trap |
|---|---|
| T1 | **Row types:** the begin must not be a turn type, and the completion must be one (§1.3). |
| T2 | **The amount owed is a snapshot.** Never recompute it at N+1: criticals and FirstThrustIgnored are read as of "now" (§1.4). |
| T3 | **The server's thrust maths ignores criticals**, so it may check lower bounds only (§2.1). |
| T4 | **`forced` has no database column**, so the cancellation row reloads with `forced=false`. The flag only has to make the row undeletable within the session (rows stay on the client until commit), so this is harmless. Nothing on the server may depend on the flag. |
| T5 | **`canChangeSpeed`'s "speed changes come first" loop skips forced rows**, so the cancellation row does not block acceleration on its own. That is why `canChangeSpeed` gets its own test (§3.2). |
| T6 | **`autoAssignThrust` never checks engine thrust.** The begin must cap it. |
| T7 | **The T−1 load window.** Turn N's row is loaded only at N+1, so nothing may look for it at N+2. If an outstanding turn is never completed (the player surrenders or times out, and `advance()` just appends an `end` row), it lapses silently, which is the same outcome as a cancellation. |
| T8 | **Never cache the owed state on the ship object.** Ship objects are replaced on any poll that carries ship data, so derive the state from the rows every time. |
| T9 | **Every gate must ask `extendedTurnForbids` with the row type it would create, including the gates the list does not name today** (§3.2). A missing call is a manoeuvre that would ignore a later change to D4. Grep for `hasJumpedOut(ship)) return false` to get the list of gates. |
| T10 | **Physical thruster directions depend on the orientation at that moment:** a rolled ship swaps port and starboard, and a reversing ship swaps front and aft. Under D10 a gravitic ship's roll or pivot may complete between the begin and the completion. Store the amount owed **by role** and map it onto thrusters only at completion (§1.4); never store it by slot. |
| T11 | **jQuery `.data()` caches the hover label**, so each icon needs its own element (§5.1). |
| T12 | **Order in `process()`:** `validateExtendedTurn` must run before `validateThrustPayment` (§2.3). |
| T13 | **The dry run must splice its scratch row in a `finally`.** Otherwise a throw inside `assignThrust` leaves a phantom uncommitted row that blocks the Commit button. |
| T14 | **React bundle:** an esbuild parse does not resolve imports ([[howto_verify_react_bundle]]), and a missing `theme` import once killed the whole UI bundle (the PlayerSettings regression). After Stage 0 and Stage 3, run a full `fvbuild.ps1 -Client`, load `game.php` and check that `window.UIManager` exists. |
| T15 | **Replay harness:** `record --games=` rewrites the manifest. Use the merge recipe only (§2.6). |
| T16 | **Phone-width screenshots:** `--window-size=390` actually lays out at about 500px. Use CDP device emulation ([[howto_headless_chrome_phone_width]]). |
| T17 | **Engine Shorted on N+1:** cancel explicitly with the `'engineShorted'` reason (D9), before the payability test runs. Its row is a *preturn* `speedchange`, so check (g) must skip preturn rows, or it would flag the critical itself. |
| T18 | **`value` and `JSON_NUMERIC_CHECK`:** the snapshot `{"any":0,"rear":2,"side":5}` is not numeric, so it survives encoding. Never store a bare number in that field. |
| T19 | **`doForcedExtendedTurnCancel` must run after `doForcedPivot`** in `doForcedMovementForActiveShip`. A gravitic ship's continuing pivot is applied at activation, so the payability dry run has to see N+1's orientation after that pivot. |
| T20 | **The blocked list is a mirror pair** (§2.2). If only one side is changed, the client will either offer an order that the server logs as illegal, or refuse one that the server would accept. |

---

## 8. Stages

Each stage can be tested on its own. Stages 0 and 1 can also be deployed on their own.

| Stage | Content | Build | Proof |
|---|---|---|---|
| **0** | Thrust panel restyle (§4.1-4.5) and hover-label restyle (§4.6), **with no change in behaviour**. Includes fixing the `getThrusters` signature and removing the dead code | `fvbuild.ps1 -Client` | tests 15 and 18; `window.UIManager` exists (T14) |
| **1** | Server: `validateExtendedTurn` (log-only), the blocked list, the `rebuildStraightTail` extraction and the harness line | none (PHP only, no new class, so no autoload) | the synthetic cases (§2.7); full replay `check` byte-identical |
| **2** | Client engine (§3): state, the blocked list, gates, actions, the `assignThrust`/`doneAssignThrust` branches, the `autoAssignThrust` cap and the `canTurn` changes. Nothing can reach it yet | watcher | drive it from the console in a local game; existing manoeuvres unchanged |
| **3** | Panel modes (§4.3), including the Make Extended Turn box | `fvbuild.ps1 -Client` | open the begin and completion modes from the console; tick and untick the box |
| **4** | Icons, callbacks, automatic cancellation and the commit-dialog line (§5). **The feature goes live** | watcher | tests 1-14, 16, 17 |
| **5** | Tooltip note, banners, docs (§6) | watcher | review by eye |
| **6** | Playtest in a fresh two-player local game, then add it to the replay corpus (merge recipe) | — | harness green, with the new game included |

Nothing gets committed. The user reviews each stage's diff and commits it themselves.

### 8.1 As built (2026-10-01): Stages 0-2
Each stage touches its own files, so each can be committed on its own: Stage 0 is `ShipThrust.js`,
`moveTooltip.js` and `tactical.css`; Stage 1 is `movement.php`, `MovementGamePhase.php` and
`replayHarness.php`; Stage 2 is `movement.js`.

Deviations from the text above:
- **D9 detection (§3.3, T17).** Engine Shorted always takes the engine offline, but writes the preturn
  `speedchange` row only on a d20 roll of 15 or more (`Engine::doEngineShorted`). Keying on the row alone
  would label most shorts `'thrust'`. `hasEngineShortedSince` also accepts an `EngineShorted` critical
  dated on or after the begin turn.
- **The box (§3.3).** `canToggleExtendedTurn` / `setExtendedTurn` became `getExtendedTurnToggle(ship, move)`,
  which returns `null` (no box) or `{checked, enabled, hint}`, and `setExtendedTurn(ship, on)`. The box has
  three states, not two. `qualifiesForExtendedTurn` is `canBeginExtendedTurn` without the D1-A thrust test;
  the box asks it, the map icon asks `canBeginExtendedTurn`.
- **`validateExtendedTurn` takes an optional fifth argument, `&$findings`**, which receives one line per
  finding. The replay harness prints them, and the synthetic cases assert on them.
- **Panel (§4.2).** The desktop panel is 260px wide: four text buttons do not fit in one row at 230px. On
  desktop it flips above the ship when there is no room below. The panel is clamped by measurement
  (`placePanel`), because the transformed container is the containing block even for `position: fixed`.
  A MutationObserver re-clamps it when `repositionThrustUi` moves the container with jQuery. Rows that
  are overpaid show an "Extra thrust +N" line.
- §11's `hasTurned` typo (`"turneleft"`) is already fixed in the current code.
- **Panel revised by the user (2026-10-01), superseding parts of §4.2 and §4.4:**
  - It wears the Gravitic Augmenter menu's green. That set moved into `theme.js` as `theme.colors.green*`,
    and `GraviticAugmenterMenu.js` now reads it too, with identical values.
  - It is compact: 180px wide on desktop, 240px docked on phones.
  - **AUTO and RESET are gone.** Its buttons are CONFIRM then CANCEL. To keep every manoeuvre pre-filled,
    jinks, rolls, emergency rolls, Contraction and turn-into-pivot now call `autoAssignThrust` when they
    open the panel, as turns, slips and speed changes already did. Pivots still never auto-assign
    (`autoAssignThrust` skips them by design).
  - **Stage 3's mock-up in §4.2 shows [AUTO] [RESET]. Drop them.**
- **Panel placement and boxes, revised by the user (2026-10-01), superseding §4.5:**
  - **There is no bottom-docked phone layout any more.** At every size the panel sits against the thruster
    ring. It goes below the ring, else above, else right, else left, whichever fits first on screen.
  - The ring follows zoom. Ship icons keep their world size, so zoomed out the old fixed ring (columns at
    60px, rows at 80px) and the panel 125px down stood far from a few-pixel hull. `getRingOffsets` now
    pulls the ring in to hug the unit's circle. It never goes further out than the old layout, and it is
    held off only far enough that tiles cannot collide. The panel also keeps clear of the hull itself,
    out to that same old reach.
  - `MovementPhaseStrategy.repositionThrustUi` fires `fv-thrust-relayout` on the container after it moves
    it. This replaced a MutationObserver, which missed zooms on a ship at the centre of the zoom: such a
    ship does not move.
  - Tile boxes: **orange** means a click is taken and pays thrust the manoeuvre still needs. **Green**
    means a click is taken as extra thrust, which shortens a turn's delay. No box means the click would be
    refused. "Taken" is `shipManager.movement.wouldAcceptThrust`, a dry run of `assignThrust` that puts the
    row back exactly.
  - starter-guide.html's movement bullets now describe the panel beside the ship, Confirm/Cancel and the
    two box colours. `img/docViewer/movement-arrows.jpg` still shows the old panel and needs re-shooting.

Proof so far:
- **Stage 1:** 20 synthetic cases, all as planned in log-only and in enforce mode. Full replay `check`:
  the eight known failures, plus 4302. 4302 is data drift (the game moved from phase 2 to 3) and was
  byte-identical with Stage 1 stashed.
- **Stage 2:** 41 console checks in a real local game page with every POST blocked (4347, a gravitic
  Traveler; 4256, a Vree Xonn). The checks cover the box both ways, D2, D4, the map-icon begin, a faked N+1
  completion, the dry run leaving no row, and all three cancel reasons.
- **Stage 0:** headless render at desktop and phone width. Tests 15 and 18 in a real game are still owed.

### 8.2 As built (2026-10-01): Stages 3-4
Stage 3 is `ShipThrust.js` only. Stage 4 is `game.php`, `shipMovement.js`, `ShipMovementCallbacks.js`,
`MovementPhaseStrategy.js` and `gamedata.js`, plus one removed line in `movement.js` (`setExtendedTurn`,
below). `tactical.css` is not touched after all: the icon colour is drawn, not filtered.

Deviations from the text above:
- **Begin-mode panel (§4.2, §4.3).** The per-direction rows stay as `paid/required`, but on a begin
  the unpaid part is shown in cyan (`statusPending`, "owed next turn"), not orange ("still needed"). Under
  them: Turn cost, Paying now (min ⌈C/4⌉) with a meter ticked at the minimum, Owed next turn (the
  total; the direction rows already show the split), Engine thrust left, and a one-line note. There is no
  separate "Aft 2 · Port 5" line, because it would not fit 180px.
- **Titles.** "Begin Extended Turn" and "Complete Extended Turn" put the side on a second line (`\n`,
  `PanelTitle` is `white-space: pre`). On one line they would overflow 180px, and wrapping put the "·" at
  the start of line two.
- **The box.** The tick is drawn with CSS, not a ✓ glyph. When the box is locked, its hint is printed
  under it as well as in the `title`. The row is 32px tall on touch screens.
- **Icon colour (§5.1), as revised by the user (2026-10-01).** The plan said a green begin icon and a cyan
  completion icon, but `turnleft.png`/`turnright.png` are already green. A lime/cyan CSS-filter version was
  built first, then replaced at the user's request: **both icons are ORANGE**. The turn arrows' art is
  painted in one colour on the canvas (`drawUIimage`'s new optional `tint`, a `source-atop` fill). It is set
  by **`UI.shipMovement.extendedTurnColour`** at the top of `shipMovement.js`, default `#ff8c00`. That is the
  one place to change it. There is no CSS for these icons. Their opacity is **`UI.shipMovement.extendedTurnOpacity`**
  next to it (default 1, as drawn). The begin panel's "owed next turn" figures (the total and the
  direction rows' unpaid part) also use `extendedTurnColour`: `ShipThrust.js` reads it at render, in place
  of the cyan first built.
- **The icons stay hidden under an open panel (user report, 2026-10-01).** Ticking or unticking the box used
  to bring back Move, Slip and Cancel under the panel, and every click flipped them. `setExtendedTurn` fired
  `ShipMovementChanged`, and `PhaseStrategy.redrawMovementUI` only hides the ring when it is showing, so
  with the ring already hidden it redrew it. `setExtendedTurn` no longer fires that event. Nothing else
  needed it while the row is uncommitted, and CONFIRM and CANCEL both fire it.
- **One completion handler** serves both completion icons, because the side was named at the begin.
- Ship icons consume **committed** rows only, so the thruster ring is always drawn at the committed
  facing. Neither a begin nor a box toggle can leave it out of date.

Proof: 41 checks in a real local game page (4347, gravitic Traveler, every POST blocked), driven through
the real icons, tiles, box and buttons. They cover:
- the D1-C box both ways, and D2 through the tiles;
- the commit-dialog line;
- Cancel Last Move;
- the map-icon begin, with its box locked;
- a faked N+1 showing the completion icon as the only icon, then the completion panel and commit;
- `PhaseStrategy.update()` cancelling an unpayable turn exactly once.

Headless screenshots at desktop and 390px phone width.

Observation, not changed: on a phone, an open Order of Battle panel draws over the thrust panel. This
has been true since Stage 0's placement and applies to every manoeuvre.

### 8.3 As built (2026-10-01): Stages 5-6
- **One reader:** `shipManager.movement.getExtendedTurnStatus(ship)` (`movement.js`). It returns null,
  `{cancelled: false, text: 'Making Extended Turn (port|starboard)'}` from the begin until the completion,
  or `{cancelled: true, text: 'Extended Turn Cancelled — Thrusters Lost | Not Enough Thrust | Engine
  Shorted · No speed change this turn'}` for the rest of the turn it was cancelled in. The map tooltip and
  the ship window both read it, the same pattern as `shipManager.getHangarManoeuvre`.
- **Map tooltip** (`ShipTooltip.js`): the limegreen note, beside Rolled and Half-Phased. Not shown for a
  cancellation (D5 puts that in the ship window only).
- **Ship window** (`ShipWindow.js` `getStatusBanners`): green `statusOk` while in progress, amber
  `statusAlert` after a cancellation. Placed after Jumping to Hyperspace. `StatusBanner` uppercases it.
- **Docs:** the FAQ entry now gives the rule instead of "not implemented", and the starter guide's
  Turning section has a bullet. `movement-arrows.jpg` needed **no** re-shoot: it shows the arrows with no
  thrust panel at all. Its alt text claimed "with the thrust window open", so the alt text was corrected.
- **Replay** was verified, not changed. In 4430's replay of turn 3 the G'Quan's path has no segment for
  the begin row, so there is no stutter. Turn 4 opens with a −60° turn in hex 8,2, its starting hex, then
  the moves.
- **Stage 6:** the user's playtest is **game 4430** (G'Quan Heavy Cruiser #2, ship 877514).
  - Turn 3: `extendTurnLeft` at speed 7, cost 5, paid 2, snapshot `{"any":1,"rear":2,"side":0}`.
  - Turn 4: `turnleft` with value `extendedTurn` as the first row, requirement `[1,null,2,null,0]`, facing
    3→2 in the starting hex, speed unchanged.
  - No `validateExtendedTurn` line in the server log for it.
  - It was added to the corpus with the merge recipe: back up `manifest.json`, `record --games=4430`,
    merge. Its `movement.txt` shows `validateExtendedTurn LEGAL | findings: none` for turns 3 and 4, and
    `validateThrustPayment` LEGAL with enforcement on.
  - Full `check`: 110 passed, 9 failed. The 9 are exactly the known set
    ([[arch_replay_corpus_known_failures]]). 4430 passes.

Proof for 3-5 together: 54 checks in a real local game page (4347, every POST blocked), driven through the
real icons, tiles, box and buttons. Besides §8.2's checks they cover:
- the ring staying hidden through tick, untick and re-tick;
- the canvas pixels of both icons reading exactly `#ff8c00`, with the plain arrows still green;
- the status reader, tooltip and banner at turn N, at a faked N+1, after completion (all gone) and after
  an automatic cancellation (amber banner, no tooltip note).

## 9. Test matrix
Play these in fresh local games, then check `tac_shipmovement` for that game ID.

| # | Setup | Do | Expect |
|---|---|---|---|
| 1 | A ship fast enough that the turn costs more than its engine thrust | Turn N: begin right, pay between min and C−1, move the rest of the way | the turn arrow is replaced by the green icon; the panel reads BEGIN with the box ticked and locked; the last point cannot be assigned; an `extendTurnRight` row is stored with `{"any":…,"rear":…,"side":…}` in `value` |
| 2 | The same ship on turn N+1 | — | only the cyan completion icon shows; the panel shows the owed split; after completion the facing changes in N+1's starting hex; delay = ⌈speed × turn-delay cost⌉ − overpayment, counted from that point; no +/− speed buttons |
| 3 | Shorten on N+1 | overpay the remainder | the delay falls by 1 per point of overpayment |
| 4 | Begin, then try other orders on turn N | — | turn, pivot and roll icons are gone; move, slip, jink, Contraction and Half-Phase are still offered |
| 5 | Begin, then cancel the move | — | the normal arrows come back |
| 6 | Destroy the owed side's thrusters in turn N's fire phase | turn N+1 | automatic cancellation with no pop-up; the ship window banner reads EXTENDED TURN CANCELLED — THRUSTERS LOST; no +/−; other manoeuvres allowed; an `extendTurnCancel` row is stored |
| 7 | A completion that needs overthrusting | turn N+1 | the player must complete it; auto-assign leaves the overthrust for the player to assign; criticals are tested at the end of N+1 |
| 8 | D1-C / D2 | on an affordable normal turn, tick Make Extended Turn, then untick it | ticking switches the panel mode and restores the facing; if the full cost was assigned, one point comes back from the thruster carrying the most; the last point cannot be re-added while ticked; unticking returns a normal turn |
| 9 | Opponent's view | watch during N's bracket, after it, and during N+1 | nothing; then a green "Making Extended Turn (starboard)" in the tooltip; it disappears once the completion is revealed |
| 10 | Replay of N and N+1 | | no stutter at the begin; the turn animates at N+1's starting hex |
| 11 | A host carrying a boarding pod or a grappled ship | | the pod mirrors the host; the cost includes the attached ship's turn cost |
| 12 | A gravitic ship that begins while rolling, a gravitic ship that begins while pivoting, a rolled ship, and a ship moving backwards | | on N+1 the completion asks for the thrusters doing each role *now* (for example starboard instead of port after the roll completes); the server's check (f) accepts it |
| 13 | A simultaneous-movement game | | automatic cancellation works for each active ship |
| 14 | Tampering from the console (log-only mode) | remove the completion, add +1 speed on N+1, edit `value`, add a pivot after a begin | server log lines appear; the movement is unchanged |
| 15 | Panel regression (Stage 0) | every manoeuvre: turn, pivot, roll, emergency roll, slip, acceleration and deceleration, ship jink, Contraction, Half-Phase, turn into pivot | behaviour is identical on desktop and at phone width; a long press unassigns |
| 16 | Engine Shorted on N+1 | | cancellation with the `engineShorted` reason; the banner says ENGINE SHORTED; no false server log line |
| 17 | Edit the D4 list on both sides to add `'slipleft', 'slipright'` | begin, then look for slips | the slip arrows disappear after the begin; a hand-made slip after a begin is logged by the server; revert the edit |
| 18 | Hover labels (Stage 0) | hover every movement icon, on desktop and at phone width | the new tooltip look; "Stop Pivoting" and "Stop Rolling" still switch; the four new icons show their own labels |

## 10. Files touched

| File | Stage | Change |
|---|---|---|
| `source/server/handlers/movement.php` | 1 | `validateExtendedTurn`, the blocked list and helpers; `rebuildStraightTail` extraction |
| `source/server/Phase/MovementGamePhase.php` | 1 | one call, in the right order |
| `tests/replay/replayHarness.php` | 1 | report line, written only when relevant |
| `source/public/client/movement.js` | 2 | §3 |
| `source/public/client/UI/reactJs/shipThrust/ShipThrust.js` (and possibly a sibling file for the panel parts) | 0, 3 | rewrite |
| `source/public/client/UI/moveTooltip.js` | 0 | inline styles replaced by a class |
| `source/public/styles/tactical.css` | 0, 4 | the hover-label class; two icon filter classes |
| `source/public/client/UI/shipMovement.js` | 4 | four icons |
| `source/public/client/ShipMovementCallbacks.js` | 4 | two callbacks |
| `source/public/game.php` | 4 | four divs |
| `source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js` | 4 | automatic cancellation |
| `source/public/client/gamedata.js` | 4 | commit-dialog list |
| `source/public/client/UI/ShipTooltip.js` | 5 | green note |
| `source/public/client/UI/reactJs/shipWindow/ShipWindow.js` | 5 | two banners |
| `source/public/docs/faq.html`, `source/public/docs/starter-guide.html` | 5 | rule text |

No database migration, no new PHP class (so no autoload regeneration), and no static ship files.

## 11. Problems found nearby: reported, not fixed here
- **`Movement::hasTurned` has a typo, `"turneleft"`** ([movement.php:635](source/server/handlers/movement.php#L635)). As a result, an OSAT that turned *left* never takes the OSAT turned-this-turn fire penalty at [weapon.php:1842](source/server/model/weapons/weapon.php#L1842).
- **`validateThrustPayment` trusts the `forced` flag from the POST.** Forced rows skip validation ([movement.php:121](source/server/handlers/movement.php#L121)), and `forced` comes straight from the client ([Manager.php:2265](source/server/controller/Manager.php#L2265)). A tampered client could mark an underpaid turn as forced and it would be saved unchecked. The comment calls forced rows "server-generated", but forced pivots are generated by the client.

## 12. Rejected alternatives
- **Make the completion a new row type.** Every turn-delay, validation and replay site, client and server, would have to learn it (§1.3).
- **Make the begin a `turnleft`/`turnright` row with a marker in `value`.** The turn delay would start at the begin, and `validateThrustPayment` would reject it as underpaid.
- **Store the amount owed by physical thruster slot** (this plan's first draft). It names the wrong thrusters once a gravitic ship's roll or pivot completes between the two turns (D10, T10).
- **Have the server write a preturn marker at N+1 carrying the remainder** (the `isPivoting` pattern). It adds a generation hook, a masking whitelist entry and a second source of truth for a row already loaded at N+1. Keep it as the fallback if the T−1 window ever shrinks.
- **Recompute the remainder at N+1** from the begin row's `assignedThrust`. The answer drifts with criticals (T2).
- **Have the server cancel automatically at the start of the turn.** It cannot know engine thrust then, because power and boost are set in Initial Orders, so it could apply only half the rule.
- **A pop-up when a turn is cancelled** (D5). Rejected as more annoying than useful; the ship window banner carries the note instead.
- **Add a new database column or table.** Not needed: `type` (varchar 45) and `value` (varchar 100) carry everything.
