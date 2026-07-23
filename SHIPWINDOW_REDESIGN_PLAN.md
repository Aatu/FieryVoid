# Ship Window Redesign & Legacy Retirement Plan

**Status: DESIGNED 2026-07-14. Stage 1 COMPLETE — user-accepted 2026-07-16 after
five feedback rounds tested in gameid 4247 (all of 1a–1e, rolled-ship mirroring,
and the round-1–5 refinements recorded below). Stage 2 COMPLETE — exit tests
passed 2026-07-17 (see the Stage 2 record below). Stage 3 COMPLETE —
user-accepted 2026-07-17 after five in-lobby feedback rounds (records below).
Stage 4 BUILT 2026-07-18 (retirement sweep + 2 refinements — record below),
awaiting bundle rebuild + user test.**

**Stage 4 (2026-07-18) — BUILT, awaiting user test.** User rider: NOTHING is
deleted — every retired piece is commented out in place under a single
greppable marker, **`STAGE4-RETIRED`**, so the whole sweep can be found (and
then really deleted) once the redesign has proven stable on live. Actual file
deletion is now a deliberate future step, not part of this stage.
- **Script tags / CSS links / templates commented out** (not removed):
  - game.php: `styles/shipwindow.css` link; `UI/systemInfo.js`,
    `UI/shipwindow.js`, `UI/flightwindow.js` script tags (single-line `<!-- -->`
    comments — bundle-legacy.js skips lines starting `<!--`, so the three files
    also drop out of game.legacy.bundle automatically); the
    `#shipwindowtemplatecontainer` + `#hitChartTable` template block is wrapped
    in `<?php if (false): ?>…<?php endif; ?>` (never emitted, easy to restore).
  - gamelobby.php: same treatment (css link, 3 script tags, template block);
    plus the `window.shipWindowManager.addEW` stub (would now THROW — the
    global no longer exists) and the legacy `#systemInfo` tooltip div.
  - lobby.css `.shipwindow { position: fixed }` override commented out.
  - The four retired files themselves (`UI/shipwindow.js`, `UI/flightwindow.js`,
    `UI/systemInfo.js`, `styles/shipwindow.css`) got STAGE4-RETIRED header
    comments marking them delete-when-stable.
- **~30 live legacy call sites commented out** (`//STAGE4-RETIRED` prefix keeps
  the original statement intact for one-glance restore): power.js (16),
  weaponManager.js (7 setDataForSystem sites), movement.js (5), defensive.js
  (2), gamedata.js shipStatusChanged, PhaseStrategy.js onShipEwChanged's
  `window.shipWindowManager.addEW`, ajaxInterface.js shipStatusWindow reset,
  ships.js doShipContextMenu opens (dead canvas-era path, belt-and-braces).
- **Structural retirements**: `ShipIcon.prototype.createShipWindow` +
  `FlightIcon.prototype.createShipWindow` (legacy DOM re-link) wrapped in block
  comments and the `consumeShipdata` call removed — `ship.shipStatusWindow` is
  never set again in game.php. weaponManager's dead legacy-DOM handlers
  commented out wholesale: `onHoldfireClicked` (line comments — it contains an
  inner block comment) and the hover glue block
  `onWeaponMouseover`→`doWeaponMouseout` (only ever bound by the retired
  windows; held the last live `systemInfo.` references in game.php code).
- **Gotcha hit during the sweep**: ships.js `initShips`/`createHexShipDiv`/
  `drawShips` were ALREADY inside a giant `/* … */` (lines 4–249) — nesting a
  new block comment there ended the outer one early and broke the parse. Those
  sites needed no action at all (reverted).
- **Verified**: node --check on all 13 touched plain-JS files + esbuild JSX
  parse on ShipWindow/ShipSection/theme; a dry-run of bundle-legacy.js's
  extractScriptSources shows the 3 retired files out of BOTH bundles and no
  missing includes; grep sweep — every remaining
  `shipWindowManager./flightWindowManager/systemInfo.` reference in loaded code
  is inside a comment. php -l pending (Docker was down); IDE PHP diagnostics
  clean on both pages.
- **Stage 4 refinements (user requests, 2026-07-18):**
  1. **Single-side-structure quarter names**: ships that use both quarter
     sections on a side purely for icon placement but have only ONE side
     structure (Vorlon capitals: real Port structure in 32 "Port Aft", 31 a
     structureless weapons shelf — VorlonCapitalShip maps addLeftSystem→32)
     now label that lone structure header plain "PORT"/"STBD".
     `getSectionNameOverrides` in ShipWindow.js (override only when exactly one
     of {3,31,32} / {4,41,42} holds a structure and it is a quarter location);
     ShipSection takes a `nameOverride` prop. Sides with two structures keep
     the quarter names; hit-chart popup keeps true location names.
  2. **Status banners**: `RolledBanner` generalised to `StatusBanner`
     ($color/$bg, amber defaults) and the map tooltip's ship-level status lines
     now render as bottom-of-window banners (grid + compact + game flight
     variants; never in lobby, never on unrevealed mines):
     green **Undetected** (trueStealth & not detected — `isUndetected` mirrors
     ShipTooltip.js's block incl. the own-ship stealth-system
     detected/detectedNew check, so banner and tooltip can never disagree; a
     Detected banner deliberately omitted per user), orange **"Ship is being
     boarded!"** (`hasAttached`), green **"Attached to <host> [side]"**
     (`attached` + `!detached`). New theme tokens `statusOk`/`statusAlert`.
- **Remaining for exit (user)**: rebuild bundles (`yarn build` — UI.bundle,
  game.legacy.bundle AND gamelobby.legacy.bundle all changed), then the §7 full
  sweep: a normal game.php session (all phases + thrust + power + fire +
  replay), a lobby session (buy/edit/windows), no `shipwindow` selectors in the
  DevTools DOM, bundle sizes noted before/after; eyeball a Vorlon Heavy
  Cruiser (PORT/STBD headers), a trueStealth ship, a boarded ship and an
  attached breaching pod for the new banners.

**Stage 4 feedback round 1 (2026-07-18) — applied:**
1. **Enemy Rolled status invisible during Movement (pre-existing bug, user
   report from game 4247)**: `TacGamedata::hideActiveShipMovement()` stripped
   ALL of an active-initiative enemy ship's current-turn moves — including the
   turn-start STATE MARKERS (`isRolled`/`isRolling`/`isPivotingLeft`/
   `isPivotingRight`, written by end-of-turn movement generation) — so the
   tooltip's Rolled/Rolling/Pivoting lines, the window's ROLLED banner and its
   port-starboard mirroring all vanished while awaiting that ship's move. Same
   family as the Gravitic Augmenter forced-jink exception. Fix: preserve those
   four marker types (turn-start facts the owner cannot change this turn and
   the opponent already saw last turn; a roll/pivot ORDERED this turn is a
   normal "roll"/"pivot" move and stays hidden until committed). Pivot markers
   included on the same principle — trim to roll-only if unwanted. Verified:
   php -l + replay harness 158 PASS (lone 4071 FAIL is a stale baseline — the
   game advanced phase 2→3 in the DB since recording; fails byte-identically
   with the fix stashed).
2. Lobby Enhancements block headers get a muted bronze/gold variant
   (`BlockTitle $gold`: text #e8cf93 on rgba(169,128,56,0.30), border
   #8a6d3b) — both the standalone grid panel and the rail's inline block.
3. StatusBanner text sat high: padding 2px-top/3px-bottom equalised to
   `3px 6px` (the border-top reads as a separator line, not banner fill, so it
   never compensated the asymmetry).

**Stage 4 feedback round 2 (2026-07-18) — applied (lobby cosmetic):**
1. **Bar-graph glyph left of the "Ship Stats" title** (`ManoeuvreStats`): a
   CSS-drawn three-bar icon (`StatsIcon` in ShipNotesPanel.js — no emoji, stays
   monochrome) sized to the 12px `CtrlIcon` footprint with the same 4px gap, so
   the title text lines up with the "Hit Chart" button's `⊕` directly above it
   (`StatsTitle` became a flex row).
2. **Enhancements list body text goes gold**: new theme token
   `colors.enhText` (`#d8be86`); `Row` gained a `$gold` prop applied to the
   enhancement lines in BOTH the standalone `EnhancementsPanel` and the rail's
   inline block — the list now reads in the block's bronze/gold family instead
   of the default blue `textAccent`, matching its border/title.

**Stage 4 feedback round 3 (2026-07-18) — applied (lobby only):** fighter-image
hover tooltip suppressed in the LOBBY flight window (`shipWindow/FighterIcon.js`
gained a local `isLobby()` and early-returns from both
`onSystemMouseOver`/`onSystemMouseOut`; touch long-press `onFighterTouchStart`
also lobby-gated). game.php flight windows keep the tooltip; child SystemIcon
hovers unaffected on both screens. UI.bundle only.

**Stage 4 feedback round 4 (2026-07-18) — applied:** six-sided ships'
PORT FWD / PORT AFT / STBD FWD / STBD AFT quarter sections (locations
31/32/41/42) no longer reserve a phantom second icon row (user report from the
Thoughtforce / Heavy Carrier screenshots). They carried an 84px `min-height`
floor (feedback round 1, "sparse hulls keep presence"), but a one-row icon
section is only ~57px (15px header + 1px border + ~3px padding + one 38px icon
row), so that floor inflated every single-row quarter by ~27px of dead space.
Since every rendered section already holds ≥1 system (≥1 row ≈ 57px), a floor
at-or-below one row is a no-op — 84px was the sole cause and only ever bit the
1-row case. Fix: `minHeight` for six-sided quarters dropped to `undefined`
(`ShipWindow.js` ~line 784) so they size to content exactly like Port/Starboard
(3/4). `align-self` start/end (GRID_VALIGN) still pins Fwd quarters to the top
and Aft quarters to the bottom of the Primary-stretched grid column, so the
spare space now falls in the transparent gap over the watermark instead of
inside the dotted panel. Big-base quarters were already floor-free (they use
`wide`/4-wide); Port/Starboard were always floor-free. ShipSection's generic
`$minHeight` prop support is left dormant. UI.bundle only — needs `yarn build`;
esbuild JSX parse clean.

**Stage 4 feedback round 5 (2026-07-18) — applied:** a rare six-sided ship that
carries BOTH a mid Port/Starboard section (loc 3/4) AND its quarter sections
(31 Port Fwd / 32 Port Aft) now draws the mid section BETWEEN the two quarters
instead of above them (user experiment on the Vree Mind's Eye; aimed at future
Vree hulls). Cause: `buildTemplateAreas` pushed the middle rows in numeric-ish
order `left`(3/4) → `lfwd`(31/41) → `laft`(32/42), so Port landed on top. Fix:
reorder the push to fore→amidships→aft — `lfwd`(31/41), then `left`(3/4), then
`laft`(32/42); `GRID_VALIGN` already gives lfwd 'start' / left 'center' / laft
'end', so Port centres between Port Fwd (hugging top) and Port Aft (hugging
bottom). Only affects ships where a mid row AND a quarter row coexist; normal
5-section ships (only 3/4) and standard six-sided ships (only quarters) render
identically — just one of the three port-side rows exists for them. Sections
are grid-area-placed (GRID_LOCATIONS render order is irrelevant), so only the
template string changed. UI.bundle only — needs `yarn build`; esbuild JSX parse
clean.

**Stage 4 feedback round 6 (2026-07-18) — applied (cosmetic):** the EW panel's
"Electronic Warfare" title bar (`EwTitle` in `ShipWindowEw.js`) now matches the
Hit Chart / Notes buttons — white `theme.colors.text` on the shared header-bar
blue `rgba(73, 103, 145, 0.25)` (was blue `textAccent` on the dark `panelBg`
fill). game.php EW panel only (lobby uses ShipNotesPanel). UI.bundle — needs
`yarn build`; esbuild JSX parse clean.

**Stage 4 feedback round 7 (2026-07-19) — applied (game.php Enhancements box +
EW tooltip):** two user requests.
1. **Gold Enhancements box promoted to game.php.** The standalone bottom-right
   `enh` grid panel (`EnhancementsPanel`, ShipNotesPanel.js) — previously
   lobby-only — now also renders in game.php's grid ship window, in the same
   bottom-right spot it has in the lobby, below the EW panel. `withEnhPanel` in
   `ShipWindow.js` dropped its `lobby &&` guard (now just
   `Boolean(ship.enhancementTooltip)`); `buildTemplateAreas` already carves the
   `enh` cell out of the bottom-right and the EW-panel span stops above it, so EW
   (top) / Starboard (mid) / Enhancements (bottom) stack cleanly in the right
   column. **Chrome width: 150px lobby / 130px game** (user follow-up 2026-07-19 — 150
   everywhere read too wide on the game screen, the original 120 too tight). The
   lobby/game `$wide` split is kept (`wide = isLobby()` in `renderControls`): `CtrlButton`
   (Hit Chart / Notes) min-width `$wide ? 150 : 130`; `EnhArea` (ShipNotesPanel.js)
   `$wide ? 150 : 130`; `EwPanel` (ShipWindowEw.js, game-only) flat 130. The box
   **replaces** the old ENHANCEMENTS lines. `ShipInfo.js` now **decides for itself**
   whether to list enhancements inline, from ship type: **hidden for full grid ships**
   (they have the gold box), **shown for mines / fighters / terrain** (compact / flight
   variants, no box) — `showEnhancements = ship.flight || ship.mine ||
   gamedata.isTerrain(...)`. Deciding inside ShipInfo rather than via a caller prop means
   every consumer obeys one rule: the Notes-button popup AND the ship-info popup
   (SystemInfo.js `<ShipInfo>`), game and lobby. (Superseded the first cut's
   `hideEnhancements` prop + `renderPopup` `hideEnh` plumbing, both removed 2026-07-19 —
   the ship-info popup was still listing enhancements for grid ships.)
   The Enhancements box header is its own styled component `EnhTitle`
   (ShipNotesPanel.js) — seeded from the former `BlockTitle $gold` look but independent,
   so it can be restyled without touching the Notes / Hangar Capacity / Flight Stats
   titles (both enh render sites use it; `BlockTitle`'s `$gold` branch is now unused).
   The gap above the Enhancements box is a single shared knob — `EnhArea`'s
   `margin-top` (marked `>>> ENHANCEMENTS-BOX GAP <<<`, set to 15px, applies to both
   pages since the component is shared).
2. **Ammo enhancements excluded from the Enhancements list.** Consumable
   ammunition enhancements (missiles / railgun shells / launcher-loaded mines)
   load an AmmoMagazine and already show in its system tooltip, so they are kept
   out of the box. Done at the authoritative source, server-side:
   `Enhancements::isAmmoEnhancement($enhID)` (prefix `AMMO_`/`SHELL_` + an explicit
   launcher-mine list — the `MINE_BL*/ML*/AML/BML/CML` ammo, distinct from the
   `MINE_ACC/ARM/DMG/...` mine-SHIP enhancements) now guards the `enhancementTooltip`
   append in both `setEnhancementsShip` and `setEnhancementsFighter` (the effect
   switch incl. `addAmmoEntry` still runs). This exactly mirrors the lobby, which
   already routes ammo into `ammoMagazine.data["Special"]` and never into
   `enhancementTooltip` (lobbyEnhancements.js §"end of non-ammo enhancements list")
   — so game.php and lobby are now consistent. Client can't do this filtering
   (it only receives the `enhancementTooltip` string, not the structured
   `enhancementOptions`). Verified: esbuild JSX parse (ShipWindow/ShipNotesPanel/
   ShipWindowEw/ShipInfo) + `php -l` Enhancements.php (Docker, both container
   paths). UI.bundle + server PHP changed; no legacy-bundle change. Open note for
   the user: in game.php the box shows enemy-ship enhancements too (same data the
   Notes popup already exposed — no new leak, but now always-visible).
3. **EW target-name `title` tooltip suppressed** (`ShipWindowEw.js`): the
   OEW/DIST/SOEW/SDEW rows already show the target name as their visible text, so
   the redundant native hover tooltip is off. Kept trivially reversible via a
   module-level `const SHOW_EW_TARGET_TOOLTIP = false` (flip to true to restore);
   `title={SHOW_EW_TARGET_TOOLTIP ? target.name : undefined}`. game.php only
   (interactive rows exist only where a map does).

**Stage 3 (2026-07-17) — COMPLETE (user-accepted after feedback rounds 1–5).** Two user riders (2026-07-17)
refine §3.2: (1) the Hit Chart button sits in the same top-left position as
game.php with the manoeuvre stats (TC/TD, Acc/Pivot/Roll, Profile, Ini, Agile)
stacked directly beneath it; (2) the fighters/notes content is ALWAYS visible —
an un-obscurable datasheet rail, not a popup — and lobbyEnhancements.js got the
requested review (bugs found + fixed, see below). Superseded legacy code is
commented out in place, never deleted (Stage 2 convention; deletion is Stage 4).
- **3a bootstrap**: gamelobby.php now loads `UI.bundle.js` (AssetLoader tag +
  preload, skipped by bundle-legacy.js) and the debug list gained
  `uiEventRelay.js` + `renderer/shipWindowManager.js` (so the lobby legacy bundle
  auto-includes them). React mounts `#shipWindowsReact` / `#systemInfoReact` are
  **fixed full-viewport `pointer-events:none` wrappers** — the lobby page
  scrolls, and absolute positioning would anchor windows/tooltips to the
  document (the legacy equivalent was lobby.css forcing `.shipwindow` to
  `position: fixed`); `ShipWindowContainer` re-enables pointer-events.
  Bootstrap block at the end of gamelobby.js (DOM-ready, after all deferred
  bundles): builds a `UIManager` + `window.shipWindowManagerReact`, installs the
  `uiEvents` handler — SystemMouseOver/SystemClicked → React SystemInfo popup,
  SystemMouseOut/CloseSystemInfo → hide, CloseShipWindow → close, all other
  events ignored. Read-onlyness costs nothing: lobby `gamedata.waiting` is
  `true` and gamephase is -2, so SystemIcon's click/action branches are inert
  while hover and touch long-press (both ungated) drive the info popup.
- **3b lobby mode in the React window** (`isLobby()` = gamephase === -2):
  - Grid variant: no `ShipWindowEw`; `renderControls` renders the new
    `ManoeuvreStats` panel (120px, styled like the EW panel) under the Hit
    Chart button; the Notes button is suppressed (`withNotes = false` — notes
    are in the rail); a `LobbyBody` flex row wraps the `SectionGrid`
    (`$inRow`: flex auto-width) plus the 200px `ShipNotesPanel` rail
    (border-left, wraps BELOW the grid on narrow screens — overlap with system
    icons impossible by construction). Rail blocks: Complement / Notes (+
    limited %, variant-of + occurrence, ISD, CUSTOM/SEMI-CUSTOM flag) /
    Enhancements (from the rebuilt tooltip).
  - Flight variant `flightLobby` (max-width 620px): FighterList + rail, rail
    topped by a Flight Stats block (armor F/S/A, offensive bonus ×5, thrust,
    initiative).
  - Compact variant (purchased mines): rail renders full-width beneath the
    body; ManoeuvreStats skipped for mines.
  - New `helpers/buildComplement.js` ports shipwindow.js:423-530 (restricted-
    bay reserved-fighter merge + default-shuttle rows); the legacy copy is
    unreachable in the lobby from this stage on, so no shared-helper dance.
  - Window sides: new `ShipWindowManager.isLeftSide(ship)` is the single
    source for BOTH the manager's one-window-per-side filter and the
    container's CSS side — lobby: `userid == 0` left (store) / fleet right
    (the legacy split, §8.3); game.php: team-based, unchanged.
    `ShipWindowsContainer` keys gained `userid` (a store blueprint and a fleet
    ship can be open together sharing a numeric id).
  - `getHeaderTint` no longer constructs a `THREE.Color` — the lobby loads no
    THREE (it would throw), and the object stringified into an invalid CSS
    declaration anyway, so returning null renders identically on both pages.
  - Hit chart: `hasHitChart` feature-detect unchanged; in-lobby presence of
    blueprint `hitChart` still to be eyeballed in the exit test (expected
    present — legacy read `ship.hitChart.length` in-lobby without error).
- **3c legacy path retirement (commented in place)**: `onShipContextMenu` →
  `shipWindowManagerReact.open` (fleet ships get `lobbyEnhancements.apply`
  first; store blueprints are the SHARED allShips objects and never have
  enhancements taken, so the mutator is not run over them); edit-confirm's
  destroy/rebuild dance → membership check + `shipWindowManagerReact.update()`;
  gamelobby.php's fake-weaponManager **hover half** commented out (legacy
  systemInfo.js glue), predicate stubs kept and extended for SystemIcon's
  render path (`isLoadedAlternate`/`getFiringOrder`/`getCalledShotInfo` →
  false/null, `selectAllWeapons` no-op); new lobby stub:
  `MineStealth.prototype.isMineRevealed` → true (blueprints have no `.team`,
  so the game-side check would render every purchased mine as an unknown "?").
- **lobbyEnhancements review (user request)** — kept the 1,700-line mutation
  switch, fixed the orchestration around it:
  1. New one-shot `apply(ship)` choke point (`ship.enhancementsApplied` flag;
     the per-enhancement `*Enh` markers stay as a second line of defence). Call
     sites: window open (fleet ships) + edit-confirm re-apply.
  2. Tooltip rebuilt fresh inside `apply()` from enhancementOptions. The old
     per-open append sat OUTSIDE the marker guards so lines duplicated on every
     window open, and its `<br>` separator was written to `this` instead of the
     ship so lines ran together. In-switch appends commented out;
     `resetEnhancementMarkers*` now also clear the flag + tooltip.
  3. Edit-confirm reset block extended with EVERY enhancement-mutated
     ship-level stat (iniativebonus, critRollMod, toHitBonus, turncost,
     turndelaycost, pivotcost, signature, detectedSignature, IFFSystem) —
     previously only defenses (+ a few flight fields) were reset, so
     enhancements kept through an edit compounded ini/crit/to-hit/etc. on every
     pass (pre-existing bug).
  React re-rendering from the mutated ship object is what kills the legacy
  remove/rebuild dance — enhancement stat changes now show live in the window.
- **Verified**: `node --check` on gamelobby.js / lobbyEnhancements.js /
  shipWindowManager.js; esbuild JSX parse on ShipWindow.js, ShipNotesPanel.js,
  buildComplement.js, ShipWindowsContainer.js; `php -l` + inline-`<script>`
  parse on gamelobby.php.
- **Remaining for exit (user)**: rebuild bundles (`yarn build` — UI.bundle,
  gamelobby.legacy.bundle AND game.legacy.bundle all changed), then the §5
  Stage 3 exit list: lobby side-by-side vs live for capital / six-sided / base /
  flight / LCV / mine / enhanced ship (buy + edit → window updates live, no
  duplicated tooltip lines) / restricted-hangar (Suom/Roka) / default shuttles /
  variant-ISD-custom flags; store window left + fleet right; hover AND touch
  long-press info popups; hit chart popup; no overlap at any fleet size; plus a
  game.php + replay spot-check (shared files changed: ShipWindow,
  ShipWindowsContainer, shipWindowManager).

**Stage 3 feedback round 1 (2026-07-17) — applied:**
1. Datasheet moved off the window's side: `ShipNotesPanel` now occupies the `ew`
   GRID AREA — the exact place the EW panel has in game.php (`$grid` mode:
   150px, EW-panel glass/dotted-border styling; flight windows keep the side
   rail, mines the full-width block). "Complement" renamed **"Hangar
   Capacity"**; stacked blocks get a 6px gap (`Block + Block`).
2. Hit chart popup sizes to its content: `$fit` on its PopupHolder (supersedes
   Stage 1 round 5's full-width decision — the geographic columns shrink-wrap).
3. Icons showed no Turn Loaded/Output in the lobby: game.php's inline
   staticShips serialise `outputDisplay: ""` for every system, but the lobby's
   default-stripped faction JSONs OMIT it, and `undefined != ''` is true — so
   `SystemIcon.getText` returned undefined for every generic system (the Jump
   Engine's client model sets its own outputDisplay, hence its lone "0").
   Fixed with an undefined/null guard in getText (loose `!= ''` kept so
   numeric-0 fall-through behaviour is unchanged in game). The lobby
   `getWeaponCurrentLoading` stub now returns the fully-loaded value
   (`normalload || loadingtime` → "1/1"/"2/2"; plain normalload read "0/1").
4. Store (left) windows looked headerless — blueprints have no ship name, so
   the white name slot was empty. Nameless ships now promote the CLASS into
   the name slot (white) and leave the class slot empty.
5. "Hit Chart"/"Notes" button labels sat high beside their glyphs: CtrlButton
   `align-items` center → baseline (12px glyph and 8px label share a baseline).
6. Bases show only Profile in ManoeuvreStats (TC/TD/Accel/Pivot/Roll/Ini rows
   dropped — bases don't manoeuvre).

**Stage 3 feedback round 2 (2026-07-17) — applied:**
1. Forward↔Primary gap (tall chrome stacks inflated grid row 1, worst on ships
   without side sections): `buildTemplateAreas` now EXTENDS the `ctrl` and `ew`
   areas downward through consecutive rows whose side cell is otherwise empty,
   so the buttons/stats and datasheet stacks span rows instead of stretching
   row 1. Side-section rows still always name both areas as a pair (rolled-ship
   mirrored `displayLocation` must always find its area). Six-sided/base
   layouts unchanged (their side cells are occupied).
2. **Bought ships had no section header bars (the green health bars) and a
   stray "0" icon** — root cause: lobby fleet ships are `jQuery.extend` clones
   (gamelobby.js getShipByType) whose systems LOSE the prototype chain
   (for..in copies prototype methods as own props, so everything else worked),
   so `instanceof Structure` was false: no header, and the structure system
   leaked into the icon grid as a "0". ShipSection now tests
   `system.name === 'structure'` (the systems.js getStructureSystem
   convention). Same latent bug fixed in SystemInfo: `system instanceof Ship`
   missed ship-level events for those clones (render fell into the system
   branch and crashed) — now `instanceof Ship || system === ship`.
3. Window position memory: drag-stop records the position per SIDE
   (module-level, session-lifetime); the next window opened on that side
   restores it (clamped on-screen; skipped on the ≤1024px full-screen layout).
   Applies to game.php too — same component.
4. Port-column sections `justify-self: end` (hug the centre column, mirroring
   starboard's `start`) via GRID_JUSTIFY, visible when side tracks are wider
   than a section (e.g. beside the 150px lobby datasheet).
5. Datasheet restructured into separate panels: the Rail is now a transparent
   4px-gap stack and each block (Hangar Capacity / Notes / Enhancements /
   Flight Stats) is its own dotted-bordered glass panel — same construction as
   Ship Stats below the Hit Chart button.
6. Width symmetry: CtrlButton min-width and StatsPanel width are 150px in the
   lobby (`$wide`), matching the datasheet panels opposite; game.php keeps
   120px (matching its EW panel).
7. All chrome headers (Hit Chart/Notes buttons idle fill, Ship Stats title,
   datasheet block titles) use the shaded header-bar blue
   rgba(73,103,145,0.25) with white text — the hit-chart section-name shade.

**Stage 3 feedback round 3 (2026-07-17) — applied:**
1. Enhancements got a standalone BOTTOM-RIGHT panel (`EnhancementsPanel`, grid
   area `enh`): buildTemplateAreas carves `enh` out of the last row's free
   right cell (or appends a `". . enh"` row when occupied); the ew span stops
   above it, so a long enhancement list no longer lengthens the datasheet
   stack and re-inflates row 1 (the carrier screenshot). The rail keeps its
   inline Enhancements block for flight/mine variants (no grid there);
   `hideEnhancements` suppresses it in grid mode.
2. Enhancement counts read "(2)" not "(x2)" (lobbyEnhancements.apply builder).
3. Notes "inconsistency" investigated — NOT a bug: the compared windows were
   DIFFERENT ships (Vorlon Heavy Cruiser `limited: 0` vs Heavy Carrier
   `limited: 33` in the blueprint JSON — verified), and the bought Omega's
   extra "Extra Marine Contingents (7)" line is the note its purchased
   enhancement appends. Each window lists its own ship's data correctly.
4. Ship Stats typography: labels 10px sentence case (was 8px CAPS) in the
   notes blue (textAccent), values white — colours flipped; same treatment in
   the Flight Stats block; "Agile ship" de-capsed.
5. Hit Chart/Notes button labels always white (matching the Ship Stats title).

**Stage 3 feedback round 4 (2026-07-17) — applied (bought-flight crash):**
1. **Bought flight windows crashed** (`Cannot read properties of undefined
   (reading 'destroyed')`) — the jQuery.extend-clone instanceof trap ONE level
   deeper: systems.js `isDestroyed` treats any flight system failing
   `instanceof Fighter` as a fighter SUBSYSTEM; a bought flight's plain-clone
   fighters failed it, `getFighterForSystem` found nothing, `.destroyed` threw.
   Now duck-types (only fighter units carry a `.systems` array) + null-guards
   the lookup; game.php behaviour identical (real instances short-circuit on
   instanceof). This was the LAST lobby-reachable `instanceof
   Fighter/Structure/Ship` (grep-verified; the rest live in game-only
   animation/PhaseStrategy code). getFighterForSystem (identity `.includes`)
   and the criticals helpers (`hasCritical` array reads) are clone-safe as-is.
2. The crash also killed the OTHER window and all later opens — an uncaught
   render error unmounts the whole React root. `ShipWindowsContainer` now
   wraps each window in a per-window error boundary: a broken window renders a
   small amber fallback frame naming the unit with a working ✕ (close →
   remove from manager → boundary remounts fresh on reopen); the sibling
   window and future opens survive. Protects game.php too.
3. Flight-window layout: the datasheet rail sat left-aligned BELOW the fighter
   images — the wrap + fit-content sizing collapsed. LobbyBody is now
   `flex-wrap: nowrap` (FighterList wraps its icons internally; FlightArea
   flex 1 1 auto, min 120px, max 400px), putting Flight Stats/Notes to the
   RIGHT of the fighters.

**Stage 3 feedback round 5 (2026-07-17) — applied, stage accepted:**
1. Enhancements panel `align-self` end → start: it now starts at the top of
   its cell, directly below the Starboard section.
2. MCV fringe case (Hawk Frigate: side cells hold SYSTEMS — structureless
   boxes — so the round-2 chrome spans are blocked and the tall lobby stacks
   inflated row 1 again): `GRID_VALIGN.fwd` center → **end**. Whenever row 1
   is inflated Forward hugs Primary and the spare space moves to the window
   TOP (over the watermark hull art); an uninflated row 1 renders identically,
   so game.php and normal hulls are unaffected (it also improves game.php
   ships with long EW target lists, the only game case that inflates row 1).
3. (User, same round: the enhancement-note appends into `ship.notes` inside
   lobbyEnhancements' setEnhancements* cases were commented out — the
   Enhancements panel is now the sole display of purchased enhancements, so
   the Notes block no longer duplicates them.)

**Stage 2 (2026-07-17) — BUILT, awaiting user test.** Per user request, every
superseded legacy function was commented out in place (not deleted) — actual
deletion stays a Stage 4 concern.
- **2a event relay**: new `client/uiEventRelay.js` defines `window.uiEvents`
  (`setHandler`/`relay`; events relayed before a handler exists are dropped,
  mirroring webglScene's own not-initialized guard). game.php loads it right
  before webglScene.js (defer order matters only for the wiring, not the React
  calls — those fire at interaction time); the bundler reads game.php's script
  tags, so the legacy bundle picks it up automatically. The wiring lives at the
  bottom of `webglScene.js` (guarded `if (window.uiEvents)`): every relayed
  event funnels INTO `webglScene.customEvent`, preserving the PhaseDirector
  chain and the render-request/idle-gating invariant. Converted call sites
  (`webglScene.customEvent(` → `window.uiEvents.relay(`): `ShipWindow.js` (8),
  `ShipWindowEw.js` (3 — its `window.webglScene` interactivity gates kept),
  `FighterIcon.js` (5), `SystemIcon.js` (8). Other React components
  (SystemInfoButtons, power/EW menus etc.) intentionally stay on
  `webglScene.customEvent` — they never render in the lobby (plan scope).
- **2b assign-thrust extraction**: the four functions moved to
  `shipManager.movement` in movement.js, minus their legacy-DOM styling
  (thruster/assignThrust classes, setData refreshes — all no-ops with the
  legacy window DOM never built). **Rename**: `shipWindowManager.assignThrust
  (ship)` became `shipManager.movement.updateAssignThrust(ship)` because
  movement.js already had the per-system `assignThrust(ship, system)`;
  `doneAssignThrust`/`cancelAssignThrustEvent`/`cancelAssignThrust` kept their
  names (the DOM-resolution `if (!ship)` fallbacks were dropped — every live
  caller passes the ship). Event names/payloads byte-identical. Callers
  updated: movement.js (10 sites), `ShipThrust.js` (ready/cancel/resetThrust/
  autoAssign + the two thruster-click closures; its two
  `shipWindowManager.setData` no-ops dropped), shipwindow.js clickPlus/
  clickMinus (legacy-window-only path, updated anyway). Originals commented
  out in shipwindow.js.
- **2c botPanel**: found DEAD — nothing anywhere calls `botPanel.setEW` (or
  `onShipStatusChanged`), and game.php has no `#botPanel` element, so the
  planned `ew.fillEWSummary` helper was unnecessary. `setEW` commented out;
  `onLogUIClicked` (live log-tab UI) untouched.
- **2d docked flights**: fleetList `doScrollToShip` now fires the existing
  `OpenShipWindowFor` custom event (same one shipTooltipMenu uses;
  `PhaseStrategy.onOpenShipWindowFor` → React `shipWindowManager.open`) —
  legacy `flightWindowManager.open` call commented out. This was the last
  visible legacy window in game.php.
- **Deliberately left**: `PhaseStrategy.js` `onShipEwChanged` still calls
  `window.shipWindowManager.addEW(ship)` — a guarded no-op (addEW returns when
  the legacy window DOM is absent) and addEW itself must stay live for the
  lobby's setData path; it goes with the ~40 guarded call sites in Stage 4.
- **Verified**: node/esbuild parse checks on all 11 touched files; grep sweep —
  zero live references to the moved/retired functions outside comment blocks,
  bundles and the lobby stub.
- **Remaining for exit** (user): rebuild bundles (`yarn build` or watches —
  UI.bundle AND game.legacy.bundle both changed), then the §5 Stage 2 exit
  test: DevTools breakpoint on legacy `ensureShipWindow` (shipwindow.js:37)
  never hit across a full session (all phases + thrust assignment + docked
  flight from fleet list + bot game + replay), plus the ForcedOffline
  regression check (§6): fire a SurgeBlaster, never open the ship's window,
  verify next-turn cooldown + server rejection of re-enable.
  → **Tests PASSED 2026-07-17** (user), with two follow-up fixes the same day:
  1. ≤1024px window scrollbar: the Container media query used `overflow-y:
     scroll`, pinning a permanent inert scrollbar on classic-scrollbar
     platforms — now `auto` + the site-standard scrollbar styles (same as
     PopupHolder) for when it genuinely engages.
  2. Fleet-list docked flight STILL didn't open — turned out to be a
     pre-existing Hangar Ops 9.1 bug, not a Stage 2 regression: doScrollToShip's
     `shouldBeHidden` guard treats every removed flight as destroyed
     (ships.js:1007 isDestroyed check), so the docked-flight branch below it
     was unreachable and the 9.1 "open the flight window from the list"
     feature had NEVER fired. Fix: the branch now sits ABOVE the guard
     (window-opening leaks no board position, which is all that guard
     protects; `.clickable` is applied to all rows, so enemy docked flights
     open too — same information you'd get clicking their on-board icon).

Stage 1 files: `shipWindow/ShipWindow.js` (grid/watermark/header/rolled),
`shipWindow/ShipSection.js` (header-integrated structure bar),
`shipWindow/ShipWindowEw.js` (footer strip + interactive target names),
`shipWindow/HitChartPanel.js` + `helpers/buildHitChart.js` (+ `ShipInfo.js` swapped
to the helper), `styled/theme.js` (design tokens, roadmap item 6 seed),
`renderer/icon/EWIconContainer.js` (highlightForTarget, legacy bundle).

**Rolled-ship mirroring (user decision 2026-07-16, supersedes the §1 non-goal):**
when `shipManager.movement.isRolled(ship)` the port/starboard grid columns swap
(3↔4, 31↔41, 32↔42 — grid areas and icon mirroring follow the drawn side, section
names keep the true location), and an amber "⟲ ROLLED — port / starboard reversed"
banner renders at the bottom of the window.

**Feedback round 1 (gameid 4247, 2026-07-16) — applied, supersedes §8.1:**
1. Hit chart left the header: "⊕ Hit Chart" labelled button top-left of the window
   body (the SCS grid's empty corner cell). Click opens, click anywhere outside
   closes (document `pointerdown` + containment check); no hover-show, no pin ✕.
2. New "✎ Notes" button beneath it — popup reuses `ShipInfo` with a new
   `hideHitChart` prop (notes / attached units / enhancements).
3. Watermark brightened: opacity 0.15→0.32, brightness 1.75; section glass
   `panelBgGlass` 0.78→0.55 alpha.
4. Ship-level hover tooltip suppressed everywhere in the window (underlay, image;
   header name inert) — clicks (`SystemClicked`) kept. Unrevealed-mine "?" hover
   kept (only way to inspect a mine).
5. Sections without a Structure system render no header bar.
6. Sizing: big-base quarter sections render `wide` (156px, 4-wide `pickOuter`
   ordering, no mirror needed — symmetric rows); six-sided quarters get
   `min-height: 84px` so sparse hulls (Vree Xill) keep presence.
7. EW became a vertical panel top-right, as a grid item: row 1 of the template is
   now always `"ctrl fwd ew"` — chrome row grows with content, overlap impossible.

**Feedback round 2 (gameid 4247, 2026-07-16) — applied:**
1. Primary section border recoloured to the shared line colour (still 2px solid).
2. Transparency pass: watermark opacity 0.45, `panelBgGlass` 0.40 alpha, idle
   SystemIcon background black → rgba(0,0,0,0.7) (state colours stay opaque;
   also affects WeaponList/FighterIcon — visually nil on dark panels).
3. Alignment/stretch fix: grid columns `auto auto auto` → `1fr auto 1fr` so the
   two side tracks always resolve equal (centre column stays on the window
   midline even with asymmetric chrome); watermark now sized square from the
   body HEIGHT (`height:80%; aspect-ratio:1/1; max-width:80%`) — short windows
   (satellites) no longer centre-crop the art into a "stretched" wide slice.
4. Mines/terrain compact windows use the watermark + click underlay too — the
   old 114px `ShipImage` thumbnail is gone entirely; `CompactBody` got
   `min-height: 120px` so sparse mine windows show the art.
5. Text brightened: header class name + Hit Chart/Notes buttons + EW panel
   title now `textAccent` (buttons go white when active).

**Feedback round 3 (gameid 4247, 2026-07-16) — applied (+ user hand-tweaks kept:
Primary border 1px, idle SystemIcon alpha 0.4):**
1. Hit Chart/Notes buttons centred in their grid column (`justify-self: center`).
2. Hit chart popup arranged geographically: Port column | Front/Primary/Aft
   column | Starboard column, side columns vertically centred (HitChartPanel
   LEFT/CENTRE/RIGHT_LOCATIONS).
3. Compact windows (mines/terrain): buttons render as a centred full-width ROW
   above the sections (`ControlsArea $row`); popup anchors at top 30px there.
4. Watermark stronger still: opacity 0.55, brightness 1.9, `panelBgGlass` down
   to 0.22 alpha (sections nearly borders-only over the art).
5. Header: name 11px + ellipsis (flex-shrink 1), class 9px + ellipsis
   (flex-shrink 3, gives way first), `title` attrs carry full text — long
   flight names no longer clip past the ✕.

**Feedback round 4 (gameid 4247, 2026-07-16) — applied:**
1. Popup un-caged: window container `overflow: visible` (watermark clipping
   moved onto SectionGrid/CompactBody), PopupHolder is now a direct child of
   the container (grid top 78px, compact 72px) so it can extend below the
   window; cap 70vh with the site-standard scrollbar (10px, #0d1620 track,
   #3c5574 thumb — same as #gameinfo/log panel) + 10px bottom padding.
2. Notes button hover-peeks the notes popup on desktop (150ms grace timer so
   the pointer can cross into the popup); click still pins, click-outside
   still closes, clicked panel always wins over hover.
3. Compact windows (mines/terrain): buttons back to a vertical centred list
   above the sections (`ControlsArea $compact`) with 5px bottom margin.
4. `windowBg` #0a3340 → #152029 (darker, desaturated) so the brightened
   grayscale watermark pops.

**Feedback round 5 (gameid 4247, 2026-07-16) — applied:**
1. Notes popup sizes to content (`PopupHolder $fit`: fit-content width, capped
   at the window); hit chart keeps the full-width span.
2. `CtrlButton min-width: 120px` — matches the EW panel opposite and equalises
   Hit Chart/Notes in compact windows too.
3. EW title = edge-to-edge bar with the buttons' `panelBg` fill, nowrap single
   line (letter-spacing 0.5px).
4. Header `align-items: baseline` + name `line-height: 26px` — name and class
   share a baseline, vertically centred in the bar.
5. Hit chart section names = shaded header bars (rgba(73,103,145,0.25),
   edge-to-edge via negative margins).

**Final tweaks + acceptance (2026-07-16):** EW panel `justify-self: center`
(matches the Hit Chart/Notes stack; noticed on the Orion base). Rolled-ship
follow-up after in-game test: swapped port/starboard sections also flip their
icon ART horizontally (`SystemIcon $mirror` — art moves to a scaleX(-1)
`::after` layer with its own stacking context, so text/health bar/state
overlays stay unflipped; direction-specific thruster art reads correctly).
**User marked Stage 1 complete.**
Covers roadmap item 5 (retire legacy ship-window DOM) plus two extensions the
design review added: unify gamelobby onto the React ShipWindow, and redesign the
window's appearance in the spirit of the original B5Wars Ship Control Sheets
(SCS). Written from the perspective of: "one window codebase, one design
language, laid out like the paper game aid players already know."

---

## 1. Goals & non-goals

### Goals
1. **One ship window implementation** — the React `ShipWindow` stack — used by
   both `game.php` and `gamelobby.php`. Legacy `UI/shipwindow.js` (1648 lines),
   `UI/flightwindow.js`, the `#shipwindowtemplatecontainer` HTML templates and
   `styles/shipwindow.css` are deleted at the end.
2. **SCS-inspired redesign**: monochrome ship art as the window's background
   watermark (not a corner thumbnail); sections arranged geographically
   (Forward top, Port left, Starboard right, Aft bottom, Primary centre) like
   the paper SCS; six-sided ships get their four side sections at side-column
   height instead of sharing a row with Aft.
3. **Hit chart as a first-class control**: hover/click button in the window's
   top-left opening a per-section hit table (replaces both the legacy
   "Display Hit Chart" button and the buried hover-the-ship-image path).
4. **EW readability**: larger, structured EW panel in game.php.
5. **Lobby gains clickable/hoverable system icons** (info popups only — the
   same SystemInfo players see in game), Notes/loadout panel that no longer
   overlaps systems.
6. **Zero functional regressions in game.php** — every interaction that works
   today (fire selection, called shots, hangar/LCV dialogs, power via React
   menus, thrust assignment, mine reveal, terrain windows, touch long-press)
   works identically after the redesign. One deliberate *addition* (user
   decision, §8.4): OEW target names in the EW strip become interactive —
   click scrolls the map to the target, hover highlights its EW line sprite.

### Non-goals
- No new *game* functionality in the lobby. Anything phase-dependent stays
  gated: `gamedata.gamephase === -2` (lobby buy phase) renders the read-only
  variant. SystemIcon's action branches are already gated on phases 3/1/5/-1,
  so the lobby falls through naturally; the lobby event relay simply never
  routes action events.
- No rework of SystemInfo / SystemInfoMenu content (reuse as-is).
- ~~Rolled-ship section mirroring: the React window has never mirrored
  port/starboard for rolled ships (legacy CSS `.shipwindow.rolled` existed but
  the code that applied the class is commented out). Parity, not a fix — noted
  as a possible follow-up.~~ **Promoted to a feature and built with Stage 1
  (user request 2026-07-16)** — see the status block at the top.

---

## 2. Current state (verified 2026-07-14)

### game.php — two stacks coexist
- **Visible window = React.** `renderer/shipWindowManager.js` defines
  `window.ShipWindowManager` (capital S), instantiated in `PhaseDirector`
  (line 23) with a `UIManager` rendering `ShipWindowsContainer` into
  `#shipWindowsReact` (game.php:708). Opened from `PhaseStrategy`
  (`onShipClicked` → `.open(ship)`, lines 39/294), max one own-team + one
  enemy-team window (filter in `ShipWindowManager.open`).
- **React components** (`client/UI/reactJs/shipWindow/`):
  - `ShipWindow.js` (383) — container, header (name/class/✕), 114×114 rotated
    ship thumbnail top-left, `ShipWindowEw` box top-right, three flex "Column"
    rows of sections: `[img, 1, EW] / [3, 31, 0, 4, 41] / [32, 2, 42]`;
    flight variant renders `FighterList`; unrevealed-mine variant renders a
    "?" icon. jQuery-UI `draggable()` on mount. Mouse/touch handlers fire
    `webglScene.customEvent('SystemMouseOver'/'SystemClicked'/...)`.
  - `ShipSection.js` (348) — `SystemIcon` grid + section structure bar
    (`hp/max A n`); the `pick`/`pickOuter` grouping keeps identical weapons
    adjacent and symmetric (worth preserving).
  - `SystemIcon.js` (815) — the complete interaction surface: firing/called
    shot/loading/selected/offline/boosted/docked visuals, click routing
    (weapon select, hangar/catapult/rail dialogs, LCV rails, augmenter
    special cases), right-click select-all, touch long-press. Everything
    routes through `webglScene.customEvent`.
  - `ShipWindowEw.js` (134) — 114px box, 8.5px font: DEW/CCEW/BDEW/detect +
    per-target OEW/DIST/SOEW/SDEW rows. The readability complaint.
  - `FighterList/FighterIcon` — flight windows incl. Hangar-Ops overlays.
- **Legacy `window.shipWindowManager`** (lowercase, `UI/shipwindow.js`) is
  still loaded and *lazily* built (`ShipIcon.createShipWindow` only re-links
  pre-existing DOM; nothing eagerly builds). In game.php it is reachable via:
  - `fleetList.js:600-604` — docked flights open the **legacy flight window**
    (`flightWindowManager.open`) because a docked flight has no map icon.
    This is the last *visible* legacy window in game.php.
  - `botPanel.js:65-68` — `shipWindowManager.addEW(ship, $("#botPanel"))`
    fills DEW/CCEW spans inside the bot panel.
  - **Assign-thrust logic that is not DOM styling**: `assignThrust`
    (fires the `AssignThrust` customEvent the React `ShipThrust` panel
    consumes; movement.js calls it from ~10 sites), `doneAssignThrust`
    (commits the move, `amendContractValue` for contraction) and
    `cancelAssignThrust(Event)` (reverts + splices the move) — called by React
    `ShipThrust.js` (140/144/150-159/260-266) and `movement.js`.
  - ~40 `setData`/`setDataForSystem` call sites across `power.js` (14),
    `weaponManager.js` (7), `movement.js` (7), `gamedata.js:410`,
    `model/system/defensive.js` (2), `ships.js`, `FlightIcon.js`,
    `ShipIcon.js` — all no-op while the window DOM is unbuilt, but all would
    `ReferenceError` if `window.shipWindowManager` disappeared.
  - `ships.js` canvas-era `initShips` path (lines 45-50) is dead — nothing
    calls `drawShips` since the WebGL renderer; treat as delete-on-sight.
- Both pages load `styles/shipwindow.css`; `lobby.css` adds overrides.

### gamelobby.php — legacy only
- **No React at all**: no `UI.bundle.js`, no `UIManager`, no `webglScene`, no
  PhaseDirector. `window.ShipWindowManager` doesn't exist.
- Right-click a ship row → `gamedata.onShipContextMenu` (gamelobby.js:3644) →
  legacy `createShipWindow` + `lobbyEnhancements.setEnhancementsShip/Fighter`
  (mutates ship/systems so the window shows enhanced stats) → `setData` →
  `open`. Enhancement changes destroy the window (gamelobby.js:3136-3139) so
  it rebuilds with fresh numbers.
- Lobby stubs (gamelobby.php inline, ~lines 150-272): a fake `weaponManager`
  (`hasFiringOrder:false`, `isLoaded:true`, `isSelectedWeapon:false`,
  `getWeaponCurrentLoading` reads `normalload`, hover →
  `systemInfo.showSystemInfo`), `movement.isRolled → false`,
  `shipWindowManager.addEW → noop`. Legacy `UI/systemInfo.js` is
  **lobby-only** (its own header comment says so).
- Lobby `gamedata` is a *different object* (gamelobby.js): `gamephase` = -2,
  `getPlayerTeam(id)` **takes a slot argument** (game.php's takes none),
  `isTerrain(shipSizeClass, userid)` exists, `getShip(phpclass, faction)` /
  `getFleetShipById(id)`. Blueprints arrive in `gamedata.allShips` via
  `gamelobbyloader.php` (see [[arch_gamelobby_static_ship_access]]);
  `window.staticShips` does NOT exist here.
- The `.notes` column (template col3) carries: TC/TD, profile F/S, Ini,
  Acc/Pivot/Roll, agile, full fighter complement (incl. restricted-bay and
  default-shuttle rendering, shipwindow.js:423-530), `ship.notes`, limited %,
  variant-of, ISD, CUSTOM/SEMI-CUSTOM flags. **This content must survive** —
  it is the fleet-selection datasheet. Its overlap with system grids on
  big/six-sided ships is one of the driving complaints.
- Hit chart is currently *suppressed* in the lobby (`gamephase > -2` gate at
  shipwindow.js:166). `ship.hitChart` data itself is present on client ships
  in game.php (React `ShipInfo.js` already renders it as text); **verify** the
  lobby blueprint Ships also carry `hitChart` before promising the lobby hit
  chart (expected yes — same static JSON — but confirm in DevTools).

### Layout bug being fixed (user report)
Six-sided ships (e.g. Kirishiac Conqueror): React row 3 renders
`[32 | 2 | 42]`, putting Port-Aft and Starboard-Aft on the same horizontal
level as Aft, which reads wrong against the hull. Bases via the legacy base
template stack 31/32 and 41/42 into tall side sections instead.

**Stop-gap APPLIED 2026-07-14** (ahead of Stage 1): for `ship.SixSidedShip`
hulls, `ShipWindow.js` renders everything between the Forward and Aft rows as
**one flex band** (`MiddleRow`, `align-items: center`): two `SideStack`
columns (Port-Front over Port-Aft, Starboard-Front over Starboard-Aft;
30% wide, stretched to the band, `justify-content: space-between` so their
sections sit at the band's top/bottom edges) flanking the Primary section,
which centres vertically in the band; Aft renders alone on a centred row
below. `ShipSection` gained an `inStack` prop (width `auto` → fills the
stack). Non-six-sided ships and bases render the original two rows,
untouched, until Stage 1's grid supersedes everything. Needs
`yarn watch`/`yarn build` + an in-game look at a six-sided ship (Conqueror /
Mind's Eye) to confirm.

Two earlier attempts, rejected on look — don't retry at Stage 1:
`align-content: center` inside a stretched section (centred the icon lines,
not the box); `align-items: center` on the row Columns (centred Primary only
within its own row — with 32/42 on a *separate* flex row, that row's height
IS Primary's height, so Primary still hugged the Forward row). The lesson
Stage 1 inherits: Primary must share one grid/flex band with all four side
sections to centre between Forward and Aft.

---

## 3. Design vision — "digital SCS"

Reference: AoG Ship Control Sheets (Omega example). What the paper sheet gets
right: the *silhouette anchors the layout* — you find a system by looking
where it physically is on the ship. What we keep from the current window:
compactness, live health bars, one-窗-per-side, draggable.

### 3.1 Window anatomy (game.php, capital ship)

```
┌────────────────────────────────────────────────────┐
│ ⊕  EAS AGAMEMNON            Omega Destroyer      ✕ │  header bar
├────────────────────────────────────────────────────┤
│                 ┌─ FORWARD ── 63/63 ▮▮▮▮▮ A7 ─┐    │
│                 │  [icons, 3-4 wide]           │    │
│ ┌─ PORT ──────┐ └──────────────────────────────┘   │
│ │ 60/60 A6    │ ┌─ PRIMARY ─── 84/84 A8 ──────┐ ┌─ STARBOARD ─┐
│ │ [icons]     │ │ [icons over the monochrome  │ │ 60/60 A6    │
│ │             │ │  ship watermark, nose-up]   │ │ [icons]     │
│ └─────────────┘ └──────────────────────────────┘ └─────────────┘
│                 ┌─ AFT ────── 58/58 A5 ────────┐   │
│                 │  [icons]                     │    │
│                 └──────────────────────────────┘    │
├────────────────────────────────────────────────────┤
│ EW   DEW 10   CCEW 0   BDEW 2  │ OEW → Sharlin 4   │  EW strip
└────────────────────────────────────────────────────┘
```

- **Watermark**: the existing `ship.imagePath` art, centred, nose-up
  (`rotate(-90deg)` like today's thumbnail), `filter: grayscale(1)
  brightness(1.6); opacity: 0.12-0.18`, `pointer-events: none`, sized to the
  primary/centre cell (spilling under adjacent cells is fine — sections have
  translucent panel backgrounds so icons stay legible). Clicking/hovering the
  *ship itself* (name in header + watermark area not covered by icons) keeps
  today's behaviour (ship-level SystemMouseOver/SystemClicked) via an
  underlay hit area in the centre cell.
- **CSS grid, not flex rows** — the structural fix:

  ```
  grid-template-areas:   (5-section ship)      (six-sided / base)
      ".    fwd   ."         ".    fwd   ."
      "port prim  stbd"      "pfwd prim  sfwd"
      ".    aft   ."         "paft prim2 saft"
                             ".    aft   ."
  ```
  Locations map: 1→fwd, 0→prim, 2→aft, 3→port, 4→stbd, 31→pfwd, 32→paft,
  41→sfwd, 42→saft. Six-sided ships thus get Port-Aft/Starboard-Aft in the
  side columns *above* the Aft row — directly fixing the reported layout.
  Sections that don't exist collapse (grid auto). Terrain/OSAT/mine keep the
  compact single-panel variant.
- **Section header** replaces today's bottom structure bar: dotted panel
  border retained, header line reads `PORT  60/60  A6` with the health bar as
  the header's background fill (green→orange-with-crits, exactly today's
  colour semantics from `ShipSection`'s `StructureContainer`). Icon grouping
  (`pick`/`pickOuter`) is kept.
- **Header bar**: `⊕` hit-chart button (left), ship name + class, ✕ close.
  Faction/team tint on the header respects the team-colour gate
  ([[arch_team_colour_logic]]) — reuse whatever palette call the fleet list
  uses rather than inventing a 7th site.
- **Hit-chart flyout** (`HitChartPanel.js`, new): hover on `⊕` shows it while
  hovered; click pins it open for longer reading (✕/re-click unpins); touch
  devices get the click path only (decided, §8.1). Renders one small table
  per section — rows `system | %` sorted descending, retargeting prefix
  stripped — i.e. the legacy `hitChartSetup` tables rebuilt in React (share
  the percent-building code with `ShipInfo.js` by extracting a
  `buildHitChart(ship)` helper so the two never drift). Anchored below the
  header, scrolls internally if tall.
- **EW strip** (game.php only): full-width footer, 11px, one labelled chip
  per value (`DEW 10`, `CCEW 0`, `BDEW n`, detect values), then per-target
  rows (`OEW → <target> n`, SDEW/DIST/SOEW with today's ConstrainedEW maths —
  reuse `ShipWindowEw`'s `getEW`/`getAmount` functions unchanged, restyle).
  "DEPLOYS ON TURN n" replaces the strip pre-deployment, as today.
  **Target names are interactive** (decided, §8.4): click scrolls the map to
  the target — fire the existing `ScrollToShip` custom event, and respect
  `shipManager.shouldBeHidden(ship)` exactly like `fleetList.doScrollToShip`
  so stealthed/undeployed targets don't leak position; hover highlights that
  entry's EW line sprite on the map via a new small `EWIconContainer` API
  (e.g. `highlightForTarget(shooter, target, on)`), cleared on mouse-out and
  guarded so a missing sprite (hidden target) no-ops.
- **Flights**: header + `FighterList` unchanged inside the new chrome; EW
  strip only if the flight has EW today (it doesn't — omit).
- **Width**: capitals ~480px (up from 400), grid collapses to today's
  footprint for small ships; ≤1024px media query keeps the full-screen
  scroll behaviour.
- Visual language: keep the established palette (#0a3340 body, #04161c
  panels, #496791 lines, #C6E2FF text accents) so the window still belongs to
  the current game skin — the SCS feel comes from layout, watermark,
  uppercase microtype and aligned columns, not a new colour scheme. Define
  the palette once as CSS variables / a `styled` theme object → this is the
  seed for roadmap item 6 (visual unification of remaining jQuery windows).
  Load the `frontend-design` skill when implementing this stage.

### 3.2 Lobby variant (gamephase === -2)

Same component, `lobby` mode differences only:
- **No EW strip** (meaningless pre-game — matches today's hidden EW div).
- **Right rail: "Datasheet" panel** (new `ShipNotesPanel.js`) replacing the
  overlap-prone `.notes` column: manoeuvre block (TC/TD, Acc/Pivot/Roll,
  Profile, Ini), complement block (fighters incl. restricted-bay merge +
  default shuttles — port `shipwindow.js:423-530` logic into a shared helper),
  metadata block (ISD, variant-of, limited %, CUSTOM/SEMI-CUSTOM, agile,
  ship.notes), enhancements block (from `ship.enhancementTooltip`, already
  maintained by lobbyEnhancements). On narrow screens the rail wraps below
  the grid — overlap becomes impossible by construction.
- **Icons hover/click → SystemInfo popup only.** This is the "added
  functionality" ask: lobby players get the same React `SystemInfo` content
  game players see (weapon stats, hangar capacity incl. default-shuttle
  fold-in, ammo). No menus, no selection.
- **Hit-chart flyout enabled** if blueprint `hitChart` is confirmed present
  (§2 verify step); otherwise hide the `⊕`.

---

## 4. Architecture changes

### 4.1 Shared event routing (the webglScene problem)
React shipWindow components call `webglScene.customEvent(...)` ~20×. In
game.php that relays to PhaseDirector. The lobby has neither.

**Do not** shim a fake `webglScene` per page. Extract a tiny relay:

- New legacy file `client/uiEventRelay.js`: `window.uiEvents =
  { relay(name, payload){ handler && handler(name, payload) }, setHandler }`.
- game.php: `webglScene.customEvent` becomes `uiEvents.relay` + its existing
  `requestRender()`; `uiEvents.setHandler` → `phaseDirector.relayEvent`.
  (Or simpler: keep `webglScene.customEvent` as-is and have React components
  call `window.uiEvents.relay`, with game.php wiring
  `uiEvents.setHandler((n,p) => webglScene.customEvent(n,p))` — smaller diff,
  render-gating invariant untouched. **Preferred.**)
- gamelobby.php: `uiEvents.setHandler(lobbyUiHandler)` — a ~60-line handler in
  gamelobby.js mapping `SystemMouseOver/ShowInfo → uiManager.showSystemInfo`,
  `SystemMouseOut → hideSystemInfo`, `CloseShipWindow →
  shipWindowManager.close(ship)`, everything else → ignore.
- React components change `webglScene.customEvent(` →
  `window.uiEvents.relay(` (mechanical, ~20 sites in shipWindow/ + SystemIcon).

### 4.2 Lobby React bootstrap
- gamelobby.php: add `<div id="shipWindowsReact">`, `<div id="systemInfoReact">`,
  load `UI.bundle.js` + `renderer/shipWindowManager.js` script tags (React/
  styled-components are self-contained in the bundle; jQuery+jQuery-UI already
  load — draggable works).
- gamelobby.js boot: `window.shipWindowManagerReact = new
  window.ShipWindowManager(new window.UIManager($("body")[0]))` and route
  `onShipContextMenu` through it (keep the lobbyEnhancements mutation call
  *before* `.open(ship)`; enhancement changes call `.update()` instead of
  destroying DOM — React re-renders from mutated ship state, killing the
  remove/rebuild dance at gamelobby.js:3136).
- **Lobby gamedata surface** used by the React stack — provide once in
  gamelobby.js, don't patch components: `getPlayerTeam()` no-arg wrapper
  (window placement), `isMyShip(ship)` (`ship.userid == thisplayer`),
  `waiting=false`, `replay=false`, `rules` stub, `getShip(id)` falling back to
  `getFleetShipById`. The existing fake `weaponManager` already satisfies
  SystemIcon's render path; extend only if a render throws (e.g.
  `isLoadedAlternate`, `getCalledShotInfo`, `getFiringOrder` → add
  false/null stubs).
- `ShipWindowManager.open`'s team filter calls `getPlayerTeam()` — the no-arg
  wrapper covers it; in the lobby both windows may be your own picks, so relax
  the filter in lobby mode (one window per `fleetList` flag instead of per
  team — mirrors today's owned/enemy split at `turn 0`, shipwindow.js:56-102).

### 4.3 Assign-thrust extraction (the real logic hiding in shipwindow.js)
Move `assignThrust`, `doneAssignThrust`, `cancelAssignThrustEvent`,
`cancelAssignThrust` into **movement.js** (they mutate `ship.movement` and
fire movement events — that's movement domain), dropping the legacy-DOM
styling block in `assignThrust` (React ShipThrust already renders allocation
state from the `AssignThrust` event). Callers to update:
`movement.js` (~10 internal sites), `ShipThrust.js` (4 sites),
`shipwindow.js` internal calls (die with the file). Keep the
`webglScene.customEvent("AssignThrust", ...)` payloads byte-identical.

### 4.4 botPanel EW
Replace `shipWindowManager.addEW(ship, $("#botPanel"))` with a small
`ew.fillEWSummary(ship, container)` helper in `ew.js` (writes the DEW/CCEW
spans; 15 lines lifted from legacy `addEW`), or mount the restyled EW strip
component. The helper is less machinery for a bot-only panel — **preferred**.

### 4.5 Docked flights in fleetList
`fleetList.js:600-604` switches from `flightWindowManager.open(ship)` to the
React manager (game.php: `phaseDirector.shipWindowManager` is not globally
reachable — expose it as `window.shipWindowManagerReact` in PhaseDirector, or
fire a `OpenShipWindow` custom event PhaseStrategy already knows how to relay
(`onShipClicked` handler path). Event is cleaner. The React window already
renders docked-state overlays (FighterIcon DOCKED labels), so no visual loss.

### 4.6 Retirement sweep (only after both pages are on React)
Delete: `UI/shipwindow.js`, `UI/flightwindow.js`, `UI/systemInfo.js` (legacy,
lobby-only), `#shipwindowtemplatecontainer` + `#hitChartTable` templates from
both PHP files, `styles/shipwindow.css` links + file, lobby `.shipwindow`
rules in `lobby.css`, the gamelobby.php `shipWindowManager.addEW` stub and the
fake-weaponManager hover half (keep the predicate stubs SystemIcon needs).
Strip the ~40 guarded `setData/setDataForSystem` call sites (power.js,
weaponManager.js, movement.js, gamedata.js:410, defensive.js, ships.js:45-68
dead block, ShipIcon/FlightIcon `createShipWindow` re-link code and their
`prepare()` callers). grep-verify: `shipwindow|shipStatusWindow|
flightWindowManager|shipWindowManager\.` must return only the React manager.

---

## 5. Staging & order of work

Ordering rationale: redesign first (player-visible win, and the lobby then
migrates onto the *final* component, not the old one); lobby unification
second; retirement last (legacy call sites live in files **shared** by both
pages, so nothing legacy can be deleted until the lobby is off it).

### Stage 1 — SCS redesign of the React window (game.php)   [~2-3 sessions]
1a. Grid layout engine in `ShipWindow.js`/`ShipSection.js` (5-section,
    six-sided, base, terrain/mine, flight chrome), watermark, section
    headers with integrated structure bars, width bump, mobile query.
1b. `HitChartPanel.js` (hover-shows / click-pins, §8.1) + shared
    `buildHitChart` helper (also swap `ShipInfo.js` to the helper).
1c. EW strip restyle (reuse getEW logic).
1d. Design tokens (CSS vars / theme object) — seed for roadmap item 6.
1e. EW strip target-name interactivity (§8.4): `ScrollToShip` on click with
    the `shouldBeHidden` guard; hover → `EWIconContainer.highlightForTarget`
    (new API in `renderer/icon/EWIconContainer.js` — legacy file, so this
    sub-step touches `yarn watch:legacy` territory too). The one intentional
    behaviour addition; everything else in Stage 1 is visual-only.
Exit: game.php pixel-check across: Omega (5-section), Conqueror (six-sided),
a base, OSAT, mine (hidden + revealed), terrain, flight, docked flight,
enemy ship (redacted power), pre-deployment ship ("DEPLOYS ON TURN").
Mostly `yarn watch`; 1e also touches `EWIconContainer.js` (legacy watch).
**No behaviour diffs except 1e.**

### Stage 2 — event relay + game.php legacy delinking   [~1-2 sessions]
2a. `uiEventRelay.js` + React components → `uiEvents.relay` (game.php wiring
    delegates to webglScene.customEvent; render gating preserved).
2b. Assign-thrust extraction to movement.js (§4.3).
2c. botPanel EW helper (§4.4).
2d. fleetList docked flights → React window via event (§4.5).
Exit: with DevTools breakpoint on legacy `ensureShipWindow`: never hit during
a full game.php session (all phases + thrust + docked flight + bot game).
ForcedOffline regression check (§6). Legacy files still loaded (lobby parity)
but unreachable in game.php.

### Stage 3 — lobby unification   [~2 sessions]
3a. gamelobby.php: bundle + mounts + manager bootstrap (§4.2), lobby
    gamedata surface, lobby event handler → React SystemInfo.
3b. Lobby mode in ShipWindow: EW strip off, `ShipNotesPanel` datasheet
    (port notes/fighter-complement logic into shared helper), hit-chart
    verify-then-enable, enhancement `.update()` re-render loop.
3c. Retire lobby usage: `onShipContextMenu` → React; delete the legacy-open
    path, `systemInfo.js` hover glue.
Exit: lobby side-by-side audit vs live for: capital, six-sided, base,
fighter flight, LCV, ship with enhancements (buy one → window updates),
restricted-hangar ship (Suom/Roka reserved lines), default shuttles,
variant/ISD/custom flags. No overlap at any fleet size. **No purchase
functionality moved or added.**

### Stage 4 — retirement sweep + polish   [~1 session]
§4.6 deletions, grep-verify, `yarn build` + statics untouched, dead-file
sweep also catches `ships.js` initShips block. Update
`arch_forcedoffline_powerentry_lazywindow` memory (the "legacy window" it
references no longer exists) and the roadmap.

Each stage is independently shippable; live can sit on any stage boundary.
Bundles regenerate per deploy as usual (never committed).

---

## 6. Risk register

| Risk | Mitigation |
|---|---|
| **ForcedOffline cooldown** — enforcement is already window-independent (`power.js applyForcedOfflineEntry` via `copyLastTurnPower`, server authoritative since 2026-06-04), but `setPowerClasses` still runs it redundantly from the legacy render path | Before Stage 2d lands: fire a SurgeBlaster, never open the ship's window, verify next-turn cooldown + server rejection of re-enable. Audit `setSystemData`/`setPowerClasses` for any *other* state writes before deleting (known: none, but verify) |
| Legacy `shipWindowManager` global vanishing while shared files still reference it | Retirement of call sites only in Stage 4, after both pages migrated; Stage 2 leaves all guards in place |
| Lobby `getPlayerTeam(id)` vs game `getPlayerTeam()` signature clash | No-arg wrapper in lobby bootstrap; never edit the game version |
| Blueprint ships share static-data references ([[arch_client_system_shared_reference]]) — watermark/notes helpers must not mutate ship/systems | Helpers are read-only; enhancement mutations stay in lobbyEnhancements |
| Lobby blueprint `hitChart` possibly absent from static JSON | Verify first; feature-detect (`ship.hitChart && Object.keys(...).length`) and hide `⊕` otherwise |
| SystemIcon render calling a weaponManager fn the lobby stub lacks | Render every unit type in lobby during 3a with console open; extend stub with inert defaults |
| Six-sided/base grid regressions on unusual hulls (UnevenBaseFourSections, SmallStarBase*, Vorlon/Vree/Mindrider capitals) | Stage 1 exit checklist includes one of each `ShipClasses.php` layout family |
| Touch behaviour (long-press info, ghost-click suppression) subtly broken by new DOM | Don't restructure SystemIcon's handler logic — restyle around it; test on a touch device per stage |
| Draggable + new grid sizing fighting (jQuery UI sets inline top/left) | Drag handle = header bar only (matches most windowing conventions; smaller hit-test surface) |
| `uiEvents` relay dropping `requestRender()` — stale WebGL frame after window interaction ([[arch_render_loop_idle_gating]]) | game.php handler delegates INTO `webglScene.customEvent` so gating/rendering path is unchanged |
| Replay screen (`replay.php`) also mounts ship windows via PhaseDirector | Include replay in Stage 1/2 checklists (`gamedata.replay` guards already exist in SystemIcon) |

---

## 7. Test checklist (run per stage; full sweep at Stage 4)

game.php: open own + enemy windows every phase (-1/1/2/5/3/waiting/replay);
weapon select/deselect + right-click select-all + called shot ⊕; ballistic in
phase 1; hangar launch / deploy-dock / LCV rail dialogs; power on/off/boost via
React menus incl. forced-offline grey-out; thrust assignment (auto + manual +
cancel + contraction), six-sided ship, base, OSAT, mine reveal flow, terrain;
flight window incl. DOCKED/DROPOUT overlays; docked flight from fleet list;
EW strip vs old values incl. ELINT ship (SDEW/DIST maths) and ConstrainedEW
Mindrider; OEW name click scrolls to target (and does NOT scroll to a
stealthed/hidden one), hover highlights the right EW line and clears on
mouse-out; hit-chart flyout: hover shows, click pins, unpin works, touch
click-only, vs rulebook SCS for Omega; deploy-turn ship; touch: tap select,
long-press info, drag. gamelobby.php: everything in Stage 3
exit list. Cross-cutting: no `shipwindow` selectors in DevTools DOM at
Stage 4; bundle sizes noted before/after (shipwindow.css + 2 legacy files
removed should offset the grid CSS growth).

---

## 8. Resolved questions (user decisions, 2026-07-14)

1. **Hit-chart flyout trigger — both.** Hover shows the flyout while
   hovered; click pins it open for longer viewing and is the only path on
   touch devices (no hover there). ✕/re-click unpins.
2. **Watermark contrast — start universal.** One conservative
   grayscale/brightness/opacity value for all factions; revisit per-faction
   auto-tune only if dark (Shadow) or bright (Minbari) hulls prove
   unreadable in practice.
3. **Lobby window placement — unchanged.** Keep the left/right split by
   `userid == 0`.
4. **OEW target names are interactive — yes (game.php only).** Click scrolls
   the map to the target (existing `ScrollToShip` event + `shouldBeHidden`
   guard); hover highlights that target's EW line sprite on the map (new
   `EWIconContainer.highlightForTarget` API). Implemented as Stage 1e; the
   redesign's single intentional functionality addition. Absent from the
   lobby (no EW strip there).
