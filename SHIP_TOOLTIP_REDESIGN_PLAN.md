# Ship Tooltip → Ship Card, and the Hex Picker working with it

**Status: Stage 1 BUILT 2026-11-09, uncommitted, not play-tested (§12). Stages 2-8 not started.**
D1 is ruled (Option A) with twelve refinements in **§11, which override §4-§6 wherever they differ**,
and a second round in **§11.1, which overrides §11**.
D2/D3 were taken as recommended when the user asked for Stage 1. D5-D10 remain open.
**Mockups (design canvas):** https://claude.ai/artifact/PcQ7rYkmV9HovyNixscazC. It has before/after,
the card in five contexts, three action styles (D1), an interactive picker + card prototype (D2: open
Tweaks to switch what a map click does), a Firing storyboard and two linked phone screens.

Client-only: legacy jQuery + CSS. No PHP, no DB, no autoload, no static ship files, no React.

---

## 0. Summary

1. **The tooltip becomes a datasheet "ship card".** It uses the frame the hex picker and the system
   card already wear: opaque, square, `--fv-line-scs`, a 3px allegiance rail, Consolas numbers and the
   system card's three type sizes. Hover gives a compact **peek**. A click gives the **pinned card**
   with actions.
2. **The content is grouped and ordered on purpose.** The order is identity → hull → status chips →
   four key cells → "from ‹selected unit›" → targeting / incoming → actions → Ship details. Today's
   list is built bottom-up, so it shows on screen in the reverse of code order (§2.1).
3. **Actions get names and values.** EW add/remove icon pairs become `− value +` steppers, with the
   EW pool shown beside them. Right-click max and touch long-press are kept. Every other action gets
   a caption. Ship details moves to a footer.
4. **The picker stops closing when you use it.** In Initial Orders, Pre-Firing and Firing,
   selecting one of your own units from the picker leaves it open. A map click that lands on a unit
   leaves it open too. Rows show SELECTED, your OEW on each enemy and where each of your units is
   firing.
5. **The card docks beside the picker on desktop** (list | detail, like a submenu). On touch it
   drills down inside the picker's sheet.
6. **Staged so the picker change ships first.** It does not depend on the card rebuild (§8, Stage 1).

---

## 1. The request

> "design and plan implementation of an improved ShipTooltip which presents the information better
> for the player and is more user-friendly/has greater utility … consider integrating better with
> the shipPicker menu … instead of closing shipPicker when a ship in its list is selected during
> Initial Orders and Firing phases, would it be better to leave it open and allow the player to
> assign EW/weapon fire against multiple ships in the picker without having to constantly re-click
> it open."

---

## 2. Today (audit)

### 2.1 The tooltip — `client/UI/ShipTooltip.js`, `styles/shipTooltip.css`, `styles/tactical.css`

- **One jQuery surface for hover and click.** The markup is at `ShipTooltip.js` L11-19 and the
  constructor at L27-76. A hover tooltip (`menu === null`) destroys itself on any mouse event inside
  it (L43-47). A click tooltip (`menu` present) gets a ✕ (L55-67).
- **The screen order is the reverse of the code order.** `addEntryElement` does
  `insertAfter('.namecontainer')` (L112-116), so each new line lands on top. That is why Distance
  and Defence lead, Ini Order comes last, and the status line sits at the very bottom above the
  buttons.
- **The same ~25 conditional lines show for every viewer in every phase** (`createForSingleShip`,
  L232-682). Pivot, roll, turn and accel costs show on an enemy in the Firing phase. The own ship's
  EW pool shows nowhere, even in Initial Orders.
- **Parentheses mean something different on each line.** `35(80)` is the base profile.
  `(acc cost: 4)` is the acceleration cost. `Turn Cost: 1 (1)` gives the base rate. `Ini Order: 1
  (total -20): base 0; mod -40` packs three numbers into one sentence.
- **Status is one run-on sentence.** It is bold italic, joined with `; `, and uses seven ad-hoc
  inline colours (`limegreen`, `orange`, `cyan`, `red`, `#e1b000`, `#b36bff`, `#d250ff`), at
  L303-538.
- **The box** is `tactical.css` L782 `.shipNameContainer`: 65% black (`--fv-overlay-soft`), 7px
  radius, centred Arial 12px, no frame. `positionElement` (L757-775) centres it under the unit and
  **never clamps it to the viewport**.
- **Buttons** come from `shipTooltipMenu.js` `renderTo` (L27-75). They are 40×40 PNG icons whose
  meaning sits in a hover-only `.info` line, and touch has no hover. Add/remove are separate icons,
  and the value they change is printed somewhere else. For OEW against a flight the value is not
  printed at all (the line at L630 skips flights, but `addOEW` has no flight gate).
- **The TARGETING rows** are written by `weaponManager.targetingShipTooltip` (weaponManager.js
  L748-977, single caller: ShipTooltip) as `Name: (mode) - Approx: 62%`. The heading is pure red
  under a white rule (`shipTooltip.css` L39-44).
- **Already good, keep:** the INCOMING list (`ShipTooltipBallisticsMenu`: shooter groups, a hit
  chance column, click to intercept) and the stack hover grid (`createForMultipleShips`, L699-753).

### 2.2 The picker — `client/UI/SelectFromShips.js`, `PhaseStrategy.js`

- `activateShip` (L1076-1099): in Deployment it selects the unit and destroys the picker. In other
  phases it calls `onShipClicked` and destroys the picker **only if the click changed the
  selection**. So a targeting click (an enemy row) keeps it open, and **selecting one of your own
  units closes it**.
- **Any map click closes it.** `showSelectFromShips` (PhaseStrategy L1204-1208) pushes a one-shot
  `hideSelectFromShips` onto `onClickCallbacks`, which `onClickEvent` (L715-735) runs before it
  dispatches the click.
- `SELECT_FROM_SHIPS_PLAN.md` §2.8 deliberately excluded "pinning the picker open". **This plan
  revisits that ruling at the user's request.**
- What it costs the player:
  - **Firing, a stack of your own flights.** Click the hex, pick flight A (the picker closes), pick
    weapons, click the enemy, Target. Then click the hex again and pick flight B. That is one extra
    click and one re-read of the list per unit.
  - **Initial Orders, OEW from a ship outside the stack onto stacked enemies.** Row-to-row already
    works, because targeting keeps the picker open. But the card opens under the hex, not beside
    the row, and nothing in the list shows where your OEW already went.

---

## 3. Goals and non-goals

**Goals**
- G1 Read at a glance: identity → state → numbers → actions. Numbers sit in fixed cells, each with
  one meaning.
- G2 Fit the context: what the viewer's relation to the unit and the phase make relevant comes
  first. Nothing is lost (§4.4).
- G3 Relationship first: when a unit is selected, the card says range, facing and your OEW from
  that unit.
- G4 Actions say what they do and show the value they change. Touch-friendly.
- G5 One family with the hex picker and the system card.
- G6 Never off-screen. Never in the picker's way. Never swallows a click while it is only a hover.
- G7 The picker becomes a place you work in, not a hurdle you clear.

**Non-goals**
- No server or payload change. No new data. Every number is computed by today's helpers.
- No React port. The menus, `weaponManager` and `ShipTooltipBallisticsMenu` all render into this
  jQuery surface, and moving them would be a rewrite of the order plumbing (§7).
- No rules change. **Every menu condition predicate is left exactly as it is** (§10, trap 1).
- No Orbitron on game.php.

---

## 4. The ship card

### 4.1 Anatomy (top to bottom)

| Section | Contents | Source |
|---|---|---|
| **Head band** | Art 44px (`AssetManager.getSmartImagePath(ship.imagePath)`; a masked mine gets the generic glyph) · name 13px bold in the allegiance **bright** tier · sub line in micro caps: class when the name does not already carry it, then size, then faction · ✕ when pinned | `getNameStyle`/`getNameColor` (the existing two-arm team gate) |
| **Hull strip** | One bar per structure block present (FWD AFT PORT STBD PRI; six-section ships add P.FWD/P.AFT/S.FWD/S.AFT and wrap). Title attribute `left / max`. **Flights:** fighter pips (alive/lost) + `4 / 6`. None for terrain or a masked mine. | `damageManager.getDamage(ship, structure)` / `maxhealth`, as `ShipSection.js` L443 `getStructureLeft` does |
| **Status chips** | Every status line of today, one chip each, coloured by family (§4.3) | the same predicates as today (moved, not rewritten) |
| **Key cells** (4) | Depend on the context (§4.2). Label micro caps, value Consolas bold, optional sub line | today's computations |
| **From ‹selected›** | Range · facing (the section that faces the selected unit) · OEW / DIST / JAM / SOEW / SDEW values, as steppers when the menu offers them · `n of N free` | `mathlib.getDistanceBetweenShipsInHex`, `weaponManager.getShipHittingSide`, `ew.getOffensiveEW`, `ew.getEwLeftFor` |
| **Targeting** | When weapons are selected: weapon · mode · hit % column (breakdown hover kept). The boarding-attach line stays as a row. Primary button `Target N weapons`. | `weaponManager.targetingShipTooltip` (row markup changes, §7) |
| **Incoming** | Today's list, unchanged; only its head is restyled | `ShipTooltipBallisticsMenu` |
| **EW** (own unit) | Pool `9 of 9 free` · steppers for CCEW / BDEW / Detect stealth / Mine detection · `DEW = every unallocated point` · Clear all EW | the `ewButtons` pairs (§5) |
| **Movement** (own unit; first in the Movement phase) | Thrust, Turn ×rate, Delay ×rate, Pivot, Roll, Accel (flights: Thrust, Pivot, Combat pivot) | today's computations, incl. LCV surcharge, crew, Quartermaster |
| **Orders** | Captioned tiles for everything else on the menu | menus (§5) |
| **Footer** | Ship details (`openSCS`) · Ini breakdown `base b · mod m` in micro text | |

### 4.2 Content per context

| Context | Key cells | Then |
|---|---|---|
| Enemy, Pre-Firing / Firing | DEFENCE % `F · S` (the facing number bright) · DEW · SPEED · INI | From ‹sel› · Targeting · Incoming |
| Enemy, Initial Orders | the same, with DEW shown as `≤ N · hidden` (**D6**) | From ‹sel› with the OEW/DIST/JAM steppers |
| Own ship, Initial Orders | DEFENCE · DEW · SPEED · INI | EW section expanded · Movement · Orders |
| Own ship, Movement | **SPEED (delay) · THRUST left/full · TURN ×rate · DELAY ×rate** | Costs (Pivot, Roll, Accel) · Combat (Def, DEW, Ini) |
| Own ship, Pre-Firing / Firing | DEFENCE · DEW · SPEED · INI | Fire orders (count/targets) · Late-EW steppers when `ew.isLateEwWindowOpen` · Orders |
| Flight (any side) | DEFENCE · **ARMOR F·S·A** · SPEED · INI | fighter pips in the hull slot · Evasion and Mission chips · Thrust / Pivot / Combat pivot |
| Mine | Signature (own team) · OEW from ‹sel› (enemy) | masked: name `Mine`, no art, no class, no hull |
| Base | DEFENCE · DEW · INI | Rotation chip |
| Terrain / jump gate | identity only | jump-gate actions (unchanged conditions) |
| Hover **peek** | the same cells | From ‹sel› as one spec row · **no actions, no footer, `pointer-events: none`** |

### 4.3 Status chip catalogue

| Today's text (line in ShipTooltip.js) | Chip | Family / colour |
|---|---|---|
| OB temporarily lowered by N (257) | `OB −N` | caution |
| Initiative temporarily lowered by N (262) | `Ini −N` | caution |
| Uncontrolled (269) | `Uncontrolled` | danger |
| Energy Drained — cannot fire (278) | `Energy drained` | suppressed `#d250ff` (`EDF_PURPLE`) |
| Rotation towards port/starboard (292) | `Rotating port` | neutral |
| Evasion: −N to hit (306) | `Evasion −N` | accent |
| Pivoting / Has rolled / Rolling / Rolled / Half-Phased (307-323) | same words | caution |
| Extended turn text (329) | the same text | caution |
| Deploying / Undetected / Detected (Teams …) (337-418) | the same words | good / danger |
| Skin Dancing / Aborted / Failed (425-429) | the same words | good / caution / danger |
| Jumping to Hyperspace (437) | `Jumping` | jump `--fv-warn` |
| Being abducted x/y power-turns (446) | `Abducted x/y` | `#b36bff` |
| Hangar manoeuvre (458), Just Launched (501, 506), Hangar Operations (509), Arrival Scatter (518) | the same words | benign cyan |
| Attached to X [loc] (482) | the same words | good |
| Ship is being Boarded! (495) | `Being boarded` | caution |
| Command node line (525) | the same text | `--fv-warn` |
| Firing modifier: N (534) | `Firing −N` | caution |
| Navigator (545), mission line (557) | `Navigator`, the mission text | neutral, `missionLine.color` |
| `, Agile` (613) | `Agile` | neutral |

Chip colours become classes in `shipTooltip.css` (`.fv-chip--benign` and so on) and keep today's
hues, so nothing changes meaning. A value is promoted to a token only if a second surface needs it,
and then it goes in `tokens.css` (the only `:root`).

### 4.4 No-loss inventory (the acceptance contract)

Every item below exists today and must still be reachable afterwards. Status lines are covered
by §4.3.

| Today | New home |
|---|---|
| Name (masked mine → "Mine"), allegiance colour, team colour for observers / 3+ teams | head band |
| `Defence (F/S): eff(base) / eff(base)%`, incl. the scout-mission profile modifier | DEFENCE cell (eff), base in the sub line / title |
| `DEW: n CCEW: n` | DEW cell + EW section (own) |
| `Support DEW`, `Blanket DEW`, `Detect Stealth`, `Detect Mines` | EW section (own) / DEW sub `+N support` |
| `OEW: n` (from selected) · `DIST: n` (/3, /4 constrained) | From ‹sel› |
| `DISTANCE: n hexes` | From ‹sel› Range |
| `Speed: s (delay d) (acc cost a)` | SPEED cell (delay sub) + Accel in Movement |
| `Thrust: rem/full` | Movement (THRUST cell in the Movement phase) |
| `Turn Cost: c (rate); Turn Delay: d (rate)` (+ LCV surcharge, crew, Quartermaster) | Movement |
| `Pivot cost / Roll cost`, flight `Pivot / Combat pivot cost` | Movement |
| `Armor (F/S/A)` (flight) | ARMOR cell |
| `Ini Order: n (total t): base b; mod m`, incl. the Triad projection and the mission modifier | INI cell (order + total) + footer (base/mod) |
| Mine `Signature`, mine `OEW` | cells / From ‹sel› |
| TARGETING list incl. `-PORT-` side line and BOARDING attach line | Targeting (side → From ‹sel› Facing) |
| INCOMING list incl. click-to-intercept | Incoming |
| Every menu button with its conditions, right-click max and touch long-press | §5 |
| Stack hover grid | unchanged content, new frame |

### 4.5 Visual grammar

- **Frame.** Opaque body `--fv-overlay` (the picker's body, since the two dock together). Head band
  `--fv-card`. 1px `--fv-line-scs`. **Square (D7)**: this ends the 7px tooltip exception for this
  surface only; `common/Tooltip.js` keeps its corners. Shadow as on the system card. **No element
  opacity** (alpha-compounding trap).
- **Width: a fixed 300px.** This also retires the "hover line widens the tooltip" loop by
  construction (the CSS fix at `shipTooltip.css` L271 can go).
- **Type: the system UI face** (`theme.fonts.ui`; add a `--fv-ui` twin to `tokens.css` with a
  sync-contract comment) and `--fv-mono` for numbers. **Three sizes only: 10 micro / 12 body /
  13 name**, the system card's scale. Labels are uppercase with letter-spacing; no Orbitron on
  game.php.
- **Allegiance.** The rail takes the `-signal` tier and the name the `-bright` tier, through the
  existing gate (`usesTeamColor` → inline team colour for observers and 3+ teams). Both arms must
  move together.
- **Hull bars** use the ship window's colours. Fill length carries the information; the crit colour
  appears only where the ship window would use it (`ShipSection.js` L115). The mockup's amber
  threshold is an option, not a requirement (**D4**).

### 4.6 Placement

- **Peek** (hover): below the unit as today, but measured and **clamped** to the viewport.
  `pointer-events: none` replaces the self-destroy-on-mouse-event hack (L43-47) (**D8**).
- **Pinned** (click): the same anchor, clamped. The header is a drag handle, per instance and never
  remembered (the picker's rule). ✕ and Esc close it.
- **Docked** (a picker is open and the card came from one of its rows): beside the picker, on the
  side away from the hex, top level with the active row, clamped, with a caret on the row. The card
  computes this from the picker's rectangle on every reposition. **The picker never writes a
  position onto the card** (§10, trap 7).
- **Touch:** inside the picker's sheet (§6.4).

---

## 5. Actions

### 5.1 The menu model stays

Menus stay arrays of `{className, condition[], action, info, supportsMaxClick}`. `renderTo` still
filters by `condition`. `bindButton` (click, right-click max, 500ms long-press) is reused unchanged.
**No predicate is edited.**

### 5.2 Additive fields (all optional; a button without them renders as a captioned tile)

- `group` + `role`: `{group: 'OEW', role: 'add'|'remove'}` on the nine `ewButtons` pairs (OEW, DIST,
  JAM, SOEW, SDEW, CCEW, BDEW, Detect Stealth, Mine Detection). Remove All EW (outside `ewButtons`)
  becomes the EW section's "Clear all EW".
- `value(menu)`: the stepper's readout. `ew.getOffensiveEW(sel, target, type)` for targeted types;
  `ew.getEWByType(type, sel)` for self types.
- `label`: the caption (`Lucky feat`, `Launch`, `Hangars`, `Enter hangar`, `Mission`, `Scan`,
  `Shunt`, `War Leader`, `Speech`, `Anticipate`, `ELINT`, `Stand down`, `Command node`, `Swap ini`,
  `Signal gate`, `Arrival`, `Cancel signal`, `Jump out`, `Select`).
- `kind: 'primary'` on `targetWeapons` / `targetSuppWeapons`. The label is computed: `Target N
  weapons`.

### 5.3 Rendering

- Visible entries that share a `group` render as **one stepper row**: label, value, `−`/`+`. If only
  one half passed its conditions, the other renders **disabled** rather than missing, so the row
  keeps its shape. (Hiding unavailable options was a lobby-menu ruling; a disabled `−` at 0 is the
  standard stepper.)
- `primary` renders as the full-width button in the Targeting section.
- `openSCS` moves to the footer.
- Everything else renders as tiles: the existing PNG at 30px plus the caption. Static `info`
  strings become the `title`. **Dynamic** infos (`fighterMissionInfo`, `luckyInfo`,
  `jealousElintInfo`, …) keep the `.info` line under the tiles, because they report state.

### 5.4 D1, the action style (canvas row 2)

- **A — steppers + labelled tiles (recommended).** Values sit where they change. Captions work on
  touch. Tiles keep the art players know.
- **B — today's icons, captioned.** The smallest change. EW values sit in a strip below and the
  add/remove pairs stay two icons.
- **C — menu rows.** The clearest. The tallest card: a full Initial Orders menu would run 15+ rows.

---

## 6. The picker and the card together

### 6.1 Staying open (D2, D3)

- **P1 — keep open on select.** Selecting your own unit from the picker no longer closes it. Any
  map click still does. This alone does **not** fix the Firing loop, because targeting an enemy
  elsewhere is a map click.
- **P2 — stays until dismissed (recommended).** P1, plus a map click that **lands on a unit**
  leaves the picker open. A click on empty space, ✕, Esc, Commit or a phase change closes it.
  Clicking another stack replaces it.
- **P3 — a pin button.** P1 by default, plus a pin in the header that turns on P2.

**D3, the phases:** Initial Orders, Pre-Firing and Firing (recommended). Movement stays as today:
units leave the hex as they plot, so the list goes stale. Deployment stays as today: its actions
destroy the picker by design. Waiting and Replay stay as today.

**How P2 works:**
- `onClickEvent` already computes `icons` before it runs the callbacks. It passes
  `{icons: icons, payload: payload}` to each callback. Every existing callback is a `bind` with one
  bound argument and one formal parameter, so an extra argument is ignored. Check that again when
  building.
- The picker's callback returns `true` (stay registered) when `icons.length > 0` in a sticky phase,
  and otherwise hides the picker and returns `undefined` (one-shot, as today).
- `showSelectFromShips` first hides any open picker. Today it relies on the map click having done
  that already, so without this a sticky picker plus a second stack would leave two pickers up.
- `activateShip` in a sticky phase never destroys the picker. It calls `refresh()`.

### 6.2 Row state and badges

| Badge | On | Source | Bleed check |
|---|---|---|---|
| `SELECTED` + accent row fill | the row of `phaseStrategy.selectedShip` | — | own |
| `OEW n` | enemy rows, phases 1/5/3 | `ew.getOffensiveEW(selected, ship)` | your own allocation only |
| `⌖ ‹target›` / `⌖ 2 targets` | own rows | `weaponManager.getAllFireOrders(ship)` filtered to `turn == gamedata.turn` | your own orders only |

- Badges are part of the collapsed-run comparison key (the picker's "rendered content" rule).
- `SelectFromShips.prototype.refresh()` rebuilds the model and the list and keeps
  `foldedGroups`, `expandedRuns`, scroll position and placement.
- It is called from `PhaseStrategy.setSelectedShip`, `onShipEwChanged` (L1556), and
  `onSystemDataChanged` (fire orders change there). Always behind `if (this.selectFromShips)`.

### 6.3 Docking (desktop, D5)

- The picker passes `dock: {picker: this, row: rowElement}` in the payload it hands
  `onMouseOverShip` / `onShipClicked`. `showShipTooltip` forwards it to `ShipTooltip`.
- `ShipTooltip.reposition` with a dock does this: it reads `picker.element` and the row's rectangle,
  places itself on `picker.placement`'s far side with an 8px gap, aligns its top with the row
  (−8px), clamps, and puts the caret on the row.
- **Reposition order.** `onZoomCallbacks` / `onScrollCallbacks` (PhaseStrategy L16-17) run
  `repositionTooltip` before `repositionSelectFromShips`. A docked card would read a stale picker
  rectangle. Fix it by moving `repositionTooltip` after `repositionSelectFromShips` in both arrays.
- `placeTooltipClear` returns early for a docked card. With an undocked one it keeps today's
  collision nudge.
- Hover on another row while a pinned card is up: as today, `onMouseOverShip` refuses to replace a
  card that has a menu. The row still highlights the unit on the map.

### 6.4 Touch (D9)

On the sheet (`fv-hexpicker--sheet`), tapping a row swaps the list for that unit's card **inside the
sheet**. A `‹ N units in hex` back link returns to the list. `ShipTooltip` gets
`dock.container`: `show()` appends to it instead of `body` and skips positioning. Steppers are
48×44 and rows stay at 52px (the 44px coarse-pointer floor). The touch traps in §10 all apply.

---

## 7. Implementation notes

| File | Change |
|---|---|
| `client/UI/ShipTooltip.js` | Split `createForSingleShip` into a pure **model builder**: ship, selected, phase → `{identity, hull, chips, cells, rel, ew, movement}`. The computations are moved verbatim. Expose it as `ShipTooltip.buildModel` for node tests; no new `<script>` tag. Then a renderer. **Keep the public API:** constructor signature, `show`, `reposition`, `destroy`, `close`, `onClose`, `update`, `refreshTargeting`, `refreshButtons`, `isForAnyOf`, `element`, `ships`, `menu`, `ballisticsMenu`, `selectedShip`. **Keep the hook classes:** root `shipNameContainer`, `namecontainer` (+`--stack`), `fire`/`targeting`, `ballistics`/`incoming`, `buttons`, `menu`/`action-buttons`/`info`, `tt-close`. Add viewport clamp, drag, dock and container options. |
| `styles/shipTooltip.css` | New `.fv-shipcard` grammar; chips; steppers; tiles; head of TARGETING/INCOMING restyled (no pure red). INCOMING row rules untouched. |
| `styles/tactical.css` | Split the L782 box rule: `.shipNameContainer` gets the card frame. The static `#shipNameContainer` div in game.php L651 has no JS reader (it looks dead): leave it, and record that. |
| `client/weaponManager.js` | `targetingShipTooltip` rows become `<div class="tt-row"><span class="weapon">…</span><span class="firingMode">…</span><span class="posHitchange hit-chance-tooltip" data-tooltip="…"><span class="hitvalue">62%</span></span></div>`, with no "Approx:". Status words keep their classes. **One caller** (verified). |
| `client/UI/shipTooltipMenu.js` | `renderTo` grouping (§5.3); footer slot for `openSCS`. |
| `client/UI/shipTooltipInitialOrdersMenu.js`, `shipTooltipFireMenu.js`, `shipTooltipMovementMenu.js` | Add `group`/`role`/`value`/`label`/`kind` to the entries. **No condition changes.** |
| `client/UI/ShipTooltipBallisticsMenu.js` | Head text only. |
| `client/UI/SelectFromShips.js` | `activateShip` gate; `refresh()`; badges in `describe()` (+ run key); dock payload; sheet drill-down; `placeTooltipClear` skips docked cards. |
| `client/renderer/phaseStrategy/PhaseStrategy.js` | `onClickEvent` passes click info; `showSelectFromShips` hides the previous picker; sticky callback; `showShipTooltip` forwards `payload.dock`; reposition order; refresh hooks. |
| `styles/hexPicker.css` | Badge + SELECTED row styles; sheet drill-down. |
| `styles/tokens.css` | `--fv-ui` (twin of `theme.fonts.ui`). Chip tokens only if a second surface needs them. |

Legacy JS only: the legacy watcher rebuilds `game.legacy.bundle.js`. After each edit, count the new
code in the bundle with `[regex]::Matches` (Select-String misses it on the minified lines). If it is
missing, run `node scripts/bundle-legacy.js` and `fvbuild -Sync`. Do not commit the bundles.

---

## 8. Stages

Each stage leaves the game playable, and each ends with a node syntax check, a jsdom harness (real
jQuery 4 + real gamedata, stubbed predicates: the hex picker's recipe, which caught two real bugs
reading did not), the bundle grep and screenshots at 1920×1080 and 390px. The user then play-tests
in a fresh local game.

| # | Stage | Scope | Accept when |
|---|---|---|---|
| **1** | **Picker stays open (P2)** | §6.1 + the SELECTED row state. Independent of the card. | IO/5/3: selecting an own row keeps the picker; clicking a unit on the map keeps it; empty-map click / ✕ / Esc / Commit close it; a second stack replaces it; Deployment, Movement and Replay behave exactly as today. |
| 2 | Card frame + model | §4.1 minus hull, §4.4, §4.5, §4.6 (clamp, drag). Menus render as today's icon row inside the new frame. | Every §4.4 item present across a fixture set (own/enemy/ally/flight/mine/base/terrain × phases); null `selectedShip` never throws; hook classes intact; observer and 3-team colours right. |
| 3 | Utility additions | Hull strip, facing + highlighted defence, EW pool, enemy-DEW wording (D6). | Values match the ship window and `calculateHitChange` on fixtures; replay shows damage as of the replayed turn. |
| 4 | Actions (D1) | §5 + targeting row markup + primary button. | Every button of all four menus still appears under exactly its old conditions (`initialOrdersTooltipHarness.js` extended to the new renderer); right-click max and long-press work on the steppers. |
| 5 | Picker badges | §6.2 + refresh hooks. | Badges update on +/−, on Target and on reselect; collapsed runs stay correct; no enemy-side data. |
| 6 | Docking | §6.3. | The card sits beside the picker through zoom/scroll/fold/drag; no jump; the caret is on the row. |
| 7 | Touch | §6.4. | Phone: tap → card in the sheet → back; no ghost hover (§10 touch traps); 44px targets. |
| 8 | *(optional)* Peek while pinned | A second tooltip slot in PhaseStrategy so hovering other units works while a card is pinned. | Needs its own ruling: it changes the one-tooltip invariant. |

---

## 9. Decisions for the user

| # | Question | Recommendation |
|---|---|---|
| D1 | Action style: A steppers + tiles / B captioned icons / C menu rows | **RULED: A**, refined per §11 |
| D2 | Picker stickiness: P1 / P2 / P3 | **P2** (built in Stage 1; P1 is a one-line change) |
| D3 | Sticky phases | **Initial Orders, Pre-Firing, Firing** (built in Stage 1) |
| D4 | Hull strip on the card | **RULED: no.** Build it, leave it commented out (§11 item 9) |
| D5 | When the picker is open, dock the card beside it (vs. under the hex as today) | **Dock** |
| D6 | Enemy DEW in Initial Orders: show `≤ N · hidden` / keep today's bare number / `—` | **`≤ N · hidden`** (today shows the full pool as if known, because enemy EW is hidden in phase 1 and `getEWLeft` returns it all) |
| D7 | Square corners for the ship card (ends its 7px exception) | **Square**, like the system card |
| D8 | Hover peek `pointer-events: none` instead of self-destroying | **Yes** |
| D9 | Touch: drill-down inside the sheet / a card stacked above the sheet | **Drill-down** |
| D10 | Pinned card draggable by its head | **Yes**, per instance |

---

## 10. Traps (read before building)

1. **One throwing condition deletes the whole tooltip, and `selectedShip` really is null**
   (InitialOrders with nothing selected, gate signals, fleets in hyperspace). Every new `value()`,
   `label` function, badge and model field must be null-safe. `getTurnDeployed(null)` and
   `isMyShip(null)` throw. → [[arch_tooltip_menu_null_selection]]
2. **`showShipTooltip` destroys and rebuilds on every call.** Any re-entry flickers. Keep the
   picker's `hoveredShipId` guard, and never rebuild on reposition.
3. **DOM-driven hover must `requestRender()`.** That is already fixed at the mutation site; do not
   bypass `onMouseOverShip`. → [[arch_render_loop_idle_gating]]
4. **Touch:** a long press fires `contextmenu` by itself; compatibility mouse events replay after a
   tap; a stale `pointerleave` can arrive after the next `pointerdown`. → [[project_hex_stack_picker]]
5. **`onMouseOutShips` does not hide the tooltip.** The touch path hides it explicitly, but only on
   touch, because on desktop the same call kills the pinned card.
6. **`onClickCallbacks` are one-shot unless they return truthy**, and `onZoom`/`onScroll` callbacks
   must return truthy or they unregister themselves.
7. **Never write a position onto a surface that owns its own repositioning.** The docked card
   derives its place from the picker; the picker does not push it.
8. **Re-anchoring on every render makes a menu jump.** The picker's side choice is sticky: reuse
   `picker.placement`, never re-decide it.
9. **Alpha compounding:** no element opacity on top of a translucent fill. → [[project_visual_unification]]
10. **No Orbitron on game.php.** Use caps + letter-spacing. → [[project_log_panel_redesign]]
11. **The team-colour gate has two arms.** The CSS classes paint 2-team participants; inline colour
    paints observers and 3+ teams. Move both. → [[arch_team_colour_logic]]
12. **A masked mine's art and class identify it.** No art, no class, no hull for an unrevealed mine.
13. **`getIniativeOrder` returns 0** for terrain, mines and undeployed units. Render an em dash.
14. **`.fire` and `.ballistics` each sit on two divs** (heading + content). Clear `.fire.targeting`,
    never a bare `.fire` (ShipTooltip L138-143).
15. **A WAITING player gets no ship data.** The model builder must tolerate missing `systems`.
    → [[arch_gamedata_polling_cache]]
16. **Bundles:** a rebuild can miss an edit that lands mid-build. Grep the bundle after every edit.
    → [[feedback_fv_workflow]]

---

## 11. Refinement round 1 (user, 2026-11-09) — overrides §4-§6

The user chose Option A, then asked for these twelve changes. The canvas boards are updated to
match.

| # | Ruling | Effect on the build |
|---|---|---|
| 1 | Drop the EW descriptions ("· fighters ≤ 10 hexes"); put the EW icon left of the label | Each EW row is `[icon 20px] LABEL … value column`. **There is no type-only EW art:** the game has only the `add*`/`remove*` PNGs (a +/− box under the type emblem). The row uses the `add*` art (`addCCEW.png`, `addOEW.png`, `addDIST.png`, …). **DEW has no icon at all**, so the mockup draws a plain shield outline in the DEW colour. Commissioning neutral type art is an open option. |
| 2 | Target Weapons and Ship Details stay buttons | Ship details is a bordered button in the footer (icon + label), not a text link. Target Weapons stays the full-width button. |
| 3 | The whole card's border takes the rail colour | 1px border in the allegiance **signal** tier (green own, blue ally, red enemy, neutral terrain). Observers and 3+ teams get the inline team colour through the same two-arm gate as the rail. |
| 4 | Target Weapons must not be blue; make it distinctive | A **solid-filled** button with a dark icon well on the left (keeps `targetShip.png` legible) and dark bold caps. Default fill `--fv-crit` `#ed6738`. The canvas's States and Firing boards carry a Tweak to try `#e1b000`, `#d9534f`, `#f39c3d`. |
| 5 | The key cells are centred | `text-align: center` on the four cells (label, value and sub line). |
| 6 | No "n of N free" readout | Removed everywhere: DEW (unallocated EW) already says it. |
| 7 | Clear all EW matches the −/+ buttons | Same border, fill and accent text as the stepper buttons. |
| 8 | "DEFENCE", not "DEFENCE %" | Label only. |
| 9 | No structure health bars | **Build the hull strip, but ship it commented out** so it can come back. The flight fighter pips go with it (also a health bar); a flight's `4 of 6` stays in the sub line. |
| 10 | Picker chips sit too close to the name; the target chip must differ from SELECTED | The chip line-height drops 13px → 12px and the name/meta gap rises 1px → 2px, so row heights are unchanged (built in Stage 1). Chip colours: **SELECTED = `--fv-accent`**, **OEW n = the ship window's OEW green `#acd7a8`**, **⌖ target = the Target button's fill**. This deliberately ends the "all chips neutral" ruling of 2026-08-14 for these three state chips only; the fact chips (Deploys T3, Unrevealed, Terrain) stay neutral. |
| 11 | DEW always first; values in one column; EW colours as the ship window | The EW list order is DEW, then the rest. Every row ends in the same `[24px][32px value][24px]` block, so DEW's value (with blank slots) lines up with the steppers' values. Label colours come from `ShipWindowEw.js` `EW_LABEL_COLORS` (DEW `#aecdea`, CCEW white, OEW `#acd7a8`, BDEW `#8ac785`, SDEW `#9ac1e5`, DIST `#e6b98f`, Detect Mines `#bfa3db`, Detect Stealth `#ccb6e2`). Values are white Consolas, as in the ship window. **Keep the two maps in step**: lift the map into a shared place (e.g. `window.EW_LABEL_COLORS` read by both) rather than copying it, when Stage 4 is built. |
| 12 | Long faction names must never wrap the header | The sub line (class · size · faction) is one line with an ellipsis, like the name. |

### 11.1 Refinement round 2 (user, 2026-11-09, after Stage 1) — overrides §11 where they differ

| # | Ruling | Effect on the build |
|---|---|---|
| R2.0 | New EW icons are committed (cfc8c208b) | `img/CCEW.png`, `DEW.png`, `DIST.png`, `DetectSEW.png`, `JAM.png`, `MDEW.png`, `OEW.png`, `SDEW.png`, `SOEW.png`, all 40×40, shown at 20px. This replaces §11 item 1's use of the `add*` art and the drawn DEW shield. ⚠️ **There is no `BDEW.png`.** The BDEW row needs one, or falls back to `addBDEW.png`, until it exists. |
| R2.1 | The Target button is too bright | Keep the hue on the 1px border, and use a **dim fill** (the hue scaled toward black, about 30%) with light text (the hue mixed 45% toward white). The icon well is darker still (`rgba(0,0,0,.45)`), so the green crosshair reads. The canvas has two tweaks: hue and dimness. |
| R2.2 | The rail runs down the whole left edge | The card's own `border-left: 3px` in the allegiance colour, not the head band's. Any docking caret moves out by the extra 2px. |
| R2.3 | Everything in an EW row sits on the row's vertical middle | The row is a flex box of fixed height (28px) with `align-items: center`. The icon is `display: block`. The label and value use `line-height: 1`. The −/+ glyphs are flex-centred in their buttons. Caps text has its ink above the box middle and the icons looked low, so the mockup lifts the icon 1px. **Verify by measuring the ink**, as the Create Game tick-box alignment was (pixel-ink centres via CDP); don't eyeball it. |
| R2.4 | Base Ini and Ini mod in every phase; the INI cell says no "total" | The footer carries `Ini base b · mod m` on every card, the hover peek included (a slim footer with no button). |
| R2.5 | No sub-lines in the key cells | Numbers only. Gone: `fwd · side`, `at max DEW`, `hidden`, `left`, `×rate`, `delay n`, `f·s·a`, `total`. **INI = the rolled total, with the order in brackets on the same line, e.g. `−20 (1st)`**, the brackets dim and regular weight. The cell grid becomes `1.2fr .8fr .8fr 1.2fr` so that fits. Where a removed sub-line was the only home of a fact, it moves: the current turn delay becomes a `Turn delay n` caution chip; the turn-cost and delay rates and the armour order go into the cells' `title`. The enemy DEW in Initial Orders reads `≤ N` with no sub-line. |
| R2.6 | No ship type or faction in the header | The sub line shows the **class only when the name does not already contain it** (e.g. "Ka'Tal" → "G'Quan Heavy Cruiser"; "Mishakur Dreadnought #3" → no sub line). **Flights** keep their count there: `4 of 6 fighters`. The ellipsis rule from §11 item 12 still applies. |

---

## 12. Stage 1 as built (2026-11-09) — picker stays open

**Files:** `client/renderer/phaseStrategy/PhaseStrategy.js`, `InitialPhaseStrategy.js`,
`PreFiringPhaseStrategy.js`, `FirePhaseStrategy.js`, `client/UI/SelectFromShips.js`,
`styles/hexPicker.css`.

- `PhaseStrategy.prototype.keepsSelectFromShipsOpen()` returns false. The Initial Orders,
  Pre-Firing and Firing strategies override it to return true. Waiting, Replay, Movement and
  Deployment inherit false and behave exactly as before.
- `onClickEvent` now hands every click callback a `{payload, landsOnUnit}` record.
  `landsOnUnit` counts **only units the player can see** (`!shipManager.shouldBeHidden`), the same
  filter the click dispatch uses. Otherwise a picker that stayed open over an empty-looking hex
  would reveal an undetected stealth unit there. Every existing callback has one bound argument and
  ignores the extra one. Movement's own argument-less sweep (`onShipMovementChanged`) passes
  nothing, which reads as "close", as before.
- `showSelectFromShips` hides any open picker first, then registers
  `onMapClickForSelectFromShips`. That callback stays registered while the click landed on a
  visible unit in a sticky phase, and otherwise closes the picker and unregisters itself. It also
  unregisters when its picker has already been replaced or closed.
- `SelectFromShips.activateShip` no longer destroys the picker in a sticky phase.
- The SELECTED state: `describe()` marks the row of `phaseStrategy.selectedShip`. It is part of
  the run key, gets an accent chip first in the meta line, plus a `--fv-card` fill and an accent
  right edge. `refresh()` rebuilds the list and keeps folds, expanded runs, scroll position, the
  card's top and keyboard focus (rows carry `data-ship-id`). `PhaseStrategy.setSelectedShip` and
  `deselectShip` call it through `refreshSelectFromShips()`. The SELECTED chip shows in every
  phase whenever the selected unit is in the stack.

**Follow-up, same day (user report, with screenshots).** With the picker staying open, a tooltip
for a unit NEXT TO the stack (clicked on the map) opened over the card. It then jumped to the
other side on the next mouse move over a row, because `placeTooltipClear` only ran from the
picker's own hover, zoom and scroll handlers. Two fixes:
- `PhaseStrategy.showShipTooltip` now calls `selectFromShips.placeTooltipClear()` as soon as the
  tooltip exists, in the same JS task as the append, so no frame paints it in the way.
- `placeTooltipClear` chooses the side with `clearSide()`: **the side that moves the tooltip
  least**, i.e. the side its own unit is on. For a stacked unit's tooltip that is still the hex's
  side, which was the old rule's only answer. The choice is **sticky per tooltip instance**, so
  zoom and scroll cannot flip it mid-gesture. It is revisited only when that side no longer fits
  on screen.

Verified: the harness now runs 41 checks (geometry via sized fake elements) and catches both new
mutants (no creation-time clear; the old always-hex-side rule).

---

## 13. Observations (not in scope unless the user says so)

- Today's OEW line is hidden for flights (L630) while `addOEW` is offered against them. The
  stepper shows the value either way; whether OEW against flights should be offered at all is a
  rules question, left alone.
- The static `#shipNameContainer` div in game.php (L651) appears to have no JS reader.
- While a pinned card is up, hovering another unit shows nothing (PhaseStrategy L1121). That is
  Stage 8's question.
