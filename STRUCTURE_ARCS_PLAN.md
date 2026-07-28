# Structure Arc Indicator — Plan

**Goal:** When a player hovers (or long-presses on touch) an exterior structure's
green health bar in a ship window, draw a **green arc wedge** on the 3D ship icon
showing that structure section's facing coverage — mirroring how `showWeaponArc()`
draws a weapon's firing arc on hover.

**Not applicable to:** primary structures (location 0, arc 0..360) and fighter
flights.

**Hard constraint (from the request):** do **not** bloat the per-request gamedata
JSON (`stripForJson` / `ajaxInterface.js`). Arc data must reach the client via the
**static ship bundle**, which is loaded once and cached immutably.

Status: **BUILT 2026-07-23 via Design A** (user chose the lighter path: fill the existing
`startArc`/`endArc` rather than add new fields). See "What was built" at the bottom for the
as-built record; Design B below is kept for reference only.

---

## Research findings (the mechanism that makes this cheap and safe)

### 1. Arcs already travel via the static ship file, not gamedata
- Base `ShipSystem::stripForJson()` does **not** emit `startArc`/`endArc`
  (`source/server/model/systems/ShipSystem.php:105`). So live gamedata carries no
  arcs — weapons get their arcs purely from the static bundle.
- The static generator serialises **public properties** via raw
  `json_encode($ship)` (`generateStaticShipFileWeb.php:245`); neither `BaseShip`
  nor `ShipSystem` implement `JsonSerializable`, so any public prop is included.
- On load, `SystemFactory.createSystemFromJson` does
  `Object.assign({}, staticSystem)` then overlays live JSON
  (`source/public/client/model/systemFactory.js:83`), so **every static field
  lands on the live client system object**.
- Verified empirically: the live `shipsCombined.js` already contains ~79,000
  `startArc` entries. Vree Xill structures carry real arcs there
  (`loc 1 → 300..60`, etc.); a normal ship's primary carries `startArc:null`.

**Consequence:** any arc value set on a Structure server-side reaches the client
for free, with zero gamedata bloat.

### 2. No per-ship blueprint editing needed — `getLocations()` already has the arcs
- `getLocations()` returns per-section `{"loc", "min", "max", "profile"}`
  (`source/server/model/ships/ShipClasses.php:2299`) and is **already overridden
  per hull shape** (~20 overrides in ShipClasses.php + a few ship files).
- It is the **authoritative** facing→section map: `fillLocations()` /
  `pickLocationForHit()` use it to allocate structure hits by facing.
- So the correct display arc for a structure at location `L` = the `getLocations()`
  entry whose `loc == L` → `min`/`max`. This is guaranteed consistent with how
  damage is actually allocated, and requires editing **no** ship files.

### 3. Structures are deliberately excluded from the existing weapon auto-arc code
- `addSystem` (`ShipClasses.php:1210-1234`): `if ($system instanceof Structure){
  $this->structures[$loc]=... } else if (startArc==0 && endArc==0 && !Bulkhead){
  /* copy section arc from getLocations */ }`.
- Structures hit the first branch and never receive an arc → that's why they are
  `null` today. Weapons/thrusters/shields fall through to the auto-arc `else if`.

### 4. Giving structures arcs is combat-inert (traced end to end)
Every server path that reads `startArc`/`endArc`:
- Normal `"Structure"` hit-chart entries resolve via `getStructureSystem($location)`
  — **location only, no arc** (`ShipClasses.php:1592`, `:2028`).
- Arc-gated protection paths (`getSystemProtectingFromDamage` `:1670`,
  `checkIsValidAffectingSystem` `:1793`) filter structures out *before* the arc
  check: structures return `doesProtectFromDamage < 1` and are not
  `DefensiveSystem`.
- The only arc-based structure selector is `getSystemsByTag($tag,$bearing)`
  (`:1617`), reachable **only** via a `TAG:` prefix in a hit chart. Grep confirms
  **no** ship uses `TAG:Structure` / `TAG:Outer Structure` (the "Outer Structure"
  hit-chart entries resolve by displayName/`hitChartName` + location at `:1601`,
  not by arc).
- `mathlib::isInArc(dir, null, null)` currently returns `true` (`null==null`), so
  today normal structures "match any bearing" in `getSystemsByTag` — but that path
  is never entered for them.

### 5. Client render + trigger anchors
- The structure health bar in the React SCS window is the `SectionHeader` in
  `source/public/client/UI/reactJs/shipWindow/ShipSection.js:137` (it has the
  `structure` system object in hand; primary uses location 0). It currently has
  **no** hover handler.
- The 3D arc drawing lives in `ShipIcon.showWeaponArc(ship, weapon)`
  (`source/public/client/renderer/icon/ShipIcon.js:564`), gated to
  `Weapon|Thruster|Shield`, radius derived from `weapon.range` (structures have no
  range → needs a fixed radius instead), weapon-blue colour `rgb(20,80,128)`,
  pushed to `this.weaponArcs`, cleared by `hideWeaponArcs()`.
- Rolled ships flip arcs in `shipManager.systems.getArcs` (`systems.js:257`) — the
  structure arc must apply the same flip.
- Hover→arc dispatch today: `SystemIcon` relays `SystemMouseOver`/`SystemMouseOut`
  (+ 400ms touch long-press) via `window.uiEvents`
  (`SystemIcon.js:297`,`:330`) → `PhaseStrategy.onSystemMouseOver`
  (`PhaseStrategy.js:695`) → `icon.showWeaponArc(ship, system)`.

---

## Recommended design — **B: display-only arc field**

Keep structure `startArc`/`endArc` **null** (combat property stays pristine) and add
a dedicated display-only field. Nothing in server or client combat reads it, so the
feature is *provably* inert.

### B1. Server — one field + one small block
1. `Structure` class (`baseSystems.php:4341`): add
   `public $displayArcStart = null;` and `public $displayArcEnd = null;`.
2. `addSystem` Structure branch (`ShipClasses.php:1210`): after
   `$this->structures[$loc] = $system->id;`, stamp the display arc:
   - loc 0 (primary): `displayArcStart = 0; displayArcEnd = 360;`
   - else: find the `getLocations()` entry with `loc == $arcLoc` (use
     `$system->getStructureLocation()` to honour home-location systems) and copy
     `min`→`displayArcStart`, `max`→`displayArcEnd`.
   - If the structure already has explicit `startArc`/`endArc` (Vree / facing-based
     hulls), seed `displayArc*` from those instead, so the drawn wedge matches the
     hand-authored arc.
   These public props serialise into the static bundle automatically and merge onto
   the client via `Object.assign` — **no `stripForJson`, no gamedata change**.

### B2. Client — one new ShipIcon method
Add `showStructureArc(ship, structure)` + `hideStructureArcs()` to `ShipIcon.js`,
modeled on `showWeaponArc`:
- Read `displayArcStart`/`displayArcEnd`; skip if not numbers, if location 0, or if
  a flight.
- Apply the same `isRolled` flip that `getArcs` uses so rolled ships match.
- Draw a **green** `CircleGeometry` wedge (health-bar green) at a **fixed radius**
  (icon silhouette radius / ~1 hex — structures have no `range`), `position.z = -1`.
- Push to a separate `this.structureArcs` array with its own `hideStructureArcs()`
  so it never fights the weapon-arc show/hide.

### B3. Client — hover + long-press trigger on the health bar
- In `ShipSection.js`, add `onMouseOver`/`onMouseOut` + touch long-press to the
  `SectionHeader` (only when `structure` exists and location ≠ 0), mirroring
  `SystemIcon`'s pattern incl. the 400ms hold and the `lastTouchActiveTime` ghost
  guard.
- Relay a **dedicated** `StructureMouseOver` / `StructureMouseOut` event via
  `window.uiEvents` (a distinct event, not `SystemMouseOver`, so the info-tooltip /
  `showInfo` behaviour is untouched).
- Handle those in `PhaseStrategy.js` (near `onSystemMouseOver`): hide all structure
  arcs, then `icon.showStructureArc(ship, structure)` on mouse-out hide.

---

## Lighter alternative — **A: reuse `startArc`/`endArc`**
Remove the structure exclusion in `addSystem` so structures get `startArc`/`endArc`
filled from `getLocations()` like weapons; `showStructureArc` then reads them via the
existing `getArcs`. ~5 fewer lines, no new field (structures already serialise
`startArc:null`, so it's just non-null values).

Safe per the trace in finding #4, **but**: it mutates a combat-semantic property
across ~2700 ships and leaves a latent edge — a future `TAG:Structure` hit chart
would suddenly arc-filter structures. Since the client method is needed either way,
Design B costs almost nothing extra and removes even the theoretical concern. **B is
recommended.**

---

## Verification & rollout
- **Server:** `php -l` on the two changed files. Regenerate the static bundle
  (browser generator) and spot-check `Vree Conglomerate.json` + a normal ship:
  confirm `displayArc*` populate and `startArc` stays `null` (Design B).
- **Combat safety:** run the replay regression harness — output should be
  byte-identical since combat is untouched (strong signal; the whole point of B).
- **Client:** `yarn build` (React `UI.bundle`). Do **not** run build or commit
  bundles here — user builds + tests in local Docker.
- **Manual tests (local Docker):** hover a structure header on (a) a 4-section ship,
  (b) a 6-section / quarter-location ship, (c) a rolled ship — green wedge matches
  the section; primary shows nothing; flights show nothing; touch long-press works.

## Scope
- Server: 2 files — `baseSystems.php` (Structure: 1 field), `ShipClasses.php`
  (`addSystem`: ~8 lines). No `stripForJson` change, no per-ship blueprint edits.
- Client: 3 files — `ShipIcon.js` (new `showStructureArc`/`hideStructureArcs`),
  `ShipSection.js` (header hover/touch), `PhaseStrategy.js` (new event handlers).
- No combat-logic change (Design B). Static-bundle regeneration required so
  `displayArc*` ship to clients.

---

## What was built (2026-07-23, **Design A**)

`baseSystems.php` was NOT touched — no new field. Four files changed:

1. **`ShipClasses.php` `addSystem`** — the Structure branch no longer *excludes* structures
   from the auto-arc fill. `$this->structures[$loc] = $system->id;` still happens, then the
   arc-copy block runs for structures too (it is now a plain `if`, not an `else if`).
   Hand-authored structure arcs (`Structure::createAsOuter`, Vree/Centauri) are non-zero, so
   the `startArc==0 && endArc==0` gate leaves them untouched.
2. **`ShipIcon.js`** — `showStructureArc(ship, structure)` / `hideStructureArcs()`, plus a
   `this.structureArcs` array. Skips loc 0, flights, and non-numeric arcs. Wedge radius is
   `max(size * 0.5, hexDistance * 0.75)` (no `range` to derive one from); colour is health-bar
   green `rgb(66,114,49)` at 0.5 opacity; roll flip comes free from `shipManager.systems.getArcs`.
   **Outline (refinement, 2026-07-23):** `buildArcOutline()` traces the wedge perimeter (apex →
   along the arc → back to apex, 32 segments to match the fill) as a `THREE.Line` at 0.9 opacity,
   added as a CHILD of the wedge so it inherits rotation/position/removal. Deliberately a line,
   not the BDEW hexagon's inset-hole ring: `LineBasicMaterial` is 1px in SCREEN space at every
   zoom, whereas a world-unit ring would vanish when zoomed out on a wedge this small. A
   full-circle wedge skips the apex points so no spurious radius line is drawn. `THREE.Line`,
   `LineBasicMaterial`, `BufferGeometry` and `Vector3` are all already in the tree-shaken THREE
   shim (`LineLoop` is NOT — hence the explicitly closed point list); verified by loading
   `three.shim.bundle.js` in node.
3. **`ShipSection.js`** — `SectionHeader` gains mouse + touch handlers relaying
   `StructureMouseOver` / `StructureMouseOut` (400ms hold, 10px cancel, `lastTouchActiveTime`
   ghost guard). An `arcShown` flag means only a section that actually raised a wedge asks for
   the sweep, incl. on unmount.
4. **`PhaseStrategy.js`** — `onStructureMouseOver` / `onStructureMouseOut`; `onCloseShipWindow`
   also sweeps (a window closed under the cursor never fires mouse-out).

### Verification performed
- `php -l` clean on `ShipClasses.php`.
- **Replay harness `check` run twice, with and without the server change: output identical
  line-for-line apart from per-game timing strings** (124 passed / 26 failed both ways; those
  26 are pre-existing baseline drift — `enhancementTooltip`, `baseRating`,
  `overkillArcStructures` — nothing arc-related). Combat inertness is measured, not assumed.
- Arc fill spot-checked across hull shapes: Omega (4-section) `loc1 330..30`, `loc2 150..210`,
  `loc3 210..330`, `loc4 30..150`, primary `0..360`; Xill/Kraken/Tyllz (6-section quarter
  locations) `loc31/32/41/42` all correct; hand-authored Vree/Centauri arcs unchanged
  (incl. Kraken's pre-existing unwrapped `300..420`, which its co-located weapons already carry,
  so the wedge renders exactly like they do).
- Static bundle regenerated (`generateStaticShipFile.php`): 98,953,201 → 99,027,409 bytes
  (+74KB — just `null` → numbers), and non-primary structures verified carrying real arcs in
  `static/json/`.
- Side effect worth knowing: `ShipSystem::setSystemDataWindow` emits `data["Arc"]` whenever
  `startArc !== null`, so structures now carry an `Arc` line in their static tooltip `data`.
  Nothing surfaces it today (structures are filtered out of the icon grid, and the new hover
  raises its own event with no tooltip) — and it would be accurate if it ever did.

### Not done here
- Manual in-game verification (the user's local Docker pass): 4-section hull, 6-section /
  quarter-location hull, rolled ship, primary shows nothing, flights show nothing, touch
  long-press. Both bundles were already rebuilt by the running `yarn watch` / `watch:legacy`
  watchers, so no build step is outstanding.
