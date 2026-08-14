# SelectFromShips redesign — the Hex Stack Picker

**Status:** **COMPLETE — Stages 0–6 built (2026-08-14), plus two rounds of play feedback.**
**Scope:** client-only. One JS file rewritten, one new stylesheet, one `<link>` added to `game.php`.
No PHP, no serialisation, no gamedata shape change.
**Owner file:** `source/public/client/UI/SelectFromShips.js` (self-contained IIFE)

### What shipped

| File | Change |
|---|---|
| `client/UI/SelectFromShips.js` | rewritten — `.fv-hexpicker` card/sheet, grouping, run-collapsing, dense tier, touch parity, keyboard/a11y |
| `styles/hexPicker.css` | **new** — tuning-knob block, component-scoped `--hp-*` allegiance palette |
| `client/gamedata.js` | **new** `getMutedTeamColorRGB` + `MUTED_TEAM_SAT` / `MUTED_TEAM_LIGHT` (§2.5); `getFleetHeaderColorRGB` converted (Stage 6) |
| `styles/tokens.css` | **new** `── Allegiance ──` group: the eight `--fv-own/-ally/-enemy/-neutral` values and their `-signal` twins (Stage 6) |
| `client/renderer/phaseStrategy/PhaseStrategy.js` | `requestRender()` in `onMouseOverShip` / `onMouseOutShips` — see feedback round 1, item 6 |
| `game.php` | one `<link>` through `AssetLoader::getAssetUrl()` |
| `styles/tactical.css` | deleted `.not-deployed` and `.name-value-button-dock` — the picker was their only consumer. `.name-value-button-ally` **stays** (confirm.js) |

`renderClassic()` / `USE_CARD_UI` / `window.FV_LEGACY_SHIP_PICKER` were never built: Stages 0–5
landed in one pass (user decision 2026-08-14), so the escape hatch would have been written only
to be deleted in the same change. **There is therefore no fallback to the old menu.**

### Three deviations from the text below, all deliberate

1. **The dense threshold keys on UNIT count, not rendered-row count.** §2.3 says "above 8 rows"
   but its own worked cases require units (a 20-unit hex collapses to 8 rows and is still
   described as dense; verification case 15 makes a 9-unit stack dense). The constant is
   `DENSE_AT_UNITS` in the JS; `--hp-dense-at` in the CSS remains documentation-only.
2. **Rows bind `mouseenter`/`mouseleave`, not `mouseover`/`mouseout`.** Rows now have children,
   and the bubbling pair re-fires on every internal boundary — the map highlight flickered as
   the cursor crossed from the thumbnail to the name. Same behaviour, no flicker.
3. **A folded group emits no rows at all** rather than hiding them with `display:none`. The list
   is rebuilt from the model on every fold anyway, so nothing is preserved by rendering them —
   and it keeps "what can be focused" a question about the DOM rather than about layout, which
   is what a `:visible` filter would have made it.

### Feedback round 1 (2026-08-14) — all applied

| # | Change |
|---|---|
| 1 | Details icon 20→**24px** in a 32px box; sheet 24→**28px** in a 40px box |
| 2 | **The header is a drag handle.** Position is per-instance and never remembered — every opening re-anchors on its hex. A dragged card opts out of `positionSelf()` (otherwise the next zoom/scroll would snap it back) and hides its caret. Card mode only; the sheet is docked |
| 3 | **Hover tooltip no longer lands on the card.** `showShipTooltip` anchors it on the hovered ship's ICON — the hex the picker is anchored to, i.e. underneath the picker. Now placed beside the card (flipping side when there is no room), or above it in sheet mode. A `hoveredShipId` guard also makes re-entering the row you are already on a no-op, since `showShipTooltip` destroys and rebuilds on every call |
| 4 | **Long press no longer opens the ship window.** It PINS the preview tooltip — stays up after the finger lifts — and suppresses the click that would otherwise select. The details button is the only touch route to the ship window |
| 5 | **Sheet gets `--hp-sheet-max-w: 400px`** and auto inline margins. `left:8px..right:8px` was fine on a 390px phone and absurd on a landscape phone or a finger-driven tablet. Landscape short screens also get a tighter list cap |
| 6 | **Pre-existing bug fixed at source.** `PhaseStrategy.onMouseOverShip` / `onMouseOutShips` mutate the scene (`setHighlighted` raises the icon out of the pile and shows its prow/movement sprites) without calling `requestRender()`. Canvas-driven hovers were masked — `webglScene.mouseMove` requests a frame for the same event — but a DOM-driven hover produces no canvas event, so the icon never redrew. Straight instance of the render-loop invariant |
| 7 | **Count leads the name and shares its font:** `2 x Scion Breaching Pod`, not `Scion Breaching Pod ×2`. Collapsed runs follow (`13 x Mine`); the separate `.fv-hexpicker__count` span is gone |

⚠️ Item 3 was fixed **structurally**, not by identifying the exact trigger: the overlap that put the
tooltip under the cursor is what made an enter/rebuild/leave cycle possible, and the tooltip can no
longer land there. Worth re-confirming in play.

### Feedback round 2 (2026-08-14) — all applied

**1. Folding a group no longer moves the card.** `positionSelf()` was being called after every
re-render, which re-runs the above/below decision from scratch: a card that had flipped BELOW the
hex because it was too tall to fit above flipped back ABOVE the moment a fold made it short enough.
Fold one group and the whole menu leapt across the hex.

Fold and expand now call `reflow(captureBox())` instead, which re-places the card **without
re-anchoring** and never flips it. It holds one edge steady: anchored **above** the hex it holds the
**bottom**, so the caret stays on the hex and the card grows and shrinks upward away from it;
anchored **below**, or dragged, it holds the **top**, which is how every collapsible panel behaves.
Then it clamps. `positionSelf()` still runs on zoom and scroll, where re-anchoring is correct
because the hex itself moved.

**2. Stage 6 shipped**, exactly as specified in §4 below, plus a colour retune:

* the eight values moved into a `── Allegiance ──` group in the single `:root` in `tokens.css`;
  `.fv-hexpicker`'s `--hp-*` colour block is gone and the row rules read `--fv-*` directly.
  Appearance-neutral — a rename.
* `getFleetHeaderColorRGB`'s three literals became the tone-mapped tints, and its observer /
  3+-team branch moved from `getTeamColorRGB` to `getMutedTeamColorRGB`. Re-audited first: still
  exactly one live consumer ([fleetList.js:282](source/public/client/UI/fleetList.js#L282)); the
  ShipWindow reference remains inside a `/* … */` block.
* `getShipLogColorCss` deliberately NOT converted. Both functions now carry a comment pointing at
  the other and saying which one was converted, so the divergence reads as intent.
* **`--fv-enemy` retuned `#d1867e` → `#e0736a`** (user: the pastel red was too soft). Chroma up
  from ~0.47 to ~0.65 at essentially the same lightness (~0.65) — anti-pastel is more chroma, not
  more brightness — so it still sits in the same band as own and ally. The full-chroma
  `--fv-enemy-signal` on the 3px bar is unchanged.

No `theme.js` twins were added: nothing in the React stack paints allegiance yet.

### Feedback round 3 (2026-08-14) — all applied

**1. `--fv-enemy` again, `#e0736a` → `#ea6a5e`** (chroma ~0.65 → ~0.77, lightness held at ~0.64),
**and the four hover-tooltip rules converted.** `.enemy.name` / `.mine.name` / `.ally.name` /
`.terrain.name` at [tactical.css:455](source/public/styles/tactical.css#L455) now read
`--fv-enemy` / `--fv-own` / `--fv-ally` / `--fv-neutral` instead of literal `red` / `limegreen` /
`#33adff` / `#dedede`. Re-audited first: **ShipTooltip.js is the only emitter** of those class
pairs (`.shipSelectList` uses `.shiplistentry`), so this is exactly one surface. Closes the last
item deferred from Stage 6 — the two surfaces sit side by side now, so the mismatch had become
visible rather than theoretical.

**2. The ship window opening on a touch long-press — real cause found.** It was never the
long-press timer (round 2 already stopped that). **A long press on a touchscreen fires a
`contextmenu` event of its own**, and the row's `contextmenu` handler routed straight to
`onShipRightClicked`. That handler now returns early for touch, via a new `isTouchInteraction()`
that reads the `pointerType` recorded by the last row `pointerdown` and falls back to
`(pointer: coarse)`. It still `preventDefault`s unconditionally, so no native menu appears over
the card. The details button's `pointerdown` guard was dropped at the same time — it existed only
to stop the old double-open, and removing it keeps the recorded pointer type accurate.

**3. Header height, and the sheet is now draggable.**
* New knobs `--hp-header-h` / `--hp-close` / `--hp-grab-h`. The sheet header was **32px against
  the card's 27px**, on top of a 12px grab bar — the one piece of chrome that grew on the screens
  with the least room. Now 26px header + 9px grab, with the ✕ growing to 24px instead, since that
  is the part that is actually a tap target.
* `bindHeaderDrag` no longer refuses sheet mode. On the first real movement the sheet **unpins**:
  its width is frozen, `right`/`bottom`/margins are dropped, and it runs on `left`/`top` like a
  card. The grab bar joins the header as a drag handle, and the CSS rule that set
  `touch-action: auto` on the sheet header — which was letting the browser claim the gesture as a
  scroll before `pointermove` ever fired — is gone. `captureBox`/`reflow` treat a dragged sheet as
  a card.

### Feedback round 4 (2026-08-14) — all applied

**1. Tooltip names looked duller than the picker's — the cause was `opacity: 0.65`.**
`.shipNameContainer` faded its TEXT along with its panel, so the *same* allegiance token rendered
at 65% strength in the tooltip and at full strength in the opaque picker beside it. New token
`--fv-overlay-soft: rgba(0, 0, 0, 0.65)` moves the translucency into the FILL: the panel is exactly
as see-through as before, the text comes back to full strength, and the two surfaces now match.

The React twin `common/Tooltip.js` was changed with it (`theme.colors.overlayBgSoft`), because the
two files carry an explicit "if you change anything here, change it there" contract and diverging
them would have reintroduced the split-skin problem item 6 existed to remove. Both comments now
warn against reintroducing element `opacity` — it would compound with the fill's alpha.

Untouched: `#weaponTargetingContainer` and `.shipSelectList` keep their own `opacity: 0.65`. Neither
uses the allegiance classes and neither was in question.

**2. The touch preview now ends when the press ends.** `onMouseOutShips` resets highlights and EW
but deliberately does NOT hide the tooltip — on the map that is the canvas mouse-out path's job, and
no canvas event fires when a finger lifts off a DOM row, so the tooltip sat there until the next map
tap. New `endTouchPreview()` calls `hideShipTooltip` explicitly.

> ⚠️ **Touch path only.** The same hide on desktop `mouseleave` would also kill the PERSISTENT
> targeting tooltip a click puts up (`showShipTooltip` with `hide=false`) the moment the cursor left
> the row. Touch is safe because `pointerup` runs BEFORE the click that creates such a tooltip —
> which the harness asserts by ordering, not just by outcome.

`longPressFired` still suppresses the click after a long press, so inspecting never commits a
selection.

### Feedback round 5 (2026-08-14) — the touch teardown, properly this time

Round 4's `endTouchPreview()` **was firing correctly** — and the tooltip still came back, because
something re-created it immediately afterwards.

⚠️⚠️ **After a tap or a long press the browser REPLAYS the gesture as simulated "compatibility"
mouse events** — `mouseover` / `mouseenter` on the row the finger landed on. Those re-ran the
DESKTOP hover handler after the touch teardown had already finished, which rebuilt the tooltip on
the spot. The same echo explains the second symptom: a row the finger had already left could be
re-highlighted by its own delayed echo while the next row lit up from a real `pointerdown`, so two
units showed heading/facing arrows at once — and it was intermittent, because whether the echo
arrives (and in what order) depends on the browser and on whether a long press suppressed it.

Fix, in the row handlers:
* `pointerenter` records `pointerType`. It fires BEFORE the compatibility events and carries the
  type they lack, so `mouseenter` / `mouseleave` can now tell a real hover from a finger's echo —
  and a hybrid device switching from screen to trackpad recovers on the next row entered.
* `mouseenter` / `mouseleave` return early when `isTouchInteraction()`. On touch, `pointerdown` is
  the only preview path and `pointerup` the only teardown, which makes the sequence deterministic.
* `endTouchPreview()` ignores a **stale** end: dragging from one row to the next can deliver row A's
  `pointerleave` after row B's `pointerdown` has started B's preview, and tearing down then would
  either cancel the new preview or, with the highlight reset landing between B's two calls, leave
  both rows lit.

> **Still by design, and a candidate if the doubling is ever seen again:**
> `showAppropriateHighlight()` re-applies `setSelected(true, true)` to the SELECTED ship, and on a
> coarse pointer `setSelected` shows its prow/movement sprites ("no hover on mobile"). So a selected
> ship legitimately keeps its arrows while another row is pressed. That is deterministic, not
> intermittent, and lives in `ShipIcon`, outside this picker.

### Feedback round 6 (2026-08-14) — all applied

**1. `ShipTooltip`'s inline styles moved into `shipTooltip.css`, and the rule under the name
now takes the name's own colour.** The tooltip's HTML string carried three `style="…"`
attributes — `.namecontainer`'s bottom rule and the two `TARGETING` / `INCOMING` section
headings. They are now four CSS rules; values came across unchanged, except
`text-decoration: bold`, which was dropped because `bold` is not a text-decoration value and
never did anything.

The section headings needed a class of their own: **`.fire` and `.ballistics` are each
carried by TWO divs** — the heading and the content beneath it — so a bare `.fire` rule
would have put a white rule and red text on the targeting readout as well. Hence
`tt-head`. The existing `.fire` / `.ballistics` / `.targeting` / `.incoming` classes are
untouched; they are what the show/hide code and `weaponManager.targetingShipTooltip` select on.

The rule under the name reads `border-bottom-color: var(--tt-name, var(--fv-neutral))`, and
`createForSingleShip` writes `--tt-name` on the root element from a new **`getNameColor()`**.
That function and `getNameStyle()` now share one gate, `usesTeamColor()`, so the line and the
text cannot drift: terrain and 2-team participants resolve to the allegiance token the CSS
class applies, observers and 3+-team games to the raw per-team `rgb()`. A **multi**-ship stack
has several allegiances and no single answer, so it keeps the neutral fallback.

> The static `#shipNameContainer` div at [game.php:570](source/public/game.php#L570) is a
> separate element with the same inline styles and **no JS consumer at all** — the new rules
> key on the CLASS, so it is untouched. Left alone deliberately; deleting dead markup is its
> own change.

**2. The picker's above/below choice is now STICKY** (`positionSelf`). `positionSelf()` runs
on every zoom step and every scroll, and it re-decided the side from scratch each time. Zoom
moves the hex in the viewport *and* changes `yOffset` (half the hex height, clamped 20–100),
so a card that had flipped below because it did not fit above would find it fitted again a
step later, jump up, and jump back on the next step — one continuous gesture, two different
layouts.

The first placement is unchanged (prefer above; the `!fitsAbove` test is algebraically the
old `top < EDGE_MARGIN`). After that the card keeps its side for as long as that side still
holds it, and needs `FLIP_SLACK` (24px) of **spare** room on the far side before moving, so a
hex parked on the boundary cannot oscillate between two marginal answers. Neither side
fitting keeps the current one and lets the clamp deal with it. Round 2 fixed the same class of
jump for fold/expand via `reflow()`; this is the zoom/scroll half.

**3. `createForMultipleShips` is a grid of silhouettes.** The hover tooltip for a stacked hex
was a run-on comma-separated list of names — the least useful shape the information has, since
the map has just shown the player those same silhouettes. It is now a wrapping grid of
`tt-stack__cell`s: the picker's **3px allegiance rail**, the ship's art at 34px (`--hp-art`,
so a unit does not change size between the two surfaces that appear side by side), and, for a
flight, its **active fighter count printed over the art**. The `Zoom closer, or click to
interact` line stays.

* **The masked-mine rule applies here too.** An unrevealed mine's `imagePath` still identifies
  the type that masking the name exists to hide, so it gets the generic ring-and-dot glyph —
  the same trap, and the same fix, as the picker's thumbnails. A thumbnail that fails to load
  falls back to the glyph rather than hiding, because in a grid with no names beside it an
  empty cell says nothing.
* Observers and 3+-team games get `--row-bar` / `--row-name` inline per cell, from a
  `getTeamColorVars()` that mirrors the picker's.
* Touch is unaffected — there is no hover on a touchscreen, so this surface only ever appears
  for a mouse.

**4. The replay-aware fighter count is now one function with two callers.**
`shipManager.systems.getActiveFighterCount` in `systems.js`; `SelectFromShips`' private
`countActiveFighters` is gone and both surfaces call it. It was about to be copied a third
time, and the replay arm is the subtle part: inside replay a fighter destroyed **on** the
viewed turn was still flying while that turn's combat happened and must still be counted.

`tokens.css`'s allegiance note said the hover tooltip was "not yet converted" — stale since
round 3, and now doubly so. Corrected.

### Feedback round 7 (2026-08-14) — applied

**1. The picker's flight count moved out of the name and onto the silhouette**, matching the
badge round 6 gave the hover tooltip's stack grid. The name line reads `Nial Flight`, not
`6 x Nial Flight`, and the `6` sits over the bottom-right of the art. The two surfaces appear
together — the picker places the tooltip beside itself — so they now say the same thing the
same way, and the row's most valuable line is spent on the name instead of on a number that
was pushing long names into an ellipsis.

* **This partially supersedes round 1, item 7.** That convention — count leads the name,
  sharing its font — still holds for a **collapsed run**: `13 x Mine` counts *units*, and the
  badge counts *fighters inside one unit*. They are different statements and both can appear
  on one row, so three identical 6-fighter flights read `3 x Nial Flight` with a `6` on the
  silhouette. `buildText` therefore keeps its `count` argument; real rows now pass `null`.
* **New element `.fv-hexpicker__thumb`** wraps the art, because an `<img>` cannot hold
  children and the badge needs something positioned to sit in. The reserved box moved to the
  wrapper; the art fills it at 100%.
  > ⚠️ `.fv-hexpicker__art` needed an explicit `display: block`. As a **direct flex item** its
  > `<span>` form (the generic mine glyph) was blockified for free, so `width`/`height`
  > applied; inside the wrapper it is an ordinary inline child, where they would not.
* The badge shrinks in the dense tier — at a 26px silhouette the comfortable size would cover
  over half of it.
* Gated on `count > 0`, which is the old truthiness test: a flight with no fighters left
  should not be on the map, and a bare `0` over the art would read as an alarm.

**2. The header and the group heads wear the rail too**, in `--fv-line-scs` — the card's own
border colour, not an allegiance signal, because neither belongs to a unit. The card now
reads as one column of stacked strips rather than as chrome sitting on top of a list.

A plain `border-left: 3px` rather than a pseudo-element: the header, the list, the groups and
the rows all start at the card's content edge with no left padding between them, so the rails
line up with no arithmetic. Each element's `padding-left` drops by 3px in exchange (header
10→7, group head 6→3), so the border adds to the rail rather than to the indent and **the
title and the fold caret do not move**.

> The DEPLOY / DOCK actions deliberately keep their 6px inset and no rail. They are buttons,
> not rows, and the inset is part of what says so.

### Local edit, kept

The header now reads `N units in hex - Click to Select` and the hint footer is commented out — the
guidance moved into the header and the `q·r` coordinates were dropped. That supersedes the §2.2
diagram below. The `.fv-hexpicker__hint` and `__coords` rules are left in `hexPicker.css`, inert,
since the JS is commented rather than deleted.

### Still outstanding

* **`img/openShipDetails2x.png` does not exist.** The `image-set()` is wired and a plain
  `url()` fallback precedes it, but until an 80×80 export of the same artwork lands, HiDPI
  users depend on the browser falling back to the 1× candidate. New filename only — never
  overwrite the 40×40 (see the comment on `.fv-hexpicker__details`).
* Stage 6, unchanged from §4 below.

---

## 1. What this menu is, and why it is the worst UI on the screen

`window.SelectFromShips` is the popup that appears when a click on the tactical map is
ambiguous. It has exactly two jobs:

* **(A) Disambiguate** — "several units are stacked here; which one did you mean?"
* **(B) Offer hex actions** — during Deployment, "place the selected unit here" / "dock it
  into one of the carriers here".

It is reached from four call sites:

| # | Call site | Condition | Ships passed |
|---|---|---|---|
| 1 | `PhaseStrategy.onShipsClicked` ([PhaseStrategy.js:323](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L323)) | >1 visible icon under the click, LoS ruler off | all visible ships in the stack |
| 2 | `DeploymentPhaseStrategy.onShipClicked` ([DeploymentPhaseStrategy.js:192](source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js#L192)) | LCV selected, clicked a friendly LCV-capable carrier with a free rail | `[thatOneShip]` |
| 3 | `DeploymentPhaseStrategy.onShipClicked` ([DeploymentPhaseStrategy.js:231](source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js#L231)) | mine/flight/LCV selected, clicked an already-placed ship on a legal, different hex | `[thatOneShip]` |
| 4 | `DeploymentPhaseStrategy.onShipsClicked` → super | stacked click in Deployment, no Shift bypass | all visible ships |

Note that in cases 2 and 3 the "picker" is showing a **one-item list** — its real payload is
the DEPLOY/DOCK buttons. That dual identity is the root of most of what is wrong with it.

### The concrete faults

1. **`opacity: 0.65` on the whole container.** Inherited from `.shipNameContainer`
   ([tactical.css:407-431](source/public/styles/tactical.css#L407)), which it shares with the
   *hover tooltip*. A hover tooltip should be see-through; a menu you are about to click
   must not be. Ship names sit at 65% over a moving starfield.
2. **Rows are bare coloured text.** `<div class="name value button">Sharlin</div>` — 16px
   text, no padding, no border, no `:hover` state, no cursor change on the ship rows (only
   `.button { cursor:pointer }` at [tactical.css:102](source/public/styles/tactical.css#L102)
   saves it). Nothing says "this is a list of choices".
3. **Tap targets are ~19px tall.** Guidance is 44px (iOS) / 48dp (Android). On a phone
   the rows are near-unhittable, and mis-taps on this menu commit a *deployment*.
4. **No identity beyond a name.** Two Nial flights, or a Sharlin and its escort, are two
   near-identical lines of red text. There is no class, no thumbnail, no size, nothing.
5. **Actions and choices are visually interleaved.** DEPLOY (orange) and DOCK (cyan) buttons
   are appended above the ship list with no separator and no heading; all three families are
   the same-sized bordered divs. The player must read to find out which is which.
6. **No frame, no header, no close.** You cannot tell where the popup ends. There is no ✕ —
   the only way out is to click the map, which on a touchscreen is also how you *place a unit*.
7. **No viewport clamping.** `positionElement` is `left = x - (w+30)/2`, `top = y - yOffset - h`
   with no bounds check ([SelectFromShips.js:333-351](source/public/client/UI/SelectFromShips.js#L333)).
   A stack near the top or side of the screen puts the menu partly off-screen; an 8-ship stack
   is ~200px tall and reliably clips.
8. **Right-click is the only route to the ship window**, and on a touchscreen there is no
   right-click. `webglScene`'s long-press→Shift+Click ([webglScene.js:232](source/public/client/renderer/webglScene.js#L232))
   is bound to the canvas, not to this DOM element, so mobile players simply cannot open a
   ship window from the picker.
9. **The hover preview is desktop-only.** Row `mouseover` calls `phaseStrategy.onMouseOverShip`,
   which highlights the icon on the map and raises the detail tooltip. It is genuinely useful
   and it is 100% unavailable on touch.

---

## 2. The design: an anchored **card** on desktop, a **sheet** on mobile

The organising idea is to stop treating this as a tooltip that happens to be clickable and
start treating it as what it is: **a context menu for a hex**. That single reframing decides
almost every open question below.

### 2.1 Surface

Adopt the `.confirm` recipe, not the tooltip recipe:

| Property | Tooltip (today) | Picker (proposed) | Why |
|---|---|---|---|
| fill | `--fv-overlay` @ `opacity:.65` | `--fv-overlay` @ `opacity:1` | you click it; it must be legible |
| border | none | `1px solid var(--fv-line-scs)` | it is a bounded object |
| radius | `--fv-radius-tooltip` (7px) | `--fv-radius-modal` (0px) | SCS skin; menus are chrome |
| z-index | 7001 | 7001 (unchanged) | above map, below `.confirm` |

This is the same argument `confirm.css` already makes for itself, recorded in
`VISUAL_UNIFICATION_PLAN.md`: *"`.confirm` deliberately takes the OPAQUE fill — a commit
dialog over a busy map must stay readable."* The picker commits deployments. It qualifies.

It also produces a welcome coherence: the picker's DOCK button can open
`confirm.hangarDeployDockCarrierPicker` ([confirm.js:4398](source/public/client/UI/confirm.js#L4398))
or `confirm.lcvDeployDockCarrierPicker` ([confirm.js:4445](source/public/client/UI/confirm.js#L4445)).
Today a translucent rounded tooltip spawns an opaque square dialog. After this they are one family.

> ⚠️ The picker must **stop using the `.shipNameContainer` class**. It shares it with
> `ShipTooltip` and with the static `#shipNameContainer` div at
> [game.php:563](source/public/game.php#L563). New namespace: **`.fv-hexpicker`**. This is
> what makes the restyle safe — no tooltip rule is touched, and the two surfaces stop
> being coupled by accident.

### 2.2 Desktop card

```
        ┌───────────────────────────────────────────────┐
        │  5 UNITS IN HEX  12·04                    ✕   │  27px header, --fv-text-dim,
        ├───────────────────────────────────────────────┤  coords in --fv-mono
        │  ▸  DEPLOY  NIAL FLIGHT                       │  32px, --fv-own       ┐ DEPLOYMENT
        │  ⊕  DOCK  NIAL FLIGHT          2 carriers     │  32px, --fv-accent    ┘ PHASE ONLY
        ├───────────────────────────────────────────────┤  --fv-rule divider
        │▏ ▨  Sharlin                         2     ▣  │  44px row
        │▏     War Cruiser                              │
        │▏ ▨  Nial Flight  ×5                 —     ▣  │
        │▏     Heavy Fighter · DEPLOYS T3               │
        │▏ ▨  Mine                            —     ▣  │
        │▏     UNREVEALED                               │
        ├───────────────────────────────────────────────┤
        │  Click to select · icon for details           │  21px hint, --fv-text-dim
        └────────────────────▼──────────────────────────┘  caret points at the hex
                            (hex)
```

The two action rows exist **only during Deployment** (`gamedata.gamephase == -1` with a unit
selected). For the rest of the game the card is header + list + hint — which is why they are
kept to 32px rather than being the visual centre of a surface they are usually absent from.

Anatomy of a row, left to right:

* **`▏` allegiance bar** — 3px full-height strip. Carries mine/ally/enemy/terrain, so
  allegiance is no longer encoded *only* in text colour (a real accessibility gain, and it
  survives at a glance when a name is long). Takes the **saturated** signal colour; see §2.5.
* **`▨` thumbnail** — 34×34, `window.AssetManager.getSmartImagePath(ship.imagePath)`. Same
  URL the map texture already fetched ([webglSprite.js:108](source/public/client/renderer/sprite/webglSprite.js#L108)),
  so it is an HTTP cache hit, not a new download. This is the single biggest win for job (A):
  the map shows you a silhouette, and now so does the menu.
  **Terrain art is included**, `object-fit: contain` inside the reserved box — planet and
  asteroid-field art is much larger and rounder than a ship sprite, so it must be fitted
  rather than cropped.
* **Name** — 14.5px, **tone-mapped** allegiance colour (§2.5); flights keep the existing `×N` count.
* **Class line** — 11px `--fv-text-dim`: `ship.shipClass`, then state chips.
* **Initiative order** — `--fv-mono`, right-aligned, `shipManager.getIniativeOrder(ship)`.
  This is the **movement group number**, not the raw initiative total: the same value the
  Order of Battle prints down its left edge
  ([gamedata.js:1846-1848](source/public/client/gamedata.js#L1846), `td.iniOrder`). Ships that
  move together share a number, which is the thing actually worth knowing in a stacked hex.

  **No column header** — the ~14px strip is not worth the vertical space in a surface that has
  to absorb 20-row stacks. The number explains itself **on desktop hover only**: a `title` on
  each value reading *"Initiative order — the movement group, as shown in the Order of Battle"*.
  Native `title` survives the container's `preventDefault` handlers (the UA drives it off hover
  state, not off the mouse events those cancel), so it needs no extra plumbing.

  > ⚠️ **Desktop only, and the touch path must stay completely inert.** Set the `title`
  > attribute **only on the fine-pointer path** (the same `useSheet` test from §2.4, inverted),
  > and give the INI cell **no touch handler of any kind** — no long-press, no
  > `stopPropagation`, no `preventDefault`. A press on the number must fall straight through to
  > the row, so it previews on press and opens the ship window on long-press exactly like every
  > other part of the row. The number is a 20px-wide target sitting next to the details button;
  > anything that made it swallow touches would eat real taps. (An earlier revision put a
  > long-press explainer here — dropped, user decision 2026-08-11.)

  > ⚠️ **`getIniativeOrder` returns `0` for terrain, mines and not-yet-deployed ships** — its
  > `validShips` filter excludes all three ([ships.js:736-758](source/public/client/ships.js#L736)),
  > so the loop never matches and the `return 0` tail fires. The picker must render **`—`**,
  > never `0`, whenever the call returns 0 or the unit is in one of those three categories.

  *Zero-cost alternative if the tooltip proves undiscoverable:* the hint footer already exists
  and is already one line — changing it to `Click to select · number = initiative order` buys
  permanent discovery for no extra height. One string, flip it either way.
* **Details button** — 30×30, containing `img/openShipDetails.png` at **20×20**. That is the
  icon players already know from the ship-tooltip menu (`.openSCS`,
  [shipTooltip.css:151](source/public/styles/shipTooltip.css#L151)); the source is 40×40, so
  20px is an exact 2:1 downscale and resamples cleanly. 40×40 button with a 24px icon on
  touch. Opens the ship window; right-click keeps working on the whole row.

  **HiDPI re-export.** Ship a **new file** `img/openShipDetails2x.png` at 80×80 — *the same
  artwork*, redrawn or upscaled, not a new icon; recognisability is the whole reason for
  reusing it — and reference it with `image-set()`:
  ```css
  background-image: image-set(url("../img/openShipDetails.png") 1x,
                              url("../img/openShipDetails2x.png") 2x);
  ```
  > ⚠️ **A new filename, never an in-place replacement.** `.htaccess` caches images for a
  > month and a CSS background cannot go through `AssetManager.getSmartImagePath()`, so
  > overwriting the existing 40×40 would reach returning players as the stale art for up to
  > that long. A new URL is cache-safe by construction.
  Check the 20px rendering against the 1× file first — if the SCS schematic still reads at
  that size, the `@2x` is a nicety and not a blocker.

Chips replace today's inline `<span class="not-deployed">`. Small caps, 8.5px, 1px border,
square. **Neutral by default** (`--fv-text-dim` on `--fv-line-soft`); only `DEPLOYS T<N>`
takes a tint (`--fv-warn`), because it is the one chip that is a genuine warning. `UNREVEALED`
and `TERRAIN` are statements of fact, not alarms.

### 2.3 Dense stacks — the 20-unit hex

A tall stack is where a picker either earns its keep or becomes a scroll hunt, so this is
designed in from the start rather than bolted on. Three mechanisms, **all keyed on row count
so there is no mode for the player to find**:

**(1) Grouping — always on when there is more than one category.** Rows are bucketed into
**Ships → Flights → Mines → Terrain**, in that fixed order, with the initiative sort preserved
*within* each bucket. A sticky header carries the category and its count (`MINES · 13`).

Headers appear **whenever two or more buckets are non-empty**, at any stack size (user
decision 2026-08-11 — the earlier 8-row gate is gone). A hex holding one ship and one asteroid
field gets two headers for two rows; that is accepted as the price of a consistent structure.

**Groups are collapsible and default to open.** The header is a button: click folds the group
to just its header, click again unfolds. Collapse state is **per-instance and not remembered**
— every time the picker opens, everything is open. (Remembering it across openings would mean
a player who folded "Mines" once could later open a picker with units silently hidden. If that
turns out to be wanted, it is a module-level map keyed on category, not a redesign.)

**(2) Identical-run collapsing.** If consecutive rows in a bucket would render *identically* —
same display label after masking, same class line, same chips, same allegiance, **same INI
value** — they are not a choice, they are a repetition. Runs of **3 or more** collapse into a
single `Mine ×13` row. **Clicking it expands the run in place** into its 13 real rows, which
stay expanded for the life of the picker.

Collapsing is **not** gated on total stack size — a run of 3 identical rows is one choice
whether the hex holds 5 units or 25. (Judgement call falling out of the always-on grouping
above; `COLLAPSE_RUN_MIN` is a knob if 3 turns out to be too eager.)

> ⚠️ The comparison key is the **rendered content**, not the ship type or phpclass. Two mines
> are "the same" only when the player would see the same row twice. A mine carrying
> `DEPLOYS T3`, or one sitting in a different INI group, stays its own row.
> ⚠️ The masked-mine rule from §3 applies to the collapsed summary too: a collapsed run of
> unrevealed mines shows the generic glyph and no class line.

#### What actually forms an identical run

Almost nothing except an **unscanned enemy minefield** — and that is by design, not by luck.
Three independent mechanisms keep everything else distinct, which is what makes this
mechanism safe to have on at all:

| Unit kind | Why it does *not* collapse |
|---|---|
| Individually-bought ships | The buy dialog takes a player-supplied name (`doBuyShip` reads the input at [gamelobby.js:3016](source/public/client/gamelobby.js#L3016)). |
| Bulk-bought mines and OSATs — **including your own mines** | The client sets `ship.name = ship.shipClass` only as a **stem**; the server numbers every minted copy — `Gravitic Mine #1`, `#2`, … — in `BuyingGamePhase::process` via `numberBulkCopy` ([BuyingGamePhase.php:415-425](source/server/Phase/BuyingGamePhase.php#L415)). Your own 13-mine field is 13 *distinct* names. |
| Ships and flights generally | They carry a real INI value, which is in the collapse key. Two units only tie when their `iniative` totals are exactly equal — so the INI column doubles as a disambiguator. |
| **Unrevealed enemy mines** | ⬅ **the one case.** Every name masks to the literal `"Mine"`, every mine's INI is `—`, and the class line is suppressed by the masking rule. Identical by construction. |

Terrain is the only theoretical extra: it also has INI `—` and takes its class as its name, so
several identical terrain markers in one hex would merge. Rare, and harmless if it happens.

The upshot worth keeping: **collapsing can essentially only fire on rows the player could not
have told apart anyway.** It is not a compression heuristic that might hide something needed;
it is a rule that removes literal duplicates — the current menu already prints those thirteen
indistinguishable "Mine" lines today.

**(3) Density tier.** Above **8 rows** the card switches to `.fv-hexpicker--dense`: a
single-line **34px** row with a **26px** silhouette and the class inline after the name
instead of beneath it. **On coarse pointers the row floor stays 44px regardless** — a mis-tap
here commits a deployment, so comfort loses to safety.

> **Grouping and density are now two independent thresholds**, which they were not in the
> first draft. Grouping keys on *category count* (≥2, always); density keys on *row count*
> (>8). Both are named constants next to the tuning knobs in §2.6 — `GROUP_MIN_CATEGORIES`,
> `DENSE_AT_ROWS`, `COLLAPSE_RUN_MIN` — so either can be retuned without touching the other.

Height is capped on the **list**, not the card (`60vh` desktop / `55vh` sheet), so the header,
the action buttons and the close never scroll away. Scroll shadows top and bottom.

Worked cases:

| Stack | Rows rendered | Height | Result |
|---|---|---|---|
| 3 ships (one category) | 3 comfortable, no headers | ≈190px | grouping needs ≥2 categories |
| 2 ships + 1 terrain | 2 headers + 3 comfortable | ≈235px | headers now appear at any size |
| 8 mixed | headers + 8 comfortable | ≈410px | last size before the dense tier |
| 20 = 4 ships + 2 flights + 13 mines + 1 terrain | 4 headers + 8 dense rows | ≈420px | fits with no scrolling at all |
| 20 distinct capital ships (worst case) | 20 dense rows, nothing collapsible | capped at 60vh | ~18 visible, scrolls; frame stays pinned |

### 2.4 Mobile / touch sheet

**"Breakpoint" here just means the rule that decides which of the two shapes you get.**
Proposed rule: use the sheet when the browser reports a **coarse pointer** (a finger rather
than a mouse) **or** when the window is **765px wide or less** — either condition alone is
enough. The width test catches small windows; the pointer test catches tablets that are wide
but still finger-driven. Everything else gets the anchored card.

```js
var useSheet = window.matchMedia('(pointer: coarse)').matches || window.innerWidth <= 765;
```

An anchored popover is the wrong form on a phone: the anchor is under the finger that
opened it, and there is no room above a hex near the screen edge. Dock to the bottom instead.

```
                     (map stays visible and un-occluded)
        ┌───────────────────────────────────────────────┐
        │             ══════════                        │  grab bar
        │  5 UNITS IN HEX 12·04                     ✕   │  32px
        ├───────────────────────────────────────────────┤
        │   DEPLOY NIAL FLIGHT                          │  40px
        │   DOCK NIAL FLIGHT              2 carriers    │  40px
        ├──────────────────────────────────────── INI ──┤
        │▏ ▨  Sharlin                         2     ▣  │  52px rows,
        │▏     War Cruiser                              │  list scrolls,
        │▏ ▨  Nial Flight ×5                  —     ▣  │  max-height 55vh
        └───────────────────────────────────────────────┘
                                  ↑ bottom: env(safe-area-inset-bottom)
```

* `left: 8px; right: 8px; bottom: calc(8px + env(safe-area-inset-bottom))` — never clipped,
  never off-screen, always thumb-reachable.
* `reposition()` becomes a **no-op in sheet mode**. `repositionSelectFromShips` is called on
  every zoom and scroll ([PhaseStrategy.js:653](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L653));
  a sheet that re-anchored on pinch-zoom would jump around under the finger.
* Rows 52px; details button 40×40 with the icon at 24px; actions 40px.

### 2.5 Colour — put the chroma where it is small

The current menu asks **eight fully saturated hues** to share one small black box: `limegreen`,
pure `red`, `#33adff`, near-white `#dedede`, `orange`, `cyan`, white body text and the rust
`#b34119` "not deployed". Nothing recedes, so nothing leads.

**The semantic mapping does not change** — green is still yours, blue still allied, red still
hostile, neutral still terrain, and `getAllyClass` / `getTeamColorStyle` keep deciding which is
which. What changes is that each allegiance is split into **two** renderings:

* the **3px bar takes the saturated signal**, where a strong colour reads as a signal precisely
  because there is so little of it;
* the **name takes a tone-mapped version** — same hue, lower chroma, lightness normalised so
  all four sit at the same perceived brightness instead of red shouting and blue sinking.

| Allegiance | Bar (signal) | Name (tone-mapped) | Was |
|---|---|---|---|
| own | `#52b352` | `#7dbf88` | `limegreen` |
| ally | `#33adff` | `#79aed4` | `#33adff` |
| enemy | `#e0483f` | `#d1867e` | `red` |
| terrain | `#8ca5c0` | `#b4c2cf` | `#dedede` |

These are promoted into `tokens.css` at **Stage 6** (§4) as `--fv-own` / `--fv-ally` /
`--fv-enemy` / `--fv-neutral` and their `--fv-*-signal` twins. Until that stage they live as
component-scoped `--hp-*` properties on `.fv-hexpicker`, so Stages 0-5 cannot touch any other
surface. Nothing in the picker changes at promotion except the property names.

Four supporting rules finish the job:

* **Action buttons on the same set.** DOCK takes `--fv-accent`. **DEPLOY takes the own-ship
  green** (`--fv-own`, the tone-mapped `#7dbf88`, *not* the saturated `#52b352` — a full-chroma
  green at button size would out-shout the rows, which is the same reasoning that put the
  strong colour on the 3px bar). This is coherent rather than merely quieter: DEPLOY places
  *your own* unit, so it wears your own colour, and the two actions now read as green = put it
  on the map / blue = put it in a carrier. Literal `orange` and `cyan` are both retired.
* **Chips go neutral — and `DEPLOYS T<N>` keeps the amber.** With `orange` gone from the
  button, `--fv-warn` now appears **exactly once** in the whole card, and it means one thing:
  this unit is not on the board yet. `UNREVEALED` and `TERRAIN` become dim text in a soft
  border. Two hues gone, and the one that stays gets sharper.
  > ⚠️ **Assumption flagged.** The instruction "the orange Deploy no longer needs to stand out"
  > was read as applying to the **button**, leaving the chip amber. If the chip was meant too,
  > it is a one-line change — but green would be wrong there (it would put "yours" colouring on
  > an enemy row that happens to be deploying late), so the chip's fallback is neutral, not green.
* **No pure white anywhere.** Header, hint and secondary text run on `--fv-text-dim`; a hovered
  row name lifts only as far as `--fv-text` (`#deebff`). White was the loudest thing in the old
  box and was carrying no meaning at all.
* **Per-team colours get the same treatment.** For observers and 3+-team games,
  `getTeamColorStyle` writes the raw team RGB to the **bar** and a tone-mapped version to the
  **name**, via a new shared helper:

  ```js
  // gamedata.js, beside getIniTeamColorRGB — same shape: a per-surface transform of the
  // raw palette. Clamps saturation and lightness so an arbitrary team colour lands in the
  // same brightness band as the four fixed tints above.
  MUTED_TEAM_SAT: 0.40,
  MUTED_TEAM_LIGHT: 0.68,
  getMutedTeamColorRGB: function (team) { /* rgb → hsl → clamp S/L → rgb */ }
  ```

  There is direct precedent for a per-surface transform: the Order of Battle already runs a
  deliberately *darkened* team palette via `getIniTeamColorRGB` for exactly this reason — see
  [gamedata.js:1832-1839](source/public/client/gamedata.js#L1832), *"so it isn't brighter than
  the muted CSS participant colours"*. Writing the helper here rather than inline is what makes
  Stage 6 nearly free: the fleet header needs the same function.

### 2.6 Tuning knobs — one block, editable without touching components

Every geometry value that anyone will want to nudge after seeing it in-game lives in **one
commented block** at the top of `hexPicker.css`, and nothing else in the file hard-codes a
size. This mirrors the `theme.hud` convention the project already runs on — *"these are the
tuning knobs — nudge them, not the components"* (`VISUAL_UNIFICATION_PLAN.md`).

```css
.fv-hexpicker {
  /* ── TUNING KNOBS ────────────────────────────────────────────────────────
     Adjust here. No component rule below re-declares any of these.          */
  --hp-act-h:        32px;   /* action button height (Deployment only)       */
  --hp-row-h:        44px;   /* comfortable row                              */
  --hp-row-h-dense:  34px;   /* dense row, engages above 8 units             */
  --hp-art:          34px;   /* silhouette box, comfortable                  */
  --hp-art-dense:    26px;
  --hp-icon-btn:     30px;   /* details button box                           */
  --hp-icon:         20px;   /* details icon inside it (2:1 of the 40px art) */
  --hp-card-w:      320px;
  --hp-list-max:     60vh;   /* scroll cap on the LIST, not the card         */
  --hp-dense-at:        8;   /* documentation only — the JS threshold        */
}
.fv-hexpicker--sheet {
  --hp-act-h:        40px;
  --hp-row-h:        52px;
  --hp-row-h-dense:  44px;   /* the coarse-pointer floor — do not go below 44 */
  --hp-icon-btn:     40px;
  --hp-icon:         24px;
  --hp-list-max:     55vh;
}
```

> ⚠️ `--hp-row-h-dense` in sheet mode is the **44px coarse-pointer floor**, not a taste value.
> A mis-tap in this menu commits a deployment. Anything below 44px there is a correctness
> regression, not a density preference.
>
> ⚠️ `--hp-dense-at` is a comment, not a live value — CSS cannot count rows. The real threshold
> is the row-count constant in `SelectFromShips.js`; keep the two in step or the note lies.

### 2.7 Interaction contract

| Input | Desktop | Touch |
|---|---|---|
| hover row | preview: `onMouseOverShip` (map highlight + detail tooltip) | — |
| press row | — | **preview on `pointerdown`** — same highlight, no extra tap |
| click / tap row | existing click handler, verbatim | same |
| right-click row | `onShipRightClicked` (ship window) | **long-press row (500ms)** → same |
| details button | `onShipRightClicked` | `onShipRightClicked` |
| hover INI value | `title` explains the movement group | **nothing — fully inert, press falls through to the row** |
| group header | folds / unfolds that group (default open) | same |
| collapsed run | expands in place | same |
| click map | closes (existing `onClickCallbacks`) | same |
| `✕` | closes | closes |
| `Esc` | closes | — |

"Press to preview, lift to select" is the whole hover interaction recovered on touch for
zero extra taps: bind the highlight to `pointerdown` and the action stays on `click`.
The 500ms long-press duration deliberately matches `webglScene`'s canvas long-press so the
gesture means the same thing everywhere on the screen.

### 2.8 Deliberately **not** included

Listed so they are decisions, not omissions: health/`combatValue` bar (needs per-ship
computation the picker has never done, and does not help answer "which one did I mean"),
drag-to-move, pinning the picker open, multi-select, weapon/EW readouts (that is
`ShipTooltip`'s job and duplicating it is how the two got confused in the first place).

---

## 3. Functional inventory — the no-loss contract

Everything below exists today and **must still work identically** afterwards. Treat this as
the acceptance checklist; each line is a thing that will silently break if the rewrite is
careless.

### Actions block (Deployment phase only, `gamedata.gamephase == -1` **and** a selected ship)

- [ ] **DEPLOY `<NAME>`** appears only when *all* of: no terrain and no `Huge 1-3` in the hex;
      the hex is not blocked by a non-mine/non-flight ship (LCVs exempt); the position passes
      `window.validateDeploymentPositionForShip`; the selected unit is not already in that hex.
      → `phaseStrategy.onHexClicked(this.payload)` then `destroy()`.
      (Logic at [SelectFromShips.js:72-117](source/public/client/UI/SelectFromShips.js#L72) —
      **copy it across unchanged**; it encodes several hard-won rulings.)
- [ ] **DOCK `<FLIGHT>`** when a flight is selected and the clicked ship(s) include friendly
      carriers with capacity. Eligibility falls back from `eligibleHangarsForFlight` to
      `distributeFlightAcrossHangars` (combined pool across bays) and reports **real free
      boxes** via `hangarFreeBoxes`, not the allocated slice.
      1 carrier → `autoQueueDockOnCarrier` → `refreshDeploymentUIForDeployStart()` →
      `selectShipInDeploymentPhase(carrier)`. >1 → `confirm.hangarDeployDockCarrierPicker`.
      Label carries `(N CARRIERS AVAILABLE)`.
- [ ] **DOCK `<LCV>`** when an LCV (`hangarRequired === 'lcvs'`) is selected and an
      LCV-capable carrier with a free rail is in the list. Exactly 1 free rail on exactly
      1 carrier → `queueLcvDeployDock` + same refresh/select pair. Otherwise →
      `confirm.lcvDeployDockCarrierPicker`.
- [ ] All three buttons `destroy()` the picker after acting.
- [ ] Every `window.DeploymentDock` / `window.confirm` call stays behind its existing
      `typeof … === 'function'` guard.

### Ship rows

- [ ] Sorted by `shipManager.hasWorseInitiveSort` (highest initiative first).
- [ ] Flights show `(N)` where N is the **replay-aware** active fighter count: inside replay,
      count by `damageManager.getTurnDestroyed(ship, ftr) === null || >= gamedata.turn`;
      outside replay, `!shipManager.systems.isDestroyed`. Copy verbatim
      ([SelectFromShips.js:257-276](source/public/client/UI/SelectFromShips.js#L257)).
- [ ] An unrevealed mine displays the literal name **"Mine"** (`mineStealth.isMineRevealed`).
- [ ] `(Deploys Turn N)` when `shipManager.getTurnDeployed(ship) > gamedata.turn`.
- [ ] Allegiance colour via `getAllyClass` → `terrain` / `mine` / `ally` / `enemy`, **and**
      the inline per-team override from `getTeamColorStyle` for observers and 3+-team games.
      Both functions move across **byte-identical** — they are one arm of the single
      team-colour gate used across the whole UI.
- [ ] Row click, **Deployment**: `deselectShip(selected)` → `selectShip(ship, payload)` → `destroy()`.
- [ ] Row click, **all other phases**: `onShipClicked(ship, payload)`, then `destroy()` **only
      if** `selectedShip.id === ship.id` afterwards. (This is what keeps the picker open when
      the click was a *targeting* action rather than a selection.)
- [ ] Row `mouseover` → `onMouseOverShip(ship, payload)`; `mouseout` → `onMouseOutShips(ship, payload)`.
- [ ] Row `contextmenu` → `preventDefault` + `stopPropagation` + `onShipRightClicked(ship, payload)`.

### Container behaviour

- [ ] `mousedown` / `mouseup` / `mousemove` on the container are `preventDefault`ed, and
      `mouseover` / `mouseout` additionally `stopPropagation` — this is what stops a drag
      that starts on the popup from panning the map and stops the hover tooltip flickering
      underneath it. **Keep it**, but scope it so the list can still scroll.
- [ ] `positionElement` accepts either a `hexagon.Offset` (→ `fromHexToViewport`) or a game
      position (→ `fromGameToViewPort`); `yOffset` clamped to `[20, 100]`.
- [ ] `reposition(position?)` returns `true` (the `onZoomCallbacks` filter keeps callbacks
      that return truthy — returning `undefined` would silently unregister it).
- [ ] `destroy()` removes the element and is safe to call twice.
- [ ] Closes on the next map click via the `onClickCallbacks` entry pushed by
      `showSelectFromShips`, and on Commit via
      [PhaseStrategy.js:174](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L174).
- [ ] Constructor signature `(selectedShip, ships, payload, phaseStrategy)` and the public
      surface `show / reposition / destroy / update / addEntryElement` are unchanged.
      (`update` is a no-op and `addEntryElement` is unreferenced today — keep both as
      harmless stubs rather than deleting them.)

### ⚠️ Four traps specific to *this* redesign

1. **The thumbnail must not leak a masked mine.** When the name has been masked to "Mine",
   `ship.imagePath` and `ship.shipClass` still identify the mine type. Rule: **if the name was
   masked, suppress the class line and render a generic mine glyph instead of the art.** The
   existing text masking is the only reason this is safe today; adding art without adding this
   rule reopens the hole. (See the info-bleed masking notes — the picker sits downstream of
   `shouldBeHidden`, which already removes undetected stealth ships entirely, so mines are the
   one remaining masked-but-present case.) The rule applies to a collapsed run as well.
2. **Reserve the thumbnail box in CSS** (`width:34px;height:34px` on the `<img>`, 26px dense).
   The card is positioned from its measured height immediately after append; if art loads later
   and grows a row, the card silently drifts off its anchor.
3. **`getIniativeOrder` returns `0`, not `null`, for terrain / mines / undeployed ships.**
   Print `—`. A literal `0` in the INI column is wrong *and* looks like a real movement group.
4. **Collapsing must never merge rows that differ.** The comparison key is the *rendered*
   content — masked label + class line + chips + allegiance — not the ship type or phpclass.
   Two mines are only "the same" when the player would see the same row twice.

---

## 4. Implementation stages

Each stage is independently shippable and independently revertible.

### Stage 0 — guard rails (no visual change)

* Add `var USE_CARD_UI = window.FV_LEGACY_SHIP_PICKER !== true;` at the top of the IIFE and
  split today's `create()` into `renderClassic()` (moved verbatim) and the new `renderCard()`.
* Switch the root element from `.shipNameContainer` to `.fv-hexpicker`, and add a temporary
  `.fv-hexpicker--classic` rule in the new stylesheet that reproduces the inherited
  `.shipNameContainer` box exactly, so Stage 0 is pixel-identical.
* Create `source/public/styles/hexPicker.css` and link it from `game.php` **through
  `AssetLoader::getAssetUrl()`** next to the other five links
  ([game.php:74-78](source/public/game.php#L74)). No `:root` block in it — `tokens.css` is the
  only one, site-wide.

Ship this, confirm nothing moved, then continue. The `window.FV_LEGACY_SHIP_PICKER = true`
console escape hatch stays available for the whole project and is removed at Stage 5.

### Stage 1 — the desktop card

* Header (`N UNITS IN HEX q·r` + `✕`), actions block, `INI` column head, row list, hint footer.
* Rows built from the inventory in §3; allegiance drives **two** CSS custom properties, so one
  class carries both renderings (§2.5):

  ```
  .fv-hexpicker__row.mine    { --row-name: var(--hp-own);     --row-bar: var(--hp-bar-own); }
  .fv-hexpicker__row.ally    { --row-name: var(--hp-ally);    --row-bar: var(--hp-bar-ally); }
  .fv-hexpicker__row.enemy   { --row-name: var(--hp-enemy);   --row-bar: var(--hp-bar-enemy); }
  .fv-hexpicker__row.terrain { --row-name: var(--hp-terrain); --row-bar: var(--hp-bar-terrain); }
  .fv-hexpicker__name        { color: var(--row-name); }
  .fv-hexpicker__bar         { background: var(--row-bar); }
  ```

  `getTeamColorStyle` then writes `style="--row-bar:rgb(raw); --row-name:rgb(toned)"` **on the
  row** instead of `color:` on the name — inline wins over the class, and both follow. Same gate
  condition as today; only the delivery and the tone-map are new.
* Action buttons: DEPLOY on `--fv-warn`, DOCK on `--fv-accent`, **32px** tall (40px in the
  sheet). Today they are literal `orange`/`cyan` at 14px with 2px padding
  ([tactical.css:473-495](source/public/styles/tactical.css#L473)). Remember these exist **only
  during Deployment** — for most of a game the card is header + list + hint.
* `positionElement` rewrite:
  1. hex → viewport (unchanged), then measure with `getBoundingClientRect()` **after** append;
  2. prefer above the hex; if `top < 8`, flip below;
  3. clamp `left` into `[8, innerWidth − w − 8]` and `top` into `[8, innerHeight − h − 8]`;
  4. position the caret at `clamp(12, anchorX − left, w − 12)` so it still points at the hex
     after clamping (hide it if the anchor is outside the card).
* `max-height: 60vh` with the **row list** scrolling, not the whole card — the header, actions
  and hint stay pinned.

### Stage 2 — density and grouping

The three mechanisms in §2.3: bucketing with sticky counted headers, identical-run collapsing,
and the `--dense` tier above 8 rows with the 44px coarse-pointer floor. Built here rather than
saved for polish, because a crowded hex is where the old menu fails worst and the new one has
the most to prove — and because the row markup is easiest to make count-aware while it is still
new.

### Stage 3 — sheet mode

* The §2.4 rule; `.fv-hexpicker--sheet` modifier applied in JS at construction (so
  `reposition()` can early-return) *and* mirrored in CSS.
* Grab bar, safe-area inset, 40px actions, 52px rows, 40px details button.
* Slide-up transform, wrapped in `@media (prefers-reduced-motion: reduce)` per the existing
  convention at [tactical.css:647](source/public/styles/tactical.css#L647).

### Stage 4 — touch parity

* `pointerdown` on a row → `onMouseOverShip` when `event.pointerType !== 'mouse'`.
* 500ms long-press on a row → `onShipRightClicked`, cancelled by >10px movement or lift;
  `navigator.vibrate(30)` if available, matching `webglScene.touchstart`.
* Verify list scrolling still works — the container's `preventDefault` handlers must not
  cover `touchmove` (they do not today; do not add it).

### Stage 5 — keyboard, a11y and cleanup

* `Esc` closes. Bind on the **document** and check `window.Settings.matchEvent` does not
  already claim the key before wiring it, so no shortcut is shadowed.
* `role="menu"` on the card, `role="menuitem"` on rows and actions, `tabindex` roving,
  `↑`/`↓` to move, `Enter`/`Space` to activate, focus trapped while open and restored on close.
* `aria-label` on `✕` and the details button; `:focus-visible` outline in `--fv-accent`.
* 120ms fade-in (reduced-motion aware), row hover `--fv-card-hover`.
* Delete `renderClassic()`, the `USE_CARD_UI` flag and the `.fv-hexpicker--classic` rule.
* Optional: audit whether `.name-value-button-ally` / `.name-value-button-dock` in
  `tactical.css` still have consumers (`confirm.js` uses `-ally`, so it stays) and whether the
  now-unused `.shipNameContainer .fire` rule can go.

### Stage 6 — promote the allegiance set to `tokens.css`, apply to the fleet list

**The final stage, and the only one that changes a surface outside the picker.** Ship it
separately, after the picker has settled in play.

**1. Promote.** Move the eight values from `.fv-hexpicker`'s `--hp-*` block into the single
`:root` in `tokens.css`, in a new `── Allegiance ──` group beside `── Status ──` (they are
signals, not chrome — same category):

```css
/* Text tints — equal perceived lightness, for NAMES and labels on a dark ground. */
--fv-own:     #7dbf88;
--fv-ally:    #79aed4;
--fv-enemy:   #d1867e;
--fv-neutral: #b4c2cf;
/* Full-chroma twins — for BARS, rules and other small marks only. */
--fv-own-signal:     #52b352;
--fv-ally-signal:    #33adff;
--fv-enemy-signal:   #e0483f;
--fv-neutral-signal: #8ca5c0;
```

Then point `.fv-hexpicker` at them. Nothing about the picker's appearance changes — this is a
rename.

**2. Apply to the fleet list.** The scope here is far smaller than it sounds, and that is worth
knowing before starting: **the fleet list does not colour its ship rows by allegiance at all.**
`.fleetlist .shipname` / `.shipclass` / `.initiative` are plain text
([tactical.css:1027-1066](source/public/styles/tactical.css#L1027)), and `.headername` /
`.playername` are a flat `#deebff`. The *only* allegiance-coloured element is the
`TEAM X - ` label, styled inline from `gamedata.getFleetHeaderColorRGB(slot)` at
[fleetList.js:282](source/public/client/UI/fleetList.js#L282).

So the whole change is inside `getFleetHeaderColorRGB`
([gamedata.js:424-439](source/public/client/gamedata.js#L424)):

* the three 2-team literals `[50,205,50]` / `[51,173,255]` / `[255,80,80]` become the
  tone-mapped own/ally/enemy values;
* the observer / 3+-team branch changes `getTeamColorRGB(slot.team)` →
  `getMutedTeamColorRGB(slot.team)`, the helper already written at Stage 1.

**`getFleetHeaderColorRGB` has exactly one live consumer.** The only other reference,
[ShipWindow.js:2092-2097](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L2092), is
inside a `/* … */` block. Confirm that is still true before editing (`grep -rn
getFleetHeaderColorRGB --include=*.js`), then this is a three-value edit with one caller.

**3. Do NOT touch the combat log** (user decision, 2026-08-11). `gamedata.getShipLogColorCss`
([gamedata.js:450-461](source/public/client/gamedata.js#L450)) keeps its own literals. It is a
sibling function with the same shape, so the temptation to "finish the job" is real — resist
it. Leave a one-line comment in each function pointing at the other and saying which one has
been converted, so the next person does not read the divergence as a bug.

**4. Also NOT in scope:** `.enemy.name` / `.mine.name` / `.ally.name` / `.terrain.name` at
[tactical.css:455-471](source/public/styles/tactical.css#L455). Those belong to `ShipTooltip`
and `.shipSelectList`, not the fleet list. Leaving them means **the hover tooltip keeps its
bright `limegreen` / `red` while the picker beneath it runs muted** — a visible inconsistency
between two surfaces that appear together. That is a deliberate consequence of the chosen
scope; converting them is four lines in the same stage whenever you want it.

**Verification for this stage specifically:** fleet list header in a 2-team game as each of the
three roles; in a 3-team game; as an observer; and a screenshot diff of the combat log proving
it did **not** move.

---

## 5. Risk register

| Risk | Mitigation |
|---|---|
| Restyling breaks `ShipTooltip` | The picker leaves the shared `.shipNameContainer` class at Stage 0 and never touches tooltip rules again. New file, new namespace. |
| A deploy/dock eligibility rule is lost in the rewrite | The eligibility blocks move across **verbatim**; only the DOM they emit changes. §3 is the line-by-line checklist. |
| Card drifts off its anchor when art loads | Fixed `34×34` on the `<img>` reserves the box before load. |
| Sheet jumps during pinch-zoom | `reposition()` early-returns in sheet mode. |
| Map pans when dragging on the popup | Container-level `preventDefault` handlers kept as-is. |
| A masked mine is identified by its thumbnail | Explicit rule in §3: masked name ⇒ generic glyph, no class line — collapsed runs included. |
| A 20-unit stack becomes a scroll hunt | §2.3: grouping + identical-run collapsing + the dense tier. The worked cases put a realistic 20-stack at ~420px with no scrolling. |
| Collapsing hides a unit the player needed | Only rows that would render *identically* collapse, and one click expands the run in place. |
| `0` printed in the INI column | `getIniativeOrder` returns `0` for terrain/mines/undeployed — render `—`. |
| Regression only visible in one phase | Verification matrix in §6 covers all four call sites × the phases that reach them. |
| Player hits a blocker mid-release | `window.FV_LEGACY_SHIP_PICKER = true` in the console restores the old menu without a rebuild, until Stage 5. |

**Not at risk:** nothing here is serialised, so the replay regression harness is not
implicated — no snapshot re-record, no `check` run needed. This is a pure client-render change.

---

## 6. Verification

Build with `fvbuild.ps1` (the picker is inlined into `game.legacy.bundle.js` by
`scripts/bundle-legacy.js` from the `<script>` tag at [game.php:348](source/public/game.php#L348);
the new stylesheet is not bundled and just needs the AssetLoader link).

Test at **1920×1080**, a **phone width (≤420px)**, and **landscape phone** (`max-height:500px`).

| # | Scenario | Expect |
|---|---|---|
| 1 | Fire phase, click a hex with 3+ stacked ships | Card lists all, initiative order, thumbnails, no actions block |
| 2 | Same, click an enemy row | Targeting fires; card **stays open** |
| 3 | Same, click a friendly row | Selection changes; card closes |
| 4 | Hover a row | Icon highlights on map + detail tooltip appears |
| 5 | Right-click a row / press the details button | Ship window opens |
| 6 | Deployment, flight selected, click occupied legal hex | DEPLOY + row list; DEPLOY places the flight |
| 7 | Deployment, flight selected, hex with **one** eligible carrier | DOCK docks immediately, selection moves to the carrier |
| 8 | Deployment, flight selected, hex with **two** eligible carriers | DOCK opens `hangarDeployDockCarrierPicker` |
| 9 | Deployment, LCV selected, click LCV carrier (1 free rail) | DOCK (blue) queues the rail directly |
| 10 | Deployment, LCV selected, carrier with 2+ free rails | Opens `lcvDeployDockCarrierPicker` |
| 11 | Deployment, terrain in the hex | **No** DEPLOY button |
| 12 | Deployment, unit already in that hex | **No** DEPLOY button |
| 13 | Shift+click (and touch long-press on the map) | Bypasses the picker entirely, as today |
| 14 | Stack at the very top / left / right screen edge | Card clamps into view; caret still points at the hex |
| 15 | 9-unit stack | Dense tier engages (grouping already on from 2 categories) |
| 16 | 20-unit stack — 4 ships, 2 flights, 13 mines, 1 terrain | 4 headers + 8 rows, mines collapsed to `Mine ×13`, **no scrolling** |
| 17 | Expand the collapsed mine run | 13 separate rows, list scrolls, frame stays pinned |
| 18 | 20 distinct ships (nothing collapsible) | Caps at 60vh, ~18 visible, header/actions/hint pinned |
| 19 | 20-unit stack on a phone | Sheet caps at 55vh, rows stay ≥44px, no horizontal overflow |
| 20 | Zoom and scroll with the card open | Card re-anchors (desktop) / stays docked (sheet) |
| 21 | Commit with the picker open | Picker closes |
| 22 | Unrevealed mine in the stack | Reads "Mine", generic glyph, **no** class line |
| 23 | Terrain and mines in the list | INI column reads `—`, never `0` |
| 24 | Two ships with the same initiative | Both print the **same** INI number (they move together) |
| 25 | Ship deploying next turn | INI reads `—` and the row carries the amber `DEPLOYS T<N>` chip |
| 26 | 3-team game and observer view | Raw team colour on the bar, tone-mapped on the name |
| 27 | Replay mode, flight that partially docked this turn | `×N` matches the pre-dock count |
| 28 | Touch: press a row and hold | Map highlight appears, then ship window at 500ms |
| 29 | Desktop: hover the INI number | Native tooltip explains it is the movement group |
| 30 | Touch: press and long-press **on the INI number** | Behaves exactly as pressing anywhere else on the row — preview, then ship window. No `title`, no swallowed tap |
| 30b | Two-category hex (e.g. 1 ship + 1 terrain) | Two headers appear even at 2 rows |
| 30c | Single-category hex | **No** headers |
| 30d | Fold a group, then fold every group | Rows hide, counts remain, card shrinks; nothing else moves |
| 30e | Close and reopen the picker | Every group is open again (state is per-instance) |
| 30f | Own 13-mine field (bulk-bought) | 13 **separate** rows — `Gravitic Mine #1…#13`, no collapse |
| 30g | Enemy unscanned 13-mine field | Collapses to `Mine ×13`; clicking expands to 13 rows |
| 31 | Stage 6 only: fleet list header, 2-team as each role, 3-team, observer | Tone-mapped `TEAM X` label |
| 32 | Stage 6 only: combat log | **Unchanged** — screenshot diff proves it did not move |

---

## 7. Decisions (settled 2026-08-11)

1. **Action button hues** — DOCK `--fv-accent`; **DEPLOY the own-ship green `--fv-own`**, not
   amber. Literal `orange` and `cyan` both retired. Amber then survives in exactly one place,
   the `DEPLOYS T<N>` chip, which sharpens it (§2.5).
2. **Terrain thumbnails** — included, `object-fit: contain` in the reserved box.
3. **Sheet breakpoint** — coarse pointer **or** ≤765px; either alone is enough (§2.4).
4. **Initiative** — the movement group number from `getIniativeOrder`, not the raw initiative
   total; `—` where that returns 0. **No column header**; explained by a `title` tooltip on
   **desktop hover only**. On touch the INI cell is completely inert and the press falls
   through to the row (§2.2) — the earlier long-press explainer is dropped as too fiddly on a
   20px target sitting next to the details button.
5. **Details button** — the existing `img/openShipDetails.png` at 20×20, plus an `@2x`
   re-export **under a new filename** for HiDPI. Same artwork — recognisability is the point.
6. **Action button height** — 32px desktop / 40px sheet, and *all* geometry lives in the one
   tuning-knob block in §2.6 so it can be nudged after the first build without touching a
   component rule.
7. **Colour** — the tone-map in §2.5: saturated on the 3px bar, muted on the name, chips
   neutral except `DEPLOYS`, no pure white.
8. **Token promotion** — **yes**, as Stage 6, the final stage: the eight values move into
   `tokens.css` and the **fleet list** header adopts them. The **combat log does not**, for now.
9. **Grouping** — on whenever **two or more categories** are present, at any stack size; the
   8-row gate is gone. Groups are **collapsible, default open**, state not remembered between
   openings. Density (>8 rows) and identical-run collapsing (runs of ≥3) are now separate
   thresholds from grouping and from each other (§2.3).
10. **Identical runs** — clicking a collapsed run expands it in place. Investigated (§2.3,
    *What actually forms an identical run*): the only real case is an **unscanned enemy
    minefield**. Bulk purchases are server-numbered `#1, #2, …`, individually-bought ships take
    a player-supplied name, and the INI value is part of the collapse key — so the mechanism
    can essentially only fire on rows that were already indistinguishable.

### Flagged assumption — RESOLVED 2026-08-14

The instruction that the orange DEPLOY "no longer needs to stand out" was meant to cover the
`DEPLOYS T<N>` **chip** as well. **All chips are neutral** (`--fv-text-dim` on
`--fv-line-soft`); `--fv-warn` now appears nowhere in the card at all, and allegiance is the
only colour in it that carries meaning. §2.2 and §2.5 above are superseded on this point.

### Still open

* `img/openShipDetails2x.png` — the `image-set()` is wired and waiting for the 80×80 export.
  The only outstanding item.

**The combat log** (`gamedata.getShipLogColorCss`) remains on its own bright literals by decision,
not by omission — it is a dense scrolling wall of text where the stronger colours still earn their
place. Both it and `getFleetHeaderColorRGB` carry a comment pointing at the other.
