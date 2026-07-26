# games.php Redesign Plan

Landing-page redesign covering three asks:

1. Review the page's styling/layout and modernise it without losing the site's feel.
2. Turn **Recent Games** from an in-page third column into a modal window (like the ladder),
   listing each game as a two-row container: name on top, players + rules below.
3. Remove the Recent Activity column and spend the freed width on **Your Games** and
   **Join Games**, with better-looking containers.

Refinements agreed 2026-07-26: the Recent Games window looks back **7 days**, not 2; it gets
**search, filter and sort controls** (§4.1); and `ajaxInterface.js` is fair game to edit since
the bundles can be rebuilt.

This is roadmap item 6 (*visual unification of legacy jQuery windows vs React overlays*)
applied to the landing page — the in-game React UI already has a token system
(`source/public/client/UI/reactJs/styled/theme.js`) whose header comment names itself the
seed for exactly this. Nothing here touches combat rules.

---

## 1. What the page is today

`source/public/games.php` renders: header → news panel → games panel (4-column grid) →
global chat → footer, plus the ladder modal via `include("ladder.php")` at line 200.

Data path:

| Column | Server | Client |
|---|---|---|
| YOUR GAMES | `DBManager::getPlayerGames` ([DBManager.php:1791](source/server/controller/DBManager.php#L1791)) | `gamedata.createGames` ([games.js:51](source/public/client/games.js#L51)) |
| JOIN GAMES | `DBManager::getLobbyGames` ([DBManager.php:1921](source/server/controller/DBManager.php#L1921)) | same |
| RECENT ACTIVITY | `DBManager::getFirePhaseGames` ([DBManager.php:1741](source/server/controller/DBManager.php#L1741)) | `gamedata.createFireDiv` ([games.js:9](source/public/client/games.js#L9)) |

Both game lists are baked into the page at render time (`games.php:64`) and re-fetched from
`allgames.php` only on a BFCache restore (`games.php:83-94`); there is no polling loop
(`ajaxInterface.startPollingGames` is a documented no-op).

### 1.1 Findings — typography

- [gamesNew.css:5-7](source/public/styles/gamesNew.css#L5-L7) is `* { font-family: Arial; }`.
  The two Google faces imported on lines 1-2 (**Bruno Ace SC**, **Orbitron**) therefore reach
  almost nothing: only `.faq-panel` / `.faction-panel` headings override the universal rule.
  On games.php every heading — including `YOUR GAMES` and `Welcome to Fiery Void!` — is plain
  Arial. Two webfonts are downloaded and unused on the site's most-visited page.
- Flat scale: body 14px, `.resources` 16px, column headers are default-size bold Arial caps.
  Nothing establishes hierarchy except position.
- Sizes are hardcoded in px across ~40 rules with 12/13/14/15/16/18 mixed without a system.

### 1.2 Findings — colour and contrast

- Two border languages collide: the landing page uses `#215a7a` + `border-radius: 5px`, the
  in-game SCS UI uses `#496791` square. `#37465a`, `#334455` and `#333` also appear.
- Visual decisions live in HTML: `style="background-color:#04161C; border-radius: 5px;"` is
  repeated on all three containers ([games.php:158](source/public/games.php#L158),
  [164](source/public/games.php#L164), [170](source/public/games.php#L170)), and the ladder /
  fleet-test labels are built with inline `<span style=...>` **in SQL result assembly**
  ([DBManager.php:1814](source/server/controller/DBManager.php#L1814),
  [1944](source/server/controller/DBManager.php#L1944),
  [1988](source/server/controller/DBManager.php#L1988)).
- `.gameRules` is `rgba(230,255,230,0.705)` at 12px — thin, and green-tinted for no reason.
- **Bug:** [gamesNew.css:630](source/public/styles/gamesNew.css#L630) is `color: 8bcaf2;` —
  missing `#`, so the header account links silently fall back to `#DEEBFF`.

### 1.3 Findings — layout

- **Dead CSS:** `.game-list` and `.game-list .notfound`
  ([gamesNew.css:399-410](source/public/styles/gamesNew.css#L399-L410)) match no element on
  any page — games.php uses `.gamecontainer`. So the "No active games" placeholder gets **no**
  colour and **no** padding, and `.gamecontainer` has no `min-height`: with one list empty the
  two columns are wildly different heights and the row reads as broken rather than empty.
- **Dead CSS:** `.games-grid`'s `repeat(auto-fit, minmax(280px,1fr))`
  ([gamesNew.css:388](source/public/styles/gamesNew.css#L388)) is immediately overridden by
  `.four-cols`'s `repeat(4, 1fr)` ([gamesNew.css:594-598](source/public/styles/gamesNew.css#L594-L598)).
- **Mobile defect:** the ≤600px media query *keeps* four columns at a 3px gap
  ([gamesNew.css:801-815](source/public/styles/gamesNew.css#L801-L815)). On a 390px phone each
  column is ~85px wide. It must stack.
- The button column fakes its alignment with magic numbers (`padding-top: 1rem` plus
  `margin-top: 2rem` on the first button) instead of sharing the header row.

### 1.4 Findings — semantics and accessibility

- Game entries in YOUR GAMES / JOIN GAMES are `div`s with jQuery click handlers
  ([games.js:47-48](source/public/client/games.js#L47-L48)) — not focusable, no keyboard
  activation, no middle-click / open-in-new-tab. RECENT ACTIVITY entries *do* use `<a>`
  ([games.js:20](source/public/client/games.js#L20)). Same list, two behaviours.
- Columns are anonymous `div`s with a bare `h3`; no landmark or accessible name.
- No `:focus-visible` styling anywhere on the page.

### 1.5 Findings — behaviour bugs (worth fixing while we're in here)

- `createGames()` is **append-only**: everything inside `if (gameDOM.length == 0)`
  ([games.js:62](source/public/client/games.js#L62)) — including the `waitingForTurn` highlight
  ([games.js:69](source/public/client/games.js#L69)). After a refetch, an already-rendered card
  never updates, so the "it's your turn" highlight is stale until a full page reload. That
  highlight is the page's single most important retention signal.
- `parseServerData` re-binds click handlers on every call
  ([games.js:47-48](source/public/client/games.js#L47-L48)) — handlers accumulate on existing
  cards with each BFCache restore.
- YOUR GAMES throws its player count away ([games.js:68](source/public/client/games.js#L68)
  removes `.players`), so an active game shows nothing but its name.
- `getFirePhaseGames` builds a full `TacGamedata` object per row
  ([DBManager.php:1759](source/server/controller/DBManager.php#L1759)) and JSON-encodes it —
  shipping `background`, `creator`, `description`, `points` to render one line of text. Its
  `WHERE` clause puts a function on the column (`DATE_ADD(p.lastactivity, …) >= NOW()`), which
  would defeat an index — though as it happens there is none to defeat (§5). Despite taking
  `$playerid` it never filters by it — the list is
  **every** recently-active game on the site, not the player's. That's the right behaviour for
  a spectate list; it just isn't what the column header ("RECENT ACTIVITY") implies.

---

## 2. Design direction: "contact readout"

**Keep** the page chrome exactly as-is — nebula backdrop, `rgba(23,39,54,0.9)` panels with
5px radius and `#215a7a` borders, `#8bcaf2` links, the green Create Game button. That chrome
*is* the site's feel and it is not the problem.

**Change** what a game entry is. Today it's a bold Arial link in a rounded box. It becomes a
small tactical readout borrowing the datasheet grammar already established in-game by the
React ShipWindow: square 1px internals, uppercase letter-spaced micro-labels, monospace
numerals, shaded header fills. A returning player then reads the landing page in the same
visual language as the game screen instead of two unrelated ones.

**Signature element — the status rail.** Every card carries a 3px stripe on its left edge
encoding the one thing a returning player wants at a glance:

| Rail | Meaning |
|---|---|
| `#84a5ce` + card fill `#254d82d0` | Your turn (keeps today's highlight, plus the rail) |
| `#3a5a72` | Active, waiting on someone else |
| `#52b352` | Joinable lobby |
| `#e6ac00` | Ladder game (overrides, in either column) |
| `#004d66` | Fleet Builder |
| `#2f4a52` | Concluded (Recent Games modal only) |

Every colour above already exists on the page — the rail re-uses them as a consistent
position rather than as five unrelated recolouring schemes. This is the one bold move; the
rest of the redesign stays quiet.

### 2.1 Tokens

New `:root` block in a page-scoped stylesheet, values taken from the existing page and from
`styled/theme.js` so no hue shifts:

```css
--fv-panel:      rgba(23, 39, 54, 0.92);  /* existing panel fill                    */
--fv-well:       #04161c;                 /* existing container fill = theme.panelBg */
--fv-card:       #0a1c26;                 /* NEW: card sits above the well          */
--fv-card-hover: #102a36;
--fv-line:       #215a7a;                 /* landing-page border, unchanged         */
--fv-line-soft:  #1a3f55;                 /* card border, quieter than the panel    */
--fv-line-scs:   #496791;                 /* in-game border = theme.colors.line     */
--fv-text:       #deebff;
--fv-text-dim:   #7f9bb8;                 /* theme.colors.textDim                   */
--fv-accent:     #8bcaf2;
--fv-turn:       #84a5ce;  --fv-turn-bg: #254d82d0;
--fv-lobby:      #52b352;
--fv-ladder:     #e6ac00;
--fv-mono:       Consolas, "Lucida Console", monospace;  /* theme.fonts.mono        */
--fv-display:    "Orbitron", sans-serif;                 /* already imported        */
```

### 2.2 Type roles

| Role | Face | Where |
|---|---|---|
| Display | **Bruno Ace SC** | news-panel `h2` only — its existing role on faq.php / factions-tiers.php |
| Subhead | **Orbitron**, uppercase, `letter-spacing: 1.2px` | column headers, modal title |
| Body | Arial (unchanged) | prose, buttons |
| Data | **Consolas** (`--fv-mono`) | player counts, turn numbers, map size, rule strings |

No new font is loaded — both faces are already imported and currently wasted. Overrides are
scoped to `.games-panel` / `.news-panel h2` / the modal so the universal Arial rule keeps
governing the other 11 pages that share `gamesNew.css`.

### 2.3 Card anatomy

```
┌─┬────────────────────────────────────────────┐
│▌│ HOUND OF SHADOW                        ▸   │  Orbitron 12px, uppercase, ellipsis
│▌│ 2/4 PLAYERS · 42×30 · SIM MOV · TERRAIN    │  Consolas 10px, --fv-text-dim
└─┴────────────────────────────────────────────┘
 ↑ status rail
```

- Row 2 is one `·`-separated monospace line, not pills — four pill chips at 10px wrap badly
  and the readout reads denser and calmer. Ellipsised, full string in `title`.
- Cards become `<a href>`: keyboard reachable, middle-clickable, and the click handlers (and
  their accumulation bug) disappear.
- Hover: `--fv-card-hover` fill + rail brightens, 120ms, `prefers-reduced-motion` respected.
- Lists get `max-height: 340px; overflow-y: auto` with the thin scrollbar already defined in
  `ladder.css`, and a real empty state — centred, `--fv-text-dim`, with `min-height` so the
  two columns stay level.

---

## 3. Layout (decision: narrowed actions column)

```
GAMES PANEL
┌─ YOUR GAMES ─────────┬─ JOIN GAMES ─────────┬─ ACTIONS ───┐
│ ▌HOUND OF SHADOW   ▸ │ ▌LADDER: DK'S GAME   │ [ Create  ] │
│  2/4 · 42x30 · SIM   │  1/2 · OPEN · STD    │ [ Fleet   ] │
│ ▌NARN SKIRMISH       │ ▌FLEET BUILDER       │ [ Ladder  ] │
│  3/4 · OPEN · TERR   │  —                   │ [ Recent  ] │
└──────────────────────┴──────────────────────┴─────────────┘
   ~460px                 ~460px                 ~190px
```

`grid-template-columns: 2.5fr 2.5fr 1fr` — the lists roughly double from ~280px to ~460px.
The buttons stay where players already look for them; the column just stops being as wide as
a game list. Button-column alignment comes from a shared header row instead of
`padding-top: 1rem` + `margin-top: 2rem`.

Responsive: 3 columns ≥900px → 2 columns with a full-width action row 600-900px → single
column stack below 600px (replacing the current four-columns-on-a-phone rule).

---

## 4. Recent Games modal

Same shell as the ladder modal (overlay + centred panel + `×`), so it reads as an established
pattern rather than a new one. Wider than the ladder's 800px — `max-width: 1040px` — because
the content is a two-up card grid rather than a table.

- **Title** `RECENT GAMES` (Orbitron). **Subtitle:** "Every game across Fiery Void with
  activity in the last 7 days." The copy states both the window and the fact that the list is
  site-wide, not personal — the current column header implies otherwise.
- **Body:** scrollable (`max-height: 60vh`), cards in a 2-up grid collapsing to 1-up on narrow
  screens:

  ```
  ┌─┬──────────────────────────────────────────┐
  │▌│ NARN CIVIL WAR                YOU LADDER │
  │▌│ TURN 14 · 2/4 PLAYERS · 42×30 · SIM MOV  │
  │▌│ melkorium, lunara                        │
  └─┴──────────────────────────────────────────┘
  ```

  **Note a change to your spec:** this card has a third line for participant names. Being able
  to search by player only makes sense if the names are visible in the result — otherwise the
  filter looks broken when it hides a card whose match you can't see. Names are dim, ellipsised,
  with the full list in `title`. Your Games / Join Games cards stay two-row as specified. Say
  the word if you'd rather keep the modal at two rows and put names in the tooltip only.

### 4.1 Control bar

```
┌─ RECENT GAMES ──────────────────────────────────────────────────── × ┐
│ Every game across Fiery Void with activity in the last 7 days.       │
│                                                                      │
│ [ Search games or players     ] [MINE] [LADDER] [ACTIVE]   Sort: [▾] │
│ Showing 12 of 47 games · Clear filters                               │
├──────────────────────────────────────────────────────────────────────┤
```

- **Search** — one box, matched against game name **and** participant usernames. One box
  rather than two: people type a name without first deciding which field it belongs to.
  Case-insensitive substring, filters as you type (debounced ~150ms).
- **Toggle chips** — `MINE` (games you're in), `LADDER` (ranked only), `ACTIVE` (hide concluded
  games). Chips rather than dropdowns: they're one click, they show their own state, and they
  match the tactical-readout language. Combined with AND.
- **Sort** — Most recent activity (default) · Highest turn · Game name A-Z.
- **Result count line** — always visible, so an active filter is never invisible, with a
  **Clear filters** link when any is set.
- `MINE` / `LADDER` / `ACTIVE` and the sort choice persist in `localStorage` (the page already
  uses it for `fv_lastGamesRequest`). The count line is what keeps persisted filters from being
  a "where did my games go?" trap.

**`MINE` defaults to off.** Your own games are already findable at a glance — they carry a `YOU`
tag and a brighter rail, and the default sort puts recent activity first, which for a
participant is usually their own game anyway. Defaulting it *on* would hide the spectating half
of what this list is for. One click either way, and the choice is remembered.

**Filtering and sorting happen client-side**, on the single fetched payload — instant, no
round-trip per keystroke. That holds because the whole 7-day set is small (see §5). If it ever
exceeds ~200 rows, move filtering into the query; the modal's render function is written so
that swap only changes where the filtered array comes from.

**Considered and rejected**, to keep the surface small: per-faction filter (needs fleet data
this query doesn't load), points/size bracket, terrain/sim-move chips (already visible on every
card — filtering by them is a rare need), and date-range pickers (the window is 7 days; a picker
inside a 7-day span is furniture).

### 4.2 States

All in the interface's voice, and the two empty cases are distinct because the fix differs:

- loading — "Loading recent games…"
- nothing in the window — "No games have been active in the last 7 days."
- nothing matches the filters — "No games match those filters." + **Clear filters**
- error — "Couldn't load recent games. Try again." + a **Try again** button

**Keyboard:** Esc closes, `×` is focusable, focus moves to the search box on open and returns to
the Recent Games button on close. Esc goes in the shared shell, so the ladder modal gains it too
(it has none today). Clicking a card opens `game.php?gameid=N`, as the current list does.

---

## 5. Data-layer changes

The blocker for restyling anything is that presentation is assembled in SQL result loops.
`name` currently arrives as HTML: `<span style="…">LADDER: </span>` + the name +
`<br><span class="gameRules">(Open, Sim. Mov, Terrain)</span>`. Fix: return structured fields
and render in the client.

**`getPlayerGames`** → `{ id, name (plain text), waiting, ladder, gamespace, rules{}, playerCount, slots }`
— gains the player count YOUR GAMES currently discards.

**`getLobbyGames`** → same shape plus `test`; stops building `LADDER:` spans, the
`Fleet Builder` label and the `.gameRules` fragment into `name`.

**`getRecentGames($playerid)`** (new, replaces the modal's use of `getFirePhaseGames`) —
plain rows, no `TacGamedata` construction. Everything the filters need comes out of one grouped
query, including the two fields the modal's controls depend on: `mine` and `players`.

```sql
SELECT g.id, g.name, g.turn, g.status, g.slots, g.gamespace, g.rules,
       COUNT(DISTINCT CASE WHEN p.playerid > 0 THEN p.playerid END)      AS playerCount,
       MAX(CASE WHEN p.playerid = ? THEN 1 ELSE 0 END)                   AS mine,
       GROUP_CONCAT(DISTINCT pl.username ORDER BY pl.username SEPARATOR ', ') AS players,
       MAX(p.lastactivity)                                               AS lastActivity
FROM tac_game g
JOIN tac_playeringame p ON p.gameid = g.id
LEFT JOIN player pl     ON pl.id = p.playerid
WHERE g.turn > 0
  AND p.lastactivity >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY g.id
ORDER BY lastActivity DESC
LIMIT 100
```

- **`mine`** finally uses the `$playerid` the current method accepts and ignores.
- **`players`** is what makes the name search possible, and it's the single most useful thing
  missing from the list today — "who played?" is why you open it.
- Prepared statement, like `getPlayerGames` / `getLobbyGames` (the current
  `getFirePhaseGames` interpolates into `$this->query`).
- `LIMIT 100` with a 7-day window: on a community-scale site that's the whole set with headroom,
  so the client can filter locally. Payload ~20KB. Confirm the real 7-day row count against the
  local DB during Stage 1 and raise/lower the limit accordingly.
- `GROUP_CONCAT`'s default `group_concat_max_len` (1024) is ample for a handful of usernames.

**On the cost of widening the window:** none. `DATE_SUB(NOW(), …)` keeps the function off the
column, which is the correct form, but there is no index for it to use — `tac_playeringame` has
only `PRIMARY KEY (gameid, slot)` ([emptyDatabase.sql:317-338](db/emptyDatabase.sql#L317-L338)),
nothing on `lastactivity` or `playerid`. It's a full scan of that table either way, so 2 days → 7
days changes the rows returned, not the work done. Adding an index is a separate, measure-first
question — noted, not proposed (the movement-index experiment was a net loss and sits on the
do-not-re-chase list).

**Rule flags:** derive them from `json_decode($rules, true)` (already done for `fleetTest` at
[DBManager.php:1938](source/server/controller/DBManager.php#L1938)) rather than
`strpos($rules, 'friendlyFire')`. Today's `strpos` checks are only correct because
`createGame.js` **deletes** disabled keys ([createGame.js:486](source/public/client/UI/createGame.js#L486),
[496](source/public/client/UI/createGame.js#L496), [536](source/public/client/UI/createGame.js#L536))
— one server-side default writing `false` would light every chip. Same pass fixes the terrain
chip, which currently shows whenever a `moons` key exists even if it is `{0,0,0}`: derive it
from `asteroids > 0 || small+medium+large > 0`.

**New endpoint** `source/public/recentgames.php`, mirroring `firePhaseGames.php`.
`firePhaseGames.php` stays on disk, unused: per the repo's not-until-stable convention, and
because a browser holding a cached copy of the previous `game.legacy.bundle.js` after a deploy
would otherwise request a 404. It's noted for the cleanup pass.

---

## 6. Stages

### Stage 0 — mockup, no repo changes
Standalone HTML in the scratchpad with realistic dummy games, published as a private artifact:
both list columns with every card state, and the Recent Games modal with a **working** control
bar — live search, chips and sort over the dummy data, so the filter behaviour can be judged
rather than imagined — plus the empty, no-match and error states. **Sign-off gate — nothing
below starts until the look is approved.**

### Stage 1 — server: structured data
`DBManager.php` only: rewrite the two existing methods' return shapes, add `getRecentGames`,
add `Manager::getRecentGames` wrapper, add `recentgames.php`. Verify against the local DB
(`howto_docker_db_access`) that the concluded-game `status` value is what the card tag should
print. PHP-lint both files in Docker.
*Acceptance:* `allgames.php` and `recentgames.php` return the new shapes; no HTML in `name`.

### Stage 2 — client: rendering
Rewrite `createGames()` as render-from-state (update in place → the your-turn highlight,
player counts and rules refresh on refetch; fixes §1.5). Cards become `<a href>`; delete the
click handlers and `createFireDiv`. In `ajaxInterface.js`, replace `getFirePhaseGames` with
`getRecentGames` pointing at the new endpoint.

New `client/recentGames.js`, modelled on `ladder.js`: open/close, one fetch, and a small
filter/sort state machine — `{ query, mine, ladder, activeOnly, sort }` → filtered array →
render. Keep fetch, filter and render as three separate functions so filtering can move
server-side later without touching the rest, and so re-filtering never re-fetches.

*Acceptance:* BFCache restore updates a stale your-turn highlight; no duplicate handlers;
typing in the search box triggers no network requests; every filter combination renders a
correct count line.

### Stage 3 — markup
`games.php`: drop the RECENT ACTIVITY column and the three inline `style` attributes, add the
shared header row, `<section aria-labelledby>` per column, `include("recentgames.php")` beside
the ladder include, repoint the button from `loadFireList()`, update the `pageshow` handler
(which references `#fireList`).

### Stage 4 — CSS
- New `source/public/styles/gamesPanel.css`, linked **only** by games.php: tokens, cards,
  grid, action column, modal card grid, control bar (search field, toggle chips, sort select,
  count line), focus rings, reduced-motion. The search field and select follow `.ladder-input`
  ([ladder.css:217-223](source/public/styles/ladder.css#L217-L223)) so form controls stay
  consistent between the two modals.
- The shared modal shell goes in `ladder.css`, not the new file — `ladder.php` is also included
  by `creategame.php` (line 428), which does not link the new stylesheet.
- Minimal surgical edits to `gamesNew.css`: the missing `#` on line 630, the dead `auto-fit`
  rule, the dead `.game-list` block, and the ≤600px four-column rule.

### Stage 5 — polish and QA
Contrast pass (target ≥4.5:1 for body, ≥3:1 for the dim monospace row), `:focus-visible`,
120ms transitions, reduced-motion, mobile down to 390px.

### Stage 6 — optional, separate
Self-host Orbitron / Bruno Ace SC. They arrive via `@import` at the top of `gamesNew.css`
(the slowest possible way — a render-blocking third-party request serialised behind the
stylesheet) on all 12 pages that link it. That was tolerable while the faces were nearly
unused; once headings actually depend on them it is load-critical. jQuery was self-hosted for
the same reason.

---

## 7. Constraints and traps

- **`client/ajaxInterface.js` is in scope** (your call, 2026-07-26), so `getRecentGames` goes
  there next to the other fetchers rather than being hand-rolled in the new module. It is bundled
  into both `game.legacy.bundle.js` and `gamelobby.legacy.bundle.js`, so both regenerate — your
  `yarn watch:legacy` handles that; I won't run a build, and the two bundle files stay
  uncommitted (they are already dirty in the working tree). `client/games.js`, `client/ladder.js`
  and the new `client/recentGames.js` are loaded only by games.php / creategame.php, neither of
  which is bundled.
- **`gamesNew.css` is shared by 12 pages** (ammo-options, chpass, creategame, factions-tiers,
  faq, fleetchecker, gamelobby, games, index, profile, reg, starterGuide). All new styling goes
  in the page-scoped file; edits to the shared file are limited to the four dead/broken rules
  listed in Stage 4.
- **No autoload regeneration.** Only methods are added to existing classes — no new PHP class.
  (New public entry scripts are not autoloaded.) Never hand-edit `autoload.php`.
- **APCu:** `getTacGames` caches its result for 2s under a deploy-versioned prefix
  ([Manager.php:39-51](source/server/controller/Manager.php#L39-L51)), so the shape change
  cannot serve stale old-shape rows across a deploy. Locally, expect up to 2s of lag when
  testing shape changes.
- **Replay harness not applicable** — no combat/rules logic is touched, and the local baseline
  is stale anyway. Verification is PHP lint + manual matrix below.
- Preserve the `GENERATING` stampede-protection path (`games.php:28-34`) — it renders before
  any of the new markup.

## 8. Test matrix

| Case | Expect |
|---|---|
| No games at all | Both columns show a styled empty state, equal height |
| Active game, your turn | Rail + fill highlight; refreshes on BFCache restore without reload |
| Active game, others' turn | Quiet rail, player count visible (new) |
| Ladder game | Gold rail + LADDER tag, in both columns and the modal |
| Fleet Builder lobby | Teal rail, "Fleet Builder" label, no player count |
| Lobby with terrain + mines + sim move | All chips correct; no chip for a disabled rule |
| Recent Games, populated / empty window / no filter match / server error | Four distinct states, correct copy |
| Recent Games, game 6 days old | Present (2-day window would have dropped it) |
| Search by opponent's username | Matches on the participants line, count line updates |
| Search matching a game name, not a player | Also matches — one box covers both |
| MINE chip | Only `mine = 1` cards remain; `YOU` tags on all of them |
| LADDER + ACTIVE chips together | ANDed; concluded ladder games hidden |
| Each sort option | Recent / turn / name orders correctly, filters preserved |
| Reopen the modal after setting filters | Chips and sort restored, count line shows they're active |
| Modal keyboard | Esc closes, `×` focusable, focus starts in search, returns to the button |
| 390px phone | Single column stack; no four-column squeeze |
| 1200px+ desktop | 2.5 / 2.5 / 1 grid, lists ~460px |

## 9. As built — 2026-07-26

Stages 0–4 **DONE**, awaiting the user's browser test (Stage 5).

| Stage | Status | Files |
|---|---|---|
| 0 mockup | accepted | scratchpad only; artifact `326dba56-d658-4573-8eec-eb3d35bcace0` |
| 1 server | done | `DBManager.php` (+`describeGameRules`, +`getRecentGames`, 2 methods rewritten), `Manager.php` (+`getRecentGames`), new `recentgameslist.php` |
| 2 client | done | `games.js` (rewritten), `recentGames.js` (new), `ajaxInterface.js` (`getFirePhaseGames`→`getRecentGames`) |
| 3 markup | done | `games.php`, new `recentgames.php` |
| 4 CSS | done | new `styles/gamesPanel.css`; 4 surgical edits to `gamesNew.css` |

### Verification run
- PHP lint clean on all 5 touched/new PHP files; `node --check` clean on all 3 JS files.
- **Server**: scratch script against the real DB — 78 active / 1 lobby / 13 recent rows, all
  structured keys present, no HTML left in any `name`, payload 3.1KB for the 7-day set.
- **HTTP**: `games.php` fetched with a seeded session cookie (http 200) and asserted against
  20 markup checks — new grid/wells/badges/modal present, `RECENT ACTIVITY` /`#fireList` /
  `loadFireList` / inline styles / `.four-cols` / `.notfound` all gone. `recentgameslist.php`
  returns correct JSON with `Content-Type: application/json`, and 403 JSON without a session.
- **Client**: 24 node unit tests over `gameCardHtml` / `cardHtml` / `apply()` — every rail
  state, the ladder-over-your-turn class order, fleet-test relabelling, HTML escaping of a
  `<img onerror>` game name, all three sorts, each chip, chips ANDed, search matching a
  player name *and* a game name, and the `GENERATING` sentinel. All pass.

### Decisions taken while building
- **Concluded status is `SURRENDERED`**, not `FINISHED` — confirmed against the DB
  (`SURRENDERED` 87 / `ACTIVE` 76 / `LOBBY` 1). The card tag reads **ENDED**, which covers it
  without asserting *how* it ended. Change the label in `recentGames.cardHtml` if you'd
  rather it said SURRENDERED.
- **`turn` added to `getPlayerGames`** — the Your Games card shows TURN n, which the original
  query didn't select.
- **Lists sort your-turn-first, then newest.** The wells scroll at 340px, and the DB returns
  no order; with 78 active games the ones waiting on you could otherwise sit below the fold,
  which would defeat the highlight the redesign is built around.
- **No `ladder.css` change.** The plan expected the modal shell to be shared there because
  `creategame.php` also includes `ladder.php` — but `recentgames.php` is only ever included by
  `games.php`, so nothing is shared and the shell lives in the page-scoped file. The Esc
  handler in `recentGames.js` closes the ladder window too, so the ladder gains Esc on
  games.php only (it had none anywhere before).
- **Dropped the `▸` chevron and the brighter "mine" rail** (mockup review): with whole-card
  hover the chevron was decoration, and a seventh rail colour would dilute the encoding — the
  `YOU` tag carries ownership.
- Dead rules in `gamesNew.css` are **commented, not deleted** (`.games-grid`, `.four-cols`,
  `.game-list`, `.create-col`, and the four-columns-on-a-phone media query), per the repo's
  not-until-live-stable convention. `.clickableGames` / `.activeName` / `.lobbyname` /
  `.gameRules` / `.players` / `.waitingForTurn` / `.game-type-*` are now unused by games.php
  but left untouched pending a check of gamelobby.php — cleanup pass.

### Two problems found and fixed in passing
- **Stored XSS on the games list.** Game names are stored raw
  ([Manager.php:291](source/server/controller/Manager.php#L291) — prepared statement, so no
  SQL injection, but no escaping either) and the old client interpolated them into an HTML
  template unescaped. A game named `<img src=x onerror=…>` would have run script in every
  player's browser on games.php. All name/player output now goes through `escapeHtml`, and a
  unit test pins it.
- **UTF-8 BOM breaks a PHP entry file.** A PowerShell `Set-Content -Encoding utf8` round-trip
  put a BOM on `recentgameslist.php`; the BOM is output before `<?php`, so `global.php`'s
  `session_cache_limiter()` fataled with "headers already sent". Rewritten BOM-free and all
  ten touched files byte-checked. Never write a PHP entry file through PS 5.1 `Set-Content`.

### Feedback round 1 — 2026-07-26 (after the user's first browser test)

1. **Taller lists** — `.fv-well` 132/340px → 176/460px (~6 cards visible → ~9); the modal
   body 60vh → 66vh.
2. **Action buttons restyled** — they were flat bright slabs in three unrelated hues sitting
   next to the new lists. Now the same family as the modal's filter chips (the in-game
   chrome's shaded header-bar fill), uppercase Orbitron, so everything that is a *control*
   reads as one thing and the cards, which are *content*, read as another. All four are now
   identical height (flex centring + one `min-height`); Create Game was taller purely because
   it was 14px against its neighbours' 13px. It keeps the site's green as a tint plus a
   brighter border rather than a solid slab.
3. **Joinable ladder games keep the green rail** — ranked is shown by the tag alone. In that
   column the rail answers "can I join it?" and the tag answers "is it ranked?". Your Games
   still takes the gold rail, where there is no green to preserve.
4. **`Sandbox` tag → `Fleet Build Only`.**
5. **Panel treatment** — news / games / chat panels lifted off the nebula: brighter hairline
   border, soft outer shadow, inset top highlight, and one directional accent stripe across
   the top. The stripe is a background *layer*, not a `::before`, because `#globalchat`
   scrolls and an absolutely positioned pseudo-element would scroll away with the chat log.
   Scoped to games.php; promoting it to gamesNew.css for the other 11 pages is a one-line move.
6. **"Welcome to Fiery Void!" was rendering in two fonts** — and the cause was broader than
   that heading. `gamesNew.css` opens with `* { font-family: Arial; }`; a universal selector
   matches every element *directly*, and a direct match beats an inherited value regardless
   of specificity. So the `<strong>` fell back to Arial while its `<h2>` was Bruno Ace SC —
   and the same was silently happening to the `<span>`s inside every column header, which
   were rendering in Arial rather than Orbitron. Fixed once, at the subtree level
   (`.games-panel *`, `.fv-modal *`, `.news-panel h2 *` → `font-family: inherit`), rather
   than by naming each nested element. That rule must stay above the face assignments:
   equal specificity, so source order decides.
7. **"Your turn" tag visibility** — outlined `#84a5ce` on the `#254d82` your-turn fill was
   about 1.4:1. Now filled, in theme.js's `textAccent` `#C6E2FF` at roughly 6:1, and still
   clearly distinct from the gold ladder tag.

Re-verified: `node --check` clean, 25/25 client unit tests pass (two updated for the ladder-rail
and tag-label changes), stylesheet brace-balanced with all 20 referenced tokens defined,
`games.php` and `gamesPanel.css` both serve 200.

### Left for the user
- `ajaxInterface.js` changed, so **both legacy bundles regenerate** — `yarn watch:legacy`
  handles it; the two bundle files stay uncommitted as usual.
- Browser test per §8, including the mobile breakpoints, which nothing above exercised.
- Stage 6 (self-hosting Orbitron / Bruno Ace SC) remains optional and separate — the faces
  are now load-critical on this page rather than nearly unused.

## 10. Out of scope

News panel content, chat panel, ladder modal internals (it only gains the shared shell and
Esc), `firePhaseGames.php` deletion, and any change to how `game.php` handles a spectator
opening a game from the Recent Games list.
