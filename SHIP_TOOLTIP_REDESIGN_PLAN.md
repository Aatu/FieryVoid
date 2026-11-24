# Ship Tooltip → Ship Card, and the Hex Picker working with it

**Status: Stage 1 BUILT 2026-11-09 (committed d2bc81238). Stages 2-7 BUILT 2026-11-10, uncommitted,
not play-tested (§14). Stage 8 not started - it needs its own ruling.**
D1 is ruled (Option A) with twelve refinements in **§11, which override §4-§6 wherever they differ**,
and a second round in **§11.1, which overrides §11**. **§14 records where the build departs from §4-§6.**
D2/D3 were taken as recommended when the user asked for Stage 1. D5, D7, D8, D9, D10 were RULED as
recommended on 2026-11-10 (asked before Stage 2); D6 was settled by §11.1 R2.5.
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
| D5 | When the picker is open, dock the card beside it (vs. under the hex as today) | **RULED 2026-11-10: Dock** |
| D6 | Enemy DEW in Initial Orders: show `≤ N · hidden` / keep today's bare number / `—` | **RULED (§11.1 R2.5): `≤ N`**, no sub line (today shows the full pool as if known, because enemy EW is hidden in phase 1 and `getEWLeft` returns it all) |
| D7 | Square corners for the ship card (ends its 7px exception) | **RULED 2026-11-10: Square**, like the system card |
| D8 | Hover peek `pointer-events: none` instead of self-destroying | **RULED 2026-11-10: Yes** |
| D9 | Touch: drill-down inside the sheet / a card stacked above the sheet | **RULED 2026-11-10: Drill-down** |
| D10 | Pinned card draggable by its head | **RULED 2026-11-10: Yes**, per instance |

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
- (2026-11-10) `tests/replay/lateEwElintHarness.js` fails on a clean tree: its stub world lacks
  `shipManager.getProjectionOrigin`, which `ew.collectEwDetectors` now calls. Pre-existing; untouched.
- (2026-11-10) Stage 2-7 harness stubs needed `officers.redlineThrustBonus` / `mayRedlineLand`,
  `shipManager.systems.isDestroyed` and `shipManager.criticals.isCutOffFighter` just to evaluate the
  Fire menu's conditions on a flight - the menu's predicates reach a long way. Not a bug.

---

## 14. Stages 2-7 as built (2026-11-10)

**Files:** `client/UI/ShipTooltip.js` (rewritten), `client/UI/shipTooltipMenu.js`,
`shipTooltipInitialOrdersMenu.js`, `shipTooltipFireMenu.js`, `shipTooltipMovementMenu.js` (additive
fields only), `client/weaponManager.js` (targeting row markup), `client/UI/SelectFromShips.js`,
`client/renderer/phaseStrategy/PhaseStrategy.js`, `styles/shipTooltip.css`, `styles/tactical.css`,
`styles/hexPicker.css`, `styles/tokens.css`, `reactJs/shipWindow/ShipWindowEw.js`,
`reactJs/styled/theme.js` and `reactJs/common/Tooltip.js` (comments). Bundles rebuilt with
`fvbuild -Client`. **`fvbuild -Sync` was NOT run: Docker was down.** Run it (or restart the php
container) before testing locally.

**The shape.** `ShipTooltip.buildModel(ship, selectedShip)` is a pure model (exported for node
harnesses) - every number is the old `createForSingleShip` computation, moved. The renderer turns it
into: head band (art, name, sub) · chips · key cells · "From ‹sel›" · EW · Fire orders · Movement /
Costs / Combat · TARGETING · Target button · INCOMING · Orders tiles · footer (Ship details + `Ini base
· mod`). The public API and every hook class are unchanged (§7).

**Menus (Stage 4).** `ShipTooltipMenu` gained `getVisibleButtons()` (renderTo's own filter, which the
harnesses lift), `bindAction()` and `bindInfo()` (renderTo's own steps, split out). The card places
each visible button by its new fields: `group` + `role` → a −/+ stepper in that EW type's row;
`kind: 'primary'` → the Target button; `kind: 'clear'` → Clear all EW; `kind: 'footer'` → Ship details;
anything else → a captioned tile (`label`, else a static `info`). **No condition was edited** - checked
against the HEAD menus in 800 cases.

**Departures from §4-§6 (the build's own decisions; the user may overturn any):**
1. **No `value(menu)` field.** The card reads every EW value through one function (`ewValue`), for
   the rows it draws and for a row it makes for a stepper, so the two can never disagree.
2. **EW values are EFFECTIVE**, as the ship window and the old lines printed them: DIST per third
   (quarter for ConstrainedEW), BDEW ×0.25, SDEW ×0.5. A stepper click moves the RAW points; the
   button's title says so.
3. **The zoom/scroll callback order was NOT changed (§6.3).** Moving `repositionTooltip` after the
   picker would undo the picker's avoidance nudge for every UNdocked card, which is why it runs first.
   Instead the picker's `placeTooltipClear` - already called on hover, zoom, scroll, fold, expand,
   refresh and its header drag - asks a card docked to it to `place()` itself. The card still derives
   everything from the picker's rectangle (trap 7).
4. **Click docking only where the picker stays open** (Initial Orders, Pre-Firing, Firing). In the
   other phases a selection closes the picker, and a card docked to it would have nothing beside it.
   A hover peek docks in every phase.
5. **The picker closing under a docked card** (its ✕, a replacement picker): the card
   `undock()`s and returns under its unit. A card inside the sheet goes with the sheet.
6. **Dragging a docked card undocks it** (its caret would point at nothing).
7. **The caret is its own element in `<body>`** (`.fv-shipcard-caret`): the card is its own scroll
   container and would clip a caret drawn outside its border. Removed with the card.
8. **Placement flips ABOVE the unit** when there is no room below and there is above; the side is
   sticky per card (the picker's rule). §4.6 only asked for a clamp.
9. **Terrain shows DEFENCE and SPEED**, not identity only: small asteroids are targetable, and
   drifting terrain moves (Terrain Drift).
10. **Anyone else's EW** shows as a read-only EW section of NONZERO rows only (CCEW, BDEW, Detect
    Stealth, Detect Mines, Support DEW) - nothing in Initial Orders, where it is hidden anyway. Their
    movement facts (thrust, turn cost/delay with rates, pivot, roll, accel) are in the SPEED cell's
    title, not a section (§2.1 called them noise on an enemy).
11. **Your own ship plotting (Movement) has no EW section** - its DEW is in the Combat section, as the
    Movement board drew it.
12. **DEFENCE with a unit selected** dims the profile that does not face it and keeps the facing one
    bright (both on a boundary).
13. **"Target N weapons"** counts the TARGETING rows that carry a hit chance - the weapons
    weaponManager itself judged able to shoot - rather than re-asking arc and range.
14. **The `-PORT-` side line left the TARGETING list** (now "Facing" in the "From" section, as §4.4
    said). Rows are `.tt-row` weapon | mode | hit chance; a status word spans the last two columns
    and keeps its old class (so its old colour).
15. **Tile captions** add "Cancel node" / "Cancel swap" for the withdrawals, so a pair's two states
    read differently.
16. **The EW icon is not lifted.** Measured on the ink at 2x: the −/+ boxes sit exactly on the row's
    middle, the caps label and value within 0.75px, and the user's DEW art centres with NO offset. The
    mockup's 1px lift put it 2px high. Icons with decorations on top (CCEW's dots, MDEW's star) read
    higher by their decoration.
17. **Hull strip** built (`buildHull`, `renderHull`, CSS) and left out, per D4/§11 item 9.
18. **New tokens:** `--fv-ui` (twin of `theme.fonts.ui`, sync comment on both) and `--fv-target`,
    `--fv-target-fill`, `--fv-target-text` (the Target button and the picker's ⌖ chip - two surfaces).
    `window.EW_LABEL_COLORS` is published by ShipWindowEw.js and read by the card (§11 item 11).
19. **The picker's `refresh()` rebuilds nothing unless a row would render differently** (a signature
    of the descriptor keys). It is now called from `onShipEwChanged` and `onSystemDataChanged`, and
    the second fires on every weapon click.
20. **Reads that walk a unit's systems fall back instead of throwing** (`readSafely`; trap 15). The
    last swallowed error is kept on `ShipTooltip.lastReadError` for a console session.

**Stage 5 badges:** `OEW n` (the selected unit's own OEW on an enemy row) and `⌖ ‹target›` /
`⌖ N targets` (an own row's fire orders this turn, interception excluded; a masked mine reads "Mine",
a unit the player cannot see reads "target"), phases 1/5/3 only, part of the run key.

**Stage 7 sheet:** a TAP on a row in a sticky phase drills down - the card replaces the list in
`.fv-hexpicker__cardslot` (`.fv-hexpicker--detail`), the header shows `‹ N units in hex` (44px), the
card hides its own ✕ and does not drag. A press is still only the preview. Back, a map click and the
sheet's ✕ all end the drill-down; the card tells the picker it has gone (`onCardGone`) however it went.

**Verified** (scratchpad harnesses, real jQuery 4 + the real files, stubbed game world):
- `card.js` - **98 checks**: no throw across 5 phases × 10 unit kinds × 4 selections × peek/pinned;
  every §4.4 item; observer and 3-team colours on both arms; clamp, flip, stickiness and head drag;
  facing, EW pool, `≤ N`; **every menu button under exactly its HEAD conditions in 800 cases**
  (fixtures × phases × selections × weapon/stealth/mine options), the old `renderTo` agreeing;
  steppers, the disabled half, click, right-click max and the 500ms long press; the primary label;
  tiles and the info line. Catches **8 of 8 mutants**.
- `dock.js` - **47 checks** with the real PhaseStrategy + SelectFromShips: badges through
  `onShipEwChanged` / `onSystemDataChanged`, run splitting, the refresh skip; docking geometry, caret,
  zoom, fold, scroll-out, side choice and stickiness, picker drag, card drag, picker ✕, stale docks,
  Movement; the sheet drill-down, back, map clicks, ✕. Catches **8 of 9 mutants**; the ninth (drop
  PhaseStrategy's stale-dock guard) is equivalent - the card's own `place()` refuses a destroyed
  picker's dock too.
- `stage1.js` (Stage 1's, 42 checks) still passes; one check updated, because a refresh with nothing
  changed now keeps the same row (and its focus) instead of rebuilding it.
- `tests/replay/initialOrdersTooltipHarness.js` 23/23.
- Screenshots (static page, real CSS + JS): ten card contexts at 1920×1080 plus a 2x crop; the docked
  card beside a real picker; the phone sheet (true 390px, touch emulation via CDP) drilled down and
  back. 48×44 steppers on coarse pointers; no horizontal scroll at 390px.

**Not verified:** the live game (no session - planting is denied), real art for every EW icon at
every DPR, and the INCOMING list under the new frame with real ballistics (its rules are untouched).

### 14.1 Refinement round 3 (user, 2026-11-10, from the first live screenshots) - overrides §14

"Overall there is too much wasted space and the mix of fv-text-dim with the team colours doesn't seem
to match up very well."

| # | Ruling | Built |
|---|---|---|
| R3.1 | Narrow the card by eliminating empty space | **300px → 250px.** The savings come from R3.2 and R3.5, plus tighter padding (sections 5/9/6, cells 4/4/5), EW rows 28 → 24px with 22×20 steppers and a 28px value column, tiles 58px, footer button 22px. |
| R3.2 | Ini and Speed to the top of Movement; Movement as four rows of two (Ini + Speed, Thrust + Accel, Turn cost + Turn delay, Pivot + Roll); show Movement on ENEMY cards too | Movement is on every ship, base and flight card, own or not (a flight: Pivot + Combat pivot). **The cells are DEFENCE + DEW** (flight: DEFENCE + ARMOR; mine: DEFENCE [+ SIG]; terrain: DEFENCE + SPEED), with DEFENCE the wider column, because Mindrider decimals (`48.75 · 48.75`) were being cut. The Movement-phase cell variant and the Costs/Combat sections are gone; while you plot your own ship, Movement comes straight after the cells. This supersedes §14 departure 10 (movement facts in the SPEED title). |
| R3.3 | Clear All EW smaller, on the ELECTRONIC WARFARE label's row, pinned right | A 15px button in the section heading (`.fv-shipcard__clr` is now a span in `.fv-shipcard__mh`). 32px on coarse pointers. |
| R3.4 | The −/+ buttons in their row's label colour | Each EW row carries `--ew-color` (from `window.EW_LABEL_COLORS`); its label and both buttons use it. Hover lights the glyph and fill and keeps the border. A disabled half stays `--fv-line-soft`. |
| R3.5 | Header one row: the name and ✕ on the same line, a little padding | 32px (was 58): art 34×24, name 13px, ✕ centred. The sub line rides on the name's line: the class (it gives way first and ellipsises) or a flight's `4/6` (title `4 of 6 fighters`; never cut). |
| R3.6 | (the colour remark) | **`--tt-dim`**, one card-scoped variable, now paints every dim label, heading and the ✕. Value: **the dim grey tinted 30% toward the card's allegiance colour** (`color-mix(in srgb, var(--tt-rail) 30%, #9a9a9a)`, so observers and 3+ teams follow their team), with a neutral `#9ea4aa` where `color-mix` is missing. Four options were shot side by side (today's blue-grey, neutral grey, tint 30%, tint 50%); 30% was chosen as the default and is one number to retune. The INCOMING list keeps `--fv-text-dim` (its rules stay untouched). |

Verified: `card.js` now 101 checks (the layout checks rewritten for R3.1-R3.5, plus the row colour and
the heading button), still 8/8 mutants; `dock.js` 47, `stage1.js` 42, IO menu harness 23 - all green.
Screenshots: the ten-context sheet, the four label-colour options, the docked card and the phone sheet
at the new width. Bundles rebuilt (`fvbuild -Client`); `-Sync` still owed (Docker down).

### 14.2 Refinement round 4 (user, 2026-11-10, "definitely improving") - overrides §14.1

| # | Ruling | Built |
|---|---|---|
| R4.1 | Movement's first row is Speed + Ini; the Ini brackets hold the bare order number | `Speed 6 · Ini −20 (1)`. Overrides §11.1 R2.5's `(1st)`; `ordinal()` is gone. |
| R4.2 | CLEAR ALL EW → CLEAR EW, in the card's new colour scheme | The menu entry's `label` is "Clear EW" and the button reads it. Text `--tt-dim`, a line `--tt-btn-line` (the rail 40% into a dark grey), transparent fill; hover: bright text, the rail colour, `--tt-btn-hover` (the rail 14% into black). Neutral fallbacks without `color-mix`. |
| R4.3 | Ship details in the same style | Same rules as R4.2. The stepper buttons keep their row colours (R3.4). |
| R4.4 | A little more width for EW values (BDEW's `1.25`, `0.75`) | Value column 28 → 36px (44px on coarse pointers, unchanged). |
| R4.5 | Grey out an EW row whose option is unavailable (DIST from an ELINT already on BDEW) | `greyUnavailableEwRows`: a row is greyed (`.fv-shipcard__ewr--off`, title "not available now") when the card's menu CARRIES that EW type's steppers and neither half passed its conditions - so no rule is restated, and the menu's own conditions decide. Only for the allocating unit: a self type on the selected unit's own card, a targeted type on another unit's card with a unit selected. Never greyed: DEW and Support DEW (not allocated), another own ship's EW section, anything in a menu without EW steppers (Movement, Waiting, the Firing menu outside the late-EW window), and the hover peek (it has no menu to ask). Grey label and value; the icon greys by `filter`, not opacity. |

Verified: `card.js` 110 checks (+9 for round 4: the swap, `(1)`, "Clear EW", DIST greyed under BDEW,
OEW still live beside it, nothing greyed when all is open, BDEW greyed under DIST while Detect Stealth
is not, DEW never, another own ship never, Firing never, the peek never), 10/10 mutants including two
new ones (grey regardless of scope; never grey). `dock.js` 47, `stage1.js` 42, IO menu harness 23.
Screenshot of the four round-4 cases. Bundles rebuilt; `-Sync` still owed.

### 14.3 Refinement round 5 (user, 2026-11-10; finished 2026-11-12) - overrides §14.2

| # | Ruling | Built |
|---|---|---|
| R5.1 | A second press of Ship details closes that ship's window | `openSCS` sends `OpenShipWindowFor` with `toggle: true`; `PhaseStrategy.onOpenShipWindowFor` closes instead when `ShipWindowManager.isOpen(ship)`. Only this button toggles - the fleet list still always opens. |
| R5.2 | Facing lines up with Movement's second column | "From" uses the same two equal columns as Movement (its 0.8fr / 1.2fr override is gone). A boundary prints `P.FWD/PORT` (no spaces) to fit the half. Measured: Facing and Initiative share a left edge on every card. |
| R5.3 | Defence and DEW cells are equal halves | `.fv-shipcard__cells--n2` 1.5fr 1fr → 1fr 1fr (123px each). |
| R5.4 | "From" and the selected unit's name looked misaligned | NOT CHANGED: they are one span, one font, one line box; measured on the ink at 2x they occupy the same pixel rows. No stylesheet game.php loads (nor the React bundle) reaches that span. Asked for a close-up if it still reads low. |
| R5.5 | No art on the pinned card; the peek keeps it, right of the name | The pinned card's `head.append(art)` is commented out in `renderHead`; the peek appends it after the name. Pinned head padding-left 9px. |
| R5.6 | Thrust and Speed swap | Movement rows: Thrust + Initiative, Speed + Accel, Turn Cost + Turn Delay, Pivot + Roll. |
| R5.7 | Ini base / mod beside MOVEMENT; Ship details fills the footer; no footer on the peek | `MOVEMENT – Ini base 0 · mod 0`, packed left (5px gaps), the pair ellipsises. `model.footer` is gone; `renderFoot` only empties. Ship details `flex: 1`, centred. The peek's footer is `:empty`. |
| R5.8 | Hex-picker chip text sat high | At 1x the caps are 7 device rows in a 12-row interior → 2 above / 3 below at every sub-pixel offset. `@media (max-resolution: 1dppx)`: `padding-top: 1px; margin-bottom: -1px` → 3 / 3, row heights unchanged. Above 1x left alone: 6 / 6 at 2x already; at 1.25x neither value wins. |

⚠️ The first attempt at this round's CSS never reached disk (a Python edit script; `python` in Git Bash
is the WindowsApps stub and exits "Permission denied") while the JS did - which is why R5.2, R5.3 and the
R5.7 gap looked untouched in the first play test.

### 14.4 Refinement round 6 (user, 2026-11-12) - overrides §14.3

| # | Ruling | Built |
|---|---|---|
| R6.1 | "From ‹selected›" and Range / Facing above the Defence / DEW cells; the OEW / DIST rows stay below them (asked: "FROM + Range/Facing only") | The From block keeps `.fv-shipcard__sec--rel` (heading + spec only); the EW spent ON the unit is a new `.fv-shipcard__sec--relew` under the cells. `findOrMakeEwRow` → `ensureSection('relew')` (after the cells; keeps its "From" heading only when no From block exists); `ensureSection('ew')` anchors after `--relew`, else the cells, else `--rel`. Body order: chips › rel › cells › relew › … |
| R6.2 | Head band and footer: a subtle wash of the rail colour instead of `--fv-card`'s blue | `--tt-wash` = rail 16% into black (`--fv-card` without `color-mix`); the footer's Ship details hovers to `--tt-wash-hover` (30%) - the shared `--tt-btn-hover` (14%) would be darker than the washed ground. The strong rail rule moved from the cells' top to the head's bottom (the cells are no longer first); the body's first block draws no rule; the stack grid's head opts out. |
| R6.3 | Ini base / Ini mod as chips, no "–" | Two `.fv-shipcard__inichip` beside MOVEMENT: `INI BASE 0`, `INI MOD −50`, outlined in the label colour, value in mono. 9px / line-height 12px / 14px tall = the heading line, so the row is no taller. Measured on the ink: 3 / 3 at 1x; at 1.25x-2x half a CSS px low at most (odd ink in an even box - unavoidable; low is the better side for caps). |

### 14.5 Refinement round 7 (user, 2026-11-12) - overrides §14.4 R6.3

| # | Ruling | Built |
|---|---|---|
| R7.1 | The Ini chips did not work: no MOVEMENT heading at all; Ini Base + Ini Mod as a stat row above Thrust + Initiative | The movement section has `title: null`; its spec starts `Ini Base`, `Ini Mod` (signed values, only when the unit has an initiative). Five rows of two; Ini Mod sits above the Initiative it adds to. The chip markup and CSS are gone. |
| R7.2 | EW values in one column with the DEW cell's number | `.fv-shipcard__vb { margin-right: max(0px, calc(25% - 4.5px - var(--tt-vb-half))) }` - derived from the card width, so it holds in the wider in-sheet card; `--tt-vb-half` 40px (70px on touch, where a 250px card is too narrow and the block sits right-aligned). Measured 0.0px off the DEW number on every card, desktop and the 390px sheet. Labels lose 12.5px; "DETECT STEALTH" (100px) still fits its 101.5px. |

### 14.6 Refinement round 8 (user, 2026-11-12)

| # | Ruling | Built |
|---|---|---|
| R8.1 | Every EW row's icon starts under the "D" of the DEFENCE label above it | The label is CENTRED, so its start depends on the platform's font: `alignEwRows` MEASURES it (a Range on the label text) and sets `--tt-ew-indent` on the card; the CSS puts it on the ICON's left margin (a row padding would shrink the row the value column's 25% margin is taken from). Runs from `show()` (in the sheet: AFTER `onCardDocked`, before which the card is not laid out), `update()` and `refreshButtons()`. Only beside the two-cell grid - an enemy mine's lone centred Defence cell leaves its rows flush. Measured 0.00px on every card: desktop, scrolling, docked, the 390px sheet. |
| R8.2 | The indent cost the labels 15px (asked): shorter names | `EW_TYPE` gains `full`: rows read DET. STEALTH / DET. MINES / SUPP. DEW, the full name in the label's title, the stepper aria-labels and the greyed-row title. Row gap 7 → 5px. Tightest: Det. stealth 9.1px spare (5.1 while the card scrolls). |
| R8.3 | The card's scrollbar in the head band's dress | `::-webkit-scrollbar` 8px: thumb `--tt-wash` with a `--tt-rail` border, hover `--tt-wash-hover`, track `--fv-overlay`. `scrollbar-color` only under `@supports not selector(::-webkit-scrollbar)` - Chrome 121+ IGNORES the pseudo-elements when it is set. ⚠️ The visual kit's cdp.mjs launches Chrome with `--hide-scrollbars`; `cdp_sb.mjs` drops it. |

### 14.7 Refinement round 9 (user, 2026-11-12)

| # | Ruling | Built |
|---|---|---|
| R9.1 | CLEAR EW centred on the column of EW values below it | `.fv-shipcard__clr` is a ZERO-width slot on that column's centre line - `margin-right: max(var(--tt-vb-half), calc(25% - 4.5px))`, the value block's own centre (it follows the block to the right edge on a narrow touch card) - and the button, `justify-content: center`, overflows it equally both ways: centred at any button width, and the heading text loses no room. `--tt-vb-half` moved up to the card (40px; 70px on touch) so the value block and the slot share it. Measured ≤ 0.01px off the values: desktop, scrolling, touch. |
| R9.2 | CLEAR EW on the same background as Ship details | `--tt-wash` (Ship details is transparent on the footer's `--tt-wash`), hover `--tt-wash-hover`. |

Observation (not changed): the EW icon BOXES start exactly under DEFENCE's "D" (R8.1), but the icon art has transparent margins - the drawn DEW / MDEW art starts ~4px right of the "D", CCEW's dots ~2px.

### 14.8 Refinement round 10 (user, 2026-11-12)

| # | Ruling | Built |
|---|---|---|
| R10.1 | Hide CLEAR EW when there is no EW to clear (everything still in DEW) | A menu condition, `hasEwToClear` (shipTooltipInitialOrdersMenu.js), on the `removeAllEW` entry: true only for a row of THIS turn's EW, other than DEW, with points in it - DEW is the remainder, not an allocation. Null-safe on its own. Live, because every EW change rebuilds the card (`onShipEwChanged` → `update`). Checked: no rows / a 0-point row / a DEW row / last turn's rows → hidden; CCEW 2 or OEW 1 → shown; spend a point → appears, Clear → gone; nothing selected → card builds, no button. |

### 14.9 Refinement round 11 (user, 2026-11-16)

| # | Ruling | Built |
|---|---|---|
| R11.1 | Show Facing on a fighter flight as on a ship (its armour differs front / side / aft) | A flight has no `outerSections` - `FighterFlight::getLocations` puts all four arcs on location 0, so `fillLocationsGUI` sends none and `getShipHittingSide` always answered nothing. `buildFacing` now sends a flight to `buildFlightFacing`, which tests the four arcs of `getLocations` (FWD 330-30, STBD 30-150, AFT 150-210, PORT 210-330; ⚠️ a MIRROR of that PHP) with `getShipHittingSide`'s own bearing maths. Listed FWD, AFT, PORT, STBD so a boundary reads as a ship's does (`FWD/STBD`). FWD/AFT light the forward Defence number, PORT/STBD the side one - `getLocations`' `profile`. |
| R11.2 | No "Target selected weapons" button when none of the selected weapons can be aimed at the unit | Renderer only, no menu condition touched: `renderActions` places a `kind: 'primary'` button only when `targetableCount` > 0 - the TARGETING rows carrying a hit chance - ≤0% only for a BALLISTIC weapon (user, 2026-11-16: `targetShip` launches a ballistic at 0% and declares nothing else below 1%; weaponManager marks the number `tt-ballistic`) - or an Abduction (a new `tt-abduction` class on that row), summed through `data-count`. A list hidden by `refreshTargeting` counts nothing (it is hidden, not emptied, so its rows can be stale). Live: `onSystemDataChanged` already runs `refreshTargeting` then `refreshButtons`. |
| R11.3 | Group identical weapons in TARGETING (same type and same hit chance - a range-penalty crit keeps its own row) | `weaponManager.targetingShipTooltip` ends by merging rows whose MARKUP is identical (name, mode, hit chance and the breakdown behind it, or the same status): the first keeps its place, gains `data-count` and a `3×` `.tt-count` in front of the name (the name's ellipsis cannot cut it). Status rows group too (`2× Heavy Laser  NOT IN ARC`). The Target label counts weapons, not rows. |
| R11.4 | The whole tooltip at 90% opacity, with a manual toggle | `opacity: 0.9` on `.fv-shipcard--translucent` (peek, pinned, stack) and its dock caret; the card inside the picker's sheet stays solid (no map under it). The toggle is an OPTIONS-tab entry, `translucentShipTooltips` "See-through ship tooltips (90% opacity)", default ON, per browser (gameOptions.js). The constructor reads it; flipping it repaints open cards (`ShipTooltip.applyTranslucency`). The header's "no element opacity" rule now names this one exception: root opacity composites the card as one layer, so nothing compounds. |

Verified: a round-11 harness (real ShipTooltip.js, the menus, gameOptions.js, mathlib.js and the real `targetingShipTooltip`, jsdom) - 88 checks: 16 flight bearings × heading (label + lit Defence number), own flight, peek, nothing selected, ships unchanged; grouping (identical, crit-different, another mode, order, `data-count`, the kept breakdown, and EVERY status - Not In Arc, Not In Range, Hex Targeted, Linked Firing, Cannot Target, Line of Sight Blocked - grouped per weapon; user confirmed 2026-11-16: "anything where it's the same result for the same type of weapon"); the button absent / present / singular / plural / Abduction / Initial Orders / hidden list / live add-remove / click; ≤0% non-ballistic (none), 1% (button), ballistic 0% / −10% (button), 0% beside 65% (button, one weapon), a ballistic and a direct-fire twin never grouped; opacity computed from the real CSS, the in-sheet card, the switch live on an open card, remembered, drawn in the OPTIONS tab. Catches 11 of 11 mutants. Bundles rebuilt (`node scripts/bundle-legacy.js`) and synced (`fvbuild -Sync`); not play-tested.

### 14.10 Refinement round 12 (user, 2026-11-16) - "tooltips can become very long … smaller screens"

| # | Ruling | Built |
|---|---|---|
| R12.1 | The ORDERS tiles move out of the card into a vertical column on its RIGHT, with a small in / out toggle (default out), hidden entirely when the unit has no options this phase | **The root is now a bare flex row**: `.fv-shipcard__card` (the old card box - border, rail, 65vh scroll, ✕) and `.fv-shipcard__side` (the panel). The panel must sit outside the card's scroll box (it would be clipped) and INSIDE the root, so place / placeDocked / keepInView / the head drag / the picker's `placeTooltipClear` all measure and move it without knowing about it. The constructor moves `.buttons` into the panel; `renderActions` → `setSide` sets `fv-shipcard--has-side` only while a tile is offered (no tile → no panel, no toggle). 78px wide (90 on coarse pointers), its head band continues the card's (same wash, 32px, rail rule), border laid over the card's right border (−2px margin), card's top-right corner squared. The toggle folds it to a tab of the head band alone (`fv-shipcard--side-folded`). Tiles keep their captions; a dynamic info line shows under them in the panel. The panel's head band drags the card too (its toggle does not). |
| R12.2 | Movement folds, and so does the EW section on your OWN card; Movement folded by default in the Firing phase | Movement has a heading again - `▸ MOVEMENT` is the fold button (a folded section needs something to name it and to open it; round 7 had removed the heading). Own EW: `▾ ELECTRONIC WARFARE`, Clear EW stays in the heading when folded. Anyone else's `EW` rows and the OEW/DIST rows (`--relew`) do not fold. CSS chevrons (two borders of a box), `aria-expanded`, a 32px target on coarse pointers. |
| R12.3 | Fire orders under Movement in the Firing phase | `model.fireAfterMovement` (phase 3 only); Pre-Firing keeps EW → Fire orders → Movement. *Superseded by R13.3: Pre-Firing too.* |

**Build decisions (the user may overturn any):**
1. **Fold state is per PHASE, shared by every card** (`folds()`, keyed `turn:phase`): each phase starts from its defaults (panel out; Movement folded in Firing only; EW open); what the player opens or closes then holds for every card - the card rebuilds on every EW click and retarget - until the phase changes. Never stored past the page. The hover peek follows the same state (it is click-through, so it cannot be toggled itself).
2. **Docked on the picker's LEFT, the panel mirrors** to the card's far side (`fv-shipcard--side-left`, row-reverse), so the card's edge and the dock caret still face the picker; dragging a docked card keeps the side it had (flipping would jump the card from under the pointer); `undock()` puts it back on the right.
3. **Undocked, the CARD (not card + panel) is centred on its unit**; the clamp keeps the whole row on screen, so near the right edge the card moves left to fit the panel.
4. **The card inside the picker's sheet (phone) has no panel** - no room beside it, and the sheet scrolls the card - so its tiles stay inline under "Orders" as before.
5. The panel is titled "Orders" (the card's existing word), not "Options".
6. `show()` sets `display: flex` itself - jQuery's `show()` would write the tag's default `block`.

Verified: round-12 harness (real ShipTooltip.js, the four menus, jQuery 4 and shipTooltip.css in jsdom; card.js's stubs) - 80 checks: the root/card/side structure and every hook class inside the card; the peek and the in-sheet card without a panel; the tile in the panel, its action and info line; no tile → no panel, live both ways; fold / unfold, kept through refreshButtons, update and the next card, reset by a new phase; Movement open in 1/5/2 and folded in 3, kept across cards, reset next turn; own EW folds (Clear EW stays, steppers kept), enemy EW and relew never; section order in 3/5/2; card centred and the row clamped; docked left / right / dragged / undocked; the panel head drags, its toggle does not. Catches 13 of 13 mutants. `dock.js` 47 and `stage1.js` 42 still pass; round 11's sections 1-3 pass (its section 4 stops on the commented-out `translucentShipTooltips` option - not this round). ⚠️ jsdom's cascade reads SOURCE ORDER, not specificity: the folded-section hide rule sits after the spec grid and the EW rows so both agree. Screenshots (headless Chrome, real CSS/JS): ten card contexts, a 2x crop of the head-band joins, docked right / left / folded beside the real picker, and the 390px phone sheet. Bundles rebuilt (`node scripts/bundle-legacy.js`) and synced (`fvbuild -Sync`); not play-tested.

### 14.11 Refinement round 13 (user, 2026-11-16)

| # | Ruling | Built |
|---|---|---|
| R13.1 | The ORDERS panel (and its toggle) moves down so its top border lines up with the bottom border of the card's head band | `.fv-shipcard__side` gets `margin-top: 33px` (the card's 2px border + the head's 32px less its 1px rule), so its 2px top border starts on the rule's row and the rule runs on into it. Its `max-height` becomes `calc(65vh - 33px)`, so the row is never taller than the card's own 65vh cap. The card's head band, ✕ and top corners now stand clear of the panel, so the round-12 rules that squared the card's top-right (top-left when mirrored) corner are gone. The panel keeps its own head band (wash, 32px, rail rule); folded it is still a tab of that band alone (31px, no rule: the toggle stays on the same line when it folds). Mirrored docking (`--side-left`) is unchanged apart from the drop. |
| R13.2 | Brighter text for the section headings | A new token, `--tt-head`: the same 30% allegiance tint as `--tt-dim`, on `#d4d4d4` instead of `#9a9a9a` (fallback `#c9cdd1` without `color-mix()`). Worn by the cell labels (DEFENCE, DEW, ARMOR), the section heads `.fv-shipcard__mh` (From, Movement, Electronic warfare / EW, Fire orders, the in-sheet Orders), TARGETING / INCOMING (`.tt-head`) and the ORDERS panel's title. Row labels (Speed, Range, …), the class sub line, the chips and the toggles stay `--tt-dim`. On a green card: heading rgb(173, 202, 173), row label rgb(132, 161, 132). The fold button's hover is still `--fv-text-bright`. |
| R13.3 | Fire orders below Movement in Pre-Firing as well | `model.fireAfterMovement = gamephase == 3 \|\| gamephase == 5` - the only two phases Fire orders show in, so it now always follows Movement. Movement's fold default is unchanged: folded by default in Firing only, open in Pre-Firing. |
| R13.4 | `--tt-head` a little brighter again (second pass, same day) | The grey under the 30% tint `#d4d4d4` → `#e6e6e6` (fallback `#c9cdd1` → `#dadde0`). Green card: heading rgb(186, 215, 186), was (173, 202, 173); red card rgb(228, 183, 180). |
| R13.5 | Fire orders folds too, open by default | `sectionElement('fire', 'Fire orders', 'fire')` - the same fold heading, chevron and `folds()` state as Movement / own EW (`fire: false` in the defaults: open in Pre-Firing and Firing). Shared by every card and the peek for the phase, like the others. ⚠️ The folded-section hide rule moved BELOW the Fire orders rules in shipTooltip.css: Chrome hides the rows by specificity anyway, but jsdom's source-order cascade did not, so the harness and the browser disagreed - keep that rule after any foldable section's rows. |
| R13.6 | The ORDERS panel collapsed by default in every phase | `folds()` default `side: true` (was `false`): each phase starts with the panel folded to its tab; opening it holds for every card for the rest of the phase, as before. Supersedes build decision 1's "panel out" in §14.10. |

Verified: the round-12 harness, carried forward as round 13 (session 44a15bb4 scratchpad `harness/round13.js`) - 108 checks: Pre-Firing order `ew,move,fire` with Movement open, Initial Orders without fire orders; the panel's 33px drop and capped height, the card's top-right corner kept round with a panel, `--tt-head` on the section head / cell label / panel title / TARGETING, `--tt-dim` kept on a row label and the toggle; the panel IN by default (a tab, title "Show orders (1)"), opened and held through refreshButtons / update / the next card, IN again in phases 5 / 1 / 2 and next turn; Fire orders' fold heading open in 3 and 5, folded rows hidden with the heading kept and its place under Movement, held through update / the next card / the peek, open again next phase, "None yet" under the heading. Catches 20 of 20 mutants (round 12's 13, plus Pre-Firing reverted, the drop removed, a heading back on `--tt-dim`, the corner squared again, the panel out by default, Fire orders folded by default, Fire orders not foldable). Headless Chrome (real CSS/JS): the head rule's row and the panel's top edge both measure 33px below the card's top on every card with a panel - right, mirrored left, open, folded - and beside the real picker (docked right / left / folded, no errors); a 2x crop of both joins. Bundles rebuilt and synced; not play-tested.

### 14.12 Refinement round 14 (user, 2026-11-16) - stats a critical has changed, in the critical orange

The system card's rule (SystemInfo `critStats`, same day) brought to the ship card: "where criticals affect stats such as Ini Mod, Thrust, Defence Profile, DEW".

| Stat | Orange when a critical in force... | Mirrors |
|---|---|---|
| Ini Mod | on the C&C: CommunicationsDisrupted, ReducedIniative(OneTurn), swtargetheld, tmpinidown, ShadowPilotPain, HangarOperations, LCVLaunchedThisTurn, EdfIniDrain, EMStormInitiative - never on an OSAT. A flight: tmpinidown, LaunchedThisTurn, ReducedIniative(OneTurn), Uncontrolled, EdfIniDrain, EMStormInitiative on its first craft | `BaseShip::getCommonIniModifiers` (exact class names; CommunicationsDisruptedOneTurn added there 2026-11-16 - it was never counted) |
| Thrust | an engine's output crit / EDF thrust drain / forced shutdown, swtargetheld on ANY system, ShadowPilotPain on the C&C (BioDrive output); a flight: EdfThrustDrain on its first craft | movement.js `getFullEngineThrust`, `FighterFlight::getEffectiveFreeThrust` |
| Defence (F-S) | ProfileIncreased on the C&C | `BaseShip::stripForJson` adds it to the profiles it sends |
| DEW (the cell, and your own card's DEW row) | a scanner's output crit / EDF EW drain / EM storm sensors / forced shutdown, RestrictedEW or tmpsensordown on the primary C&C, a BSG hybrid's SensorLoss | ew.js `getScannerOutput` (its SensorLoss line fixed 2026-11-16 - it passed the class name as the system, so subtracted nothing; and the SERVER's EW::getScannerOutput now takes tmpsensordown off a ship too, as the page and the bot always did) |

`ShipTooltip.js` `critStats(ship)` (in `buildModel`, through `readSafely`) sets `crit` on the cells, the two Movement rows and the DEW EW row; the renderer adds `fv-shipcard__cv--crit` / `fv-shipcard__crit` / `fv-shipcard__sv--crit`. The CSS replaces the value's BRIGHT colour only: a Defence number dimmed for facing away stays dim (the facing still reads), and an EW row greyed as not open stays grey. Values are unchanged - they already carry the effects. Not coloured: Speed, Accel, turn costs (no critical changes them) and the rolled Initiative total (Ini Mod is the part a critical changes).

Verified: headless Chrome on the visual kit with the REAL criticals.js - 11 cards (all four stats, none, C&C sensor crit only, scanner drain, tractor on a weapon, every crit ENDED, an OSAT, the one-turn comms crit, an enemy with facing, a storm/EDF flight, a flight whose craft 1 only dropped out): 36 computed colours, 10 of 10 mutants caught. The round-13 harness still passes (108). Bundles rebuilt and synced; not play-tested.
