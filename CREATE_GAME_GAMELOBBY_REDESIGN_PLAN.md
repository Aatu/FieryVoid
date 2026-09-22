# Create Game & Gamelobby Redesign Plan

Planning document only — nothing in this plan has been built yet. Covers two pages:
`source/public/creategame.php` (+ `client/UI/createGame.js`) and `source/public/gamelobby.php`
(+ `client/gamelobby.js`, `client/lobbyEnhancements.js`).

Also covers the ship **buy / edit / bulk-buy confirm dialogs** (`client/UI/confirm.js`,
`styles/confirm.css`) reached from Gamelobby's purchase panel — added 2026-09-22, see §10.

Companion to `GAMES_PAGE_REDESIGN_PLAN.md` and `VISUAL_UNIFICATION_PLAN.md` — this plan
**reuses** the visual language those two established (the `--fv-*` tokens in
`styles/tokens.css`, the "contact readout" card grammar in `styles/gamesPanel.css`) rather than
inventing a third look. Nothing here touches combat rules, movement, or firing.

Every numbered item from the brief is addressed below; each place I made a judgment call is
marked **Decision** with its reasoning, and §8 lists them together for a quick yes/no pass.

---

## 1. What exists today (audit)

### 1.1 Create Game (`creategame.php`, `client/UI/createGame.js`)

Single long form inside one `<panel class="panel large create">`, two-column split (Game
Options | Scenario Description), then a second panel for Map Layout & Teams, then chat. One
`<form id="createGameForm" method="post">`, one POST at submit
(`creategame.php:354-362` — `docreate=true` + a hidden `data` field createGame.js fills with
JSON before submit).

Findings:

- **Scenario Description is 9 raw `<select>`/`<input>` rows** (creategame.php:216-294) with no
  visual hierarchy beyond a shared label style; "Additional Info" is a plain 3-row `<textarea>`.
  These get client-concatenated into **one free-text string** (§1.1.1) that gamelobby.php later
  has to reverse-engineer with regex.
- **Terrain controls are hand-built, not reusable.** The asteroids/moons dropdowns
  (creategame.php:126-174) are custom divs with literal `style="width:140px;background-color:
  #0c1a28;..."` inline on every option row — doesn't touch `tokens.css`, and adding a third
  terrain type today means copy-pasting this whole block again.
- **Background picker is a blind `<select>`** of 26 numbered filenames (creategame.php:74-86,
  fed by `Manager::getMapBackgrounds()` at Manager.php:415) — zero visual preview for a choice
  that's purely visual (the hex-grid backdrop).
- **No password/private-game concept anywhere** — grepped `DBManager.php` for
  `password|private`, zero hits outside player auth. This is new schema, not a UI gap.
- **No "save these settings" anywhere** — every game starts from the same
  `$defaultGameName` + all-blank options (creategame.php:12-15).
- **Team/slot "Add" is already a clone-based mechanism** — `createGame.js:962` and `:1049`
  `.clone()` a hidden template div and rewrite its ids. "Copy Slot/Team" is a small variant of
  a mechanism that already exists, not a new one.
- **The map preview canvas already works well** — `#mapPreview` (545×390) already redraws live
  as team/slot fields change. This is the one piece of the page that already behaves like a
  modern tool; leave the mechanism alone, just give it a legend (§3.4).

#### 1.1.1 The free-text scenario round-trip — the core fragility

`createGame.js` builds `description` as one formatted string (e.g.
`"EXPECTED POWER LEVEL: Tier 1\nFLEET REQUIREMENTS: ...\n"`). `Manager::createGame`
(Manager.php:364-396, specifically line 372) stores it verbatim in `tac_game.description`
(TEXT). `gamelobby.php:690-741` then reconstructs structure by: stripping `<br>` tags,
stripping a `***...***` header line, splitting on newlines, then splitting **each line on its
first colon** to recover a label/value pair — with a hand-coded special case for "Additional
Info" (lines 716-725) because it can span multiple lines.

This is a text→text round trip standing in for structured data. It's the reason Scenario
Description looks "basic" on **both** ends: Create Game only offers flat rows because the
field is one string, and Gamelobby only offers flat label:value lines because that's all it
can reliably recover from that string (a free-typed victory condition containing a colon, e.g.
a time, would silently misparse).

**Decision:** move scenario data to a structured JSON column at creation
(new `tac_game.scenario`), alongside the existing free-text `description` (kept as-is for
anything else that reads it raw). Gamelobby then renders straight from JSON with no parsing.
This one schema change is what unlocks the readability upgrade on both screens at once — see
§6.

### 1.2 Gamelobby (`gamelobby.php`, `gamelobby.js`, `lobbyEnhancements.js`)

1244-line PHP file: header → name panel → scenario/map split → teams panel → buy panel
(filters, tier checkboxes, faction list, ship list) → chat.

Findings:

- **Scenario description renders via the fragile parser above**, as a wall of
  `<span class="scenariolabel">X:</span> <span class="scenariovalue">Y</span><br>` pairs
  (gamelobby.php:724-737) — no grouping, no visual distinction between "who can play" facts and
  "how the match ends" facts.
- **"OPTIONS SELECTED" is one comma-joined PHP string** (gamelobby.php:745, built at
  527-658) — `', Ladder Game, Simultaneous Movement (Brackets: 4), Mines Allowed, ...'` —
  unreadable past about four active options and impossible to scan for one flag.
- **Map preview is 400×300, unlabelled** (gamelobby.php:755-757) — no legend for deployment
  zones or terrain markers.
- **The faction list is already grouped** (`gamelobby.js:2448` `parseFactions`) into six
  buckets — Major/League of Non-Aligned Worlds/Minor/Ancients/Other/Custom — with a hand-rolled
  `[+]`/`[-]` text-toggle per group (line 2497). Functional, but: no search box, "Custom
  Factions" is one flat bucket (nowhere for the user's Nexus/Escalation-Wars/Other-Universe
  split to go), and a plain text glyph as the only tap target is a poor fit for touch.
- **The three faction "wheels" leave the site** — `gamelobby.php:674-680` links to
  `old.wheelofnames.com` for Tier 1/2/3 random picks, with no tie-in to this game's actual
  tier/custom filters (a wheel spin can hand you a faction the scenario forbids).
- **The ISD per-ship filter already exists and already works** — `#isdFilter`
  (gamelobby.php:813-817) is a manual numeric box, and it works because `ship.isd` is already a
  populated field on every ship class (`ShipClasses.php:66`, `public $isd = 0`). A **game-level**
  ISD cutoff is therefore a "seed and lock an existing control" problem, not a new-data problem
  — see §4.4.

---

## 2. Design language to reuse

Nothing new needs inventing — reuse the games.php "contact readout" grammar wholesale:

- `.fv-panel-head` bar per section, `--fv-card` / `--fv-card-hover` fills, `--fv-line-scs`
  borders, square `--fv-radius-chrome: 0`, Consolas for numerals.
- `.fv-well` / `.fv-card` for anything that is a *list of chips or rows* (scenario facts,
  faction groups, saved presets) instead of a new card component.
- `.fv-btn` family (from `gamesPanel.css`) for Next/Back/Confirm, replacing the legacy
  `.btn-create-submit` one-off styling.
- **New signature element for this pair of pages:** a left-edge accent rail per section, keyed
  to *topic* rather than urgency (Game Options = `--fv-accent` blue, Scenario =
  `--fv-warn` amber, Teams/Map = `--fv-own` green) — reusing tokens that already exist. The
  wizard's step indicator (§3.1) then reads as "which coloured rail am I in," the same way a
  games.php card's rail tells you your-turn vs waiting at a glance.

---

## 3. CREATE GAME redesign

### 3.1 Structure: a 4-step wizard, one page, one POST

Steps, matching the brief's own grouping:

1. **Game Options** — name, background (visual picker), ladder, movement, terrain, mines,
   reinforcements, desperate, friendly fire, points, **new:** private/password, **new:**
   In-Service Date.
2. **Scenario Description** — tier/power level, fleet requirements, custom factions, forbidden
   factions, enhancements, map borders, called shots, victory conditions, additional info —
   same fields, restructured presentation (§3.3).
3. **Teams & Map** — map template, map size, teams/slots with Copy Slot/Team, live map preview.
4. **Summary & Confirm** — read-only recap of all three steps, grouped exactly like the
   eventual gamelobby layout (so this screen doubles as "what your opponent is about to see"),
   Back jumps to the exact step, Confirm fires the one existing POST unchanged.

**Decision:** one page, one `<form>`, one POST — the four "steps" are `<section>`s toggled by a
small state machine in `createGame.js`, not four separate page loads. This keeps the entire
existing data contract (`Manager::createGame`, `GameRules`, `PlayerSlotFromJSON`) untouched —
the wizard is a client-side presentation change, not a new endpoint. Lower risk, no
half-created game sitting server-side if someone abandons mid-wizard, and the chat/ladder
`include()`s at the bottom of the page keep working exactly as now.

Progress indicator: four labelled steps in the panel head
(`GAME OPTIONS — SCENARIO — TEAMS & MAP — CONFIRM`), current step lit with `--fv-accent`,
**completed** steps stay clickable to jump back directly (not just a Back button). Validation
that blocks moving forward (e.g. a team needs ≥1 slot) shows inline using the existing
`.confirm`/warning styling — never a native `alert()`.

### 3.2 Step 1 — Game Options

- **Background picker → visual grid.** Replace the 26-item `<select>` with a scrollable strip
  of thumbnails (crop the existing 26 JPGs via CSS `background-position` — no new asset
  pipeline needed for v1), selection styled like an `.fv-card` pick (accent border + check
  glyph on the selected tile).
  *"Refresh existing backgrounds"* is a separate **content** task, not architecture —
  recommend re-cropping/upscaling the visually muddiest few (eyeball
  `8.Moon-AndreasSchantl.jpg`, `1.Default.jpg`) once the picker exists, not before; there's no
  point re-shooting art for a `<select>` nobody could see anyway.
  *"Add new map backgrounds"* needs **zero code** beyond the picker itself —
  `Manager::getMapBackgrounds()` already lists everything in `img/maps/` at request time.

- **Terrain options, made extensible.** Today: two hardcoded custom dropdowns (asteroid count,
  three moon-size counts). New ask: add "Dust and Meteorites" plus "a menu for adding more in
  future."
  **Decision:** generalise into one repeatable **Terrain Features** list — a
  "+ Add Terrain Feature" row where each entry is `{type: <select>, value: <number>}`, carried
  in the submitted JSON as `terrainFeatures: [{type, value}, ...]` instead of one flag per
  feature. Server-side, extend `GameRules.php` (source/server/model/GameRules.php) exactly the
  way `AsteroidsRule`/`MoonsRule` already do it — a private `getXRules($rules)` method plus one
  more `array_push`, and a new `DustAndMeteoritesRule` class matching the shape of the existing
  rule classes (lines 111-117 and 142-165 are the templates). Adding a fourth terrain type after
  that is one new `<option>` + one new Rule class — no UI rework. This is what "build menu with
  adding more in future" is actually asking for.

- **Existing checkboxes** (Ladder / Simultaneous Movement / Mines / Reinforcements / Desperate /
  Friendly Fire / Unlimited Points) — keep as checkboxes, add a one-line grey helper caption
  under each label (most of this is jargon to a new player — ties into
  [[project_dev_roadmap]] Tier-2 onboarding item). Group under the `.split-header` bar
  convention already in the file (creategame.php:64/217/300) — apply it consistently; today only
  two of the three columns actually get one.

- **New: In-Service Date.** A single year input next to the Tier/Custom-Factions notes (B5W
  eras are year-based, a bare year is enough precision). Stored as
  `tac_game.in_service_date` (nullable INT — blank = no cutoff, so every existing game's
  behaviour is unchanged). Feeds the gamelobby ISD filter directly (§4.4).

- **New: Private / password-protected game.** A "Private Game" checkbox reveals a password
  field. New `tac_game.password_hash` column (nullable — null = public). Reuse the
  `password_hash(PASSWORD_DEFAULT)` convention already established for player accounts
  ([[arch_auth_password_hash_migration]]) rather than inventing a second hashing scheme. The
  Join flow (games.php's Join Games list, and the actual slot-take action) needs a
  password-prompt gate — flagged as backend-touching, held to its own stage (§7, Stage 8).

- **New: Save & reuse settings.** "Save these settings" / "Load saved settings" next to the
  step header.
  **Decision, v1:** localStorage only (`fv.createGamePreset.<name>`), the same pattern
  `Settings.js` already uses for persisted player prefs (project map §4). Zero backend work,
  ships in the same stage as everything else in §3.2. A DB-backed, cross-device preset (a
  `tac_saved_game_settings` table shaped like the saved-fleet tables) is a natural v2 if presets
  ever need to survive a browser wipe or be shared — not built now since nobody asked for
  cross-device sharing yet ([[feedback_fv_workflow]] scope discipline).

### 3.3 Step 2 — Scenario Description, restructured

Every existing field stays (tier, requirements, custom factions, forbidden factions,
enhancements, borders, called shots, victory conditions, additional info) — the ask is
presentation, not content.

- Lay out as a **card grid** (2 columns desktop, 1 column mobile): each field gets its own
  small card — an eyebrow label plus its control — instead of a flat list of label/select rows.
  This is the SAME shape Gamelobby will render each field as (§4.1), so building one shared
  renderer (a small `client/UI/scenarioCard.js`, loaded by both pages) covers both without
  writing the layout twice.
- The existing "select one of a few + reveal a text box on Other" pattern stays exactly as-is
  functionally, just restyle the reveal as a short slide/height transition instead of an
  instant `display:none`→`block` swap — one deliberate motion moment here, not scattered
  everywhere else on the page.
- **Submit as structured JSON**, not a formatted string:
  ```
  scenario: {
    tier, tierCustom, fleetRequirements, customFactions, forbiddenFactions,
    enhancements, enhancementsPoints, mapBorders, calledShots,
    victoryConditions, victoryCustom, additionalInfo
  }
  ```
  This is the field that removes gamelobby's regex parser entirely (§4.1).
- **Backward compatibility:** existing lobbies only have the old free-text `description`.
  Gamelobby's renderer needs both paths — `scenario` JSON present → structured render; absent
  (a legacy game) → today's parser, unchanged. No backfill migration; old games simply age out.

### 3.4 Step 3 — Teams & Map

- Map template `<select>` and size inputs are already fine — unchanged.
- **Copy Slot / Copy Team** buttons sit next to "Add Slot"/"Add Team": clone the **source**
  row (not the blank template) using the exact `.clone()` mechanism already used for template
  cloning (`createGame.js:962`, `:1049`), copying every field's current `.val()` across and
  re-indexing ids/names the same way the existing Add flow already does. Additive to an
  existing function, not a new mechanism.
- The live preview canvas is untouched technically — it gains a legend strip underneath
  (your deployment zone / other team's / terrain) using the `--fv-own`/`--fv-enemy`/
  `--fv-neutral` tokens that already exist for exactly this purpose (tokens.css:220-229) but
  aren't used on this page yet.

### 3.5 Step 4 — Summary & Confirm

Read-only recap in the same three-card layout Gamelobby will use (§4.1) — this screen is
literally a preview of the opponent's view. Each card carries an "Edit" link back to its own
step, not a generic Back. Confirm fires the existing single POST (`docreate=true`) — no server
contract change from the wizard itself.

---

## 4. GAMELOBBY redesign

### 4.1 Scenario Description → structured render

- **New games** (with `scenario` JSON present): render as a **fact-card grid**, one small card
  per field — eyebrow label, value beneath — using `.fv-card`/`.fv-well`. This is the same
  component the Create Game summary step renders (§3.5); one shared `scenarioCard.js` renderer
  used by both pages avoids writing the layout twice.
- "OPTIONS SELECTED" (today's one comma-joined sentence) becomes a **row of chips** — one small
  pill per active rule ("Ladder", "Sim. Movement ×4", "Mines", "Asteroids (12)", "Friendly
  Fire") — reusing the `.fv-count-badge` pill markup already established in `gamesPanel.css`,
  instead of a run-on sentence that stops being scannable past four items.
- **Legacy games** (free-text `description` only): keep today's regex parser as the fallback,
  visually upgraded into the SAME card/chip shells where the data allows it (it still has
  labels/values, just recovered by regex instead of read from JSON) — so old and new games
  don't look jarringly different side-by-side.

### 4.2 Map Preview

Modestly bump the canvas (400×300 → ~480×360) now that the scenario column no longer competes
for the same visual weight, add the legend from §3.4, and label deployment zones with team
names/colours pulled from the same helpers tokens.css already calls out as the runtime twins
for this exact job (`gamedata.getTeamColorVars` / `getMutedTeamColorRGB`) — reuse, don't
reinvent a second colour path.

### 4.3 Faction picker overhaul

Current state: flat six-group outline list, `[+]`/`[-]` text toggles, no search
(`gamelobby.js:2448-2541`).

- **Add a search box** above the list ("Filter factions…") wired into the SAME filter pipeline
  the tier/custom checkboxes already drive (gamelobby.php:852-861) — one filter pipeline, two
  inputs feeding it, not a second competing mechanism.
- **Split "Custom Factions" into sub-groups** (Nexus, Escalation Wars, Other Universe /
  thematic packs) as asked. The data already carries this — the per-faction directory names
  under `model/ships/` are literally `Nexus*`, `Escalation*`, `BSG*`,
  `ZStarWars`/`ZTrek*`/`StarWarsCloneWars`, etc. (project map §3). Add a small lookup table
  (faction name → sub-group label) beside the existing `forceCustomGroup` override list
  (`gamelobby.js:2465`) — same mechanism, one more level of grouping applied only inside the
  Custom bucket; the five official-faction groups are untouched.
- **Mobile-first shape:** collapse the whole picker to a single combobox-style control on
  narrow viewports — tapping it opens a full-height sheet (search box pinned at top, grouped
  list beneath) rather than a long inline accordion the player has to scroll past to reach the
  ship list below it. Desktop keeps the current inline accordion — it already works there —
  and just gains the search box.
- Group headers get a real disclosure triangle + a larger tap target (today: a plain `[+]`/
  `[-]` text glyph, `gamelobby.js:2497`) — small change, meaningfully better on touch.

### 4.4 In-Service Date filter

- When a game has `in_service_date` set (§3.2), Gamelobby **pre-fills and locks** the existing
  `#isdFilter` box (gamelobby.php:813-817) to that value, swapping its label to something like
  "In-Service Date: 2258 (fixed by scenario)" — reusing the exact filtering logic already wired
  to that input, since `ship.isd` is already populated per ship. Zero new filtering code; this
  is "seed and lock an existing control."
- When no cutoff is set (legacy games, or a creator who left it blank), the box behaves exactly
  as it does today — fully backward compatible.

### 4.5 FV's own faction randomiser

Replace the three external Wheel-of-Names links (gamelobby.php:674-680) with an in-page
**"Randomise My Faction"** control:

- A button plus a small toggle ("Restrict to: current tier filters / all allowed factions")
  sitting next to the tier checkboxes it reads from.
- Fully client-side: the faction list is already loaded and already tier/custom-tagged
  (`gamelobby.js:2448-2541`) — the randomiser picks a random entry from whatever the CURRENT
  filter state resolves to, so it can never suggest a faction the scenario forbids, then opens
  and highlights that faction's group. No server round-trip, no new data.
- The "custom toggle" the brief asks for **is** the existing tier/custom filter state, reused
  rather than duplicated as a second setting.
- This drops the external-site dependency entirely — nothing on this flow should send a player
  off `fieryvoid.eu` to pick a faction.

---

## 5. Shared UI principles (applied per the Game UI Frontend skill guidance)

Both pages are pre-game **configuration** screens, not the live playfield — the skill's
"protect the playfield" rule translates here as "protect the decision the player is mid-way
through":

- One primary action visible at a time (Next/Confirm on Create Game; Ready/Save Fleet on
  Gamelobby) — never buried below a long scroll, which is today's Gamelobby buy-panel problem
  on a phone.
- Secondary/rarely-touched settings (Additional Info free text, forbidden-factions free text,
  saved-preset management) sit behind a clearly labelled disclosure, not open by default.
- Reserve real motion for actual state changes (the wizard step transition, the faction sheet
  sliding up on mobile); keep hover/focus states cheap and instant everywhere else, and respect
  `prefers-reduced-motion` — already a pattern in `gamesPanel.css:1149`, reuse it rather than
  re-deriving it.
- Design mobile-first for the two genuinely hard mobile surfaces named in the brief: the
  faction picker (§4.3) and the wizard navigation (§3.1) — both get an explicit mobile layout
  in this plan, not a media-query afterthought bolted on later.

---

## 6. Data model / schema changes

**Flagged separately — none of this should be built without a sign-off pass, since it's the
one part of this plan that touches `DBManager.php` / `db/emptyDatabase.sql` rather than pure
front-end.**

| Change | Column | Nullability | Risk |
|---|---|---|---|
| Structured scenario | `tac_game.scenario` (JSON/TEXT) | nullable, additive | Low — old rows are NULL, fallback parser (§3.3) handles it |
| In-Service Date cutoff | `tac_game.in_service_date` (INT) | nullable, additive | Low |
| Private game | `tac_game.password_hash` (VARCHAR) | nullable, additive | Medium — touches the JOIN flow (games.php's Join Games list + the slot-take action), needs its own review pass; same rollback caution as [[arch_auth_password_hash_migration]] applies to whatever hashing helper is reused |
| New terrain rule(s) | none — lives in the existing `rules` JSON blob | n/a | Low — follows the existing `GameRules.php` extension pattern exactly |
| Saved presets (v1) | none — localStorage | n/a | None |

All four DB-touching changes are additive-nullable columns on `tac_game` — no existing row
needs a backfill, no existing query breaks. Per repo convention, each ships as its own named
patch file (e.g. `db/addGameScenarioJson.sql`, `db/addGameInServiceDate.sql`,
`db/addGamePassword.sql`) plus the matching block in `db/emptyDatabase.sql`, exactly like
`addGameRules.sql` did for the existing `rules` column.

---

## 7. Staged build-out

Each stage is independently shippable and testable on its own.

- **Stage 0 — Shared groundwork.** No user-visible change: add the three additive columns
  (§6, minus the password join-flow work), stub the shared `scenarioCard.js` renderer. Lets
  every later stage build on real columns instead of a guessed shape.
- **Stage 1 — Create Game visual pass, still single-page.** Restyle in place: background
  picker grid, Terrain Features list, consistently grouped headers, structured scenario submit
  (§3.2/§3.3) — without the wizard yet. Biggest readability win, fastest, and the safest place
  to prove the new `scenario` JSON round-trips correctly before building navigation on top of
  it.
- **Stage 2 — Create Game wizard shell.** Wrap Stage 1's sections into the 4-step navigator +
  summary screen (§3.1/§3.5). Purely client-side, no new server contract.
- **Stage 3 — Copy Slot/Team + save/reuse presets (localStorage).** Small, isolated, no schema
  dependency.
- **Stage 4 — Gamelobby scenario/map rendering.** Structured-JSON render + legacy fallback
  (§4.1), map preview legend (§4.2). Depends on Stage 0's `scenario` column existing.
- **Stage 5 — Gamelobby faction picker overhaul.** Search, custom sub-groups, mobile sheet
  (§4.3). Independent of every other stage — could ship first if the mobile complaint is the
  most urgent one; ordered here only because it's the largest single chunk of new JS.
- **Stage 6 — In-Service Date end-to-end.** Create Game field + Gamelobby locked filter
  (§3.2/§4.4). Small, once Stage 0's column exists.
- **Stage 7 — FV faction randomiser.** Replaces the Wheel links (§4.5). Purely additive, no
  dependency on other stages.
- **Stage 8 — Private/password games.** Held to last deliberately — the only change touching
  the JOIN flow and therefore auth-adjacent code (games.php's Join Games list, the slot-take
  path). Wants its own focused review pass rather than riding along with a UI stage.
- **Stage 9 — Buy/Edit/Bulk-Buy dialog restructure** (§10.2). Accordion sections + sticky
  total-cost bar, built once against `confirm.showShipBuy`/`showBuyBulk` since both dialogs
  share the same row-building code. Independent of every Create Game/Gamelobby-page stage
  above — could ship any time, including before Stage 1, since it touches a different file
  entirely (`confirm.js`/`confirm.css`, not `gamelobby.php`/`createGame.php`).
- **Stage 10 — Filter box inside the buy dialog** (§10.2). Small, rides after Stage 9 since it
  assumes the accordion sections exist to filter within.
- **"Dust and Meteorites"** rides with Stage 1 (the UI slot for it) plus a small
  `GameRules.php` addition any time before Stage 1 ships — content, not its own stage.
- **Map background refresh** is art curation, not a coding stage — flagged for review once
  Stage 1's picker exists and the muddy ones are actually visible.

---

## 8. Open decisions — defaults I picked, flag any you want changed

1. **Wizard shape:** one page/one POST with client-side steps, vs. genuinely separate PHP
   pages per step. Picked: one page (§3.1) — lower risk, no partial-game server state.
2. **Saved presets:** localStorage-only v1 vs. DB-backed from day one. Picked: localStorage
   (§3.2) — matches the `Settings.js` precedent, no schema needed, upgrade path stays open.
3. **Scenario storage:** a new `tac_game.scenario` column vs. folding it into the existing
   `rules` JSON. Picked: separate column (§6) — `rules` has an established `GameRules.php`
   contract; mixing presentation-only fields into it risks an unrelated rule-parsing
   regression later.
4. **Terrain extensibility:** a generic repeatable "Terrain Features" list vs. one more
   hardcoded checkbox+dropdown pair for Dust and Meteorites alone. Picked: generic list
   (§3.2) — barely more work now, and it's literally what "build menu with adding more in
   future" asks for.

---

## 9. Testing / verification

No automated coverage exists for either page today — the replay harness
([[project_replay_harness]]) is server-simulation only and doesn't touch page rendering.
Verification is manual, per stage:

- Desktop 1920×1080 and a real phone width (390px) screenshot pass, before/after, each stage.
- Per [[feedback_fv_workflow]]'s testing convention: create a **fresh** test game per test
  (never `safeGameID`), covering both a legacy-shaped game (created with today's code, then
  viewed under the new Gamelobby renderer, to prove the fallback path) and a new-shaped game
  (created and viewed fully through the new wizard).
- Confirm `fvbuild.ps1 -Check` still passes after any DB/autoload-touching stage (Stage 0,
  Stage 8) — no ship-data or replay regression is expected, but it's the standing pre-deploy
  gate.

---

## 10. Buy / Edit / Bulk-Buy Ship Dialogs (added 2026-09-22)

Reached from Gamelobby's purchase panel (`.addship` click → `gamelobby.buyShip`/`buyBulk`/
`editShip`, `gamelobby.js:3538`/`3228`/`3889`). Not a separate page — a `.confirm` modal built
entirely in JS, shared by all four pages via `confirm.css`. In scope because the number of
Enhancements/Options a ship can carry has grown past what the dialog's layout was designed for.

### 10.1 What exists today (audit)

- **One flat, unbounded list.** `confirm.showShipBuy` (`confirm.js:1242`, the single-ship buy
  dialog) and `confirm.showBuyBulk` (`:1487`, shared by bulk-buy AND edit — `existing` is the
  only switch between them) both loop over `ship.enhancementOptions` and `.prependTo()` one
  `.missileSelectItem` spinner row per entry into the same scrolling box. There is no grouping,
  no pagination, no collapse — a ship with a long enhancement/option/ammo list is just a long
  scroll.
- **Category signal already exists in the data, but only drives text colour.** Each row already
  knows what it is: `enhIsOption` (enhancement vs. option) and an ammo-type substring match
  (`HEAVY AMMO`/`MEDIUM AMMO`/`LIGHT AMMO`/`AMMO`, `confirm.js:1328` and `:1567`) — today these
  only prepend a coloured `<span>` to the row's own label (amber for options, cyan for ammo).
  Nothing structural reads these flags; they're the ready-made grouping key.
- **The Total Cost readout isn't pinned, and ends up buried.** `showShipBuy` inserts the
  `.totalUnitCost` row once, *before* the enhancement loop (`confirm.js:1250-1251`) — but every
  row added afterwards also calls `.prependTo(e)`, which inserts at the top of the container
  each time. By the time the loop and the missile-options/fighter-size rows that follow are all
  added, the total-cost row has been pushed down past all of them. On a ship with many rows the
  running total scrolls out of view during exactly the interaction (adding more things) where a
  player most needs to see it.
- **The dialog shell is a fixed 540px box that just grows and scrolls**
  (`confirm.css:21-42` — `max-height:100%; overflow-y:auto`, no internal sectioning). This is
  the literal container for every `multi-value-confirm` variant in the file (hangar
  dock/launch/recover dialogs use the same shell) — whatever layout mechanism is added should
  stay compatible with that shared shell rather than forking it.
- **Dead code found in passing:** `confirm.showShipBuy` is defined **twice** in the same object
  literal (`confirm.js:653` and `:1242`) — the second silently wins, matching the
  `Manager::advanceGameState` duplicate-definition pattern already known elsewhere in this
  codebase. Whoever builds this stage should delete the first copy rather than edit it by
  mistake.

### 10.2 Design direction: accordion sections + a sticky summary, not tabs

**Decision:** group rows into collapsible **accordion sections**, not tabs. Tabs hide whatever
is selected in a category the instant you leave it, which is the wrong tradeoff for a screen
whose entire job is tracking a running cost across everything you've picked — this dialog
already has one real bug in that direction (the buried total, above), and tabs would add a
second, worse one. An accordion keeps every section reachable without a full page of open rows,
while never fully hiding a category's selected state:

- **Sections, built from data that already exists** (no new server-side modelling needed):
  **Ammo & Ordnance** (the existing ammo-type tags + missile options), **Enhancements**
  (`enhIsOption === false`), **Options** (`enhIsOption === true`), and a **reserved fourth
  section for Officers** if/when that feature is designed — this plan only reserves the slot in
  the layout, it does not design what an Officer is or does; that's a game-rules feature with
  its own scope, not a UI arrangement question.
- **Each collapsed section header shows a count + subtotal badge** — e.g. "Enhancements · 3
  selected · 45pts" — reusing the `.fv-count-badge` pill pattern from `gamesPanel.css` so a
  collapsed section never fully hides what's chosen inside it.
- **A sticky total-cost bar** (`position: sticky`, pinned to the top or bottom of the `.confirm`
  box) replaces the current prepend-and-get-buried row — this is the direct fix for the bug in
  §10.1, independent of whether accordion sections ship in the same pass.
- Sections default to **open when the ship has few rows total** (today's experience,
  unchanged for a lightly-equipped ship) and **collapsed by category once the row count passes
  a threshold** (say ~8), so the dialog only gets more structured exactly when it's currently
  straining, not always.
- Multiple sections may be open at once — this isn't a radio-tab switch, just a way to hide bulk
  you aren't currently adjusting.

**Also add a filter box** at the top of the dialog ("Filter…") for ships with a lot of rows —
grouping helps browsing; a text filter is the actual fix for "I know the name of the
enhancement I want," which a B5W player very often does. Filters within whichever sections are
open; an empty section (everything filtered out) collapses itself.

Both `showShipBuy` and `showBuyBulk` should keep building from the **same** row-construction
code (as they already mostly do) so the accordion/sticky-bar/filter logic is written once and
shared — do not fork into two copies the way the dead `showShipBuy` duplicate shows this file
has drifted before.

### 10.3 Schema / backend impact

**None.** Every row already carries the flags needed to group it (`enhIsOption`, ammo-type
string). This is a pure `confirm.js` + `confirm.css` restructure — no `tac_game`/`tac_ship`
column, no `Manager.php`/`DBManager.php` change, no `GameRules.php` involvement. The eventual
Officers feature, whenever it's designed, is the one part of this that WOULD need new schema —
tracked as a future item, not part of this UI stage.

### 10.4 Open decision

**Collapse threshold** — I picked "~8 rows in a section" as the point where it defaults
collapsed rather than open; this is a guess pending your eye on how the sections actually fill
up once built. Easy to retune as a single constant once it's in front of real ship data (a
heavily-loaded capital ship vs. a bare fighter will want different defaults, which is exactly
why it's a per-section count check rather than a global one).
