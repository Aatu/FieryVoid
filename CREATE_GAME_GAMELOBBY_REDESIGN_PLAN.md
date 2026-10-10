# Create Game & Gamelobby Redesign Plan

**Build status: Stages 0-1 BUILT 2026-09-23, Stages 2-3 BUILT 2026-09-24, Stages 4-8 BUILT 2026-09-25
(see §12); Stages 9-10 not started.** Covers two pages:
`source/public/creategame.php` (+ `client/UI/createGame.js`) and `source/public/gamelobby.php`
(+ `client/gamelobby.js`, `client/lobbyEnhancements.js`).

Also covers the ship **buy / edit / bulk-buy confirm dialogs** (`client/UI/confirm.js`,
`styles/confirm.css`) reached from Gamelobby's purchase panel — added 2026-09-22, see §10.

**Read §11 before implementing anything.** A Design-canvas mockup pass (2026-09-22) validated
and corrected a number of decisions in this plan — most significantly the faction-picker
architecture (§4.3), several real-code facts about how the Store ship list and team colours
actually render that weren't in the original audit, and a few UI decisions this plan guessed
wrong the first time. Where §11 and an earlier section disagree, §11 is the corrected version;
the earlier sections have been patched in place where practical, but §11 is the complete record.

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
  to *topic*. **Final rail assignment, settled across the mockup rounds (supersedes the initial
  guess in this bullet):** the wizard's STEP-INDICATOR tab bar is simplest — Steps 1-3 always
  `--fv-accent` blue, Confirm always `--fv-own` green, regardless of which step's content you're
  looking at (this is the one place "urgency"-style colour, not topic colour, won this argument).
  Inside each step, content cards use: Game Options = `--fv-accent` blue; Scenario Description =
  `--fv-purple` (a new token, not urgency-coded — kept deliberately distinct from Gamelobby's own
  Scenario cards, which stayed blue); Teams & Map = `--fv-own` green for Team A's card/map zone,
  `--fv-enemy` red for Team B's (NOT `--fv-ally` blue — see §11.4, this was wrong for a full
  round before the real team-colour semantics were confirmed). On the CONFIRM step's summary
  cards specifically, all three cards (Game Options/Scenario Description/Teams & Map) are
  `--fv-accent` blue, uniformly — an explicit later correction, not amber/green as an earlier
  mockup pass had them. See §11 for the rest of what the mockup settled and corrected.

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
enhancements, borders, victory conditions, additional info) — the ask is presentation, not
content. **Called Shots is dropped** (user decision, confirmed against the mockup 2026-09-22) —
do not carry that field into the structured `scenario` JSON below.

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
    v, tier, tierCustom, fleetRequirements, fleetRequirementsCustom, customFactions,
    forbiddenFactions, enhancements, enhancementsPoints, mapBorders,
    victoryConditions, victoryCustom, additionalInfo
  }
  ```
  This is the field that removes gamelobby's regex parser entirely (§4.1). **Corrected at
  Stage 0:** the original list here missed `fleetRequirementsCustom` (the real form's
  Fleet Requirements select has an "Other" + `#req_custom` reveal, same as Tier and Victory),
  and gained a `v` schema version. The authoritative key list is now
  `scenarioCard.FIELDS` in `client/UI/scenarioCard.js` — §12.1.
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

**Reversed from this section's original guess, per the mockup (§11.3):** the top of Gamelobby
ended up as a three-column row — **Teams | Scenario Description | Map Preview** — rather than a
two-column Scenario/Map row with Teams full-width below. In three columns Map Preview is the
*narrowest* of the three (its own share, with Scenario Description and Map Preview stretched to
match each other's height, while Teams sits at its own natural height beside them) — so the
canvas actually gets SMALLER than today's 400×300, not bigger. Still gets the legend (deployment
zones + terrain markers) below the map. Team colours for the deployment-zone labels/overlays are
**not** `gamedata.getTeamColorVars`/`getMutedTeamColorRGB` (no such function was found this
session) — see §11.4 for the real, verified team-colour functions and which one applies to a
2-team vs. 3+-team game.

### 4.3 Faction picker overhaul

Current state: flat six-group outline list, `[+]`/`[-]` text toggles, no search
(`gamelobby.js:2448-2541`), with the ship list (all categories, all variants) rendered inline
underneath whichever faction is expanded — see §11.2 for exactly how deep that nesting really
goes (base hulls + every variant, not just base hulls as an earlier mockup pass wrongly assumed).

**Architecture revised from this section's original shape, validated across 3 mockup rounds —
this is the single biggest structural change to come out of the mockup pass:** picking a
faction and browsing that faction's ships are now two SEPARATE steps, on both desktop and
mobile — not "inline accordion on desktop, combobox sheet on mobile" as first planned here.

- **The Store/purchase panel shows ONLY the currently-active faction's ships**, scoped, never
  the full group→faction tree. A compact "[Faction name] · Switch Faction ▾" context bar sits
  above the ship list; the ship list itself is flat (category → base hull → nested variants),
  no faction-level nesting at all once a faction is picked.
- **A dedicated Faction Picker — same shape on desktop and mobile** — is where the six-group
  tree actually lives: search box pinned at top, the six groups (Custom Factions still split
  into its Nexus/Escalation Wars/Other Universe sub-groups, unchanged from the original ask),
  each faction a row, **stopping at faction level** — no ship/category nesting inside the
  picker at all. Desktop opens it as a centred modal; mobile opens it as a full-height sheet.
  Picking a row closes the picker and scopes the Store panel to that faction.
- **Tier/Custom filters and the faction randomiser (§4.5) live IN the picker**, not above the
  ship list — they scope which FACTIONS are selectable, not which ships show, so they belong
  where faction-picking actually happens.
- **Add a search box** inside the picker ("Filter factions…"), wired into the same filter
  pipeline the tier/custom checkboxes already drive (gamelobby.php:852-861).
- **Split "Custom Factions" into sub-groups** (Nexus, Escalation Wars, Other Universe) as asked
  — unchanged from the original plan. The data already carries this (per-faction directory names
  under `model/ships/` are literally `Nexus*`, `Escalation*`, `BSG*`,
  `ZStarWars`/`ZTrek*`/`StarWarsCloneWars`, etc., project map §3) — add a lookup table beside the
  existing `forceCustomGroup` override list (`gamelobby.js:2465`), applied only inside the
  Custom bucket. **Custom Factions gets a distinct yellow accent** (matches the mockup's
  `--fv-warn` treatment) in the picker; every other (non-custom, non-selected) faction row
  shares one neutral rail colour — no more one-colour-per-faction, simplified after mockup
  feedback ("for now," may return as a per-faction thing later).
- Group headers get a real disclosure triangle + a larger tap target (today: a plain `[+]`/
  `[-]` text glyph, `gamelobby.js:2497`) — unchanged from the original ask.
- **Ship-level "Show Custom" is a SEPARATE toggle from the picker's faction-level "Show
  Custom."** `applyCustomShipFilter` (gamelobby.js ~2966, `#toggleCustom`) hides any
  fully-CUSTOM ship (`ship.unofficial === true`, not `'S'`/SEMI-CUSTOM) inside an otherwise
  official faction's ship list, independent of which factions are selectable. The mockup's
  Store panel does not yet have a control for this — flagged as a gap to design, not built.

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
  sitting **inside the Faction Picker** (§4.3's dedicated modal/sheet), next to the tier
  checkboxes it reads from — not next to the ship-browsing panel, since picking now happens in
  the picker, not the Store view.
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
- **Every stage must be mobile-friendly, not just those two surfaces** (user requirement,
  2026-09-23: "the new page design should be mobile friendly too", applying throughout the
  project). Each stage's own markup/CSS must work at 390px before it counts as done — §9's
  phone-width screenshot pass is a gate, not a nice-to-have. Prefer layouts keyed to their
  CONTAINER's width (see §12.1's fact grid) over viewport media queries: the same component
  sits in a third-width lobby column on desktop and a full-width phone screen.

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

Each stage is independently shippable and testable on its own. **Stages 1-3 (Create Game) have
a user-approved visual reference** — the mockup canvas's 4 wizard-step artboards (§11) — build
against those directly rather than re-deriving layout from this section's prose description.

- **Stage 0 — Shared groundwork. ✅ BUILT 2026-09-23 — as-built record and traps in §12.1.**
  No user-visible change: add the three additive columns (§6, minus the password join-flow
  work), stub the shared `scenarioCard.js` renderer. Lets every later stage build on real
  columns instead of a guessed shape.
- **Stage 1 — Create Game visual pass, still single-page. ✅ BUILT 2026-09-23 — §12.2.**
  Restyle in place: background
  picker grid, Terrain Features list, consistently grouped headers, structured scenario submit
  (§3.2/§3.3) — without the wizard yet. Biggest readability win, fastest, and the safest place
  to prove the new `scenario` JSON round-trips correctly before building navigation on top of
  it.
- **Stage 2 — Create Game wizard shell. ✅ BUILT 2026-09-24 — §12.3.** Wrap Stage 1's sections
  into the 4-step navigator + summary screen (§3.1/§3.5). Grew three user additions: moon counts
  as count combos, **Maps with Terrain** (templates with pre-placed terrain — a new server rule),
  and faint white hexes in game for Dust / Meteor Swarms.
- **Stage 3 — Copy Slot/Team + save/reuse presets (localStorage). ✅ BUILT 2026-09-24 — §12.4.**
  Small, isolated, no schema dependency. User placement change: Save Settings sits beside
  Confirm & Create Game on the Confirm step, not in Game Options; Load Settings stays beside
  Game Name.
- **Stage 4 — Gamelobby scenario/map rendering. ✅ BUILT 2026-09-25 — §12.5.** Structured-JSON render + legacy fallback
  (§4.1), map preview legend (§4.2). Depends on Stage 0's `scenario` column existing.
- **Stage 5 — Gamelobby faction picker overhaul. ✅ BUILT 2026-09-25 — §12.6.** Search, custom
  sub-groups, mobile sheet (§4.3). Grew the whole Purchase Fleet panel restyle (the Store scoped
  to one faction needed it) and four user refinements: Check as a Recent-Games-coloured button
  opening its report in a window, a Store that grows to fit, a wider Store column, and variant
  links not italic.
- **Stage 6 — In-Service Date end-to-end. ✅ BUILT 2026-09-25 — §12.7.** Create Game field +
  Gamelobby locked filter (§3.2/§4.4), plus an "In-Service Date: N" rule chip on the Confirm step
  and in the lobby.
- **Stage 7 — FV faction randomiser. ✅ BUILT 2026-09-25 — §12.8.** Replaces the Wheel links
  (§4.5): the Faction Picker's sticky footer, rolling from the rows the picker's filters leave.
- **Stage 8 — Private/password games. ✅ BUILT 2026-09-25 — §12.9.** Held to last deliberately — the only change touching
  the JOIN flow and therefore auth-adjacent code (games.php's Join Games list, the slot-take
  path). Wants its own focused review pass rather than riding along with a UI stage. As built:
  a password page in place of the lobby, and slot.php refusing a slot, until the password is
  entered; a Private tag in Join Games; a Private Game chip.
- **Stage 9 — Buy/Edit/Bulk-Buy dialog restructure. ✅ BUILT 2026-09-26 — §12.10.** Accordion
  sections + a fixed foot holding the total, built once for `confirm.showShipBuy` / `showShipEdit` /
  `showBuyBulk` (one shell, one set of row builders). User placement change: the Total Cost sits on
  its own right-aligned row ABOVE Cancel / Buy Ship, not beside the buttons as in the mockup.
- **Stage 10 — Filter box inside the buy dialog. ✅ BUILT 2026-09-26 — §12.11.** (§10.2) Small,
  rides after Stage 9 since it assumes the accordion sections exist to filter within. As built: a
  search box just above the first section's head on any dialog with 8+ rows; while it holds text, every
  section with a match opens and every section without one folds, dimmed, keeping its badge.
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
   hardcoded row per type. **Reversed after the mockup (see §11.6):** the mockup built FIXED
   rows — one row per known terrain type (Asteroids, Moons Small/Medium/Large, Dust Clouds,
   Meteorites), no "+ Add Terrain Feature" affordance — and the user approved that shape across
   two rounds without asking for the generic add-row UI back. This still maps cleanly onto
   `GameRules.php`'s real extension pattern (one new Rule class per terrain type, e.g.
   `DustAndMeteoritesRule`) — "extensible" turns out to mean "cheap to add a new fixed row +
   Rule class when a type ships," not "player-facing generic add-a-row control." Build the fixed
   list, not the generic repeater.

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

**RESOLVED 2026-09-26 (user): no collapse threshold at all** — every section starts open and the
player folds it by hand (§12.10). The original proposal, kept for the record:

**Collapse threshold** — I picked "~8 rows in a section" as the point where it defaults
collapsed rather than open; this is a guess pending your eye on how the sections actually fill
up once built. Easy to retune as a single constant once it's in front of real ship data (a
heavily-loaded capital ship vs. a bare fighter will want different defaults, which is exactly
why it's a per-section count check rather than a global one).

### 10.5 Refinements from the mockup pass

- **A "Base Hull" line item** sits above the accordion sections, showing the ship's own point
  cost, so the sticky total is arithmetically transparent (base + every section's subtotal =
  the total shown) rather than an unexplained number.
- **Ammo & Ordnance rows are NOT colour-coded by ammo type.** Today's dialog prepends a
  coloured `<span>` per ammo tier (amber/cyan per §10.1's audit) — drop this in the redesign.
  Every action link and every row label in this dialog family reads in one consistent colour;
  colour-per-category was tried in the mockup and explicitly rejected.

---

## 11. Mockup findings (Design-canvas pass, 2026-09-22) — read before implementing

Canvas: https://claude.ai/artifact/4z8DSdV35K2AxZ5VmUkG1w — a Design-canvas mockup covering all
4 Create Game wizard steps, Gamelobby's main screen, both faction pickers (desktop modal +
mobile sheet), the Buy Ship accordion dialog, a with/without-Reinforcements bought-fleet
comparison, and a 4-team panel variant. **Create Game (all 4 steps) is user-approved and closed
as of this pass** ("I am content for now with Create Game design"); Gamelobby is still open to
further iteration. Read the canvas directly for exact layout/copy before implementing — this
section is the durable facts extracted from it, not a substitute for looking at it.

This section supersedes anything above it in this document where the two disagree (several
earlier assumptions — faction-picker shape, map-preview sizing direction, terrain UI shape, rail
colours, team colours — turned out wrong or were revised after real-code verification; the
corrections are already folded into §2/§3.3/§4.2/§4.3/§4.5/§8 above, this section is the
supporting detail and the parts that don't have an obvious home elsewhere in the doc).

### 11.1 Real category order and open/closed defaults for the Store ship list

The 7 size-class categories render in this order top-to-bottom — **Mines, Immobile Structures,
Capital Ships, Heavy Combat Vessels, Medium Ships, Light Combat Vessels, Fighters** (ending with
Fighters) — the reverse of the array order `sizeClassHeaders` is declared in
(`gamelobby.js:2783`), because the render loop walks it backwards. Default open/closed: Capital
Ships, Heavy Combat Vessels, Medium Ships and Fighters start open; Light Combat Vessels,
Immobile Structures and Mines start closed (a category's own ship rows still sort correctly
whether or not it starts collapsed). Within an open category, base hulls sort by cost
DESCENDING, except Mines, which sorts alphabetically
(`orderShipListOnPV` vs `orderShipListOnName`, gamelobby.js:2412/2824).

### 11.2 Variants are real, independently-purchasable rows — get this right the first time

**This was gotten wrong twice during the mockup pass before a user screenshot of the live game
settled it — do not re-derive, this is verified against primary source.** A ship variant
(`ship.variantOf != ''`, an alternate loadout of a base hull) is NOT hidden inside a "pick your
loadout" step of a Buy dialog. It is its own row, indented directly under its base hull, with
its own independent "Add to fleet"/"Show details" links — exactly like a base hull, just styled
differently. The real render function is **`parseShips`, `gamelobby.js:2764-2930`** — there is a
dead, never-invoked "old, simple version" of the same function name at lines 2679-2698 that
caused the first misread; confirm which `parseShips` is actually called before trusting a
reading of this area again. Nesting mechanism: an outer loop walks cost-sorted base hulls only
(`if (ship.variantOf != '') continue;` at line 2858 — this only stops a variant from being used
as an outer anchor, it does not hide it); a separate inner loop
(`gamelobby.js:2885-2914`) re-scans the full ship list for every variant of that base hull and
appends each one immediately beneath it, in the base hull's own category regardless of the
variant's own `shipSizeClass`.

**Visual treatment** (`prepareClassName`, gamelobby.js:2701-2740, cross-checked against a
live-game screenshot): base hull name → **bold**, colour `#90b1ee`. Variant name → **italic +
indented**, colour `#578bec` — two distinct blue shades, this is the actual distinction, not a
single shared "dim" colour. Category header text: `#d0dbec` (`.categoryType`, `lobby.css:234`).
Action links ("Add to fleet"/"Show details"): `#DEEBFF` (`.store .ship .clickable`,
`lobby.css:364`).

**Every ship name carries a real "(TAG)" suffix**, built from three ship-object fields (no
lookup table): rarity letter from `ship.occurence` (misspelled in the codebase, not
"occurrence") — common→C, uncommon→U, rare→R, unique→Q; an optional `NN%` from `ship.limited`
shown only when `0 < limited < 100`; and `SEMI-CUSTOM` (`ship.unofficial === 'S'`) or `CUSTOM`
(`ship.unofficial === true`, boolean). A fully-CUSTOM (not semi-custom) variant is hidden by
default — see §4.3's ship-level "Show Custom" note. Fighters additionally carry a
`[H]`/`[M]`/`[L]`/`[U]`/`[SHF]` size badge (`getFighterSizeTag`, gamelobby.js:2745-2761) and a
per-unit cost note for squadron purchases, e.g. "348p (58 ea.)".

### 11.3 Gamelobby top-of-page: three columns, Teams first

Final layout, left to right: **Teams | Scenario Description | Map Preview**. Teams sits at its
own natural height; Scenario Description and Map Preview are the two that visually match height
(nested in their own sub-row) — Teams is deliberately NOT forced to match, since its height
varies with team/slot count. With 3+ teams, the Teams panel grids into 2 columns (same pattern
as the separate 4-team artboard) instead of stacking indefinitely — not demonstrated live in the
2-team mockup artboard, since that would require actually showing a 3+-team scenario there.
Scenario Description's own internal order: the "Game Rules" chip row (Ladder/Sim.
Movement/Mines/etc., one pill per active rule) sits ABOVE the fact-card grid, not below it — an
explicit reorder request. A subtlety worth remembering for any stretched/flex-matched card
layout: if a panel gets force-stretched taller than its own content, `justify-content:
space-between` on its content does NOT close the resulting gap — it just relocates the gap to
BETWEEN the panel's own children. The actual fix is to not force-stretch panels whose content
heights are allowed to differ (`align-items: flex-start` on the row, not `stretch`).

### 11.4 Team colours — verified against `gamedata.js`, do not invent

**2-team games** use the RELATIVE mine/ally/enemy scheme already established everywhere else in
the app (combat log, ship icons, `getFleetHeaderColorRGB`): green = your own team, red = the
OTHER team (enemy), blue = "ally" — which specifically means *same team, different player slot*,
never "the other team." A viewing participant's own team is always green; the other of the two
teams is always red — never blue, however tempting "give team B its own colour" looks. Tokens:
`--fv-own`/`--fv-own-signal` (green) and `--fv-enemy`/`--fv-enemy-signal` (red), already in
`tokens.css`.

**3+ team games use a completely separate, ABSOLUTE per-team-index palette**
(`teamBaseColorsMultiTeam`, `gamedata.js`): 1 Green / 2 Orange / 3 Cyan / 4 Purple / 5 Yellow /
6 Blue / 7 Magenta / 8 Red — do not reuse the 2-team own/ally/enemy tokens for a 3+-team
context; they mean something different there (there's no single unambiguous "ally"/"enemy" once
more than 2 teams exist). `tokens.css` has no ready-made tokens for team-index 2-8; the mockup
added local `--fv-team2`/`--fv-team3`/`--fv-team4` (`#ff9628`/`#28e6e6`/`#aa5ae6`) for its
4-team demo artboard — worth promoting to real `tokens.css` entries if this palette gets used
more than once in the real build.

**Reinforcements' real colour is `#00b8e6` cyan** — found in `gamedata.js` comments ("FV's 'not
here yet' cyan, the same value as the blue Jump Point marker and the fleet list's hyperspace
rows"), not an invented colour. "Main Fleet" (the non-Reinforcements bought-ship group header)
is not this colour and isn't green either — settled on the page's own chrome blue (`--fv-accent`)
specifically so it's visually distinct from the more saturated Reinforcements cyan while still
"fitting the page's colour scheme."

### 11.5 Bought-ship rows: name vs. shipClass, and the action-link colour rule

**A bought ship shows its player-given name AND its hull class as two separate pieces of text**,
per the real template (`gamelobby.js:943-949`, classes `.shipname`/`.shiptype`): `ship.name`
(defaults to `ship.shipClass` until the player renames it) rendered bold, `ship.shipClass` (the
hull's display name, e.g. "G'Quan Heavy Cruiser") rendered normal-weight, same base text colour,
`padding-left:3px`, in a fixed `min-width:150px` name column so classes align down the list
(`lobby.css:286-308`). This is NOT the same axis as the Store's size-CATEGORY grouping
(Fighters/Medium/Heavy/Capital/etc) — conflating the two was an error caught mid-mockup.

**Every clickable action link in the Purchase Fleet panel — Show Details, Add to Fleet, Details,
Edit, Copy, Reinforcement⇄Main Fleet, and Remove — shares ONE colour.** An earlier mockup pass
gave "Remove" its own red, which the user explicitly rejected as clashing; there is no
destructive-action colour distinction in this part of the redesign, unlike normal web-app
convention.

### 11.6 Small, settled UI decisions worth carrying into the build

- No checkmark (✓) glyphs on completed wizard-step tabs — tried, explicitly removed.
- No "new" tags/badges next to newly-added fields (Private Game, In-Service Date, Dust
  Clouds/Meteorites) — tried, explicitly removed; the fields just look like any other field.
- **A checkbox that reveals a dependent control** (Simultaneous Movement's bracket-count
  dropdown, default 8; Desperate Measures' which-teams-affected dropdown, default "Both Teams")
  is INLINE, appended right after the row's own label text, one line tall — same established
  pattern as "Ladder Game *View Ladder*" already uses in the same card, not a separate flex
  column beside the checkbox (that shape was tried, looked bad — the dropdown visually
  stretched to the row's full label+caption height as a flex sibling of the caption block, and
  was dropped after a screenshot showed exactly why). The dependent control is only present in
  the markup at all when its checkbox is checked in that mockup's state — an unchecked row (e.g.
  Desperate Measures) shows no dropdown, with the caption text itself naming what ticking would
  reveal and its default, since a static mock can't show a live show/hide toggle. **In-Service
  Date**, which has no boolean checkbox (blank = off), instead puts its year input as the FIRST
  element in the row, left-aligned to the same x-position every other row's checkbox starts at —
  not indented behind a blank spacer div (tried, didn't actually align), and not floated to the
  right edge of the container (the original placement, also wrong).
- Terrain Features card: single column of rows (label + count dropdown each), not the two-column
  grid an earlier pass tried — the two-column split stopped being necessary once the card moved
  beside Rules & Options instead of spanning the step's full width.

---

## 12. Build log

### 12.1 Stage 0 — shared groundwork (built 2026-09-23)

**What landed** (no user-visible change; nothing calls the renderer or touches the columns yet):

- `db/createGameRedesign.sql` — ONE migration, not the three files §6 suggested (matches the
  more recent `reinforcements.sql` precedent: one feature, one file). Adds `tac_game.scenario`
  (TEXT), `in_service_date` (INT), `password_hash` (VARCHAR(255)), all `DEFAULT NULL`, after
  `description`. Uses `ADD COLUMN IF NOT EXISTS`, so re-running it is a no-op (verified by
  running it twice against a throwaway `CREATE TABLE … LIKE tac_game` copy). Matching block in
  `db/emptyDatabase.sql`. **Safe to apply ahead of any code** — every existing `tac_game` query
  was audited: the one INSERT (`DBManager::createGame`) already names its columns, and every
  `SELECT *` maps its row field-by-field into `TacGamedata`.
- `client/UI/scenarioCard.js` — `window.scenarioCard`. `FIELDS` is **the storage contract** for
  the scenario JSON (keys + the option strings, the latter verified verbatim against
  creategame.php's `<option value>`s by script); `normalise(raw)` accepts a JSON string, an
  object or nothing and returns known keys only, all trimmed strings; `render(raw, {plain})`
  returns an escaped `<dl>` fact grid (or `""`). Display rules taken from the mockup: an
  "Other"/"Up to X points" choice shows what was typed ("Up to 20 pts"); Forbidden Factions is
  left out when it says "None"; empty facts are left out (so an older, shorter scenario renders
  cleanly); Additional Info spans the full width and keeps its line breaks via CSS
  `white-space: pre-line`, never `<br>` markup. Each field has a form `label` (creategame.php's
  wording) and a shorter `factLabel` (the mockup's lobby wording — "Tier", "Custom Factions").
- `styles/scenarioCard.css` — `.fv-scn-*`. NOT gamesPanel.css's `.fv-card` (that is games.php's
  clickable `<a>` card and the file isn't linked on either page). **Column count follows the
  grid's own width**: at most two columns, one once a column would be under 11rem — so it is
  two-up in a third-width lobby column and one-up on a phone without a media query. `plain`
  modifier drops each fact's card chrome for use inside the Summary step's card.
- Wired on both pages: CSS via `AssetLoader`; JS via `AssetLoader` on creategame.php, but as a
  PLAIN tag inside gamelobby.php's `$debug` block (bundle-legacy.js skips AssetLoader tags —
  a versioned tag there would never enter the lobby bundle).

**Traps found — read before Stages 1, 4, 6 and 8:**

1. **Lobby payload uses `JSON_NUMERIC_CHECK`** (`Manager::getGameLobbyDataJSON`). Any
   numeric-looking STRING nested anywhere in the payload becomes a number, and some change value
   on the way ("0012" → 12, "1e5" → 100000). Stage 4 must publish `scenario` as its **raw JSON
   text** (a string at the top level — never numeric) and let `scenarioCard.normalise` parse it
   client-side; publishing a decoded object would let this flag rewrite player free text.
2. **The DB connection is `utf8` (3-byte)** (`mysqli_set_charset(…, 'utf8')`). A 4-byte
   character (emoji) in a free-text field can't be stored. Stage 1: re-encode the validated
   scenario server-side with `json_encode`'s DEFAULT flags (never `JSON_UNESCAPED_UNICODE`) — the
   stored text is then pure ASCII `\uXXXX` and immune. (`description` already has this latent
   problem today; not ours to fix here.)
3. **`description` must KEEP being written** alongside `scenario`. Besides the PHP parser,
   gamelobby.php's inline JS regex-matches it directly — `/CUSTOM FACTIONS \/ UNITS:\s*Allowed/i`
   at ~lines 361 and 384 (the "Show Custom" default and its warning). Stage 4 can switch those
   two to `scenario.customFactions` with the regex as the legacy fallback.
4. **`TacGamedata`'s constructor rewrites `\n` → `<br>` in `description`.** Do not route
   `scenario` through that path, or through anything else that does.
5. **`password_hash` must never enter `TacGamedata`.** `stripForJson` builds its object by hand,
   so a property it doesn't name stays server-side — but that payload is inlined into
   gamelobby.php and polled by game.php, so one careless `$strippedGamedata->… = $this->…` would
   hand a private game's hash to everyone who opens it. Stage 8: give the join path its own
   narrow query. **Done at Stage 8: `DBManager::getGameAccess` is the only read (§12.9).**
6. **creategame.php has NO `<meta name="viewport">`** (gamelobby.php does). Without it a phone
   lays the page out at ~980px and scales it down, so NO responsive CSS on that page can ever
   take effect. Not added at Stage 0 because it changes how today's fixed-width page renders on
   phones (user-visible); **Stage 1 must add it**, since the restyled page is designed for it.
7. **Mockup leftover:** `CreateGame_Step4_Summary` still shows a "Called Shots: Allowed" fact
   and paraphrased values ("Hard edge"). Called Shots is dropped (§3.3) and values are always
   the real option text — the renderer already does both.
8. **Verifying at 390px with headless Chrome on Windows:** the browser enforces a minimum
   window width (~500px) and just crops the screenshot, so `--window-size=390,…` lays out
   wider than it shows and looks broken/overflowing. Test a component inside a 358px-wide box
   (390 minus 16px gutters) on a wider page instead, or use real device emulation.
9. **Local DB:** the migration was only validated against a scratch copy — apply
   `db/createGameRedesign.sql` to the local DB (and later test/live) before Stage 1's code runs.
   The legacy watcher wasn't running at build time; the lobby bundle was rebuilt by hand with
   `FV_NO_MINIFY=1 node scripts/bundle-legacy.js` (what the watcher runs).

### 12.2 Stage 1 — Create Game restyle, single page (built 2026-09-23)

**⚠️⚠️ DEPLOY ORDER: `db/createGameRedesign.sql` MUST be applied to a database BEFORE this
stage's code runs against it** (local, test and live). `DBManager::createGame` now names the
`scenario` column, so without the migration EVERY game creation fails — the Fleet Builder included.

**What landed:**

- **creategame.php** rebuilt as three `.cg-section` panels — Game Options / Scenario
  Description / Teams & Map — then one Create Game button. Still one form, one POST; no wizard
  (Stage 2). Added `<meta name="viewport">` (§12.1 trap 6). `createGame.js` is now versioned
  through `AssetLoader`: it and the markup change together, and a cached copy against the new
  page would find none of its ids.
- **Background picker:** a horizontally scrolling strip of lazy-loaded thumbnails (the 26
  backgrounds total 2.3 MB). A radio group named `background`, so it is still one Tab stop with
  arrow keys; the choice still repaints the page backdrop as a full-size preview.
- **Rules & Options:** the mockup's check rows with a one-line caption each. Brackets and
  Desperate teams are the inline dependent selects of §11.6, shown only while ticked. Real option
  values kept (brackets 1-12, default 8 from `SimultaneousMovementRule`; Desperate
  Both/Team 1/Team 2). **Labels follow the mockup except "Desperate Scenario"**, which keeps the
  real wording (the mockup's "Desperate Measures" is not a term used anywhere else).
  All element ids are unchanged, and the page now re-reads every ticked box on load — a browser
  restoring the form (Back, failed POST) used to leave a ticked box whose rule was never set.
- **Terrain Features:** six fixed count rows, no master "Add Terrain" checkbox; a type enters
  the rules only when its count is above zero (`readTerrain`). Moons (0-5 / 0-4 / 0-2) are plain
  `<select>`s (Stage 2 made them count combos too, §12.3). **Asteroids, Dust and Meteor Swarms are a COUNT COMBO** (`cgCountCombo` +
  `createGame.initCountCombos`), keeping the old asteroid box's best-of-both-worlds behaviour at
  the user's explicit request: ANY value 0-48 can be typed (digits only, clamped on change) or
  stepped with the mouse wheel while focused (the page's shared wheel handler), and the ▾ lists
  ONLY the named presets — None/Few/Several/Pack/Lots/Horde/Swarm/Zounds. A first cut that listed
  every number 0-48 in a `<select>` was rejected: it lost free typing and the wheel, and buried
  the presets. The combo is an ARIA combobox (input keeps focus, `aria-activedescendant`;
  ArrowDown opens, arrows move, Enter picks without submitting, Escape/Tab/outside click
  close; one open at a time). On touch, the ▾ opens the list WITHOUT focusing the field, so the
  phone keyboard doesn't cover it. ⚠️ `.cg-section` deliberately has NO `overflow: hidden`: on a
  phone, Meteor Swarms is the last row of its panel and its list must drop past the panel edge.
- **Dust / Meteor Swarms** (user rulings 2026-09-23): each count is that many **single-hex** units,
  0-48 with the asteroid presets, labelled "Dust" and "Meteor Swarms". New `DustAndMeteorsRule`
  (rules key `dustAndMeteors: {dust, meteors}`, clamped 0-48, absent when both are 0).
  `BuyingGamePhase` creates `spawnDustField` / `spawnMeteoroid` — the Triad Asteroid Salvo's own
  classes, so collision damage and not blocking line of sight come for free; their map names stay
  "Dust Field" / "Meteoroid Field". **Placement ruling:** they may sit adjacent to ANY other
  terrain, moons included (no moon buffer), but never share a hex; they are placed last. Every
  other terrain type keeps its old spacing. Also shown in the lobby's Options Selected line and
  counted by the games list's TERRAIN chip.
- **Scenario Description:** built from `scenarioCard.FIELDS` (which gained `help` text), 8 purple-
  rail cards in a 2→1 container-width grid. "?" expanded the help INLINE rather than as a floating
  bubble — **superseded at the Stage 2 review (§12.3): it is the mockup's floating window now.** "Other" / "Up to X points" reveal
  their input with the plan's one deliberate animation (grid-row 0fr→1fr, off under
  `prefers-reduced-motion`). Submits BOTH `scenario` (JSON) and the legacy `description`, whose
  format is byte-compatible with the old one minus the Called Shots line.
- **Server:** `Manager::cleanScenario` keeps known keys only (its `$scenarioKeys` mirrors
  `FIELDS` — change both together), trims and caps them (4000 chars for Additional Info, 200
  otherwise, digits only for points), stamps `v: 1`, and encodes with DEFAULT flags so the stored
  text is pure ASCII (§12.1 trap 2). Option values are deliberately not checked against the lists.
- **Teams & Map:** map preview + legend beside team cards. One `teamColor()` drives the canvas
  zones, the team rails and the legend, matching the game's own palettes (§11.4): 2 teams
  relative (Team 1 green, Team 2 red); 3+ teams absolute (`teamBaseColorsMultiTeam`), repainted on
  every refresh because adding a third team turns Team 2 from red to orange. The legend names teams
  ("Team 1", "Team 2"…) rather than the mockup's "Your / Other team's deployment": nothing on this
  canvas is per-viewer, and no terrain is drawn on it. The canvas keeps its 545×390 drawing surface
  and is only CSS-scaled (nothing on it is clickable).
- **Mobile:** everything collapses to one column (grids by their own width where possible,
  900px where they split the page); form controls go to 16px under 600px (iOS zooms the page on
  focusing anything smaller) and 44px tall on touch screens; `lobby.css`'s
  `#mapPreview { min-width: 480px }` and base.css's fixed 1000px chat panel are overridden here.

**Deliberately NOT in Stage 1** (they appear in the Step 1 artboard but belong to later stages):
In-Service Date (Stage 6), Private Game (Stage 8), Save/Load Settings and Copy Slot/Team (Stage 3),
the step tabs and Next/Back (Stage 2).

**Verified:** PHP lint on all seven files; `cleanScenario` + rule round-trip + the REAL
`BuyingGamePhase::advance()` placement pass run against a stub DBManager — 40 randomised runs,
2480 units placed, zero shared hexes, dust/meteors landing adjacent to other terrain 664 times,
asteroid/moon spacing unchanged; creategame.php rendered through PHP CLI and driven in headless
Chrome (the posted `data` checked field by field); screenshots at desktop and 390px (the latter
inside an iframe, which IS its own viewport — see §12.1 trap 8). `fvbuild -Check`: autoload map
up to date, ship validator clean, replay 130/131 — the one, **game 4251** (ship 6's system
`output` values each 1 lower), fails identically with every Stage 1 server change stashed, so it
is pre-existing drift awaiting a re-record, not this stage.
**Count combo, verified 2026-09-24 with Docker down** (not restarted): creategame.php syntax-
checked with the `php-parser` npm package (negative-controlled — it rejects a planted error); the
combo driven in headless Chrome on the previous CLI render with the three rows swapped for a
line-for-line mirror of `cgCountCombo`'s output — typing, clamping, wheel, ▾, keyboard, mouse pick,
outside-close, Escape and the posted rules all as intended; screenshots of an open list on desktop
and of the LAST row's list on a phone, unclipped. ⚠️ A real PHP lint + CLI render of the final
creategame.php is still owed once Docker is back up.

**Found, not fixed:** `lobby.css` still carries `#asteroidsDropdown` / `#moonsDropdown` rules for
elements that no longer exist anywhere (dead, harmless).
**Owed item closed at Stage 2:** creategame.php now lints clean under the real `php -l` and
CLI-renders with no PHP errors (Docker was up on 2026-09-24).

### 12.3 Stage 2 — Create Game wizard + Maps with Terrain (built 2026-09-24)

**⚠️ One schema change, added at review: `tac_game.rules` varchar(400) → text** (appended to
`db/createGameRedesign.sql` + `emptyDatabase.sql`). A terrain map's `terrainLayout` is 0.9-1.9 KB of
rules JSON, so without it creating a game on any "Maps with Terrain" entry fails with "Data too
long for column 'rules'". Apply before this stage's code runs (local, test, live); the statement
is a no-op on re-run and keeps every stored value and the `'{}'` default (checked on a scratch copy
of `tac_game`). **One new server class** (`TerrainLayoutRule`, autoload map regenerated) and
`game.legacy.bundle.js` needs rebuilding for the renderer change — both happen in a normal deploy
build.

**The wizard (plan §3.1 / §3.5, mockup Step 1-4 artboards):**

- creategame.php's three `.cg-section`s are now steps 1-3 (`#cgStep1..3`, `data-step`), plus a new
  read-only **Step 4 Summary & Confirm** (`#cgStep4`). Still one form, one POST, no endpoint change.
  One shown at a time via the `hidden` attribute — `.cg-page [hidden] { display:none !important }`
  because `.cg-btn` and friends set their own display, which beats the browser's `[hidden]` rule.
- **Step bar** (`.cg-steps`): four `<button>`s, `aria-current="step"` on the current one. Rails per
  §2: steps 1-3 blue, Confirm green, always; progress shows as fill/brightness (current tinted,
  passed full strength, ahead faded). No ✓ glyphs (§11.6). Every tab is clickable.
- **Navigation rule:** going FORWARD (Next, or any later tab) runs `createGame.validateStep()` on
  every step being passed and stops on the first that is not ready, with the reason in the bar and
  the field flagged (`aria-invalid`) and focused. Going back never checks. Kept deliberately short —
  only what would reach the lobby blank or broken: a game name; an "Other" / "Up to X points" with
  nothing typed; a limited map with no size; a slot with no name.
- **Sticky nav bar** (`.cg-nav`, `position: sticky; bottom`): Cancel (step 1, → games.php) /
  Back, then Next ("Next: <next step>", just "Next" on a phone) or, on Step 4, **Confirm & Create
  Game** ("Create Game" on a phone). The step error lives INSIDE the bar — at the foot of a long
  step it could be scrolled out of view.
- **Submit guard replaced.** The old mousedown/touchstart `allowSubmit` flag also refused the
  keyboard's own Enter/Space on the button. Now: the form submits only from Step 4 (Enter in a
  field on steps 1-3 is an implicit submit through Confirm and is refused), every step is
  re-checked on the way out, and Confirm disables itself after the first press (two presses = two
  games) — re-enabled on `pageshow` for a bfcache Back. `isFleetTest` keeps its exemption.
- **Summary** (`renderSummary`, rebuilt each time Step 4 opens): Game Options card (background
  thumbnail, name, one chip per active rule — the lobby's "Options Selected" itemised; "No
  optional rules" when none), Scenario card (`scenarioCard.render(…, {plain:true})` — the renderer
  Stage 0 built for exactly this), Teams & Map card (a second canvas painted by the same
  `paintMap()`, template/size/terrain line, one block per team in the §11.4 colours). Each card
  has Edit → its step.
- On a step change: short entry animation (off under reduced motion), the step bar scrolled into
  view, focus to the step's heading (`tabindex="-1"`, no ring) so a screen reader announces it.
- Mobile: step bar shows "1 / 4" + short names; nav labels shorten; Summary cards stack at 900px.
  Checked at 390px in an iframe (§12.1 trap 8) on steps 1, 3 and 4.

**Moons use the count combo** (user addition 1): the three moon rows are `cgCountCombo`s like
Asteroids/Dust/Meteor Swarms — typed, wheel, ▾ presets. Ranges UNCHANGED (0-5 / 0-4 / 0-2); the
presets are every count ("None (0)", "1", "2"…) — `cgCountCombo` now prints a bare number without
the "(n)" gloss and skips presets above the row's max.

**Maps with Terrain** (user addition 2) — Map Template entries with PRE-PLACED terrain, the same
every game:

- **Server:** new `TerrainLayoutRule` (rules key `terrainLayout: {name, units:[{type,q,r,h}]}`).
  `type` is a short key (`asteroidS/M/L`, `asteroid2`, `asteroid3`, `moonS/M/L`, `dust`,
  `meteors`) mapped to the phpclass in `TerrainLayoutRule::$types` — the rules blob is published to
  every client, the class is the server's business. Checked for SHAPE only (known type, ints,
  facing wrapped 0-5, ≤150 units, name ≤60 chars); balance/overlap is the creator's choice, like a
  deployment zone. `getUnitsOnMap($gamespace)` drops units whose centre is off the map (same bounds
  as `ReinforcementEntry.onMap`), and BOTH halves of BuyingGamePhase read through it, so the unit
  created and the hex it is placed on always come from the same list.
- `BuyingGamePhase::process` (slot 1): `addTerrainLayout()` creates one unit per entry FIRST, named
  in the same series as the random terrain ("Asteroids #n", "Moon #n", "Dust #n", "Meteor Swarm
  #n"); the random adders gained a name offset so they number on after it — no duplicate names.
- `BuyingGamePhase::advance`: layout units are placed BEFORE the random pass — each entry takes the
  lowest-id unplaced unit of its phpclass (units of a class are interchangeable, and any extra of
  the same class from a random count is left for the random pass). Their hexes and moon centres
  are registered exactly as a random unit's would be, so random terrain on top keeps its usual
  spacing from them. The footprint maths moved into `getTerrainHexes()` (used by both passes).
- **Random counts still work on top** of a terrain map; Step 1's Terrain card says so while a
  terrain map is picked. Typing a Width/Height turns the template to Custom and **drops** the
  layout (it was laid out for the template's size).
- Lobby: the Options Selected line gains "Terrain Map: <name> (N features)" (name HTML-escaped —
  it came from a POST); the games list's TERRAIN chip counts layouts too. The lobby MAP PREVIEW
  markers are Stage 4's job — the data is already in `gamedata.rules.terrainLayout`.
- **Client:** the maps live in `createGame.mapData` as `{base, name, blurb, terrain}` entries,
  resolved onto their base template by `getMapConfig()`. Preview (and the Summary copy) draws white
  markers of real size — a disc of radius Huge+½ for a moon, a dot per hex for the rest, irregular
  asteroids turned by `h` with `createGame.rotatedHex` (a port of `Mathlib::getRotatedHex`,
  **verified identical to the server on all 32 irregular units**). Dust/Meteor markers are fainter:
  `TERRAIN_ALPHA` 0.9 / `FIELD_TERRAIN_ALPHA` 0.45, legend swatches follow them.
- **Seven maps**, one per size family: Close Quarters (Small 30x24), Asteroid Belt / Twin Moons
  (Standard 42x30), Crossroads (Four Teams 42x30), Fractured Front (2v2 42x40), Shattered Moon /
  Meteor Storm (Large 60x40). Each is authored as one quadrant/half and mirrored about the map's
  TRUE centre — half a hex left of hex 0,0 on an even width (the map box is drawn centred there, and
  the standard zones at -19/+18 are symmetric about it): mirror = `(q,r) → (-1-q+(r&1), r)`,
  flip = `(q,-r)`, and an irregular asteroid's mirrored facing is found by matching footprints.
  Every map keeps two hexes clear of each deployment zone, never overlaps, keeps non-field terrain
  one hex apart and moons 7 apart. Crossroads and Fractured Front are added to
  `forbiddenLadderMaps` (not two teams of one slot); the other five are allowed in ladder games.

**In-game Dust / Meteor hexes** (user addition 3): `BallisticIconContainer.generateTerrainHexes`
now draws them with the same white `hexWhite` region as asteroids and moons, at
**`FIELD_TERRAIN_DIM` = 0.5** (a multiplier on the region's fill/rim opacity — the user asked for a
value to tune by hand). Matched by phpclass (`spawnDustField` / `spawnMeteoroid`) as well as the old
Enormous + size-class-5 test, so they are drawn whether or not a blueprint carries those fields —
which also covers the Triad Asteroid Salvo's spawned dust/meteors.

**Traps found:**

1. **jQuery 4 has no `$.trim`** (creategame.php loads jQuery 4.0) — it threw on the first Next.
   Use `String(x || "").trim()`.
2. **jQuery 4 runs ready-handlers ASYNCHRONOUSLY.** A test driver on `window.load` can run BEFORE
   createGame's ready handler, whose `initWizard()` then resets to Step 1 — looks like a broken
   step jump. Drive tests from `jQuery(fn)` + `setTimeout`.
3. **gamesNew.css's `* { font-family: Arial }`** hits every nested span: the step bar's and the
   buttons' inner spans need their face named (or `inherit`) explicitly.
4. **Git Bash `sed -i` and `grep -c $'\r$'` both lie about CRLF** — sed strips CRs, grep hides
   them. Check line endings with a byte count in node or `git ls-files --eol`.

**Verified:** real `php -l` on all six server files; creategame.php CLI-rendered with no PHP
errors; a scratch run of the REAL path — `GameRules` round trip → `addTerrainLayout` + all three
random adders with offsets → `BuyingGamePhase::advance()` against a stub DBManager — for all seven
maps with random terrain on top: every layout unit on its exact hex with its facing, zero shared
hexes, zero duplicate names; malformed layouts dropped or trimmed as intended. Wizard driven in
headless Chrome (validation, forward-jump stop, Back, terrain map set/posted/dropped on a typed
size, moon clamp 9→5, submit refused before Step 4); screenshots at desktop and at 390px of steps
1, 3 (Asteroid Belt, Meteor Storm) and 4 (Shattered Moon, Crossroads) plus the error state.
**Not verified in a live game:** the in-game dust/meteor hexes and a real terrain-map game start —
create a game on a terrain map, buy, and check the terrain lands where the preview showed.

**Review refinements (user, 2026-09-24):**

- **Scenario "?" = the mockup's floating window**, not an inline row: the "?" sits right after the
  label; a click/tap opens a purple-bordered bubble (`.cg-help-bubble`) just under it with an arrow
  pointing at the "?". `createGame.positionHelp()` measures from the card's PADDING box (what
  `left`/`top` resolve against) and slides the bubble left to stay inside the card — on a phone the
  "?" of "Custom Factions / Units" sits nearer the edge than the bubble is wide. One open at a time;
  a click elsewhere or Escape closes it (Escape returns focus to the "?"); a step change closes it.
  No hover-to-open — deliberate, so mouse and touch behave the same.
- **Additional Info** is one grid cell like the rest, which in FIELDS order puts it under Map
  Borders beside Victory Conditions (the mockup). FIELDS' `wide` is untouched — it still spans the
  read-only fact grid (Summary, lobby).
- **Slot numbers on one line:** a flex row of fixed 3.75rem centred inputs, each field as wide as
  that or its label (~422px for all six). Too narrow for that — a phone, or the two-column layout
  below ~1200px viewport — and it becomes two rows of three in EQUAL columns so the numbers still
  line up, via `@container (max-width: 430px)` on `.slot` (the page's first container query:
  it has to follow the slot's width, which a viewport query cannot know).
- **Confirm step = two columns:** Game Options above Scenario Description (`.cg-sum-col`) | Teams &
  Map. Stacks in that order under 900px.

Verified in headless Chrome: the help windows' open/switch/toggle/inside-click/outside-click/
Escape+focus/step-change states, arrow landing on the "?" centre; Additional Info's position; slot
row one line at 1440/1280, 3+3 aligned at 1024/390, with Unlimited Points too; screenshots of steps
2-4 at desktop and 390px (iframe). `php -l` clean.

**Second review round (user, 2026-09-24):**

- **Dust / Meteor Swarms are now their own terrain classes** (user's change): `DustField` and
  `MeteorSwarm` in `ships/terrain/`, mirroring the Triad's `spawnDustField` / `spawnMeteoroid`,
  which stay for the Asteroid Salvo. Everything that names a class had to follow:
  `TerrainLayoutRule::$types`, BuyingGamePhase's placement weight, `BallisticIconContainer`'s
  `FIELD_TERRAIN_CLASSES` (all four listed). ⚠️ `RammingAttack`'s terrain gate now reads
  `isDustField` / `isMeteoroid`, which only those classes declare, and warnings throw here — so
  `!empty()`, or a jump gate / shipyard / jump point (non-Enormous terrain, auto-given a
  RammingAttack) fatals the pre-firing step. The "Class MeteoriteSwarm not found" lobby fatal was
  a game created while the class's `phpclass` string still read "MeteoriteSwarm" (the container's
  rsync copy had not picked up the rename); that game's rows were repaired by hand.
- **Terrain names are unnumbered now** (user): the random adders and `addTerrainLayout` both name
  a unit by its class alone ("Asteroids", "Asteroid" for the irregulars, "Small Moon" / "Moon" /
  "Large Moon", "Dust Field", "Meteor Swarm") - so the "zero duplicate names" check above no longer
  applies. The numbered versions are kept commented out, and the name offsets are still passed, so
  numbering can be switched back on without touching `process()`.
- **Map Preview restyled to the mockup's Teams & Map artboard** (`createGame.paintMap`, both Step 3
  and the Summary): the map is a dark well (`--fv-well`) under a faint grid every few hexes (~24px,
  anchored on brighter centre lines); each zone a 0.14 wash of its team colour with a DASHED edge
  on its inner sides only (a side on the rim is left to the rim); a mono "TEAM n" label in the
  zone's corner nearest the rim; terrain grey `#5a6a76` discs, those ≥ 6px with the mockup's halo
  drawn INSIDE the true footprint; dust/meteors fainter with no halo. The canvas now takes the
  map's proportions (height/width clamped 0.45-1.0, letterboxed beyond) so the map fills the
  frame, and is drawn at 2x its logical 545px width for sharp lines once CSS scales it. The old
  fixed 6px x-nudge is now exactly half a hex (`toX`), the true-centre offset it approximated.
  The legend keeps team names (not "Your / Other Team's Deployment" - nothing here is per-viewer).

### 12.4 Stage 3 — Copy Slot / Copy Team + Save / Load Settings (built 2026-09-24)

Client only: creategame.php, createGame.js, createGame.css. No server, schema or bundle change
(createGame.js is served directly through `AssetLoader`, not bundled).

**Copy Slot / Copy Team** (plan §3.4, mockup Step 3):

- **Copy Slot** is a small ghost button beside each slot's Remove Slot link. It copies the
  source's name, points, deploy turn and zone (`createGame.slotFields`) into a new slot at the END
  of the same team — appended exactly as Add Slot appends (`createSlot`, no full re-render), so the
  pressed button survives and keeps focus.
- **Copy Team** sits beside + Add Slot. It adds the next team (max + 1) with a copy of every slot
  of the source team, then re-renders like Add Team (which also repaints the 3+-team palette). A
  slot still named after its team follows the new number: "Team 1 (North)" → "Team 3 (North)";
  "Team 12 Escort" is not touched (`(?!\d)`).
- **Zones are copied as-is**, so a copy overlaps its source until moved. Deliberate: Add Team
  already puts a new team on Team 1's or Team 2's zone, and any automatic placement would be a guess.
- Ids: `nextSlotId()` = max + 1, and `createGame.slotid` is kept at the last id used, so Add Slot
  after a copy never reuses an id (the server stores the posted `id` as the slot number, and the
  creator takes slot 1 — `Manager::createGame`).
- Hidden in a ladder game with the other add/remove controls (`refreshSlotsUI`).
- In a slot too narrow for the name + both actions (a phone), `.cg-slot-actions` wraps under the
  name. A new copy is scrolled into view with `scroll-margin-bottom` clearing the sticky nav.

**Save / Load Settings** (plan §3.2 — "save these settings" / "load saved settings"):

- **Placement (user, 2026-09-24):** Load Settings ▾ beside Game Name on Step 1 (as the mockup);
  **Save Settings in the sticky nav, left of Confirm & Create Game, on the Confirm step only** —
  NOT beside Load as the mockup drew it. So only a form that passed every step's checks is saved.
- **Storage:** ONE localStorage key, `fv.createGamePresets.v1`, holding an array
  `[{name, saved, mapLabel, settings}]`, newest first — not the §3.2 sketch's one key per preset
  (`fv.createGamePreset.<name>`), which would need a key scan to list them. Names are unique
  ignoring case and outer spaces; saving an existing name replaces it (the note and the button
  say "Replace"). Every access is try/caught: a throwing or corrupt store reads as an empty list,
  and a failed save says so and keeps the box open.
- **A preset is the FORM, not the posted data** (`readSettings`): checkboxes and the two
  dependent selects by element id, terrain counts by id, `readScenario()`, template + size, the
  game name, the background and the slots. Rules, a template's pre-placed terrain layout and the
  map preview are never stored — `applySettings` replays the form through the page's OWN
  handlers, which rebuild them. **Order matters there:** Ladder off first (it greys out maps and
  prunes slots); the template (resets teams); Unlimited Points (rewrites every slot's points, so
  its change handler only runs when the state actually flips); THEN the saved slots, sorted by id
  and renumbered 1..n (`presetSlots`, which rejects them unless Teams 1 and 2 are present); the
  other rules; Ladder last. Anything the page no longer offers (a background file, a template, an
  option value) is skipped rather than forced; a vanished template falls back to Custom with the
  saved size.
- The Load menu (`.cg-preset-menu`) is a disclosure list: each row loads on click and shows
  "<map> · <date>"; its × deletes after the page's own `confirm.confirm` dialog (clicks inside
  `.confirm` do not close the menu). Escape, an outside click/tap or Tab-ing out closes it.
  Footer: "Saved in this browser only."
- The save-name box opens INSIDE the nav bar, over its right end. ⚠️ **Enter in that box is
  caught on keydown**: on the Confirm step the form's implicit submit would otherwise CREATE THE
  GAME. "Saved as …" / "Loaded …" float just above the bar (`#cgNavStatus`, role=status, 4 s).
- Mobile: Load wraps under the name; the nav keeps Back / Save / Create Game on one line down to
  360px (spacer dropped, labels `nowrap`, tighter padding and letter-spacing under 600px); the
  save box puts Save + Cancel under the name together.

**Verified** with a CDP driver (Chrome over Node 23's built-in WebSocket — real mouse clicks and
real key presses, and true 390/360px device emulation with touch, which avoids §12.1 trap 8):
Copy Slot/Team values, ids, DOM, focus, palette, rename rule, Unlimited Points, ladder hide/show;
Save → real Enter saves and posts nothing, empty name refused, Replace, Escape; three presets
(2v2 + copies + scenario "Other"/points + rules; a terrain map with Unlimited Points; ladder on a
custom 50x36 map) each loaded over the others — **the posted `data` and every visible control
identical to when saved**, ladder on→off and off→on included; delete confirm/cancel, menu close
paths, corrupt and throwing storage; Confirm & Create Game still submits. `php -l` clean, CLI
render clean. Screenshots: desktop steps 1/3/4, phone 390 and 360 steps 1/3/4.

### 12.5 Stage 4 — Gamelobby top of page: Teams | Scenario | Map (built 2026-09-25)

No schema change (Stage 0's `scenario` column is read, nothing new is written). One server
addition: `Manager::getGameScenario` / `DBManager::getGameScenario`. The lobby legacy bundle needs
rebuilding (a new script, `client/UI/mapPreview.js`, is in gamelobby.php's `$debug` list) — a
normal deploy build does it.

**What landed** (plan §4.1 / §4.2 / §11.3, plus three user asks):

- **Layout:** the old name panel, Rules & Info / scenario split and the separate full-width Teams
  panel are replaced by `.lb-top`: a title bar (game name, **Leave Game** moved up here) and one
  row — **Teams | Scenario Description | Map Preview**. Teams keeps its own height; Scenario and
  Map match each other. 1422px wide (the Purchase panel's outer edge — `.panel.large.lobby` is
  content-box); narrower screens keep Create Game's ~18px gutter. ≤1180px: Teams beside
  Scenario-over-Map; ≤760px: one column. New page stylesheet `styles/gameLobby.css` (`.lb-`
  prefix, createGame.css's grammar), linked after lobby.css and gamesNew.css.
- **Fleet Builder** (user ask): no teams, map or scenario, so the row is replaced by one slim
  "Rules & Info" panel holding the links; title "Fleet Builder".
- **Teams:** the slot template is now a compact row — player (or "Open slot"), a mono meta line
  (slot name · points · "deploys on turn N" only when N > 1), and pill buttons. **Every JS hook is
  kept** (`.slot`, `.takeslot`, `.selectslot`, `.leaveslot`, `.status`, `.playername`, `.name`,
  `.points`, `.depavailable`): the handlers are bound to the TEMPLATE at load and cloned with
  `clone(true)`. Deployment-zone coordinates are no longer printed on the row (the map shows the
  zones). The old slot rules in lobby.css were deleted (two carried `!important`); their
  show/hide logic (`.taken` / `.ready` / `.selected`) lives in gameLobby.css, plus a guard so an
  OPEN slot never shows Select (a `show()` left from before the slot was left used to leak).
  Team blocks are inserted in team order (a copied team's slots can be numbered below another's).
  Head: "N / M filled · K ready".
- **Team colours** (`gamedata.getLobbyTeamColor` / `getLobbySlotColor`, §11.4 + the game's gate):
  a participant in a 2-team game sees own team green / other red, and on the map a team-mate's
  slot ally blue; an observer or anyone in a 3+-team game sees `teamBaseColors` /
  `teamBaseColorsMultiTeam` by number. Repainted on every poll (`paintLobbyTeams` after
  `createSlots`) — the colours are the viewer's and flip the moment they take a slot.
- **Map Preview:** Create Game's drawing, moved into the shared **`client/UI/mapPreview.js`**
  (`window.mapPreview`: palette, `teamColor`, `TERRAIN_TYPES`, `rotatedHex`, `terrainKinds`,
  `paint(canvas, {width, height, zones, terrain})`); createGame.js keeps a thin `paintMap`
  adapter. The lobby draws every slot's zone (the viewer's last, on top), one "TEAM n" label per
  team, and the **rules.terrainLayout** markers (the Stage 2 ask); random terrain has no markers —
  it is not placed until the game starts. Legend: one swatch per team ("(you)" on the viewer's),
  "Ally" when a team-mate slot exists, and the two terrain kinds when present. Map size
  ("42 × 30" / "No boundaries") in the panel head, replacing the old "Map 42x30" option.
  Zone labels are now sized for the canvas's DISPLAYED width (~10px as shown; 11 min, 18 max
  logical): scaled into a ~360px column, a fixed 11px label read at 7px. A desktop Create Game
  still computes 11, so it is unchanged there; on a phone its labels get bigger.
- **Scenario Description:** Game Rules chips above the fact grid (§11.3), then the facts, then the
  links. New game → `scenarioCard.render(tac_game.scenario)`; old game (NULL scenario) → the old
  first-colon parser, unchanged, now emitting scenarioCard's own `.fv-scn-*` markup server-side
  (`lobbyLegacyScenarioFacts` in gamelobby.php; a colon-less line continues the fact above;
  Additional Info wide + multi-line; empty facts left out).
- **Scenario transport:** `Manager::getGameScenario` — ONE indexed read per page load (not per
  poll, and not in TacGamedata: it never changes, and game.php never needs it). Printed into the
  inline script as a JS string with `JSON_HEX_*` and parsed by `scenarioCard.normalise`, so
  JSON_NUMERIC_CHECK never touches it (§12.1 trap 1: "0012" stays "0012"). A DB error returns
  null and the page falls back to the description parse. Skipped entirely in Fleet Builder.
- **Rule chips** (user ask 1): `scenarioCard.ruleChips(rules, {unlimitedPoints})` +
  `renderRuleChips` — ONE list for the lobby and Create Game's Confirm step (its old
  `summaryRules` is gone); styles are `.fv-rule-chip` in scenarioCard.css (Create Game's
  `.cg-chip` rules deleted). Colours: **Ladder gold `--fv-ladder`** (games.php's), **terrain
  white** (Asteroids, Moons, Dust, Meteor Swarms, Terrain Map), **Simultaneous Movement green**
  (`--fv-mine-soft` text, #52b352 line), **Reinforcements cyan #00b8e6**, **Mines purple
  `--fv-purple`**, everything else the page blue. Wording is Create Game's ("Desperate Scenario
  (Both teams)"). Changes from the old "OPTIONS SELECTED" line: Reinforcements now shown;
  Unlimited Points shown when every slot is unlimited; "Standard Movement" and "No Terrain"
  dropped (defaults); "Terrain Map: … (N features)" is a chip on BOTH pages now, so the Confirm
  step previews the lobby exactly.
- **Links** (user ask 2): FAQ · Factions & Tiers · Ammo & Options (game.php's USEFUL LINKS set —
  FAQ is new to the lobby), then the three random-faction wheels, kept (quieter) until Stage 7's
  randomiser replaces them.

**Side effect fixed:** the old `drawMapPreview` called `getContext` on a missing canvas in Fleet
Builder, so every bulk buy/edit/copy there ended in a TypeError; it now returns quietly.

**Traps found:**

1. **Harness recipe for gamelobby.php without a lobby game** (the local DB had none): a CLI
   script that declares a stub `class Manager` (`getGameLobbyDataJSON`, `getGameScenario`,
   `getAllFactions`, `__callStatic`) BEFORE including global.php, so the page never reaches the
   DB, with payloads taken read-only from real games' `stripForJson()` (status → LOBBY,
   ships → []). Load the HTML with a `<base href>` and a deferred `data:` script after the bundle
   tag that no-ops `ajaxInterface.startPollingGamedata`.
2. **A CLI script calling `Manager::` under `/usr/src/current` gets the PRODUCTION varconfig**
   (localhost/aatu): the container's varconfig symlink exists only in the rsync copy. Require
   `docker/php/varconfig.php`. Manager caches the first connect failure (`$dbUnavailable`), so
   every later call in that process fails the same way — it looks like the feature is broken.
3. **CDP phone emulation on this page reports `innerWidth` 1430** — the 1400px Purchase panel
   widens the visual viewport — while the layout viewport is still 390 (media queries match).
   Measure `.lb-top`, not `innerWidth`. And once touch emulation had been on, `(pointer: coarse)`
   stayed on in that browser after switching it off: measure desktop sizes in a fresh one.

**Verified:** `php -l` on the four PHP files; five lobby variants rendered through the REAL
gamelobby.php (global.php turns warnings into exceptions — none): 2 teams with every chip kind
and a hostile scenario (`<script>`, `&`, colons, "0012" all shown as text), the viewer on two
slots, a legacy description-only game on an open map seen by an observer, 4 teams, Fleet
Builder. Driven in headless Chrome over CDP at 1600, 1000, 390 and 360 (true device emulation):
chip classes and computed colours, facts, team rails, slot button visibility, legend, nothing
overflowing `.lb-top`, no console errors. Select Slot moves the selection and flips the Select
buttons; Take / Leave Slot reach `submitSlotAction`; Leave Game navigates to `?leave=true`; a poll
that seats an observer flips the rails and legend to relative. **Create Game:** the map preview is
**pixel-identical** to the HEAD code (Step 3 and Summary canvases hashed, old vs new
createGame.js, six setups incl. Twin Moons, Crossroads, Meteor Storm, No Boundaries, 3 teams);
Confirm chips checked for class and colour on desktop and phone.
**Not verified:** a real lobby on the local server — polling, slot.php take/leave, a game created
through the wizard and opened in its lobby, and Fleet Builder purchases end to end.

**Found, not fixed:** `setSlotData` writes the creator's slot name with `.html()` (pre-existing);
the Crossroads template logs "Target container for team 3/4 not found!" on Create Game
(identical in the HEAD code); lobby.css keeps dead rules for the removed markup
(`.lobby-split-container`, `.rules-info-*`, `.mapPreviewContainer*`, `.unlimited-points-text`…);
the custom-factions default still regex-matches `description` (§12.1 trap 3 — still correct,
since `description` is still written).

**Review refinements (user, 2026-09-25, same day)** — supersede the layout bullets above:

- **Column order is now Map Preview | Scenario Description | Teams** (DOM order too), and **no
  panel is stretched to match another** (`.lb-pair` is `align-items: flex-start`; the links'
  `margin-top: auto` pin is gone). The Map is usually the tallest (chips under it), so it leads
  and the row steps down left to right with no gap inside Scenario. Stacked (≤1180px) Scenario
  comes back BEFORE the Map (`.lb-scenario { order: -1 }`) and the pair re-stretches its panels'
  WIDTH (`align-items: stretch` — flex-start in a column shrinks them). On a phone (≤760px) Teams
  is still stacked FIRST (`order: -1`) — it is where a phone player takes a slot.
- **Slot meta says "deploys T3"** (was "deploys on turn 3"; the fleet list's "Deploys T3"): beside
  Leave Slot the meta line has 210px and "Team 1 · Unlimited · deploys turn 3" needed 214 (220
  for turn 12); "deploys T12" needs 197. Still wraps in two cases: the viewer's own slot when it
  is ALSO Ready (the badge leaves the meta 148px), and a two-digit turn on a 390px phone.
- **Teams is a fixed 380px** (`flex: 0 0 380px`) instead of a 1/2.8 share (~502px at 1422); the
  122px it gave up went to the Map Preview (`flex: 1.05` vs Scenario's 1), so Scenario keeps its
  ~493px. Measured at 1600: Scenario 493 · Map 517 · Teams 380. `.lb-slot-main`'s basis dropped
  10rem → 9rem so the viewer's own **Ready badge + Leave Slot** stay on the player's line at
  380px (they wrapped by 12px). ≤1180px Teams shares the width again (`flex: 1`, max 380px).
- **Rule chips moved under the Map Preview** (below the legend, in the same panel), with a small
  "Game rules" label (`.lb-rules-label`, the links' label style) since the panel head no longer
  says what they are.
- **One white "Terrain Features" chip** on both pages (`scenarioCard.ruleChips`) replaces the four
  per-type chips: "Terrain Features: Asteroids (2) · Moons (2 Small, 2 Medium, 2 Large) · Dust (3)
  · Meteor Swarms (2)", only the types present. No-break spaces inside each type and before each
  dot, so a long chip wraps only BETWEEN types. **"Terrain Map: <name>"** stays its own chip but
  lost its "(N features)" — the Map Preview shows them.
- **Zone labels stack instead of overprinting** (`mapPreview.paint`, so both pages): a label whose
  box would touch one already placed moves one line (1.25 × label size) away from the rim — down
  from a top corner, UP from a bottom one — until clear. Labels are placed in team order (numeric
  compare on the text; bottom-anchored ones in reverse) so a stack reads Team 1, 2, 3 top to bottom
  either way. A label touching one with the SAME text is dropped: Create Game labels every SLOT,
  so two slots of one team in one zone would otherwise list "TEAM 1" twice. Non-overlapping maps
  are pixel-identical to before (canvas hashes, 2-team and 4-team distinct-zone setups).

### 12.6 Stage 5 — Faction Picker + Purchase Fleet panel (built 2026-09-25)

No schema or server change. `gamelobby.php`, `client/gamelobby.js`, `styles/gameLobby.css`
(new sections at the bottom), `styles/lobby.css` (dead rules removed). The lobby legacy bundle
must be rebuilt (the watcher was not running: `FV_NO_MINIFY=1 node scripts/bundle-legacy.js`).

**What landed** (plan §4.3 + the mockup's Purchase Fleet panel + four user refinements):

- **Faction Picker** (`#lbFactionPicker`): a window on a desktop (540px, fixed height so it does not
  jump as filters change), a full-screen sheet at ≤600px. Search box ("Filter factions…", Enter picks
  the first match; focused on open only where `(pointer: fine)`), the tier boxes + Show Custom + its
  Show Customs / Show Only Customs select (MOVED here with their old ids/classes, so gamelobby.php's
  handlers and the custom-allowed warning are unchanged), then the six groups with ▾/▸ disclosure
  buttons and live counts, stopping at faction level. Rows are buttons: name + tier tag (T1/T2/T3/
  Ancient/Other; "· current" on the Store's faction) + the power rating as a tooltip. Closed by ×,
  overlay click or Escape (not while a `.confirm` dialog sits over it); focus returns to the opener.
- **Custom sub-groups** (`gamedata.customSubgroups`, name-pattern table): Nexus (14), Escalation Wars
  (7), Other Universe (11: BSG, 12 Colonies of Kobol, Star Trek, Star Wars, ZStarTrek, ZStarWars,
  ZTrek). Four custom factions match none — Great Crusade Orieni Imperium, House Valheru, The System,
  What If — and are listed straight under Custom Factions, ahead of the sub-groups. The six groups
  start open (the old list's request); the sub-groups start closed; a typed search shows matches in
  closed groups (`.is-searching`). Custom rows and the Custom group carry the `--fv-warn` rail, names
  `--fv-custom`; every other row one neutral rail; the current row `--fv-accent`.
- **Store scoped to one faction** (`selectStoreFaction`): a bar naming it ("Tier 1 · Major Faction ·
  62 ships") with Choose / Switch Faction; `#store` keeps one `.lb-store-faction` per faction loaded
  (lazily, `getShipsForFaction` as before), only the chosen one shown, so switching back is instant.
  Nothing is chosen on load (a lobby is served no bought ships, so there is no faction to default to).
  Size categories are disclosure buttons with counts; a category the Name/Cost/ISD filters empty is
  hidden, and its count follows the filters. Rows: name (+ fighter size tag), cost, **Show details ·
  Add to fleet** (mockup order; the links are buttons). One row builder, `storeShipRow`, for base
  designs and variants (the variant's Show details still passes its BASE design's id, as before).
- **Purchase Fleet panel**: the lobby panel grammar (`.lb-panel`), 1422px like `.lb-top`. Head =
  "Purchase Fleet" + "0 / 3500 pts · 3500 pts left" (Fleet Builder's cap input + Unlimited box sit
  there). One bar: ship filters left (All / No Filters, Name, Cost, ISD, Reset) | fleet tools right
  (Buy as Reinforcement — it stayed here, it is a buy mode not a faction filter — Load Fleet by #ID,
  Load a Fleet ▾, Save Fleet, Ready). Store | Fleet columns (Store 1.25 : 1, 1.6 : 1 at ≤1180px, stacked
  at ≤760px). Actions: Fleet Checker rules · Check · Save Fleet · Ready.
- **User refinements:** (1) **Check** wears games.php's Recent Games colours (`.fv-btn--recent`:
  rgba(61,92,92,.5)); Save Fleet games.php's fleet blue, Ready Create Game's green. Its report opens
  in a window (`#fleetcheck`, "Fleet Correctness Report", the tournament-rules line + rules link in
  its sub-head) instead of the panel below the buy panel — `checkChoices` now only fills
  `#fleetchecktxt` and calls `openLobbyModal`. (2) The Store has no max-height: the panel grows to the
  whole faction list. (3) The Store is the wider column — no row wraps at 1280px+ (fighters' "348 (58
  ea.)" included); 2 of 50 wrap at 1000px, onto a clean second line. (4) Only a variant's NAME is
  italic (`prepareClassName`'s `<i>`, whose `&nbsp;` indent moved to CSS).
- **Chat panel** below now matches the width (1422px, phone width on a phone) — it was the last thing
  widening a phone page.

**Traps found:**

1. **jQuery `show()` on an element hidden by a stylesheet writes the element type's DEFAULT display**
   (`block` for a section). `gamedata.enableBuy` show()s `.buy`, so the flex panel is wrapped in a
   plain block `.lb-buy-wrap.buy.buy-panel-container`. Keep that wrapper.
2. **`hidden` loses to any author `display`** — every hidden-toggled lb- element that is flex
   (`.lb-modal`, `.lb-faction`, `.lb-fgroup`, `.lb-store-faction`, `.lb-picker-empty`) needs its own
   `[hidden] { display: none }`.
3. **lobby.css styles the bought rows through `.store`** (`.store .ship`, `.store span`, `.store .ship
   .clickable` — the last is why the fleet's links are #DEEBFF, not `.ship-actions`' #578bec). `store`
   therefore stays on the FLEET column only; the Store column dropped it so none of those rules reach
   its rows.
4. Test-harness trap: the first `.showship` in the Store may sit in a collapsed category (zero-size
   box) — a CDP click at its centre lands on `<html>`. Show details itself works.

**Removed from lobby.css** (451 lines, every class grep-checked unused across source/public): the
old faction tree (`.factiongroup-header`, `.store .faction*`, `.factionsubgroup-header`,
`.faction-display-name.custom-faction`, `.shipshidden`), the old store rows (`.store .shipsizehdr*`,
`.categoryType`, `.category-container`, `.pointcost`, `.store .storeship`, `.store .variant`), the old
buy header / filter / tier rows, `.points-readout*`, `.max-points-input` (restyled in gameLobby.css),
the store table, the top Save/Ready and their 600px block, the fleetcheck panel, `.Load-Fleet-by-ID`,
`.fleetIdInput` and the `.tier-label-style` reinforcement rules (moved to `.lb-check`).

**Verified** (headless Chrome over CDP, real gamelobby.php rendered via CLI for games 4387 / 4381
(every rule, Reinforcements, Mines) / 4382 (Fleet Builder) / 4386 (5 teams), `?debug` scripts AND the
rebuilt bundle): picker open/filter/search/Enter/Escape/overlay-click/focus return; Show Custom with
the "not allowed" warning up (Escape does not close the picker behind it); sub-group counts; No /
All Filters; custom faction bar; Narn store at 1600 / 1280 / 1000 / 390 (page 390 wide, nothing
overflowing); buying a G'Quan + a Frazi flight into Main Fleet with the Reinforcements headers;
Fleet Builder cap; Check window on desktop and phone; Show details opening the ship window.
**Not verified:** a real server session — polling, Load a Fleet / load by #ID, Save Fleet, Ready,
a real touch device.

**Found, not fixed:** `ajaxInterface._sendRequest` never calls the error callback when the loader
answers `{error: …}`, so the Store says "Loading ships…" until the faction is picked again (the old
tree stayed open-and-empty the same way).

**Decisions the user may revisit** (as first built - all but the Fleet column were settled by the
review below): Show Custom in the picker only; no "Jump to" chips; the Fleet column's bought rows
keep their legacy look (§11.5's restyle not done); four un-grouped custom factions; link order Show
details · Add to fleet.

**Review refinements (user, 2026-09-25, same day)** — supersede the bullets above where they differ:

- **Link order back to Add to fleet · Show details** (the live page's order).
- **Every custom faction is a Custom Faction** — the "Minor > Major > … > Custom" priority now puts
  `isCustom` first, so Drakh (was Major), Barada Imperium and Ch'Lonas Cooperative (Minor),
  Thirdspace (Ancients) and Custom Ships (Other) moved in; their tier still comes from the rating.
  `forceCustomGroup` is gone. Sub-groups, in order: **Babylon 5 Wars** (no name test — it takes every
  custom faction the others do not: What If, Great Crusade Orieni Imperium, House Valheru, Custom
  Ships, Drakh, Thirdspace, Barada Imperium, Ch'Lonas Cooperative), Nexus (14), Escalation Wars (7),
  Other Universe (12, now with The System). A future custom faction whose name matches none of the
  three patterns lands in Babylon 5 Wars.
- **Scrollbars** in both windows (`.lb-modal`, `.lb-modal-body`, `.lb-picker-list`) are the chat
  panel's: 8px, transparent track, `--fv-scroll-thumb` / `-hover`. Firefox gets `scrollbar-color`
  only under `@supports not selector(::-webkit-scrollbar)` — a Chromium that sees `scrollbar-color`
  ignores the pseudo-elements.
- **"Buy as Reinforcement" removed.** `#reinforcementModeToggle` stays, `hidden`, at the top of the
  Fleet column: it IS the buy-mode state (`buyingReinforcement`, `setBuyTarget`,
  `applyFleetGrouping`); the MAIN FLEET / REINFORCEMENTS headers are its only control now.
  `applyReinforcementRule` just forces it off without the rule; lobby.css's `.cyan-tick` rule went.
- **Show Custom back in the Purchase bar** (`#toggleCustomShips`, row 1 after ISD): a SECOND BOX for
  the picker's `#toggleCustom`, not a second setting — ticking either ticks both (inline handlers in
  gamelobby.php), the not-allowed warning fires from either, All / No Filters and the
  customs-allowed default set both. So it reveals CUSTOM ships in an official faction and custom
  factions in the picker together, exactly as the one old box did. (Splitting them into two
  settings would need a rule for a custom faction's own ships, which are all "custom".)
- **Load a Fleet, Save Fleet and the top Ready are one equal set**: 150px on a desktop; on a phone a
  third of the row each (`flex: 1 1 4.5rem` — a basis of 0 let the padded buttons start 28px ahead
  of the unpadded menu wrapper) with 8.5px type; "Load a Fleet" still wraps at ≤360px, inside the
  same 40px height.
- **Report restyled** (`fleetCheckRowsHtml` + `.fc-*` in gameLobby.css). The checker's RULES are
  untouched; only its markup changed: the inline colour spans became `.fc-ok` / `.fc-bad` /
  `.fc-warn` (the R_ constants and 15 inline copies), "Jump engine: present / NOT present!",
  "Static structures present!", "Non-Combat units present!", the unescorted-10% line and
  "CORRECTNESS NOT CHECKED!" now carry a verdict class, and the old "Overall" / CAUTION prefixes are
  built as an `.fc-overall` band and an `.fc-caution` block. `fleetCheckRowsHtml` splits the `<br>`
  lines into rows: `<u>/<b>` headings → `.fc-section`, a `<i>` hull name → `.fc-line--hull`, a " - "
  line → an indented item, and a row with a verdict takes its rail (green / yellow / red, red also
  tinted); blank lines are dropped.
- **Fleet Checker rules** left the Purchase panel; it is a Useful-Links-style chip (`.lb-link`) in the
  report window's sub-head.
- **Category chips** (the mockup's, row 2 of the bar, "Show: All · Fighters · Light Combat · Medium ·
  Heavy · Capital · Structures · Mines"): a FILTER, not a scroll — one pressed shows only that
  category of the Store's faction, opened; All shows every category as it was (so the label is
  "Show:", not the mockup's "Jump to:"). A chip whose category the faction has none of is disabled;
  a pressed chip the next faction lacks falls back to All (`applyStoreCategory`). parseShips now wraps
  each category's header + body in a `.lb-cat` (`data-cat` = its index); the ship filters' "emptied"
  state moved onto it too (`.lb-cat.is-empty`).
- Row 1 of the bar is the ship filters; row 2 the chips (left) and the fleet tools (right).

Verified the same way as above (debug scripts and rebuilt bundle, 1600 / 1400 / 390 / 360 / 320,
scrollbars captured with `--hide-scrollbars` off): picker groups and every sub-group's members,
Show Custom sync both ways, chips incl. the fallback on Terrain, equal buttons, a report with
bought ships (hull rows, bad rows, rails), no Fleet Checker link left in the panel.

**Second review round (user, 2026-09-25):**

- The category chips run in the Store's own order: All, Mines, Structures, Capital, Heavy, Medium,
  Light Combat, Fighters.
- **Reset Filters removed** (link and handler); All / No Filters still clear the three text fields.
  All / No Filters are now chips too - one shared `.lb-chip` style (the category chips add
  `.lb-cat-chip` only as their JS hook); `.lb-textlink` is gone.
- `.lb-check` text is the bar's dim label colour ("Filter by:") everywhere - the Purchase bar's Show
  Custom and the picker's tier / Show Custom boxes - and so is the picker's "Filter factions…"
  placeholder.
- Custom sub-groups are listed alphabetically (sorted at render): Babylon 5 Wars, Escalation Wars,
  Nexus, Other Universe.
- Both Show Custom ticks are `--fv-warn`, the Custom Factions rail colour, instead of base.css's
  pure yellow `.yellow-tick` (scoped to `.lb-check`; Fleet Builder's Unlimited tick keeps base.css's).

**Third review round (user, 2026-09-25)** — supersedes the Show Custom and All / No Filters bullets above:

- **All / No Filters removed from the Purchase panel** (markup and handlers) - they set the Faction
  Picker's boxes. Row 1 is now: Filter by: Name · Cost · ISD · Show Custom | **Reset Filters** (a
  `.lb-chip`), which clears the three fields and unticks the Store's Show Custom only.
- **The two Show Custom boxes are now two settings** (plan §4.3's separate toggle, finally built):
  the picker's `#toggleCustom` decides which FACTIONS can be picked; the Purchase bar's
  `#toggleCustomShips` decides whether the Store shows CUSTOM ships of an OFFICIAL faction
  (`applyCustomShipFilter` reads it). The rule that made the split possible: a row's `data-custom`
  is now `!isCustomFaction && ship.unofficial === true` - the same rows `.highlight-custom-ship`
  marks - so a custom faction's own ships always show once it is picked. Both default on where
  customs are allowed or in Fleet Builder; the not-allowed warning (`warnIfCustomsNotAllowed`)
  fires once, from whichever is ticked first.
- **Load a Fleet menu restyled** (`populateFleetDropdown` builds classed rows, `.lb-fleetmenu-*` in
  gameLobby.css; lobby.css's white `#fleetDropdownList` rule is gone): the windows' dark panel, rows
  with a hover, padlock yellow (private) / green (public) with titles, the name with its `#id` in dim
  mono (the number Load Fleet by #ID takes), damage badge in the fleet list's `--fv-crit`, points in
  mono, a dim ✖ that reddens on hover, a "Default fleets" head in place of the dashed divider (default
  rows padded so names and points line up), the chat scrollbar. On a phone it opens rightwards from
  the Load a Fleet button, which is the left third of its row there.
- **The picker's Show Customs / Show Only Customs select** wears the same look (`.lb-select` in
  gameLobby.css): closed, the Load a Fleet button (display type, ▼ drawn by `.lb-custom-mode::after`);
  open, the saved-fleet menu's panel and rows. It is still the native `<select id="customSelect">` -
  every handler, the keyboard and a phone's own picker are unchanged - styled through
  `appearance: base-select` / `::picker(select)` where supported (Chrome 135+), with a dark native
  list (`color-scheme: dark`) elsewhere. ⚠️ The windows' Escape handler now stands aside while a
  `select:open` exists: it used to close the whole Faction Picker and leave the list floating.

### 12.7 Stage 6 — In-Service Date end-to-end (built 2026-09-25)

No schema change (Stage 0's `tac_game.in_service_date`, INT NULL, is written and read for the first
time). No new class. The lobby legacy bundle needs rebuilding (`gamelobby.js`, `scenarioCard.js`).

**What landed** (plan §3.2 / §4.4, mockup Step 1 + Gamelobby_Main):

- **Create Game:** the LAST row of Rules & Options, as the mockup draws it: a year box standing where
  the other rows' checkboxes stand (§11.6 — measured: same left edge on desktop and phone), label "In-
  Service Date", caption "Locks the lobby's ISD filter to this year, so only units in service by then
  can be bought. Blank = no cutoff." `#inServiceDate`, `type=text inputmode=numeric`, placeholder
  "e.g. 2258"; digits only, four at most (the lobby's ISD box's own sanitiser). 6.75em wide, so it
  still fits the placeholder at a phone's 16px.
- **Validation** (`validateStep(1)`): blank is fine; anything else must be a four-digit year ≥ 1000 —
  "In-Service Date: enter a four-digit year, or leave it blank." A short year would lock the lobby's
  filter below every unit there is (ISDs run 1700s-2280s; a few are text — see below).
- **Posted** as its own top-level `inServiceDate` (a number, or null) beside `scenario` — not a rule
  and not a scenario fact. `Manager::cleanInServiceDate` keeps one to four digits (booleans, floats,
  "22a8", 0 → null) and `DBManager::createGame` writes it (a null binds as SQL NULL).
- **Presets:** `readSettings` stores the box's text; `applySettings` restores it — blank for settings
  saved before this stage (they had no cutoff). No `PRESET_VERSION` bump needed.
- **Rule chip** (`scenarioCard.ruleChips(rules, {inServiceDate})`, blue "rule" kind, after Unlimited
  Points): "In-Service Date: 2258" — on the Confirm step AND under the lobby's Map Preview, so the
  Confirm step still previews the lobby exactly and a player sees the cutoff before opening the Store.
- **Lobby transport:** `Manager::getGameScenario` / `DBManager::getGameScenario` now read BOTH creation-
  time settings in the one per-page-load query and return `array('scenario' => text|null,
  'inServiceDate' => int|null)` (gamelobby.php was its only caller). Never in TacGamedata / the polled
  payload: it never changes, and game.php does not need it.
- **Locked filter** (gamelobby.php): with a cutoff, `#isdFilter` is printed pre-filled and `readonly`,
  in a `.lb-field--locked` label (accent text + border, a Font Awesome padlock, title "Fixed by the
  scenario's In-Service Date") — the mockup's locked field. `readonly`, not the mockup's `disabled`:
  it stays focusable and a screen reader reads "ISD, read only, 2258". The filtering is the untouched
  `applyCustomShipFilter`, which already runs when each faction loads, so later ships never appear.
  **Reset Filters** now clears `.not('[readonly]')`. Without a cutoff the old editable box is printed,
  byte-for-byte as before.
- The filter's long-standing rule is unchanged: a ship whose ISD is 0 or text ("Ancient",
  "Primordial", "Variable" — Triad, Shadow customs, Kirishiac, jump gates, shipyards) always passes,
  so a cutoff never hides those.

**Verified:** `php -l` on the four PHP files. `cleanInServiceDate` on 15 inputs. A real
`DBManager::createGame` + `getGameScenario` round trip on the local DB (2258 and null) inside a
transaction that was ROLLED BACK (no game left; ids 4388/4389 were consumed). `Manager::getGameScenario`
read-only on games 4387 / 4381 / a missing id. **Create Game** CLI-rendered and driven over CDP with real
input: "22ab" → "22"; Next refused with the message, field flagged and focused; "22587" → "2258"; Next;
Confirm chip; Save Settings by Enter (nothing posted) and Load; a pre-Stage-6 preset loads blank; the
posted `data` carries `inServiceDate: 2258` (a number) or `null`, never inside `rules`; Enter on Step 1
refused; 390px phone (true emulation): no overflow, box 108×44 at 16px, aligned. **Lobby**: the
bundled gamelobby.php rendered from game 4387's real payload through a stub `Manager` returning 2258 /
null, served over a local HTTP server with a stand-in `gamelobbyloader.php`: field readonly + padlock +
accent, chip present; Earth Alliance loaded → all 15 ships with ISD > 2258 hidden, none of the rest;
typing into the box changes nothing; a Name filter + Reset Filters → name cleared, year and hiding kept;
390px phone: no overflow. The no-cutoff lobby: editable empty box, no chip, typed 2245 + Enter hides
the later ships, Reset shows them again. No console errors anywhere. Screenshots desktop + phone.
**Not verified:** a game created through the real form on the local server and opened in its lobby.

**Saved fleets respect the cutoff (user, same day)** — first built, the cutoff only filtered the Store,
so Load a Fleet / Load Fleet by #ID could field a later ship. Now `gamedata.doLoadFleet` (the one funnel
both load paths go through; buys come from the filtered Store and copy / edit act on ships already
bought) leaves out every saved unit whose numeric ISD is above `gamedata.inServiceDate` — the Store's
own test, so text / 0 ISDs pass — loads the rest, and says which (the window is described in the next
paragraph). If no unit is left, nothing loads. Per UNIT: leaving units out only lowers the cost, so
the caller's affordability check still holds. `gamedata.inServiceDate` (null by default) is set once by gamelobby.php's inline script;
`renderScenarioPanel` reads it from there for the chip. The Fleet Checker still does not look at ISD
(nothing can reach the fleet past the cutoff now).
**Verified** over CDP with real `loadSavedFleet.php` responses dumped read-only from the local DB
(fleets #105 Centauri, #106 Narn, #108 Ancients) through the page's own load paths — only
`ajaxInterface.loadSavedFleet` stubbed, the slot made unlimited and #106 made public so the existing
gates let them through: cutoff 2242 → Narn loads 6 of 9 (G'Quan at exactly 2242 stays; Bin'Tak 2245,
G'Karith 2253, Frazi 2249 listed), by #ID and via the dropdown alike, 3100 of 5198 pts; Ancients
(ISD text) and Centauri (≤ 2202) load whole with no notice; an all-late fleet loads nothing; no cutoff
→ all 9; a `<b>` ship name shown as text. No console errors.

**Mines per unit too, and a "Units Not Loaded" window (user, same day).** The Allow Mines check in
`doLoadFleet` used to refuse the WHOLE fleet ("Saved fleet contains units not available for this
scenario") when any mine was in it. Now it works like the ISD check: ONE per-unit pass drops mines
(without Allow Mines; Fleet Builder still loads them) and then late units (a mine is reported as a
mine, never twice), loads the rest, and `gamedata.showLeftOutNotice` opens one window:

- Title **"Units Not Loaded"**, or **"Fleet Not Loaded"** when nothing is left, which also leads with
  a bold "Nothing was loaded: no unit in this fleet is allowed in this scenario."
- Mines as ONE sentence, not a list (a mine fleet can carry dozens): "Mines were not loaded with this
  fleet, as mines are not allowed in this scenario."
- Late units as a real list under "These units entered service after this game's In-Service Date of
  **2242**, so they were not loaded:" — each row in the Fleet Checker report's "bad" look (red rail,
  faint red tint): the name bold in `--fv-enemy` red, the hull under it in dim text when the name
  differs, "ISD 2250" right-aligned in mono red. The list scrolls past 45vh so Close stays on screen.
- `confirm.fleetNoticeHtml(bodyHtml, title)` is new — the same window with the caller's own markup (a
  `<ul>` cannot sit in fleetNotice's `<p>`); `fleetNotice` now calls it, unchanged in output. Styles:
  `.fleetNotice*` in confirm.css's `.fleetDialog` block.

Verified the same way (fleet #99: a bulk ×2 DEW mine + EA ships ISD 2240-2259): cutoff 2242 without
mines → only the Orion Starbase loads, mines sentence + a 5-row list; no cutoff → everything but the
mine (6264 of 6418 pts), mines sentence only; a mines-only fleet → "Fleet Not Loaded", nothing loaded;
Allow Mines on, or Fleet Builder → all 7 rows, no window; Narn at 2242 → list only; a plain
fleetNotice still renders `<p>…</p>` with Close alone. Desktop 420px and phone 340px windows, nothing
overflowing. No console errors.

### 12.8 Stage 7 — the faction randomiser (built 2026-09-25)

No schema or server change. `gamelobby.php`, `client/gamelobby.js`, `styles/gameLobby.css`. The lobby
legacy bundle needs rebuilding (`gamelobby.js`).

**What landed** (plan §4.5, the mockup's two picker artboards):

- **The three Wheel of Names links are gone** (the "Random faction" row of `$lobbyLinks`, and its
  `.lb-link--quiet` style). Nothing on the page sends a player off-site to pick a faction. The links
  block is one row now — Scenario Description panel and Fleet Builder's Rules & Info alike.
- **The Faction Picker's footer** (`.lb-picker-random`), the mockup's sticky "🎲 Randomise My
  Faction" — a full-width `.lb-btn` with Font Awesome's `fa-dice` (FA 6.5 is already on the page)
  rather than an emoji. It is pinned to the window's foot (`margin-top: auto`) however short the
  filtered list is, on a desktop and in the phone sheet.
- **What it rolls from** (`gamedata.rollFaction`): the rows the list shows right now — whatever the
  tier boxes, Show Custom (+ Show Only Customs) and the search box leave; a row in a CLOSED group
  counts (folding is not filtering). Uniform `Math.random`; repeats allowed. So it can only suggest a
  faction the player could pick by hand, and the scenario's limits are the ones the player set there.
  **Decision:** no separate "Restrict to: current tier filters / all allowed factions" toggle (§4.5's
  first sketch) — the mockup dropped it, and §4.5's own last bullet says the filters ARE that toggle.
- **It points the faction out, it does not pick it** (`gamedata.showRolledFaction`): the row gets
  `.is-rolled` — `--fv-mine` green rail and tint, the "yours" colour, apart from the current faction's
  blue and the custom yellow, winning over both — with a 0.9s flash (`lb-roll-flash`, restarted by a
  reflow so a repeat still flashes; off under `prefers-reduced-motion`). Its closed group (a Custom
  sub-group) is opened and the LIST is scrolled to centre it — set directly, not `scrollIntoView`,
  which could also move the page under the window. The footer line changes from "Rolls one of the
  factions listed above." to "Rolled **name** [T1]" (custom names `--fv-custom`), and a green
  **Choose** (Ready's colours) appears beside it. Focus stays on the button: Enter / Space roll again.
  **Why not pick at once:** picking closes the window and fetches the faction's ships — a re-roll
  would then mean reopening the picker each time; this way rolling again costs nothing.
- **Choose** triggers the rolled row's own click, so it is exactly a hand pick (Store scoped, window
  closed, focus back to the opener). Any pick — Choose or a row — forgets the roll.
- **Filters vs the roll** (`filterFactionList`): with no row left the button is `disabled` (and the
  list's "No faction matches these filters." shows); a filter change that hides the rolled row
  forgets it; one that keeps it leaves it. The result text is the only `aria-live` part; Choose is a
  static button toggled with `hidden` (it has its own `[hidden] { display: none }` — `.lb-btn` is
  inline-flex, §12.6 trap 2). On a touch screen Choose is 40px tall, the button 44px.

**Verified** (headless Chrome over CDP, the bundled gamelobby.php rendered from game 4387's payload
through the Stage 6 stub `Manager`, served with the stand-in `gamelobbyloader.php`): links = one row,
no wheel links; a real click rolls one row, marked, shown, inside the list's view, its footer line and
Choose shown, focus kept; a real Enter re-rolls. Pools, each rolled 800-2000 times: default filters
(customs not allowed in this scenario) 47 of 47 factions came up, no stray, no custom; Tier 3 +
Ancients only → 19 of 19, only those two tiers; search "narn" → 1 of 1; Show Only Customs → 41 of 41,
and a roll inside a closed sub-group opens it (`aria-expanded` true). A search that hides the rolled
row forgets it, disables the button and a roll then does nothing; one that keeps it keeps it. Choose →
Narn Regime in the Store (62 ships loaded), window closed, roll forgotten, focus on Switch Faction.
Rolling the current faction shows the roll (green rail, "T1 · current" tag green). A row click forgets
the roll. Reduced motion → `animation-name: none`. 1600 (540×780 window) and a 390 phone (full sheet,
footer flush with its foot, button 358×44, Choose 77×40, the longest custom name — "Nexus Dalithorn
Commonwealth (early)" — wraps inside the footer, no overflow anywhere). No console errors.
**Not verified:** a real server session and a real touch device.

### 12.9 Stage 8 — private / password games (built 2026-09-25)

No schema change: Stage 0's `tac_game.password_hash` (VARCHAR(255) NULL) is written and read for the
first time, so `db/createGameRedesign.sql` must be applied wherever this runs (it already must be, since
Stage 1). No new class; the autoload map is unchanged. Both legacy bundles need rebuilding
(`ajaxInterface.js` is in the game bundle too; `gamelobby.js` / `scenarioCard.js` in the lobby's).

**What "private" means (decision).** A private game has a password. A player who holds no slot in it
and has not entered the password this session gets a **password page in place of the lobby** — the
lobby's own data is never built for them — and **slot.php refuses them a slot**. The creator holds
slot 1 from creation, so never needs it; a player who has taken a slot never needs it again; one who
entered it keeps the game unlocked until their session ends (`$_SESSION['unlockedGames'][gameid]`), so
leaving a slot and coming back costs nothing. The page gate, not a prompt on games.php: gamelobby.php
has to guard a typed-in URL anyway, so there is one door, it works without JS, and a player arriving
from a link someone pasted meets it too. **It guards the lobby PAGE and the slot, not the data:**
`chatdata.php` and `gamedata.php` still serve any game to any logged-in player, as they always have
(spectating) — gating those would put a DB read on the busiest endpoints.

**What landed:**

- **Create Game** (the mockup's Step 1): "Private Game" between Game Name and Load Settings — a
  "Require password" box, and while it is ticked (§11.6) a password field (placeholder "Password to
  join", `maxlength` 64, autocomplete off) with a **Show / Hide** button: the creator has to pass the
  password on and can never change it, so they can check what they typed. On a phone it takes its own
  line and the field the rest of the width (16px, 44px tall).
- **Validation** (`validateStep(1)`): ticked with nothing typed → "Private Game: enter a password, or
  untick Require password."
- **Posted** as a top-level `password` (trimmed string, or null) beside `inServiceDate` — not a rule,
  not a scenario fact. The field has no `name`, so it is never posted on its own, and the submit
  handler **empties it** once the JSON holds it, so a browser does not take the form for a sign-up and
  offer to save it over the player's account password (not verifiable headless — see below).
- **Server** (`Manager`): `normaliseGamePassword` (trim, 64 characters, and bcrypt's 72 bytes — used
  on creation AND on entry, so a longer paste still matches) → `hashGamePassword`
  (`password_hash(PASSWORD_DEFAULT)`, the player accounts' scheme; blank → null = public) →
  `DBManager::createGame`'s new last parameter. `getGameAccess($userid, $gameid)` returns
  `name / status / private / member / locked` and never the hash; `isGameLocked`, `markGameUnlocked`,
  `checkGamePassword` → `'ok' | 'wrong' | 'throttled'`. **Throttle:** 10 wrong passwords per player per
  game in 15 minutes (APCu; the window starts at the first miss), after which even the right one is
  not checked until it expires; a right one clears the count.
- **The ONE read of the hash** is `DBManager::getGameAccess` (name, status, hash, and whether the player
  holds a slot, in one query). `getLobbyGames` selects only `password_hash IS NOT NULL` as a `private`
  flag. Nothing reaches TacGamedata (§12.1 trap 5 holds).
- **gamelobby.php:** the password POST is handled BEFORE `session_write_close()` (it writes the
  session) and a right one redirects 303 to the lobby by GET, so a reload never re-posts. The gate is
  checked after the leave handling and BEFORE `getGameLobbyDataJSON`: a locked player gets a small
  page of its own — the lobby's `.lb-panel` grammar, a "Private Game #id" head with a Font Awesome
  padlock in `--fv-warn`, the game's name, one line of explanation, the password field (autofocused),
  Back to Games and a green Open Lobby; "That is not this game's password." / "Too many wrong
  passwords. Wait a few minutes, then try again." in `--fv-enemy`. Styles `.lb-gate*` at the foot of
  gameLobby.css; `.lb-gate-page` fills the viewport (base.css scrolls the backdrop on a phone, so a
  short page showed black under it).
- **slot.php:** `takeslot` on a locked game → `{"error": "This game is private. Open it again from the
  Games page and enter its password to take a slot."}`; `ajaxInterface.submitSlotAction` now shows a
  slot.php error as written (escaped) instead of "AJAX error: success". Leaving is never gated.
- **games.php Join Games:** a yellow "Private" tag with a padlock drawn inline (no icon font on that
  page), title "Needs a password to join", before the Ladder tag. The rail stays the joinable green —
  it can be joined, with the password.
- **Private Game chip** (`scenarioCard.ruleChips(rules, {isPrivate})`, new yellow `private` kind, the
  Load a Fleet menu's private padlock colour) LEADS the Game rules row — on the Confirm step and under
  the lobby's Map Preview (`renderScenarioPanel`'s new third argument).
- **Saved settings:** `readSettings` keeps `privateGame` (the tick) — the password never goes into
  localStorage (players reuse passwords). Loading brings the tick back with the field empty (a password
  already typed stays); Next then asks for one. Settings saved before this stage load public. Save's
  note adds "The password is not saved." while the box is ticked. No `PRESET_VERSION` bump.

**Verified.** `php -l` on the five PHP files. A server scratch script (34 checks, APCu on): the
normaliser (trim, 64-character cap, a 64 × "é" paste cut to 36 characters / 72 bytes, non-strings),
hashing, a real `Manager::createGame` private + public pair (bcrypt stored / NULL), creator member and
unlocked, stranger locked, a public or missing game never locked, wrong / blank / padded-right / public
/ missing-game password results, the session unlock, the throttle (10 misses, the 11th refused even when
right, other players unaffected, TTL 900, a success clears), the games list flag with no hash in its
JSON, the gamedata payload with no hash and no "password" at all, take slot → member → leave → locked
again; both games deleted. **End to end on the REAL local site** (nginx, headless Chrome over CDP,
three players logged in by session files planted in the php container — 28 checks, no console errors):
the Create Game box / Show / Hide / validation / Enter creating nothing / the Confirm chip / Save
(note, storage without the password) / Load (tick back, empty, Next asks) / the posted JSON (trimmed,
top-level, not in rules, field emptied) → the creator straight into the lobby with the chip; a second
player sees the Private tag, clicking the card gives the gate (focused, no lobby data in the page), a
direct `slot.php` takeslot is refused, a wrong password by Enter shows the message, the right one
(padded) lands in the lobby by GET, Take Slot works, leaving goes to games.php and reopening needs no
password; a third player on a 390px phone: gate 16px / 44px / buttons 44px / no overflow, eleven wrong
passwords → throttled, the right one still throttled; Create Game's row on the phone; the Fleet
Builder still opens straight into its lobby. Screenshots desktop + phone. `fvbuild -Check`: autoload
map up to date, ship validator clean (no new errors), replay 129/130 — the one failure is game
**4251**, the known pre-existing drift (§12.2). Test games 4391-4394 and the session files are deleted.
**Not verified:** a browser's password manager (whether emptying the field really stops "save this
password?"), a real touch device, the live server.

**Found and fixed (user, same day) — pre-existing, and bigger than this stage:** `DBManager::takeSlot`
never checked that the slot was free or that the game was still in its lobby (`UPDATE tac_playeringame
SET playerid = … WHERE gameid = … AND slot = …`). The page hides Take Slot on a taken slot, but a
hand-made POST to slot.php could move a player out of their slot — in an ACTIVE game too, handing over
their fleet. Nothing relied on the overwrite (changeUser.php only swaps the session's user; takeSlot
is the only writer of a slot's player besides leaveSlot). Now `takeSlot` returns **true / false**:

- **Refused** (false): no such slot, the game is not LOBBY, or ANOTHER player holds the slot. Retaking
  one's own slot (a double click) is fine.
- **Claimed first, by a conditional UPDATE** (`… AND (playerid IS NULL OR playerid <= 0 OR playerid =
  caller)`), so of two players taking a slot at once exactly one wins (0 rows affected → re-read: still
  the caller's is fine, anyone else's is a refusal). **Only then** are the caller's slots on the other
  team left — one `leaveSlot` per slot — where the old code left them FIRST, so a refused team switch
  would have cost the player the slot they had. The ladder handicap still runs last, after the old
  slots are gone (it takes "whoever else holds a slot" as the opponent). Ids are `(int)`-cast.
- `Manager::takeSlot` touches the game only on success; `Manager::createGame` throws (rolling the new
  game back) if the creator cannot be seated. slot.php answers a refusal with `{"error": "That slot
  cannot be taken: another player has it, or the game has already started."}`, which the lobby shows
  as written (the `submitSlotAction` change above).

Verified: a scratch script on the local DB (14 checks — stranger vs the creator's slot, a free slot,
double click, a taken other-team slot refused WITH the old slot kept, a second same-team slot, a team
switch leaving every old-team slot, missing slot / game, string ids, an ACTIVE game refusing free and
taken slots alike, a ladder join still getting the opponent's points); over HTTP, slot.php refuses a
taken slot and seats a free one; in the lobby, Take Slot on a slot taken behind the page's back shows
the message and seats nobody. `fvbuild -Check` unchanged (only game 4251). Test games 4395-4397 deleted.

### 12.10 Stage 9 — the Buy / Edit / Bulk-Buy dialogs (built 2026-09-26)

Client only — `client/UI/confirm.js`, `styles/confirm.css`, five lines of `client/gamelobby.js`, and
two hidden templates deleted from `gamelobby.php`. No schema, no PHP logic, no autoload change. The
lobby legacy bundle needs rebuilding (`confirm.js` is in it).

**One dialog, three entry points.** `showShipBuy` (a new ship or flight), `showShipEdit` (a bought one,
re-opened to edit — or, with a new third argument `'copy'`, to copy) and `showBuyBulk` (mines and bulk
OSATs; its new fourth argument `'copy'` only renames the window) are all built from
`confirm.buyDialogShell` plus shared row builders (`addBuyNameRow`, `addBuyFlightSizeRow`,
`addBuyQuantityRow`, `setBuyBase`, `addBuyEnhancementRows`, `addBuyMissileRows`, `openBuySections`).
The three near-identical copies of the enhancement loop are gone, and so are the two dead
commented-out copies (§10.1's "defined twice" `showShipBuy` was in fact inside a `/* … */`, as was a
second `handleInputChangeEdit`) — deleted, as §10.1 asked.

**The window** (the mockup's `Gamelobby_BuyShipDialog`, in the lobby windows' `.lb-modal-panel` look):

- **Head:** title (Buy Ship / Buy Flight / Edit Ship / Copy Ship / Bulk Buy / Edit Purchase / Copy
  Purchase) over `Class · Faction · ISD year`, and a × close box.
- **Body, the only part that scrolls:** the unit block — Name (every ship dialog; never on bulk, as
  before), Flight size (a flight with a size selector: − / value / +, wheel), Quantity (bulk: typed,
  wheeled or stepped, ≥ 1, a text box rather than `type=number`) — then a **Base Hull** line (the
  mockup's §10.5 addition: "Base Hull — 6 × BA Starfox Fighters", "…, each" on bulk), then the
  **sections**: Ammo & Ordnance, Enhancements, Options. Each head is a real `<button aria-expanded>`
  with the lobby's disclosure box (as the Store's size categories — the mockup had a chevron on the
  right) and a badge: "N available", or "N selected · X pts" once anything in it is taken. Any number
  can be open at once. **The heads wear the Purchase panel's MAIN FLEET band** (user, same day: "a
  brighter colour so they stand out"): bright title, a `#90b1ee` wash and a 3px `#90b1ee` accent bar —
  on the LEFT, as these titles are left-aligned — and a `#90b1ee` rule under an open one. (A `#90b1ee`
  TITLE was tried and rejected: it reads dimmer than the near-white MAIN FLEET text.) A row: name over
  its price note | the row's cost | − value + — **the cost LEFT of the spinner** (user, same day), so
  every spinner and dropdown keeps one right edge; the cost shows once taken, a saving such as Poor
  Crew or Sluggish as "−169 pts" in `--fv-own`.
- **Foot, never scrolls:** **the total on a row of its own above Cancel | Buy Ship, right-aligned**
  (user, this stage — the mockup had it beside the buttons); the bulk dialog shows "Per unit" beside
  "Total cost". Buy is Create Game's green, Cancel a quiet outline. The window is dimmed round by a
  100vmax box-shadow rather than an overlay element, so every `$(".confirm").remove()` takes it away.

**Rulings (mine — the user may revisit):**

- **Sections by the data's own signals.** Ammo = the name carries an `(AMMO)` / `(HEAVY|MEDIUM|LIGHT
  AMMO)` tag (every ammo class's `enhancementDescription`; the server files these as ENHANCEMENTS,
  `enhIsOption` false), plus `EXT_AMMO` / `EXT_HAMMO` (extra shots for a fighter's gun — options by
  the flag, ammunition by what they are), plus a flight's missiles (`getMissileOptions`, with the old
  "PER MISSILE LAUNCHER" instruction as a section note). Otherwise `enhIsOption` → Options, else
  Enhancements. Mine-UNIT enhancements (`MINE_ACC`, `MINE_DMG`, …) carry no tag, so they are
  Enhancements; launcher mines (`MINE_BLB`, …) are "(AMMO) Basic Mine", so they are ammunition.
- **Titles lose the coloured prefixes (§10.5):** no "(OPTION)", no "(AMMO)"; only a magazine SIZE
  survives, as words: "(HEAVY AMMO) Basic Shell" → "Heavy Ammo — Basic Shell". The price note keeps
  its old wording ("up to 3 levels, 10pts plus 5pts per level"), except an ammunition row reads "up
  to 220, 4pts each" — its limit is magazine rounds, not levels.
- **No auto-collapse (§10.4 resolved by the user, same day):** every section starts OPEN, however
  long, and the player folds one by hand. (Built first as "more than 8 rows opens closed"; the user
  had Ammo & Ordnance exempted, then dropped the rule altogether — `BUY_COLLAPSE_AT` is gone.)
- **Officers is reserved in `confirm.BUY_SECTIONS` but never shown** — a section no row is filed into
  is hidden, so a dead "coming soon" bar never reaches players (the mockup drew one, dimmed).
- **Arithmetic is transparent:** in a ship dialog Base Hull + every section's subtotal = Total cost
  (row costs are for the whole flight, × flight size); the bulk dialog works per unit — Base Hull +
  subtotals = Per unit, and Total = Per unit × quantity (rounded up, as before).
- Escape (focus anywhere in the window) and the × close it like Cancel.

**The DOM gamelobby.js reads back — unchanged, and listed in the block comment above
`buyDialogShell`:** `.selectAmount.shpenh<N>` and its data keys, `.confirm .selectAmount`
(missiles: `firingMode` / `value`), `.fighterAmount` (text), `.confirm .totalUnitCostAmount`
(`data("value")`), `#bulkQuantity` (`.val()`), `.confirmok`'s data and `this`. The name box is read
as `input[name=shipname]` now (was `.confirm input` — the FIRST input, which Stage 10's filter box
would have become). `copyShip` / `copyBulk` pass `'copy'`.

**Traps found and fixed along the way:**

1. ⚠️ **`getTotalCost` priced from a HIDDEN PAGE TEMPLATE.** `fighterCost = $(".totalUnitCostAmount")
   .data("value")` was unscoped, so by document order it hit gamelobby.php's hidden `.totalUnitCost`
   template (which the dialog's setup had also written the base cost onto), never the dialog's own
   span — that one holds the RUNNING total, and reading it would compound on every click. Now the
   base rides on the dialog's own total as `data("baseCost")` (`setBuyBase`), and the flight
   maximum on `.fighterAmount` as `data("maxSize")`. With both off the templates, the templates
   (`.missileSelectItem`, `.totalUnitCost`) are deleted.
2. **Only the LAST missile type's − / + and wheel worked** — the old code bound them once, after its
   loop. Bound per row now.
3. **Tab was swallowed** in every value box (`preventNonNumericInput` allowed digits, arrows,
   Backspace / Delete / Enter only) — keyboard focus could not leave one. Tab and Escape allowed.
4. **The bulk dialog carries ONE `.totalUnitCostAmount` now** (the row total; the per-unit figure is
   `.costPerUnitSpan` alone). `canAffordEdit` still skips its DOM read for bulk (comment updated) —
   that span is quantity × unit, never the single-unit `pointCost`.
5. **An opaque panel.** At `.lb-modal-panel`'s 0.98 alpha the Store's text ghosted visibly through
   the rows: that panel sits on a dimming overlay, but this dim is a shadow drawn only OUTSIDE the
   box, so the page directly behind is at full brightness.
6. On a phone, the typing fields (name, quantity, the value boxes, the Chameleon dropdown) are 16px
   (iOS zooms into anything smaller on focus), steppers 36px, section heads and buttons 44px, and the
   steppers' hover is behind `@media (hover: hover)` so a tapped one does not stay lit. "ISD&nbsp;2256"
   so the year never wraps away from its label.

Old CSS removed from `confirm.css` (all of it served only these dialogs): `.confirm .missileselect`,
the `.missileSelectItem*` rules (incl. the `.enhChoiceItem` percentage arithmetic and a dead
`.combo` widget), `.fighterAmount`, `.totalUnitCost*`. The new rules are one `.confirm.buyDialog`
block at the foot of the file; it uses `--fv-display` (Orbitron), which gamelobby.php loads through
gamesNew.css — these dialogs open nowhere else.

**Verified — end to end on the REAL local site** (nginx, headless Chrome over CDP, local player 3 by a
planted session file, a Fleet Builder game; 46 checks, no JS errors; driver `s9.mjs` in that session's
scratchpad, on the Stage 8 `cdp.mjs`): the Verloka Mine Cruiser from the Store's own "Add to fleet" —
title / subtitle / 17-8-3 rows / Ammo closed / Officers hidden / no prefixes / Base 675 = Total /
the foot's total row above right-aligned buttons / Elite Crew + Poor Crew (green saving) + three
typed Long Range Missiles with Base + subtotals = Total at each step / the body scrolling under a
foot that does not move / Buy at the dialog's total with the typed name and every count recorded;
Edit (seeded name, badges and total — no double charge) → Poor Crew off → saved at the new total;
Copy (title, button, total) → a second row; × / Escape / Cancel close and buy nothing; Tab leaves a
value box. BA Starfox: Buy Flight, 6 × per-craft base, the Dogfight missile in Ammo with its note,
flight size 5 moving base and total, bought as 5 craft with missiles loaded. D'Shal DEW Mines: Bulk
Buy, quantity ± and typed ("4x" → 4), per unit = base + subtotal, total = per unit × 4, bought at
bulkBuy 4; Edit Purchase and Copy Purchase seeded. Dargan (Chameleon): the dropdown row in Options,
no note, buttons hidden, same right edge as the steppers, a pick "1 selected · 0 pts", bought with
`CHAM_DISG=1`. Phone (390 × 844, touch): 8px gutters, fits the height, nothing overflows, the two
buttons share a row at 44px, 36px steppers, 44px heads, 16px fields. Screenshots desktop + phone.
`php -l gamelobby.php`. Test games 4399-4403 and the session file deleted.
**Not verified:** a real touch device / iOS; Stage 10's filter box (built afterwards — §12.11).

### 12.11 Stage 10 — the filter box in the Buy / Edit / Bulk-Buy dialogs (built 2026-09-26)

Client only — `client/UI/confirm.js` and `styles/confirm.css`. No PHP, no schema, no `gamelobby.js`
change (nothing there reads the dialog's inputs generically: the name is `input[name=shipname]` since
Stage 9 for exactly this reason). The lobby legacy bundle needs rebuilding.

**The box.** **Just above the first section's head** (user, same day) — inside the scrolling body,
under the unit block and the Base Hull line, as wide as the sections, 8px above them: on most ships
that is the Enhancements head, on one with ammunition Ammo & Ordnance (the box filters every section,
so it goes above all of them). Built first where the mockup (`Gamelobby_BuyShipDialog`) drew it — a
row of its own between the window's head and the body, so it never scrolled away; the user moved it
down. `.buyDialogFilter`, in `buyDialogShell`, hidden until `openBuySections` → `confirm.addBuyFilter`
shows it. It is the Faction Picker's search box in this
window: a native `type="search"` (Chrome's own clear ×, as `#factionSearch`), 34px, `.lb-input`'s
colours, dim placeholder. The placeholder names the sections the unit actually has — "Filter ammo,
enhancements, options…" (`BUY_SECTIONS[].word`), "Filter enhancements, options…" on a ship with no
ammunition. All three dialogs get it with no per-dialog code.

**Matching (`buySearchText`).** Every word typed must be in a row's NAME, in any order and any case;
accents are dropped and every run of punctuation is one space, so "range long" and "LONG-range" both
find "Long Range Missile", "heavy shell" finds "Heavy Ammo — Basic Shell". The price note is not
searched. The unit block (name / flight size / quantity) and the Base Hull line are never filtered.

**What a filter does (`filterBuyRows`):**

- A non-matching row gets `.is-filtered` (display none) — it stays in the DOM, so whatever it holds is
  still bought, still in the badge and total, and still read back by `gamelobby.js`. (Verified: Elite +
  Poor Crew taken under two different filters, bought under a third that matched nothing — both
  recorded, at the dialog's total.)
- **Every section with a match is OPENED, and every section without one collapses itself:** folded,
  its head `disabled` and dimmed (no band, `--fv-line-scs` bar), but its badge still says what is
  bought in it. Emptying the box puts every section back as the player had it BEFORE typing (the fold
  is remembered on the section as `openBeforeFilter` when a filter starts).
- Badges: "N selected · X pts" whenever anything in the section is taken, filter or no filter;
  otherwise "3 of 17 match" / "1 of 8 matches" / "no match" while filtering, "17 available" when not
  (`paintBuyBadge`, now shared by `paintBuySummary`, which parks `taken` / `subtotal` on the section).
- Nothing anywhere: "Nothing here matches “zzqx”." under the sections (`.buyFilterEmpty`).
- No scrolling on a change of the text: the matches sit directly under the box being typed in. (While
  the box sat above the body, the body scrolled back to the top on every change; that went with the
  move.)
- A flight's "Missiles are bought per missile launcher" note shows only while a missile row does
  (missile rows now carry `.buyMissileRow`).
- `:last-child` cannot skip a hidden row, so the last row SHOWN in a section loses its rule by class
  (`.is-lastShown`).

**Keys.** Escape in a box with text empties it and keeps the window (stopPropagation, so the shell's
Escape-closes handler does not see it); Escape in an empty box closes the window as before. Enter
moves focus to the first row the filter leaves — its value box (type the count straight in), its
dropdown (Chameleon) or, for a missile, its + — as Enter in the Faction Picker's search picks the first
faction (`enterkeyhint="next"`).

**Rulings (mine — the user may revisit):**

- **Shown from 8 rows** (`confirm.BUY_FILTER_AT`, counting every section). Measured over every unit in
  `static/json`: ships median 10 rows (10th percentile 8), flights median 4 (max 10), mines 2-7, bases
  median 7. So nearly every ship has the box and small flights and mines do not. Unlike Stage 9's
  collapse threshold the user dropped, this one hides nothing — it only decides whether the box is
  drawn.
- **§10.2 said "filters within whichever sections are open".** Built instead as above (a match OPENS
  its section), because filtering only open sections would leave the very row being searched for
  folded away inside a section the player had closed — and the Faction Picker's search already opens
  every group with a match (`.is-searching`). A section with no match still "collapses itself", as
  §10.2 asked; it is not hidden outright, because its badge is how a folded section says what is
  bought in it.
- A no-match section's head is disabled rather than openable onto an empty body.

**Verified — end to end on the REAL local site** (Stage 9's recipe: nginx, headless Chrome over CDP,
player 3 by a planted session file, a Fleet Builder game; driver `s10.mjs` in this session's
scratchpad, 31 checks after the move, no JS errors): Verloka (28 rows) — the box inside the body,
under Base Hull, 8px above the first section and exactly as wide; typing into it from a list scrolled
to the bottom keeps the box and its first match in view; the placeholder, "elite" → Elite Crew alone with Ammo / Options folded + disabled + "no match", a disabled
head that will not open, Enter onto Elite Crew's value box, "range long" / "LONG-range", a no-match
Enhancements still showing "1 selected · 338 pts", the empty note, totals unchanged by any filter,
Escape (filled → cleared, empty → closed), folds restored after a filter (Enhancements folded by hand →
opened by "crew" → folded again when cleared), a buy under a no-match filter, Edit with the filter and
seeded badges; D'Shal mines (6 rows) have no box; BA Starfox (threshold lowered in the test) — the
missile note with "dogfight", gone with a non-missile word; Dargan — Enter onto the Chameleon dropdown;
phone 390 × 844 touch — 16px text, 40px box, 13px in from the dialog's edges, nothing overflows.
**Stage 9's own driver re-run: 47/47.** Test games 4407-4412 and the session files deleted. (After the move, Stage 9's driver: 46/47 — the one miss is its "Base Hull — 6 × …" label check, text the user removed from the Base Hull line by hand the same day.)

⚠️ **Trap:** the Edit tool wrote the accent-stripping regex `/[\u0300-\u036f]/` as the two LITERAL
combining characters (it still ran identically, which is why only the diff showed it). Rewritten as
escapes with node; check the bytes (`cat -A`) after writing any `\u` escape into a file.

**Not verified:** a real touch device / iOS (the native search field's own clear button there); Firefox
(no native clear button — Escape still clears).

**Same-day follow-ups.** The user's own edits, committed with Stage 10: the head's subtitle (`Class ·
Faction · ISD`) commented out in `buyDialogShell`, the "Base Hull —" prefix dropped from that line's
label (`paintBuySummary`), and the name label now "Enter Name". Then, at the user's ask, a **compaction
pass** (`confirm.css` only) — the foot was far taller than it needed to be, and the window should show
more rows before scrolling:

- The dialog sets `line-height: 1.3`. It had inherited the page's 1.5 (gamesNew.css), which put ~8px of
  leading into every two-line row — the biggest single cost.
- Buttons 36 → 28px tall (padding 4px 16px); the gap total → buttons 10 → 6px; list → total 11 → 7px;
  foot padding 6 / 8.
- Head padding 3px, close box 30px; body padding 10 / 8; name box padding 5px; the Base Hull line and
  every section / filter gap tightened (8 → 6px); section heads 34 → 28px; rows padding 7 → 5px; the
  filter box 34 → 30px.
- Touch: buttons, section heads and the close box 44 → 40px, the filter box 40 → 36px; steppers stay
  36px (they set a phone row's height).

Measured on the Verloka Mine Cruiser (desktop 1600 × 1000 / phone 390 × 844, same 88vh / full-height
window): foot 95 → 70px / 101 → 82px, head 47 → 37 / 53 → 45, a noted row 52 → 42.5 / 52 → 47, rows
in view before scrolling **10 → 14 / 8 → 11**. Stage 10's driver 31/31; Stage 9's driver, updated for the
user's edits and the 40px touch sizes (`s9b.mjs`, this session's scratchpad), 47/47. Test games
4413-4417 deleted.

Then (user): **the Buy dialog's head names the class** — "Buy G'Quan Heavy Cruiser", "Buy BA Starfox
Fighters" — in place of "Buy Ship" / "Buy Flight" (`showShipBuy`; the button keeps "Buy Ship" /
"Buy Flight"). The class is escaped (`escapeBuyText`) — the shell writes the title as HTML. The title
takes `overflow-wrap: anywhere`: the longest class in the game, Raiders' "Narn Privateer L'Karus
Raider Cruiser (2244 refit)", is one line on desktop and two on a 390px phone, clear of the close box.
Edit / Copy / Bulk titles unchanged. `title.mjs` 6/6, `s9b.mjs` (titles updated) 47/47; games
4418-4419 deleted.

Then (user): **folding a section made the whole window jump** — `.confirm` is centred (`top: 50%` +
`translate(-50%, -50%)`), so every fold re-centred it and the top slid down. Now all three dialogs open
through `confirm.openBuyDialog` (append → `getTotalCost` → `anchorBuyDialog` → fadeIn): the window is
measured where centring puts it — the used `top` less half its border box, both in the containing
block's own terms, so page scroll and a phone's zoomed-out visual viewport do not enter into it — and
pinned there (`.is-anchored`, `--buy-top`, `translateX(-50%)` only). Folding and filtering then move
only the BOTTOM edge. It opens at its tallest (every section open, nothing filtered), so it never needs
to grow below that; the frame's cap is `min(88vh, 100vh − top − 6vh)` (phone: `min(100dvh − 16px,
100dvh − top − 8px)`), so a full-height window keeps exactly its old size and position and a browser
window made shorter while it is open keeps the bottom on screen. A short window (D'Shal mines) still
opens centred. Verified (`anchor.mjs`, 18/18, desktop + phone): no jump between the first painted frame
and the settled one, same top after folds / filter / clear, from a page scrolled 1200px, on Edit, and
with the viewport cut to 700px while open.

Also the user's own edit, same day: the **Base Hull line is commented out** of `buyDialogShell` (the
§10.5 "base + subtotals = total" line). Nothing breaks — `setBuyBase` / `paintBuySummary` write to an
empty set — but `s9b.mjs`'s ten Base Hull read-backs now fail (they read 0 / ""); the totals they print
all still add up. `s10.mjs` measures the filter against whatever sits above it now: 31/31. Games
4420-4424 deleted.

### 12.12 Final refinements (user, 2026-09-26)

After Stage 10, a last round from the user's review of the finished pages. No schema change. Three new
ship classes (autoload + statics regenerated with `fvbuild -Server`); both legacy bundles rebuilt.

- **Faction Picker: two or three columns** (`gameLobby.css .lb-picker-cols`). The window is 1000px wide
  (was 540), `min(900px, 88vh)` tall. `parseFactions` puts the groups in an inner `.lb-picker-cols` box
  inside the scrolling `#factionList` - ⚠️ a multi-column box with the list's fixed height would
  overflow into extra columns off to the right instead of scrolling. Groups `break-inside: avoid`;
  Custom Factions may break BETWEEN its sub-groups (never inside one, never straight after its header),
  so with customs shown and opened the sub-groups spread over two columns. Groups are spaced by margin,
  not a rule line (a rule at a column top would dangle). Columns rebalance as groups open and close, so
  a header click scrolls the list (not `scrollIntoView` - it can move the page) to keep that header in
  view. One column on a phone. Measured at 1600 × 1000: [Major + League] [Minor + Ancients] [Other],
  list 812px of content in a 644px view (was one ~1700px column).
- **Filter chips** in place of the checkboxes: All · None | Tier 1 · Tier 2 · Tier 3 · Ancients · Other
  | Custom (+ its Show Customs select, still right after it). Any number on at once. Each chip is a
  `<label class="lb-chip lb-chip--check">` round its OLD checkbox (`.tier-filter` / `#toggleCustom`,
  visually hidden, still focusable - Space toggles it), so every handler and the customs-allowed default
  are untouched; the ticked look is `:has(> input:checked)` (the Custom chip yellow). ⚠️ That selector
  must outrank `.lb-chip:hover:not(:disabled)` (0,3,0) - hence `.lb-chip.lb-chip--check:has(...)` -
  or a chip under the pointer hides that the click just toggled it. All / None
  (`gamedata.setAllTierFilters`) set the five TIER chips only - my call: Custom is a different question
  with its own mode and not-allowed warning, and None + Custom would list nothing anyway (the tier and
  custom filters are ANDed). Both read pressed while that is the state (`filterFactionList`).
- **Group header colours**: the official groups' headers are `--fv-accent` (was `--fv-text`, the faction
  names' colour); the Custom sub-group headers (Babylon 5 Wars, Escalation Wars, Nexus, Other Universe)
  now use the group headers' own type (display face, 10.5px, caps) in `--fv-warn`, indented.
- **Map Preview shows the hex grid** (`mapPreview.paintHexGrid`, both pages) - and is now drawn in the
  GAME's proportions: rows are `ROW` = √3/2 of a hex's width apart (pointy-top, odd rows half a hex left,
  as `coordinateConverter.fromHexToGame`; the game sizes the map box and the zones in hex widths ×
  row heights). So a 42 × 30 map is 0.619 as tall as wide (was 0.714), zones and terrain move with it,
  and the old square grid and centre lines are gone. Moon discs now have the AREA of their hexes (a
  circle cannot match both the flat-sided width and the pointed height of a hex disc); the other markers
  are unchanged. Grid lines thin below a 12px hex (the 60 × 40 and open maps).
- **Create Game Confirm step: bigger map** - `.cg-sum-card .cg-map-frame` 400 → 560px max, i.e. the card's
  full width on a desktop (539 × 333, was 400 × 247); the cap keeps the stacked (< 900px) layout sane.
- **Dust Clouds** (user ask; user picked three sizes): `DustCloudThreeHex` / `DustCloudFiveHex` /
  `DustCloudSevenHex` in `ships/terrain`, each `extends DustField` - so its collision (`DustCollision`,
  speed / 2, once a turn per target across all dust), no LoS block and no ram are inherited, not copied.
  `Huge = 1` + `hexOffsets` for the 3 (centre, NW, W - the triangular asteroid's shape) and the 5 (centre,
  NE, NW, W, SW); `Huge = 1` alone for the 7 (the disc). Points 3 / 5 / 7. `addDustAndMeteors` makes
  `floor(dust / 6)` of the Dust count clouds, a d3 picking the size - the random asteroids' 2/3-hex mix;
  placement weights 13 / 12 / 11 put them before the single Dust Fields (10), biggest first, under Dust's
  "may touch other terrain, never share a hex" rule. `BallisticIconContainer.FIELD_TERRAIN_CLASSES` lists
  them, so they get the dim white hexes. Images `img/ships/dustCloud{Three,Five,Seven}Hex.png` were drawn
  FROM dust.png (one tile per hex, clipped to the hex, at dust.png's own 2.56 px per game unit), centre hex
  at the image centre and north up at facing 0 - the irregular asteroids' convention - on a 660px image
  for `canvasSize` 520. Not added to the Terrain Maps' `TerrainLayoutRule` types (not asked).
- **Purchase Fleet**: a custom ship of an official faction is `--fv-warn` (lobby.css `.highlight-custom-ship`,
  was rgba(255,255,0,.77)) - the Show Custom ticks' and Custom Factions' yellow; the cost stands 30px
  clear of Add to fleet (`.lb-ship-cost` margin-right 18px + the row's 12px gap; was 12px).

**Verified:** real local site over CDP as player 211 in lobby 4381 (Asteroid Belt) / 4386 (5 teams), and
Create Game as player 3 - lobby driver 17/17 (canvas ratio, 3 columns, no group split with customs shown
and every sub-group open, header colours and type, chips: single toggle, None, pick-from-nothing, two at
once, All, keyboard Space + focus ring, Custom chip + mode select + warning, cost gap 30px, custom-ship
yellow); phone 3/3 (one-column sheet, 32px chips, map inside 390); Create Game Confirm map 539px, no
sideways scroll desktop or phone; native-resolution canvases checked by eye (dots in hex centres, 2/3-hex
asteroids in adjacent hexes). Dust Clouds (stub `DBManager`, no DB writes): the one-in-six mix for dust
0/5/6/12/17/48, all three sizes over 300 rolls, each footprint 3/5/7 distinct hexes within 1 of its
centre at all six facings through `RammingAttack::getTerrainOccupiedHexes`, and 20 full `advance()`
passes (3 moons, 12 asteroids, 48 dust, 10 meteor swarms on 42 × 30): no shared hex, every cloud placed,
none off the map. `checkShipData` PASS - its 10 new warnings are "no hit chart for locations 1/2" on the
three clouds (inherited from Dust Field) and on Dust Field / Meteor Swarm themselves, none in the baseline
yet. Replay harness: 119 pass, 5 fail - 4251 (known) and 4345 / 4347 / 4349 / 4350, IDENTICAL with the
server changes stashed (Ancient fighters' notes text), so not from this round.
**Not verified:** a Dust Cloud in a live game (its icon and dim hexes on game.php, a ship flying through
one) - create a game with Dust ≥ 6.

### 12.13 Lobby layout: main | side, locked picker columns (user, 2026-09-26)

Client only (`gamelobby.php`, `client/gamelobby.js`, `styles/gameLobby.css`, `styles/lobby.css`); the lobby
legacy bundle rebuilt. Decided with the user from §12.12's measurements - the Fleet column beside a
~1750px Store was ~1650px of empty column, four times the top row's dead space - and two picks: the
side column holds **Teams + Your Fleet** (Teams alone would leave that column empty below 226px), and the
Game Rules chips go in a **strip under Map | Scenario** (in either panel they add ~150-200px to it).

- **Two columns** inside `.lb-top` (1422px): `.lb-layout` grid `1fr | 420px`. MAIN: `.lb-brief` (Map Preview |
  Scenario Description, `align-items: start`, then `.lb-rules-panel` across both - label left, chips beside),
  then Purchase Fleet. SIDE: Teams, then **Your Fleet** (`.lb-fleet-wrap.buy`, `position: sticky; top:
  10px`; the side column stretches to the main one's height, which is what gives the sticky room). Measured
  at 1600: main 986, Map 368 / Scenario 393 tall, rules strip 111. The Purchase panel moved inside
  `.lb-top` - its old 1422px width rule is gone.
- **Purchase Fleet is the Store only**: filter bar, category-chip bar, Store (full main width - no row
  wraps). The points readout, `#reinforcementModeToggle`, `#fleet` (still inside `.lb-fleet.store` -
  lobby.css styles the rows through `.store`) and every tool moved to Your Fleet: head "Your Fleet" +
  points (Fleet Builder's cap box too); foot Load Fleet by #ID, then a 2 × 2 grid Load a Fleet · Save Fleet /
  Check · Ready. **One set of Save / Ready / Check now** - the old top-bar and bottom-bar duplicates are gone
  (handlers are class-bound, so nothing else changed). The panel is at most `100vh - 20px`: the list
  scrolls inside (chat scrollbar), head and tools stay. `.lb-panel`'s `overflow: hidden` is lifted on it so
  the saved-fleet menu can open UPWARDS past the list: `.saved-fleet-wrapper` is static there, so the menu
  is placed against the whole tools box (panel-wide) - a panel pinned to the window has nothing below it.
  Both sections carry `.lb-buy`, so the purchase-scoped button rules dress both.
- **Bought rows** in the 388px column: `.ship.bought` is a wrapping flex row there - name, then class and
  cost in ONE box (`.boughtClassCost`, new span in both row builders; `row.find(".boughtPointCost")` still
  finds the cost) - so class and cost drop to their own line together, never "…Cruiser" + a stray "900p".
  An empty `#fleet` says "No ships yet. Add to fleet in the Store puts them here."
- **Map & Scenario** button in the title bar (`gamedata.initBriefToggle`, `aria-expanded`, ▲ / ▼ like Load a
  Fleet): folds `#lbBrief` away so the Store starts at the top (678 → 146px); remembered per game in
  localStorage (`fv.lobbyBriefClosed.<gameid>`, try/catch - no storage just means open). Reopening redraws
  the map (its labels are sized for the width it is shown at). My call over §12.12's "auto-fold after the
  first purchase": folding content above the pointer mid-purchase would jump the Store under it.
- **Narrower**: ≤1180px side 360px and the brief stacks Scenario / Map / Rules; ≤960px side 320px; ≤760px one
  column - `.lb-main` / `.lb-side` become `display: contents` and the panels are ordered Teams, Scenario,
  Map, Rules, **Your Fleet (above the Store, not sticky)**, Store.
- **Action links in `--fv-accent`** (user): the Store's Add to fleet · Show details (`.lb-linkbtn`) and the
  fleet rows' Details · Edit · Copy · (Reinforcement) · Remove (lobby.css `.store .ship .clickable`, was
  #DEEBFF); hover white + underline.
- **Faction Picker columns are LOCKED** (user: groups jumped between columns as the multi-column box
  rebalanced on every open/close and filter). `gamedata.layoutPickerColumns` deals the six groups into
  `.lb-picker-col` boxes (a grid, `--picker-cols`) once per list, in order and whole, from each group's FULL
  size - every faction row, Custom's sub-groups counted closed - so no filter, search or disclosure moves
  anything; a column the filters empty stays empty. The split minimises the tallest column, ties going to
  the most even (least sum of squares): at 3 columns **[Major, League] [Minor] [Ancients, Other, Custom]**.
  Column count by viewport (3 ≥ 860px, 2 ≥ 560px, else 1), re-dealt only when a `matchMedia` change moves it.
  The header click's scroll-into-view (needed only while groups could move) is gone.

**Verified** (real local site over CDP): `layout.mjs` 18/18 - desktop columns and heights, three real
purchases landing in Your Fleet, link colours, Your Fleet pinned at 10px with its tools on screen after
scrolling 1600px, the saved-fleet menu opening upwards on screen, fold / reload-still-folded / reopen, the
picker's deal and every group staying put through Tier 1 off + Custom on + all sub-groups opened + Minor
closed, an observer (no Purchase / Your Fleet), Fleet Builder (Rules & Info over the Store, no Ready, cap
box in the head), 1100px, phone order; `rows.mjs` 3/3 (class + cost together on four rows, Check opens and
Escape closes the report with focus back); §12.12's `lobby.mjs` 17/17 and `phone.mjs` 3/3 with their
picker selectors updated for the column boxes.
**Not verified:** Save Fleet and Ready clicked for real (they write), a real touch device.

### 12.14 Lobby layout reshaped: two rows (user, 2026-09-26) - supersedes §12.13's column layout

User feedback on §12.13 in play: Purchase Fleet too wide and Your Fleet too narrow; Your Fleet started at a
different height for every team count and sat apart from the Store; panels at different heights read as
cluttered. The user's shape, built as asked (`gamelobby.php` + `gameLobby.css` only - no JS change):

- **Upper row** `.lb-upper` (grid `1fr | 420px`): `.lb-brief` - Map Preview | Scenario Description **at one
  height** (the brief grid now stretches them; Scenario's links pinned to its foot with `margin-top: auto`,
  the map keeps its picture at the top) over the Game Rules strip - beside **Teams**. Teams keeps its own
  height but is **never taller than the brief**; past that its list scrolls inside (chat scrollbar). How:
  Teams sits in `.lb-teams-slot`, stretched to the row with `contain: size`, so Teams' own height is left
  out of the row's and the brief alone sizes it; Teams inside is `max-height: 100%` (a stretched grid
  item's height is definite, so the percentage resolves). Measured at 1600: 2 teams 226px under a 518px
  brief; 8 teams capped at exactly the brief's 484px, list 718 in 448 visible; 5 teams 430 = brief 430.
- **Lower row** `.lb-lower` (grid `1.25fr | 1fr`, the share Store | Fleet had as one panel): Purchase Fleet
  | Your Fleet, **tops level**, always 14px under the brief whatever the team count (781 | 625 at 1600).
  Your Fleet is still pinned 10px down while the Store scrolls; in the wider panel every bought row is
  one line again.
- **Folded** (Map & Scenario hidden): `.lb-upper:has(> .lb-brief[hidden])` - one column, the slot's
  containment and the cap lifted, the teams laid side by side (`auto-fill` 18rem) - a Teams strip across
  the top, then the lower row.
- ≤1180px: Teams 360px, the brief stacked (Scenario / Map / Rules), lower `1.6fr | 1fr`. ≤760px: both rows
  one column, Teams first at its whole height, Your Fleet over the Store, not pinned. Fleet Builder: Rules
  & Info, then the lower row.

**Verified** (real local site over CDP): `layout2.mjs` 14/14 - equal Map / Scenario, links at the foot,
Teams short with 2 teams, capped and scrolling with 8, within the brief with 5, lower row level and at the
same place for 2 / 5 / 8 teams, 1.25 : 1, three purchases one line each, pinned Your Fleet, fold, observer,
Fleet Builder, 1100px, phone; §12.12 / §12.13 drivers `lobby.mjs` 17/17, `rows.mjs` 3/3, `phone.mjs` 3/3.

### 12.15 More refinements (user, 2026-09-27)

Client only (`creategame.php`, `client/UI/createGame.js`, `styles/createGame.css`, `styles/gameLobby.css`, one
comment in `gamelobby.php`). No bundle to rebuild: Create Game loads its script unbundled, and no lobby JS changed.

- **Create Game: In-Service Date is a checkbox** like the other Rules & Options (supersedes §11.6's "year box
  where the checkbox would be"). `#inServiceDateCheck`; ticked, `#inServiceDateWrap` (`.cg-dep`) shows the year
  box INLINE AFTER the label - the same place, gap and height as Simultaneous Movement's and Desperate
  Scenario's selects - and focuses it. The user wrote "to the left of the header ... like Simultaneous
  Movement"; I followed the Sim Move pattern (right of the label). `readInServiceDate` is null while
  unticked (a year typed and then unticked is kept in the box but never posted); Next refuses ticked + not a
  four-digit year ("enter a four-digit year, or untick In-Service Date"). Save Settings writes the year only
  while ticked, so a saved year MEANS ticked - `applySettings` ticks from it, and presets saved before this
  (year or blank) load the same way. No new preset field.
- **Store width** (`.lb-lower`): measured every faction's widest Store row (name + cost + links) with the Store's
  Show Custom on: 676px (12 Colonies of Kobol's Marine Assault Raptor), Earth Alliance (Early) 658 (the Atlas
  flights), Brakiri only 531; 75 of 87 factions fit in 600. Purchase Fleet is now `min(732px, 55%)` - that
  row + the Store's 52px of frame + 4 spare - and Your Fleet the rest: 732 | 674 at 1600 (was 781 | 625).
  Below a 1331px row the 55% is the old 1.25 : 1 share; ≤1180px is still 1.6 : 1 (where some names wrap,
  as before). Fonts differ by platform - a row that does not fit wraps its cost and links, as it always could.
- **Store line spacing**: `.lb-ship` `line-height: 1.3` (it inherited the page's 1.5) and 3px padding - rows 24px,
  were 29 (the pre-redesign Store was ~21). About 20% more ships per screen.
- **Own slot: no blue rail** (`.lb-slot.selected` box-shadow gone) - it clashed with the team rail beside it; the
  blue border and tint still mark it.
- **Map | Scenario | Rules as ONE container**: `.lb-brief` wears the panel frame (border, radius, shadow,
  gradient; no gap) and the three panels inside it lose theirs, keeping a 1px dividing line each (Scenario's
  left edge, the rules strip's top; stacked ≤1180px, the map's top instead). Brief 503px, was 518.
- **Purchase Fleet and Your Fleet level at load**: `.lb-lower:has(#store:empty)` stretches both (their panels
  `height: 100%`) - until a faction is chosen there is nothing to pin Your Fleet against. At 1600 both 296px
  (Purchase was 222). Once the Store has a faction the row goes back to `start` and Your Fleet is pinned as
  before. ⚠️ Found on the way (§12.14's): the phone's one-column flex kept the grid's `align-items: start`, so
  Your Fleet shrank to its content (294 of 354px) - `align-items: stretch` in the ≤760px rule.

**Verified** (real local site over CDP): Create Game `isd.mjs` 13/13 desktop (checkbox lined up with the others,
box after the label and focused, same gap and height as Sim Move's select, blank + ticked refused, digits only,
unticked = no cutoff and Next passes, Confirm chip "In-Service Date: 2258", save ticked / unticked, load both
and an older preset); phone 12/13 - the one miss is Sim Move's own select wrapping under its longer label there,
as before. Lobby `lobby15.mjs` 1600 17/17 (one frame, touching panels, dividers, no rail, level at load with the
tools at the foot, 732 | 674, EA Early / 12 Colonies / Brakiri every row one line and 24px with Show Custom on,
back to own heights after a pick, pinned while scrolled, folded level, 8 teams capped); 1100 and phone - the
brief's stacked dividers, level at load, widths steady on the phone before and after a pick. §12.14's
`layout2.mjs` 12/14 - the two misses are this round's by design (rules strip now touches the map; 732px, not
1.25 : 1).

### 12.16 Refinements 3 (user, 2026-09-27)

`gamelobby.php`, `styles/gameLobby.css`, `styles/createGame.css`, `client/gamelobby.js`, the lobby's React damage
menus (`system/menuControls.js`, `ApplyDamageMenu.js`, `CriticalEffectsSection.js`, `SystemEnhancementsSection.js`,
`SystemInfoMenu.js`, `shipWindow/FighterDamageMenu.js`, `shipWindow/MineDamageMenu.js`, `styled/theme.js`), one
comment in `tokens.css`, and the `$this->faction` line of 130 ship files. Built: `npx vite build` (UI.bundle.js),
`FV_NO_MINIFY=1 node scripts/bundle-legacy.js`, `fvbuild.ps1 -Statics`.

- **Own slot in the team's colour**: `.lb-slot.selected` border = the team's `--rail`, fill = 14% of it mixed into
  the slot's own `#081420` (`color-mix`) - the page blue washed out Leave Slot's red.
- **Load Fleet by #ID**: the box straight after its label (`.lb-fleet-tools .lb-field` `flex-start`, was
  `space-between`).
- **Purchase bar's Show Custom is a `Custom` chip**: the picker's markup (`.lb-chip--check.lb-chip--custom` round
  the same `#toggleCustomShips` checkbox, so every handler and Reset Filters are unchanged); yellow when on. The
  `.lb-check` rules it used are deleted (nothing else used them).
- **Your Fleet head = Purchase Fleet head** (33.8px, was 38.5): `.lb-buy-points` `line-height: 1` (its 13px line
  at the page's 1.5 set the height). Fleet Builder's cap field is 22px with `margin: -3px 0`, overhanging the
  title's line instead of growing the head. ⚠️ On a phone, Fleet Builder with the cap SHOWN still wraps its head
  to two lines (title + points will not fit 352px) - as before.
- **Create Game In-Service Date box**: exactly the label's line (18.75px; `.cg-input.cg-input--year`, two classes
  to beat the coarse-pointer `min-height: 36px`) and `display: block` - sat on its wrapper's text baseline it made
  the line 2px taller even at 18.75. Ticking no longer moves the label, caption or row (desktop and phone). It no
  longer matches Sim Move's 28px select (§12.15's `isd.mjs` check of that fails by design).
- **Damage menus in the lobby look** (ApplyDamageMenu = Enhancements / Damage / Critical Effects; the fighter and
  mine menus share its chassis, so they moved with it): `MENU_CHROME` = `.lb-panel` (opaque navy gradient, `#2a6b8f`
  frame, 6px corners, shadow; `bg`/`titleBg` are GRADIENTS now - paint with `background`); section bars wear the
  Buy dialog's section-head band (Orbitron caps, 3px left bar, wash) in each section's ink, and the bar colour runs
  on down the rows as a 3px rail (`SECTION_INK.*.bar/wash/title`, new). Bronze / teal / rust kept; Damage moved
  from the 202° slate to a 190° teal, since the chassis is now a 201° navy. Tickers 20px, 2px corners; picker 22px;
  `MenuHeader` = the panel-head band; Apply-to-all = Orbitron caps. `SystemInfoMenu` draws NO frame round the menu
  in the lobby (`$bare` at `gamephase === -2`; game.php untouched). `theme.fonts.display` added (Orbitron) - lobby
  surfaces only, game.php does not load it.
- **No 'Z' on the Star Trek / Star Wars factions**: `ZStarTrek (TOS) Federation` / `(early) Federation` / `(early)
  Suliban` / `Klingon` → `StarTrek ...`, `ZStarWars` → `StarWars`, `ZTrek Playtest Other Factions` → `Trek Playtest
  Other Factions` (directories unchanged); `getPowerRating` cases and the picker's Other Universe regex follow.
  Safe for running games: no table stores a faction (ships rebuild from `phpclass`), and every faction comparison
  is ship-to-ship. The ONE persisted copy is a Walkers CPD scan note (`CPDSCAN`, `notekey_human` = the target's
  faction): a running game where a CPD already scanned one of these fleets would lose that adaptation. None
  locally. Deploy needs the statics regenerated (a missing JSON falls back to the slow live build); old `Z*.json`
  files are orphans. Replay corpus games 4255 and 4308 now differ ONLY by these faction strings - re-record them.
  Not touched: `customs/layoutTest.php` ("ZNexus Velrax"), the `files/FV_factions.txt` / `FV_tiers.txt` texts.

**Verified** (real local site over CDP): `r16.mjs` 9/9 at 1600 (team-coloured slot, box 6px after its label,
chip = Reset Filters' height, yellow when on, Reset unticks it, heads equal incl. Fleet Builder capped), phone 8/9
(the Fleet Builder wrap above); `isd16.mjs` 3/3 desktop and phone; menus by screenshot (`menu.mjs`, `menu2.mjs`,
no JS errors); `zfac.mjs` 7/7 (all six listed with their tiers, Other Universe, three Stores load). §12.15's
`lobby15.mjs` 15/17 - the misses are this round's slot colour and the user's own 8px `.lb-lower` gap.

### 12.17 Your Fleet shows one figure (user, 2026-09-27)

The head's "0 / 3500 pts · 3500 pts left" wrapped; it now shows ONE figure in the head's bright `--fv-text-accent`, bold:
**"3500 pts left"**, or for a slot with no limit the points SPENT, **"691 pts"** (my call - with no limit there is nothing
"left", and dropping the spent total would leave an unlimited slot with no fleet total anywhere). `gamelobby.php` markup:
`.lb-buy-spent` (holds `.current`) + `.remaining-points-container`, and `calculateFleet` toggles `.lb-buy-spent` on
`maxPoints == -1` (it already toggled the other). `.max` / the slash are gone outside Fleet Builder; Fleet Builder keeps its cap
controls IN FRONT of the figure: "Unlimited [x] · 691 pts", or "[3500] pts [ ] · 2809 pts left". `.lb-buy-slash` rules deleted.
Head still 33.8px = Purchase Fleet's in all four cases on desktop; on a phone Fleet Builder with the cap field showing still wraps.
Verified with `r17.mjs` (desktop 13/13, phone 12/12 - it also covers WEAPON_ENHANCEMENTS_PLAN.md §13, Repeater Gunsights).

### 12.18 Slot buttons pass + Store rows on a phone (user, 2026-10-05)

`styles/gameLobby.css` only.

- **Slot buttons, one family** (user: the green Take Slot clashed with the Ready chips). The user's 2026-10-01 green
  (borrowed from the Ready button, its text colour an undefined `--fv-greenText`) is gone, and NO slot button wears
  allegiance or Ready green - the team rails and the own slot's tint are already green / red / a team colour. Take
  Slot = the only FILLED one: Your Fleet's Save / Check teal (`rgba(0, 77, 102, 0.692)`, games.php's Fleet button)
  with an `--fv-accent` edge, brighter teal on hover. Select and Leave Slot = a pair of outlines: page blue
  (`--fv-text`, was the dim that looked disabled) and Leave Game's red, filling only on hover - the user's 0.3 red
  fill went brown over the team tint. The 90px `min-width` is kept. Five candidates were shot side by side in the
  live lobby; the accent-blue Take Slot of Stage 4 read paler and weaker than the teal.
- **Ready is a lamp, not a chip**: a 7px `--fv-own-signal` dot with a glow, then the word in `--fv-mine-soft`, no box
  or fill - so a state can never be taken for a button. Green stays: it is what the Ready button leaves behind.
- **Store rows <= 600px** (user: cost under the name on a phone): as in landscape, name + cost share line 1 and Add to
  fleet · Show details take line 2 (`.lb-ship-name` basis 0, `.lb-ship-links` basis 100%). The cost goes flush
  right there (`margin-right: 0`, level with the category counts - my call, the name needs the room). The name's
  3px lead-in moved from lobby.css's inline `.shiptype` (`!important`, indents a wrapped name's FIRST line only) to
  the `.lb-ship-name` block, so a wrapped name's lines start level at every width; single-line rows unchanged.

**Verified** (real local site over CDP, seeded lobbies 4448 = 2 teams / 4449 = 4 teams with every slot state):
desktop, phone and spectator Teams shots plus forced `:hover`; Store rows at 320 / 390 / 600 phone, 844 landscape
and 1600 desktop - cost on the name's line 50/50 Narn, 19/19 12 Colonies, fighters included; the 1600 and Narn
landscape shots are byte-identical to before; no JS errors. Unchanged by design: Leave Slot still shows on a Ready
slot, where clicking it only raises "You have already confirmed your fleet".

### 12.19 Two refinements (user, 2026-10-05)

- **Add to fleet level with the ship's name**: the name's 3px lead-in moved off `.lb-ship-name` onto the row's
  left padding (27 / 45 variant, 15 / 29 on a phone), so the links start under the name's first letter when they
  drop to their own line (phone and landscape). Desktop rows are byte-identical to before.
- **Smaller slot buttons**: 8px type, 0.5px tracking, `3px 7px` padding, 24px tall (36 on touch, unchanged); the
  Ready lamp's word follows at 8px. `min-width` 90 → 82 = Leave Slot's own width, so Take Slot and Leave Slot still
  line up down the right edge; Select (only ever beside Leave Slot) keeps its natural ~57px. Room for the slot's
  name beside Select + Leave Slot in the 420px Teams: 22 characters on one line, was 14 (all three at 82px would
  give 17). At <= 1180px and on a phone the pair still drops under the name (`.lb-slot-main`'s 9rem minimum), where
  the name has the full width anyway.

### 12.20 Leave Slot in steel (user, 2026-10-05)

The red outline fought the own slot's green tint. Leave Slot only ever sits on that green slot or a plain one, so
those were the reference: five colours were shot on both (and on a 4-team game's orange own slot), at rest and on
hover - red (clash), steel, amber (the page's Custom / enhancement colour already), white (louder than the card),
dim ghost (read as disabled). Steel won: `--fv-gunmetal` edge, `#c3ccd6` text, a grey wash on hover. Select stays
the page blue, so the pair still differs.

### 12.21 Ready beside the player's name (user, 2026-10-05)

`gamelobby.php` slot template + `styles/gameLobby.css`. The `.status` span moved from `.lb-slot-actions` into
`.lb-slot-player`, after `.playername` (still in the cloned template, class unchanged - only `.lb-slot.ready .status`
shows it, no script finds it by position). `.lb-slot-player` is a wrapping flex row, the lamp centred on the name,
its 22px `min-height` gone so the name's line keeps its height; a name too long for both puts Ready on a line of its
own. The buttons now have the right-hand side to themselves: a Ready own slot with Select + Leave Slot no longer drops
its buttons under the name (4-team lobby), and on a phone the Ready own slot keeps Leave Slot on its line.
