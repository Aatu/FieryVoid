# Log Panel Redesign — game.php `#logcontainer`

Roadmap item: visual unification, **surface 3 follow-up**. VISUAL_UNIFICATION_PLAN.md Stage 4
converged this panel's *colours* onto the tokens but deliberately left its **geometry, type and
information design** alone. This plan is that second half, plus the functional work the user asked
for on 2026-08-31.

Reference for the target look: `styles/gamesPanel.css` + GAMES_PAGE_REDESIGN_PLAN.md — the
"datasheet grammar": square 1px internals, Orbitron uppercase micro-labels, mono tabular numerals,
a 3px state rail on every row, controls that all read as one family.

**Out of scope (already refreshed):** GAME CHAT and CHAT — `styles/chat.css` `.fv-chat`.
That file is also the *precedent* to follow: a legacy container contract left intact, with the
new look hung off a new class alongside.

---

## 0. What is actually there today

| Piece | Markup | Behaviour | Styles |
|---|---|---|---|
| Panel + tab strip | `game.php:795-905` | `client/UI/botPanel.js` | `tactical.css:821-965`, `1142-1190` |
| Combat log (live + print) | `combatLog.php` | `client/combatLog.js` | `tactical.css:967-1112`, `1382-1440` |
| Fleet list (INFO tab) | `game.php:827,893` | `client/UI/fleetList.js` | `tactical.css:1257-1360` |
| Declarations | `declarations.php` | `client/declarations.js` | `tactical.css:1114-1140` |
| Save Fleet | `game.php:844` | `client/savedFleets.js` | `tactical.css:1199-1242` |

### The five structural defects

1. **The tab strip is hand-positioned.** Six tabs at hard-coded `left: 0 / 104 / 208 / 312 / 416 /
   520px` (`tactical.css:934-965`) plus `#expandBotPanel` pinned right, and *both* mobile media
   queries (`1815-1858`, `1967-2010`) restate the same six positions as percentages. Adding a
   seventh tab is a three-place edit, and the comment at `:959` records that a tab with no `left`
   silently stacks on top of `#logTab`. There is also a live collision: `#settingsTab` (416px,
   commented out in the markup) and `#declarationsTab` (416px) claim the same slot.

2. **`#logcontainer` uses element `opacity: 0.85`** (`tactical.css:825`). That fades the *text* as
   well as the panel, so every log line, fleet row and declaration renders at 85% strength. This is
   exactly the alpha-compounding trap VISUAL_UNIFICATION_PLAN.md documents and `--fv-overlay-soft`
   already solves elsewhere. Fix: move the translucency into the fill.
   ⚠️ Removing `opacity` normally destroys a stacking context — here it does **not**, because
   `#logcontainer` is `position: fixed` with `z-index: 10`, which creates one on its own. The
   9999/10000/10001 z-indexes inside stay contained.

3. **`#combatLogButtons` overlaps the print** — the reported bug. It is
   `position: absolute; top: 0; right: 0` *inside* the scrolling `#combatLogContainer`
   (`tactical.css:1033-1056`), so (a) `#LogActual` flows from the container's top and runs
   underneath it, and (b) the bar scrolls out of reach as soon as the log is longer than the panel.

4. **The live log and the printed log fight over one box.** `#log` is `position: absolute`,
   `overflow-y: visible` (`tactical.css:1382-1394`) and holds the replay stream as direct children;
   `#combatLogContainer` is a second absolutely-positioned, `overflow-y: scroll` layer on top of it.
   Consequences: `$("#log").scrollTop(...)` in `logDestroyedShip` and `logFireOrders` is a **no-op**
   (an `overflow: visible` box cannot scroll), and live entries overflow the panel's 150px instead
   of scrolling inside it. The `onTurnStart` comment block (`combatLog.js:10-26`) is a cleanup hack
   made necessary purely by this arrangement.

5. **`logFireOrders` emits the damage `<ul>` as a SIBLING of its `.logentry` div**
   (`combatLog.js:270-273` closes the div before the list). That is what forces the two-selector
   cleanup in `onTurnStart`, and it is why an entry cannot be collapsed — there is no element that
   contains an entry *and* its damage.

### Smaller findings worth folding in

- ~~`declarations.doResetEW` is dead code.~~ **DELETED 2026-08-31.** Not merely uncalled: its one
  statement was `ew.resetEW(shipID)`, and **`ew.js` has no `resetEW`** — it has `removeEW`, and
  `resetEW` appears nowhere in the tree outside this call. So the function could only ever have
  thrown a `TypeError`, and nothing referenced it. Six lines removed from `declarations.js`.
- `callGameDescriptionActual` (`declarations.js:427`) writes into `#declarationsActual` but is not a
  *mode*, so the chips keep claiming "Own EW by Source" while the panel shows the briefing.
- Printed log rendering is `targetDiv.innerHTML += html` per group (`combatLog.js:441-443`), which
  reparses the whole container once per fire group — O(n^2) on a big turn.
- `#expandBotPanel`'s label is the literal string `Click!` (`game.php:817`).
- `.fleetlist` columns are floats at 30/30/10/10/15% (`tactical.css:1315-1359`), so a long ship name
  wraps and drags the whole row out of alignment.

---

## 1. Design language

Everything below comes from `styles/tokens.css`. **No new `:root` block** — that file stays the
only one (VISUAL_UNIFICATION_PLAN.md). Two tokens are added there:

```css
--fv-well-soft: rgba(4, 22, 28, 0.86);   /* #logcontainer's fill, replacing element opacity */
--fv-log-rail: 3px;                      /* the state/allegiance rail width, shared by 4 surfaces */
```

| Role | Value | Where |
|---|---|---|
| Panel fill | `--fv-well-soft` | `#logcontainer` |
| Row fill / hover | `--fv-card` / `--fv-card-hover` | fleet rows, log entries, declaration cards |
| Hairline | `--fv-line-scs` (chrome), `--fv-line-soft` (internal) | everywhere |
| Micro-label | `--fv-display` 9.5px/700, 1.2px tracking, uppercase | tab labels, column heads, head bars, control chips — **chrome only** |
| Data | `--fv-mono`, `font-variant-numeric: tabular-nums` | CP, initiative, to-hit, damage, turn number |
| Body | Arial (inherited) 12px | log prose, chat, descriptions |
| Corners | `--fv-radius-chrome` (0) everywhere; controls keep 2px | matches `.fv-btn` |
| State rail | 3px left border | `.fv-card` grammar, reused on every row type |

**One new stylesheet: `styles/logPanel.css`,** linked in `game.php` after `tactical.css`. The
log-panel blocks listed in §0 move out of `tactical.css` in the same commit — no rule lives in two
files. Rationale: it mirrors `gamesPanel.css` (page-scoped, one surface, reviewable as a unit) and
takes ~450 lines out of a 2,640-line sheet. `tactical.css` is linked by `game.php` only, so nothing
else can be affected.

---

## 2. Stages

### Stage 1 — Shell: tab strip, panel fill, sizing
*`game.php`, `tactical.css` -> `logPanel.css`, `botPanel.js`*

- `#logUI` becomes `display: flex; gap: 2px`, tabs `position: static`. **Deletes** the six `left:`
  rules and both mobile blocks' restatements of them (~55 lines). New tabs then cost nothing.
- Tab type -> Orbitron micro-caps. Selected tab: panel fill + a 2px `--fv-accent` top rail, replacing
  the `top: -25px` 1px-lift hack. Unselected recedes on `#020E12` as now.
- `.newMessage` amber treatment **kept as-is** — it was a deliberate fix for players missing unread
  chat; only its type scale is restated.
- `opacity: 0.85` -> `background: var(--fv-well-soft)`. See §0.2 for why this is safe.
- `#expandBotPanel` -> a chevron with `title="Expand log panel"` and `aria-expanded`, replacing
  `Click!`.
- **Drag-resize grip** on the panel's top edge, height persisted to `localStorage`, double-click
  toggles compact/tall. Reuses the ship window's grip pattern (SHIPWINDOW_REDESIGN_PLAN.md) —
  ⚠️ read `arch_scaled_window_coordinate_spaces` first: the log panel is **not** scaled, so unlike
  the ship window nothing here divides by a scale factor.
- Each panel gets a `.fv-log-head` bar in the `.fv-panel-head` grammar: tab name left, contextual
  readout right (`TURN 7 · FIRING`, `4 FLEETS · 12 UNITS`, ...).
  ⭐ **Suppressed at compact height.** 25px is 17% of a 150px panel, and the tab directly above it
  already names the panel — so the head bar only appears once the panel is expanded past a
  threshold. Its readout moves to the right edge of the control bar while it is hidden.

**Two geometry rules this stage must get right** (both were defects in the first mockup):

- **The tab strip must be a flow sibling of the panel, not an overlay.** Today it is
  `position: absolute; top: -26px` against a panel whose tabs are ~27px tall, so the strip settles
  1–2px *inside* the panel and clips whatever is at the top of it. Put the strip and the panel in a
  flex column and pull the strip down with `margin-bottom: -1px`, so the selected tab merges with
  the panel body and nothing depends on the tab's rendered height.
- **Every control in a segmented group needs one shared `line-height`.** Chips and readouts sized
  only by padding come out at different heights and read as overlapping. Give the turn stepper
  collapsed 1px borders (`margin-left: -1px`) and a single line-height across all three cells.

### Stage 2 — Combat Log: fix the overlap, then restructure the box
*`combatLog.php`, `logPanel.css`, `combatLog.js`*

**2a — the reported bug (ship this alone if you want it early).**
`#combatLogContainer` becomes a flex column; the control bar becomes `position: sticky; top: 0`
with an opaque `--fv-well` fill and a bottom hairline; `#LogActual` becomes the scrolling body.
Content can no longer run underneath the bar, and the bar can no longer scroll away.

**2b — controls. ⭐ ONE ROW IS A HARD CONSTRAINT** (user, 2026-08-31). At the default 150px the
panel has ~120px of body left once the control bar is drawn, so a bar that wraps to a second row
costs a fifth of the log and a summary strip costs another sixth. The budget, left to right:

| Control | Form | ~width |
|---|---|---|
| Turn stepper | `◀ TURN 7 ▶` + `LIVE`, borders collapsed into one control | 152px |
| Sort | a **`<select>`**, not chips — `Resolution` / `Attacker` / `Target` | 118px |
| Whose fire | segmented `All` · `Mine` · `Enemy`, shared borders | 118px |
| Hits | one toggle chip | 46px |
| Find | text box, right-aligned after a flex spacer | 104px |

- **The group labels are gone** (`SORT`, `SHOW`) — the controls name themselves, and each label was
  ~40px of a 800px row.
- **Sort by DAMAGE is CUT** (user). It was the only sort needing the damage index, so `sortGroups`
  no longer needs one — see 2c.
- **No summary strip.** Cut with the turn band; the turn number lives in the stepper, which is
  where a player looks for it anyway.

**2c — sorting, without touching the replay path.**
`groupByShipAndWeapon` (`combatLog.js:665`) stays the canonical **resolution** order — it is shared
with the replay animation and must not change. A new pure `combatLog.sortGroups(groups, mode)` is
applied **only in `showLog`**. With DAMAGE cut it reads nothing but the shooter and target names,
so it needs no damage index and no second sweep of the fire orders.
Filters are a pre-render `.filter()` on the group array; `gamedata.isMyShip(shooter)` is safe here
because `logFireOrders` resolves the shooter through `gamedata.getShip` even on the printed path.
A filtered-out count is always shown, so a filter never looks like an empty turn.

**2d — render once.** `logFireOrders(..., printedLog=true)` returns its HTML instead of doing
`innerHTML +=`; `showLog` joins and assigns once. Removes the O(n^2) reparse.

**2e — the entry structure.** Move the damage `<ul>` **inside** the `.logentry` div. This retires
the two-selector cleanup in `onTurnStart` and makes collapse possible.
⚠️ **Shared with the live replay path** — `LogAnimation` holds the return value of `logFireOrders`
and passes it to `removeFireOrders`, which currently removes only the div and leaves the sibling
`<ul>`. Nesting fixes that too, but this is the one change in the plan that must be verified with
the **replay regression harness** (`project_replay_harness`) before it is committed.

**2f — readability.** Per-entry 3px allegiance rail from `gamedata.getShipLogColorCss` (already
computed at `combatLog.js:170`); damage lists collapsed by default above N entries; left/right arrow
keys step turns while the panel has focus.

⭐⭐ **THE LOG'S TEXT COLOURS DO NOT CHANGE** (user, 2026-08-31). Everything the live log paints
today is kept verbatim; the refit adds a rail and takes away the element opacity, and that is all:

| Element | Colour | Where it comes from today |
|---|---|---|
| Log prose | `#7ba2ea` | `#logcontainer`'s own `color` — the documented "considered choice" |
| Every ship name | `#deebff` | `#log .shiplink` |
| **The target only** | its team colour | inline from `getShipLogColorCss(target)` |
| `FIRE:` header | shooter's team colour | inline from `getShipLogColorCss(ship)` |
| Damage | `#c54120` | `#log .damage` |
| Criticals | `rgba(255,166,0,.781)` | `#log .critical` |
| Shield absorption | `#6fb7ff` | `.shieldabsorb` |

**A pastel tint on the shooter's name was tried and PULLED.** Softened team tints on ship names
fight the full-chroma damage red and critical amber in the damage list immediately beneath, and
those are the colours that have to win — they are the reason a player is reading the entry. The one
name that carries a colour is the target, because *who was shot at* is the fact the eye is looking
for. Do not reintroduce muted name tints here; the `--fv-*-mid` tier exists for dense lists like
the hex picker, and the combat log is deliberately not one of them.

**`FIRE:` is CONTENT, not a chrome micro-label.** It is the first word of the sentence, so it is
sized against the 12px body it sits in (Orbitron 11px ≈ 12px Arial optically), not against the 9.5px
tab labels. The Orbitron micro-cap scale in §1 applies to chrome only — tabs, head bars, column
heads, control labels.
~~Sticky `Turn N` band and a turn summary strip.~~ **CUT** (user, 2026-08-31) — a 22px band plus a
stats line is a sixth of a compact panel for information the stepper already carries.

### Stage 3 — Fleet List
*`fleetList.js`, `logPanel.css`*

- **Grid, not floats.** `grid-template-columns: minmax(0,1.6fr) minmax(0,1.4fr) 62px 74px 96px`.
  Names ellipsis instead of wrapping; CP and initiative in mono tabular so the columns compare.
- Column head in the `.fv-col-head` grammar; fleet header becomes a card head with a 3px team rail
  (`gamedata.getFleetHeaderColorRGB` already supplies the colour, `fleetList.js:284`), the CP total
  as a mono readout, and `[Orders committed]` / `[Waiting for ...]` / `[Deploys on Turn N]` as
  `.fv-tag` chips rather than inline-styled spans. Header sticks while its own list scrolls.
- **Hover highlight (requested).** Ship rows get a new `unitrow` class at build time
  (`fleetList.js:419`); the mine-group (`:477`) and reinforcement (`:512`) builders deliberately do
  **not**.
  ⭐ **`unitrow` means "there is something here worth opening", and `setRowState` takes it away for
  two of the four out-of-play states** (user decisions, 2026-08-31). The distinction is NOT
  on-the-board vs off it — it is **gone from the battle** vs **yours and still coming back**:

  | Row | Interactive? | Why |
  |---|---|---|
  | In play | **yes** | normal |
  | **Docked** | **yes** | Hangar Ops Stage 9.1 — the window is the ONLY route to a bay's contents |
  | **Hyperspace**, own/team | **yes** | you bought them; the window is the only way to look at them before they arrive |
  | Jumped | no | gone from the battle |
  | Destroyed | no | gone; there is no value in inspecting a wreck |
  | Mine group | no | not a single unit — it is a bulk row |
  | Reinforcement *summary* | no | the ENEMY's aggregate row; there is no ship object to open |

  So `setRowState` strips `unitrow` and `.clickable` for **`destroyed` and `jumped` only**. It is
  still the right home for it — it already runs for exactly these states and already strips the
  sibling state classes — and it replaces the two scattered `removeClass("clickable")` lines at
  `:813` and `:844`.

  **Click behaviour follows board presence, not interactivity:** an on-board unit scrolls on left
  click and opens its window on right click; a unit that is off the board but yours (docked,
  hyperspace) opens its window on **either**, because scroll-to-ship has nothing to do there. That
  is exactly what Stage 9.1 already does for docked flights — this only extends the same rule to
  hyperspace rows.
- **Right-click / long-press -> ship window (requested).**
  `contextmenu` on `.unitrow` -> `preventDefault()` -> resolve ship -> guard (below) ->
  `webglScene.customEvent('OpenShipWindowFor', { ship })`. That event is already handled
  (`PhaseStrategy.js:37`). `OpenShipWindowFor` is preferred over `onShipRightClicked` because the
  latter also *selects* the ship, which a fleet-list row should not do.

  ⭐⭐ **THE GUARD IS NOT A BARE `shouldBeHidden`, AND THIS IS THE WHOLE TRICK.**

  ```js
  if (!gamedata.isMyorMyTeamShip(ship) && shipManager.shouldBeHidden(ship)) return;
  ```

  `shouldBeHidden` (`ships.js:1141`) is a **board-presence** test — "do not draw this on the map" —
  not an information test. It returns **true for a unit of your own** that is merely not deployed
  yet (`getTurnDeployed(ship) > gamedata.turn`, `:1150`, with one narrow Deployment-phase exception)
  and, via the destroyed check at `:1142`, for every docked flight. A bare guard therefore blocks
  precisely the two cases the user asked for. A window reveals **no board position**, which is what
  that guard exists to protect — the same reasoning already written into `doScrollToShip`'s
  docked-flight branch (`fleetList.js:668-674`).

  **Why `isMyorMyTeamShip` is the exactly-right bypass, and not a judgement call:** it is the
  byte-for-byte client twin of the server's own mask.
  `TacGamedata::hideHyperspaceReinforcements` keeps the real ship rows for
  `$ship->userid == $this->forPlayer || $ship->team == $playerTeam` and **deletes the ships from the
  payload** for everyone else (`TacGamedata.php:1274`). `gamedata.isMyorMyTeamShip`
  (`gamedata.js:236`) tests `userid === thisplayer || team === getPlayerTeam()` — the same two
  clauses. So: *if a hyperspace ship row exists at all, this viewer is already entitled to it*, and
  the guard is only there for the case masking does not cover — an **enemy** ship that is on the
  board but stealthed and undetected, where the row exists and the position must stay secret.
  That case still returns early, so nothing leaks.

  This one line also retires the ordering hack: the docked branch in `doScrollToShip` sits above the
  guard *because* `shouldBeHidden` is the wrong test there. Give the window path the right test and
  the branch survives only to choose window-over-scroll, which is what it should have been doing.

  ⚠️ Slightly over-strict in one corner: in a **finished** game the server discloses everything
  (`$currentGameFinished` returns early from the mask), so an enemy hyperspace row can exist
  post-mortem — and `shouldBeHidden` will still refuse it. Safe direction, and post-mortem
  disclosure is the server's business, not the fleet list's. Leave it.
  ⚠️ **Touch.** Per `arch_hex_stack_picker`: an Android long press fires `contextmenu` **by itself**,
  so that path needs no timer — but iOS Safari does not, so a `pointerdown` long-press timer
  (~450ms, cancelled on movement, `navigator.vibrate(30)`) is the fallback, with a `longPressFired`
  flag so the two routes cannot both fire, and suppression of the click that would otherwise also
  scroll-to-ship. `-webkit-touch-callout: none` on the row stops iOS's own selection callout.
  The hex picker chose *not* to open a window on long press because a mis-timed tap there could
  commit a selection; a fleet-list row has no such cost, so the gesture is safe here.
  Discoverability: a small info affordance at the row's right edge on hover, which doubles as an
  explicit tap target on touch — the same reasoning as the picker's details button.
- **Its own one-row control bar**, same grammar and same budget as the combat log's:
  - **Team `<select>`** — `All teams` / `Team N`, built from the same `uniqueTeams` sweep
    `displayFleetLists` already does (`fleetList.js:233-244`). In a 2-player game it has two real
    options and is arguably noise, so hide it below three teams.
  - **`On map only` toggle** — hides anything not currently on the board: **destroyed, jumped,
    docked, and reinforcements still in hyperspace.** ⭐ It reads the state `updateFleetList`
    already paints (`setRowState`'s four classes) rather than re-deriving it, so the two can
    never disagree.
    ⭐⭐ **It must hide the FLEET BLOCK too, not just rows.** A slot whose whole fleet is still in
    hyperspace (a `depavailable > gamedata.turn` reinforcement slot) would otherwise leave a header
    and a column row with nothing under them. Hide a block when its visible unit-row count is 0.
    ⚠️ Mines ARE on the map — the bulk mine row stays.
  - The head bar's readout recounts against the filter, so it always describes what is on screen.
- **Destroyed rows are NOT italic** (user, 2026-08-31). The red and the "Destroyed" label in the Ini
  column already carry it; italicising a whole row of proper nouns only made them harder to read.
  Today the italic comes from `.fleetlistentry .destroyed`-adjacent rules — drop `font-style` there
  and keep the colour. Out-of-play rows also lose the pointer cursor, since they no longer act.
- ~~**Health bar** under each row for `currValue / baseValue`.~~ **CUT** (user, 2026-08-31) — too
  much visual clutter in a list this dense, and the `curr/base` figure already in the value column
  says the same thing.
- Sortable column heads (name / class / ini / value), collapsible fleets, and scroll-into-view +
  flash on the row of the currently selected ship.

### Stage 4 — Declarations
*`declarations.php`, `declarations.js`, `logPanel.css`*

Same information, restructured. No change to what is read out of `gamedata`.

- Controls -> three chip groups on one line (`SIDE Own|Enemy`, `SHOW EW|Fire`, `BY Source|Target`),
  in the same sticky bar as the combat log, plus **BRIEFING** as a fourth *mode* rather than a
  button — fixing the §0 defect where the chips lie about what is on screen.
- Output -> per-unit cards in the `.fv-card` grammar instead of a `<big><b>`/`<br>` dump: unit name
  in Orbitron caps, class in dim mono, then a mini-table (`what` / `value` / `at whom`) with values
  right-aligned in mono so a column of EW can be compared at a glance.
  The relation word (`at` / `by`) is its own dim span with a real gap after it — run together with
  the name, `OEW 3 at Vorchan Talon` reads as one token rather than three fields.
- **Per-unit totals** — EW emitted / OEW received, currently a mental sum.
- **Fire mode**: `4x Heavy Laser -> Vorchan  45-60%` with the to-hit as a small meter, and a
  per-target roll-up so "everything shooting at my Vorchan" is one line rather than a scan.
- ~~Wire `doResetEW` to a per-unit **Reset EW** control.~~ **N/A — the function is DELETED**
  (2026-08-31, done ahead of this stage). It called `ew.resetEW()`, which has never existed in
  `ew.js`, so it could only ever have thrown. Nothing to build here.

### Stage 5 — QA
- `fvbuild.ps1 -Check` (validator + replay harness). **Stage 2e is the one that needs the harness.**
- Desktop + phone portrait + phone landscape, each of the six tabs.
- Observer view and a 3+-team game — every allegiance surface here is a **two-arm gate**
  (`arch_team_colour_logic`, `arch_info_bleed_masking`): CSS classes for 2-team participants, inline
  JS colour for observers. Any rail colour added in Stages 2-4 must be set on both arms in the
  same commit.
- A masked view: a WAITING player is served no ship data (`arch_gamedata_polling_cache`), so every
  new panel must render with an empty `gamedata.ships`.

---

## 3. Open decisions

1. **New `styles/logPanel.css`, or keep it in `tactical.css`?** Plan assumes the new file.
2. **Tab strip overflow.** Six tabs already fill 624px. Do new tabs wrap to a second row, or scroll
   horizontally? Plan assumes wrap on narrow, since the panel is anchored bottom-left.
3. ~~**Does the fleet-list hover/right-click extend to destroyed / jumped / docked rows?**~~
   **ANSWERED (user, 2026-08-31), and the line is not where I first drew it.** The split is *gone
   from the battle* vs *yours and still coming back*:
   **Destroyed and jumped → inert.** No highlight, no window; `setRowState` strips `unitrow`.
   **Docked → keeps it** (Hangar Ops Stage 9.1 stands — the window is the only route to a bay's
   contents). **Hyperspace reinforcements → keeps it**, for the owner and their team, who are the
   only viewers the server gives real rows to. Guarded by `isMyorMyTeamShip || !shouldBeHidden`,
   which is the client twin of the server's own mask — see Stage 3.
4. **Default combat-log sort** — RESOLUTION (what it does today) or ATTACKER (easier to read)?
   Plan keeps RESOLUTION, with the choice remembered in `localStorage`.
5. ~~**Stage 4's per-unit Reset EW** — wire it up, or delete the dead function?~~
   **ANSWERED AND DONE (2026-08-31): DELETED.** It was not merely uncalled — it called
   `ew.resetEW()`, which **does not exist** in `ew.js` (that file has `removeEW`, never `resetEW`),
   so any invocation would have thrown a `TypeError`. Removed from `declarations.js`; no Stage 4
   Reset EW control.
6. **Stage 2e** touches the live replay path. Ship it with Stage 2, or hold it until the rest is
   live-stable?
