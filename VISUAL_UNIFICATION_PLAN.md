# Visual Unification Plan (roadmap item 6)

Re-review of the styling layer **after** item 5 (ship-window redesign / legacy retirement)
landed, plus a staged, revertible delivery plan.

Status: **COMPLETE — all stages 0–5 built 2026-07-30, through six review rounds.** The whole
site (game screen *and* landing pages) is converged on the SCS skin; **no live stylesheet
rule or React source paints legacy chrome any more** (verified per-property in round 6);
1,862 lines of dead CSS deleted, `shipwindow.css` included. See §8 for the as-built record,
the standing exceptions (ini drawer, tooltip radius, sprite chrome) and the three remaining
open items — none of which is a blocker and one of which belongs to item 5.
Written 2026-07-29.

Related: [SHIPWINDOW_REDESIGN_PLAN.md](SHIPWINDOW_REDESIGN_PLAN.md) (item 5, the source of
the SCS design language), [GAMES_PAGE_REDESIGN_PLAN.md](GAMES_PAGE_REDESIGN_PLAN.md)
(item 6 already applied to the landing page — its conventions are the precedent this plan
extends rather than replaces).

---

## 1. What the re-review found

The roadmap line describes item 6 as *"two fonts-and-borders worlds on one screen"* and
prescribes *"shared CSS custom properties applied to the 15 legacy stylesheets"*. Both
halves of that sentence are now out of date.

### 1.1 It is four worlds, not two

Item 5 did not reduce the split — it added the best-looking member of it. On game.php today:

| # | World | Defined in | Border | Corner | Fill | Numerals |
|---|---|---|---|---|---|---|
| 1 | Legacy chrome | [tactical.css](source/public/styles/tactical.css) | `#587e8d` | 8px / 30px | `#0a3340` @ .85 | Arial |
| 2 | Legacy modals | [confirm.css](source/public/styles/confirm.css) | `#496791` | 5px | black | Arial |
| 3 | **Old** React overlays | [styled/Container.js](source/public/client/UI/reactJs/styled/Container.js#L3-L8) | `#587e8d` | **30px** | `#0a3340` | Arial |
| 4 | **New** React SCS | [styled/theme.js](source/public/client/UI/reactJs/styled/theme.js) | `#496791` | **square** | `#152029de` | **Consolas** |

Worlds 1 and 3 are the same skin expressed twice — `Container.js` is a styled-components
transcription of `#phaseheader`, right down to `box-shadow: 5px 5px 10px black`. World 4 is
the item-5 product. So the visible tension is **3-vs-4**: a square, monospaced, desaturated
datasheet window sitting next to 30px-radius pill-cornered overlays.

Which surfaces belong to which:

- **World 1/2 (jQuery):** `#phaseheader` + commit buttons, `#logcontainer` + tabs,
  `#shipMovementUI`, `#iniGui`, `#infowindow`, fleet list, `.confirm` dialogs, replay bar.
- **World 3 (old React):** `#weaponList`, `#showEwButtons`, `#fullScreen`, `#playerSettings`,
  `#shipThrust`.
- **World 4 (new React):** `#shipWindowsReact` only.

### 1.2 A fifth world already exists — and it is the model to copy

The games.php redesign introduced a real token set, `--fv-*`, with the explicit note *"Roadmap
item 6, applied here"* ([gamesPanel.css:1-56](source/public/styles/gamesPanel.css#L1-L56)).
It works, it is documented, and its values were deliberately taken from `theme.js` so the two
stacks agree. **This plan's job is to finish what that started, not to invent a scheme.**

Two problems to inherit and fix:

- The `:root` block is **duplicated** in [ladder.css:22-43](source/public/styles/ladder.css#L22-L43),
  because creategame.php includes ladder.php but does not link gamesPanel.css. The comment
  there says so and flags that the copies "have to be retuned together". A third copy would
  be a bad trend.
- [gamesNew.css:339-397](source/public/styles/gamesNew.css#L339-L397) already *consumes*
  `--fv-text` / `--fv-hero` / `--fv-accent` on 12 pages, but they are only *defined* on
  games.php. Benign today (only games.php uses `.resources` / `.update-title`), latent
  otherwise.

### 1.3 "15 legacy stylesheets" is now 12 — and ~1,900 lines are dead

| Stylesheet | Lines | Linked by |
|---|---:|---|
| tactical.css | 1941 | game.php **(only cache-busted file)** |
| confirm.css | 1166 | game, gamelobby, games, creategame |
| gamesPanel.css | 1257 | games |
| gamesNew.css | 912 | 12 pages |
| lobby.css | 902 | 8 pages |
| createGame.css | 752 | creategame |
| ladder.css | 692 | games, creategame |
| replay.css | 221 | game |
| shipTooltip.css | 153 | game |
| base.css | 150 | 13 pages |
| reg.css | 142 | index, reg |
| chat.css | 83 | included 4× into game.php |
| ~~shipwindow.css~~ | 1307 | **dead** — STAGE4-RETIRED on both pages |
| ~~B5CGM.css~~ | 349 | **dead** — zero references |
| ~~games.css~~ | 63 | **dead** — zero references |
| ~~helper.css~~ | 143 | **dead** — helper.php include is inside a comment |

1,862 of 10,233 CSS lines (18%) are unreachable. Not item 6's job to delete (shipwindow.css
is under the user's *"nothing deleted until live-stable"* rider), but it *is* item 6's job not
to waste effort tokenising them.

### 1.4 The palette is already 90% coherent

Across all 16 stylesheets the top values are `#496791` (64×), `#deebff` (60×), `#215a7a`
(50×), `#8bcaf2`, `#587e8d`. The React tree's top values are `#f2f2f2`, `#ffffff`, `#496791`,
`#deebff`, `#587e8d`. **The two stacks are painting from the same tin and neither knows it.**

This is the single most important finding for scoping: item 6 is mostly *not* a recolouring
job. Colour drift is a handful of values. The visible mismatch is **geometry and type** —
30px pill corners vs square, and Consolas numerals in one window and nowhere else.

### 1.5 Two duplications worth collapsing

- **The map tooltip is defined twice, identically.** Legacy
  [`.shipNameContainer` (tactical.css:341-364)](source/public/styles/tactical.css#L341-L364)
  and React [`Tooltip` (common/Tooltip.js:17-31)](source/public/client/UI/reactJs/common/Tooltip.js#L17-L31)
  both say: black fill, `opacity: 0.65`, `border-radius: 7px`, arial 12px, `z-index: 7001`.
  Two stacks, one look, zero shared source. They agree today by luck.
- **The "site-standard scrollbar" is copy-pasted 6 times with 4 different thumb colours**
  (`#5a7ea8`, `#3c5574`, `#3a6a96`, `#2a4a66`).

---

## 2. Risks and constraints (read before touching anything)

### 2.1 ⚠️ The cache-bust gap — this is the blocker

`.htaccess` serves `text/css` with **`ExpiresByType text/css "access plus 1 month"`**
([.htaccess](source/public/.htaccess)). Of the 12 live stylesheets, **exactly one**
(`tactical.css`, [game.php:73](source/public/game.php#L73)) is routed through
`AssetLoader::getAssetUrl()` and carries `?v=mtime`. Its comment says why:

> *"?v=mtime: .htaccess serves CSS with 'access plus 1 month', so without a cache-buster a
> stylesheet edit can take up to a month to reach returning players."*

Every other link is a bare `<link href="styles/x.css">`. **Editing any of them ships a
split skin to returning players for up to a month** — half the screen retuned, half not.
That is strictly worse than the current honest mismatch.

**Nothing else in this plan may deploy until this is fixed.** It is a mechanical one-line
change per link tag with zero visual delta.

### 2.2 The universal Arial rule

[gamesNew.css:5-7](source/public/styles/gamesNew.css#L5-L7) is `* { font-family: Arial; }`.
A universal selector matches every element **directly**, and a direct match beats an inherited
face regardless of specificity — so nested elements snap back to Arial even when their parent
sets a display face. ladder.css already documents this trap and neutralises it locally. Any
type-role work on the 12 gamesNew pages hits it.

### 2.3 Inline styles beat stylesheets

[confirm.js](source/public/client/UI/confirm.js) carries 88 inline `style=` attributes,
including **16 inline `color:`** and 17 `font-style:`. A confirm-window restyle that only
edits confirm.css will leave those off-palette. They must be converted to classes in the same
change or explicitly left alone.

### 2.4 Sprite chrome cannot be tokenised

`#shipMovementUI`'s 25 movement icons, `.ok` / `.cancel` commit buttons, and the entire EW
action-button set in [shipTooltip.css](source/public/styles/shipTooltip.css) are PNG
`background-image`s. CSS custom properties cannot restyle them. **This bounds the achievable
result** — a fully unified screen would need art work, which is out of item 6's scope. Say so
up front rather than discovering it at stage 4.

### 2.5 What is *not* risky

- **`!important` density is low** — 29 in tactical.css, 2 in confirm.css, 1 in base.css.
  Overrides will behave predictably.
- **No JS reads colours.** The single `getComputedStyle` call
  ([ShipWindow.js:629](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L629)) reads
  box metrics for scale-to-fit, not paint. Token changes cannot break behaviour.
- **Token values are already cross-checked** against `theme.js` with source comments.
- **The replay harness does not cover CSS at all** — it is neither protection nor an
  obstacle here. Verification is visual, per §6.

---

## 3. Strategy

The roadmap's prescription — *"apply shared CSS custom properties to the 15 legacy
stylesheets"* — is the wrong first move. Tokenising 10,000 lines produces a huge diff, a
month-long split-skin exposure, and **zero visible change**, which is the entire point of the
item. And 18% of it is dead code.

Instead: **build the plumbing invisibly, then spend the visible change where it is seen.**

Every stage below is independently deployable and independently revertible. Stages 0–2 are
provably zero-delta (values are copied, not chosen). The first pixel moves at stage 3.

```
Stage 0  cache-bust every stylesheet link        ── no visual change ── PREREQUISITE
Stage 1  one shared tokens.css, kill the copies  ── no visual change
Stage 2  theme.js reads the same values          ── no visual change
Stage 3  unify the tooltips (the free win)       ── first visible change, tiny
Stage 4  roll the chosen skin across the screen  ── the actual work  [DECISION NEEDED]
Stage 5  cleanup + dead-file deletion            ── after live-stable
```

---

## 4. The stages

### Stage 0 — Cache-bust the stylesheets *(prerequisite, zero visual change)*

Route every `<link rel="stylesheet">` in `source/public/*.php` through
`AssetLoader::getAssetUrl()`, matching the existing tactical.css line.

- **Files:** game.php (4 live links), gamelobby.php (4), games.php (6), creategame.php (6),
  index.php (3), reg.php (3), profile.php (2), chpass.php (2), changeUser.php (1),
  faq.php (3), starterGuide.php (3), factions-tiers.php (3), fleetchecker.php (3),
  ammo-options-enhancements.php (3), chat.php (2), combatLog.php (1), declarations.php (1).
- All of these either load `global.php` themselves or are included into a page that does, so
  `AssetLoader` is in scope. Verify per file — do not assume.
- Note: chat.css is linked **4 times** in one game.php render (chat.php ×2 + combatLog +
  declarations). Harmless, but worth collapsing while in there.
- ⚠️ Do **not** touch the two commented-out `shipwindow.css` links — they are STAGE4-RETIRED
  markers, not live links.

**Exit:** every page's HTML shows `styles/x.css?v=<mtime>`; a stylesheet edit is visible on
hard-reload-free refresh. **Deploy this alone and let it settle before stage 1** — its whole
purpose is to be in players' caches *before* the first real edit.

### Stage 1 — One shared `tokens.css` *(zero visual change)*

Create `source/public/styles/tokens.css`: a single `:root` block that is the **union** of the
gamesPanel.css and ladder.css sets, plus the world-1/2/3 chrome values that stages 3–4 will
need. Every value is copied verbatim from an existing file — nothing is chosen.

```css
/* Surfaces */
--fv-panel / --fv-well / --fv-card / --fv-card-hover      (from gamesPanel.css)
--fv-chrome:      #0a3340;   /* tactical.css #phaseheader + styled/Container.js  */
--fv-chrome-scs:  #152029de; /* theme.colors.windowBg                            */
--fv-overlay:     black;     /* tooltips + .confirm                              */

/* Lines */
--fv-line:        #215a7a;   /* landing page      */
--fv-line-scs:    #496791;   /* theme.colors.line + confirm.css  */
--fv-line-chrome: #587e8d;   /* tactical.css + Container.js      */
--fv-line-soft:   #1a3f55;

/* Text */
--fv-text / --fv-text-dim / --fv-accent                    (from gamesPanel.css)

/* Type */
--fv-mono / --fv-display / --fv-hero                       (from gamesPanel.css)

/* Geometry — new names, existing values; stage 4 retunes these and nothing else */
--fv-radius-chrome:  8px;    /* #phaseheader        */
--fv-radius-pill:    30px;   /* Container.js        */
--fv-radius-modal:   5px;    /* .confirm            */
--fv-radius-tooltip: 7px;    /* both tooltips       */

/* Scrollbar — collapses 6 copies / 4 thumb colours to one pair */
--fv-scroll-track / --fv-scroll-thumb / --fv-scroll-thumb-hover

/* Status — already agreed across both stacks */
--fv-ok / --fv-warn / --fv-crit / --fv-mine / --fv-ladder / --fv-ended
```

Then:

1. Link `tokens.css` **first** on every page (after stage 0, so it is versioned from birth).
2. Delete the `:root` blocks from gamesPanel.css and ladder.css. Their `var()` usages
   (96 and 53 respectively) keep working unchanged.
3. Fix the latent gamesNew.css gap from §1.2 for free — `--fv-hero` / `--fv-text` /
   `--fv-accent` now resolve on all 12 pages.

**Why geometry tokens matter more than colour tokens here:** per §1.4 the palette barely
disagrees. The corner radius is *the* visible tell. Naming the four radii now means stage 4 is
a four-line retune instead of a search-and-replace across 10,000 lines.

**Exit:** every `--fv-*` referenced anywhere resolves; brace-balance check per file; the four
token-consuming pages diffed against pre-change screenshots and identical.

### Stage 2 — `theme.js` and the old React overlays read the same values *(zero visual change)*

1. Extend `styled/theme.js` with the world-1/3 chrome values under clear names
   (`chromeBg`, `chromeLine`, `radiusPill`, `radiusTooltip`, `overlayBg`), sourced from the
   same literals stage 1 tokenised.
2. Rewrite [Container.js](source/public/client/UI/reactJs/styled/Container.js) and
   [common/Tooltip.js](source/public/client/UI/reactJs/common/Tooltip.js) to read them
   instead of hard-coded hex. **Five components** ride on these (`EwButtons`, `FullScreen`,
   `PlayerSettings`, `SystemInfo`, `ShipThrust`) — retuning them all becomes one edit.
3. Leave `system/*.js` alone for now. Those 39-per-file hex values are mostly **semantic
   state signals** (called-shot magenta, overload orange, docked cyan), not chrome. They are
   a stage-5 tidy at most, and several already coincide with theme tokens
   (`#496791` = `line`, `#427231` = `healthOk`, `#ed6738` = `healthCrit`,
   `#e1b000` = `warning`).

**Two sources of truth remain by design** — `tokens.css` for CSS, `theme.js` for
styled-components. They are kept in sync by comment cross-reference, exactly as
gamesPanel.css already does. A build step that generates one from the other is possible but
is not worth a new pipeline dependency for ~30 values; revisit only if they actually drift.

**Exit:** `yarn build`; the five overlays pixel-identical.

### Stage 3 — Unify the tooltips *(first visible change — and it is nearly free)*

Per §1.5 the jQuery map tooltip and the React tooltip are already the same design, declared
twice. Point both at `--fv-overlay` / `--fv-radius-tooltip` / `--fv-text` and they become one
thing that cannot drift.

This is deliberately chosen as the first visible stage because:
- the visual delta is ~zero, so it cannot look wrong;
- it exercises the whole pipeline end-to-end (CSS token → jQuery surface, JS token → React
  surface) on the most-hovered element in the game;
- if the plumbing is broken, this is where it shows up cheaply.

**Exit:** hover a ship, a system icon, and a weapon; all three tooltips identical; replay mode
checked too.

### Stage 4 — Roll the skin *(the actual work; target fixed by §5 = SCS)*

Surface-by-surface, each its own commit and each revertible alone. Order — cheapest and
most-seen first:

1. **The five old-React overlays** — free after stage 2: one `Container.js` retune
   (`--fv-radius-pill` 30px → 0, line → `#496791`, fill → `#152029de`) converts
   `EwButtons`, `FullScreen`, `PlayerSettings`, `ShipThrust` and `SystemInfo` at once.
   Highest ratio of visible change to edited lines in the project — **do it first and look
   at it** before committing to the rest.
2. **`#phaseheader` + commit buttons** — small, always on screen, sets the tone.
3. **`#logcontainer` + tabs** — largest legacy surface, bottom-left, always visible.
4. **`.confirm` dialogs** — highest risk (4 pages, plus the inline-style problem from §2.3).
   Do last, and treat the inline `color:` conversion as part of it.
5. **`replay.css`** — the palette outlier (a cyan `#7fe7f5` family nothing else uses). Decide
   deliberately whether replay *should* read as a distinct mode; if yes, leave it and record
   that as intent.

**Out of scope, stated explicitly:** the sprite-image chrome of §2.4. `#shipMovementUI`, the
ok/cancel buttons and the EW action buttons will still look 2011 after item 6 completes,
because they are PNGs. If that is unacceptable the art is a separate project.

### Stage 5 — Cleanup *(after live-stable)*

- Delete `B5CGM.css` (349), `games.css` (63), `helper.css` (143) — zero references, safe now.
- Delete `shipwindow.css` (1307) **only** as part of the item-5 STAGE4-RETIRED deletion sweep,
  under the user's live-stable rider. Not item 6's call to make early.
- Optional: fold `system/*.js` semantic colours into `theme.js` if any turn out to be chrome
  rather than signal.

---

## 5. Target skin — DECIDED 2026-07-29: **converge on the SCS look**

Stages 0–3 are direction-independent. Stage 4 needed a target skin; the user chose
**(A) everything moves toward the item-5 SCS language**. The alternatives considered and
rejected were (B) softening the SCS window back toward the rounded 2011 chrome, and (C)
splitting by role (rounded chrome / square datasheets).

Rationale for the choice: item 5 already spent the design effort and the look was accepted
through five stages, and the landing page has *already* been pulled this way — gamesPanel.css
states its direction as *"the datasheet grammar of the in-game React SCS windows … so the
landing page and the game screen read as one product"*. Anything else would strand two
finished projects.

**What this pins down for stage 4:**

| Property | Target | Token |
|---|---|---|
| Border | `#496791` | `--fv-line-scs` / `theme.colors.line` |
| Corner radius | **0** (square) | the four `--fv-radius-*` retune to 0 |
| Chrome fill | `#152029de` | `--fv-chrome-scs` / `theme.colors.windowBg` |
| Body text | Arial `#deebff` / `#C6E2FF` | `--fv-text` / `--fv-text-accent` |
| Numerals | **Consolas** | `--fv-mono` / `theme.fonts.mono` |
| Micro-labels | uppercase, letter-spaced | as ShipSection headers |
| Header bars | `rgba(73,103,145,0.25)` fill | the established SCS header-bar blue |

The `--fv-radius-pill: 30px` → `0` retune is the single highest-impact line in the whole
project: it converts all five old-React overlays at once (§4 stage 2 made them share one
declaration). Do that one first and look at it before going further — it is also the easiest
thing in the plan to revert if the square screen reads as too severe.

**Deliberate exception:** `--fv-radius-tooltip` (7px) may stay rounded. Tooltips are transient
and float over the map rather than docking to chrome, and both stacks already agree on 7px
(§1.5). Decide by eye at stage 3; record whichever way it goes as intent.

---

## 6. Verification

No automated coverage exists for CSS — the replay harness does not touch it (§2.5). So:

- **Per stage, before/after screenshots** of every affected surface at 1920×1080 and at a
  phone width (the item-5 scale-to-fit work makes small screens a real case).
- **Stages 0–2 must be pixel-identical.** If anything moves, the stage is wrong — that is the
  whole reason they are separated from stage 3.
- **Brace-balance + token-resolution check** per stylesheet (the GAMES_PAGE_REDESIGN_PLAN
  precedent: *"stylesheet brace-balanced with all 20 referenced tokens defined"*).
- **Both pages, both stacks:** game.php and gamelobby.php share confirm.css and the React
  bundle; a change to either lands on both.
- **Replay mode** — a teamless viewer path that has already produced one regression
  (item 5 round 12).
- `yarn build` for any `theme.js` / `styled/` change; **never commit the bundles**.

---

## 7. Estimate

| Stage | Size | Visual risk |
|---|---|---|
| 0 — cache-bust | ~45 link tags, mechanical | none |
| 1 — tokens.css | 1 new file, 2 `:root` deletions | none |
| 2 — theme.js + Container/Tooltip | 3 files | none |
| 3 — tooltips | 2 declarations | trivial |
| 4 — roll the skin | 5 surfaces, one commit each | **this is the whole risk** |
| 5 — cleanup | 3 file deletions (+1 gated) | none |

Stages 0–3 are a short, safe sitting. Stage 4 is open-ended by nature and should be run the
way item 5 was — build a surface, look at it, iterate.

---

## 8. As built (2026-07-30) — stages 0–3

### Stage 0 — cache-bust ✅

**49 links across 17 files** now routed through `AssetLoader::getAssetUrl()`. All 12 live
stylesheets verified to resolve to `styles/x.css?v=<mtime>`; all 17 files `php -l` clean.

Untouched on purpose, all dead: the two STAGE4-RETIRED `shipwindow.css` markers
(game.php, gamelobby.php), game.php's commented `helper.css`, chat.php's commented second
block, and helper.php (its only include site is inside a comment).

**Not collapsed:** the 4× chat.css link per game.php render (§4 called it "worth collapsing
while in there"). Collapsing means hoisting the link out of the three partials into four
parent pages' `<head>`s — four pages to touch, a new way for a partial to be included
without its stylesheet, and the browser already dedupes identical URLs to one fetch. Left
as-is deliberately; all four now carry the same `?v=`.

### Stage 1 — tokens.css ✅

New [styles/tokens.css](source/public/styles/tokens.css): **40 tokens**, one `:root`, linked
first on all 14 pages. The `:root` blocks in gamesPanel.css and ladder.css are gone,
replaced by comments explaining where they went. Every value copied verbatim.

Verified: 26 distinct `--fv-*` consumed across all stylesheets, **zero undefined
references**; all 17 stylesheets brace-balanced; `tokens` is the first stylesheet in the
link order on every page.

§1.2's latent gap is closed — gamesNew.css's `--fv-text` / `--fv-hero` / `--fv-accent` now
resolve on all 12 of its pages, not just games.php.

Two things worth knowing:

- **The scrollbar tokens are defined but not yet wired.** §1's sketch had Stage 1
  "collapsing 6 copies / 4 thumb colours to one pair", but collapsing four colours into one
  necessarily repaints three of them — that is a visible change, and it would have broken
  the stage's own "provably zero-delta" contract (§6: *"Stages 0–2 must be pixel-identical.
  If anything moves, the stage is wrong"*). `--fv-scroll-*` therefore holds the
  games.php/ladder values, and rewiring the six sites is Stage 4 work.
- **`--fv-text-dim` is NOT `theme.colors.textDim`.** gamesPanel.css and ladder.css both
  carried the comment `/* theme.colors.textDim */` against `#8ca5c0`, but theme.js says
  `#7f9bb8`. Pre-existing drift, not introduced here. Kept as two deliberate values (the
  landing page runs a lighter dim over its nebula backdrop) and the misleading comment is
  corrected in tokens.css.

### Stage 2 — theme.js ✅ *(needs `yarn build`)*

theme.js gains `colors.chromeBg` / `chromeLine` / `chromeText` / `overlayBg` and a new
`radii: { pill, tooltip }`. [Container.js](source/public/client/UI/reactJs/styled/Container.js)
and [common/Tooltip.js](source/public/client/UI/reactJs/common/Tooltip.js) read them instead
of hard-coded hex.

Zero-delta proven mechanically, not by eye: every substituted value asserted equal to the
literal it replaced. The two keyword→hex swaps are exact synonyms — `white` → `#ffffff`
(`theme.colors.text`), `black` → `"black"` (`theme.colors.overlayBg`). All three files parse
under esbuild.

⚠️ **The bundle is not rebuilt.** `yarn build` is needed before any of Stage 2 or 3 reaches a
browser, and the bundles are never committed.

### Stage 3 — tooltips ✅

`.shipNameContainer` (tactical.css) now reads `--fv-overlay` / `--fv-radius-tooltip` /
`--fv-text-bright`, and each side carries a comment pointing at the other.

**Deviation from §4's sketch:** it said point both at `--fv-text`. `--fv-text` is `#deebff`;
both tooltips are currently pure white. Following that literally would have shifted the
text colour of the most-hovered element in the game — a real visible change, against the
stage's "the visual delta is ~zero, so it cannot look wrong" rationale. Added
`--fv-text-bright: #ffffff` instead (= `theme.colors.text`, which is what the React tooltip
was already painting). Moving both to `#deebff` is now a one-line Stage 4 decision if wanted.

Per §5's deliberate exception, the 7px radius is **kept rounded** and that intent is recorded
in the comment on both sides — tooltips float over the map rather than docking to chrome.
Reversible by retuning `--fv-radius-tooltip` and `theme.radii.tooltip` together.

### Stage 4 surface 1 — the five old-React overlays ⏳ *(built 2026-07-30, awaiting the look)*

Per §4 and §5 this is the "do it first and look at it before committing to the rest" step.

| | Before | After | Source |
|---|---|---|---|
| Border | `#587e8d` | `#496791` | `theme.colors.line` |
| Fill | `#0a3340` | `#152029de` | `theme.colors.windowBg` |
| Corner | 30px pill | **0** | `theme.radii.pill` |
| Text | `#deebff` | unchanged | `theme.colors.chromeText` |
| Shadow | `5px 5px 10px black` | unchanged | already matches the SCS window |

The after-values are not chosen, they are *read off* `ShipWindowContainer`
([ShipWindow.js:63-69](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L63-L69)) —
the overlays now paint from the same two declarations the ship windows do.

**Scope note — EwButtons was not optional.** `MainButton` is
`styled(ContainerRoundedRightSide)`, and its six subclasses re-declare `#0a3340`/`#587e8d`
for their *untoggled* branch. Retuning only `Container.js` would have left the buttons
fighting the shell they sit in, so the untoggled branches move with it. The **toggled**
values (`limegreen`, `#1b533d`) are signals and are deliberately untouched.

**Deliberately NOT in this commit:**

- `PlayerSettingsForm.js` — the settings *dialog* (its own `#587e8d` border, `#0a3340`
  header, 8px radius). It is a modal, so it belongs with the `.confirm` pass §4 schedules
  **last**, not with the map overlays.
- `system/*.js` (PowerCapacitor, ShieldGeneratorList, SystemActivation) — §4 stage 2
  point 3 defers these; they are ship-window menus, not map overlays.
- Everything jQuery. `#phaseheader` and `#logcontainer` still hold literal hex and still
  paint 2011 teal — which is why `theme.colors.chrome*` and `--fv-chrome` / `--fv-line-chrome`
  are still live values, not dead ones.

**Expect a deliberately mixed screen at this point:** square SCS overlays around the map
edges, rounded teal `#phaseheader` and log at top and bottom. That is the plan working as
intended, not a bug — surfaces 2–5 close the gap.

**Revert = 3 values:** point Container's two declarations back at `chromeLine`/`chromeBg`
and put `theme.radii.pill` back to `"30px"`.

**Naming debt accepted:** `ContainerRounded*` no longer round anything. Renaming touches
EwButtons/FullScreen/PlayerSettings imports, which would mix a rename into a look-and-see
commit. Logged as a Stage 5 tidy.

#### Review round 1 (user, 2026-07-30)

EW button strip: **accepted** — "square as predicted". Two changes came out of the look.

**1. The Settings button was oversized on phones — fixed.** Not a sizing bug introduced by
the reskin, a sizing assumption invalidated by it: the mobile box (30×36) had been tuned
against the *rounded* shape, where a 30px bottom-left radius carves a full quarter-disc out
of a 30px-wide box — **~18% of its area** — so it read as a small tab tucked into the
corner. Square, the same box is 18% more ink. Pulled the area back ~17% (28×32, glyph
22px) rather than putting the corner back, so the button keeps the SCS language.

Worth remembering as a general rule for surfaces 2–5: **anywhere a large radius was doing
size work, squaring it is not visually neutral.** Check `#phaseheader` (8px, minor) and
`.confirm` (5px, minor) — both are small radii on large boxes, so neither should show this.

**2. `#iniGui` + `#backDiv` stay rounded — STANDING EXCEPTION.** User decision. They are a
drawer and its pull-tab anchored to the left screen edge, rounded on the **right-hand
corners only** (`0 10px 10px 0`), and that curve is what makes them read as sliding out
rather than as docked chrome. Same reasoning as the tooltip exception in §5.

Implemented as its own token, `--fv-radius-drawer: 10px`, precisely so a future retune of
`--fv-radius-chrome` cannot sweep them up by accident — the two surfaces now consume a
token that nothing else touches, and both declarations carry the reason inline. Verified
the responsive overrides at both breakpoints don't re-declare radius, so the exception
holds at every width. `#iniSlider` itself is the pullIn/pullOut **PNG** inside `#backDiv`,
so it is §2.4 sprite chrome and CSS could not have squared it anyway.

Still open from this round: the ini panel's inner `#iniTable` carries its own `15px`. Left
alone — it sits inside the rounded drawer, so it is consistent either way. Decide when
surface 2/3 converts the panel's colours.

### Stage 4 surfaces 2 + 3 — the game.php chrome ⏳ *(built 2026-07-30, awaiting the look)*

Both done the plan's way: **point the literals at the tokens (zero-delta), then retune the
token (the visible half).** `--fv-radius-chrome` 8px → **0** is now the single knob for the
whole legacy-chrome radius, so this pair reverts on one line.

**Surface 2 — `#phaseheader`.** Border → `--fv-line-scs`, fill → `--fv-chrome-scs`,
radius → `--fv-radius-chrome`, shadow → `--fv-overlay`.

⚠️ **The opacity moved 0.85 → 0.95, and that is a correction, not a restyle.** The old fill
was *opaque* `#0a3340`, so `opacity: 0.85` meant 0.85. `--fv-chrome-scs` is `#152029de` —
it carries its own ~0.87 alpha, and element opacity **multiplies** with it. Keeping 0.85
would have landed at ~0.74 effective: noticeably more map showing through a strip that has
text on it. 0.95 × 0.87 ≈ 0.83, which is where it was, and it is the exact pairing
`ShipWindowContainer` already uses. **The two must be retuned together or not at all.**

This is the same lesson as the Settings button in review round 1, in a different costume:
*a value that looks independent can be load-bearing for something the reskin changes.*

**Surface 3 — `#logcontainer` + tabs.** Half-converged already: its fill was `#04161C`,
which **is** `theme.colors.panelBg` / `--fv-well`. Only the borders actually moved. Opacity
stays 0.85 here — `--fv-well` has no alpha to multiply with, so the surface-2 trap doesn't
apply.

Also in this surface:
- **`.logUiEntry` tabs squared** — the most arguable call in the pair. Rounded-top is a
  strong "this is a tab" affordance. They follow `--fv-radius-chrome` rather than getting
  carved out like the ini drawer, because there the curve is a *cue about behaviour*
  (it slides out) whereas here it is decoration — the tabs read as tabs from position
  alone. Reversible by giving them their own token.
- **`#expandBotPanel`** — byte-for-byte the same tab pattern in the same bar; converted
  with them, since leaving it rounded-teal beside squared tabs would be a plain defect.
- **The scrollbars folded onto `--fv-scroll-*`** — the deferred half of Stage 1, now that
  a visible change is allowed. `#iniGui` keeps its own alpha'd thumb (`#3c5574b6`): it is
  the drawer, already an exception, and its translucent thumb lets the map read through.
- **`#combatLogButtons` / `#declarationsButtons` — border only.** The `#215a7a` fill and
  4px radius stay: that fill is the button's identity rather than panel chrome, and a
  small radius on a small control is not what §5's squaring decision was about.

**Deliberately not touched:** `#logcontainer`'s `color: #7ba2ea`. The two commented-out
alternatives directly beneath it (`#e0e7ef`, `#deebff`) are previous attempts that were
reverted — that is a considered choice, and it styles log *content*, not chrome.

### Stage 4 surface 4 + the three decisions ⏳ *(built 2026-07-30)*

**The three open decisions, as answered by the user:**

1. **Ini drawer colours — converted, rounding kept.** The drawer now stays distinct by
   *shape* rather than by colour, which is what the round-1 exception was actually about.
   ⚠️ The alpha trap struck twice more here: `#iniGui` (opacity 0.9) and `#backDiv` (0.8)
   both had opaque fills, so both needed compensating (→ 0.95 and 0.92).
2. **The custom tooltips joined the black family.** `.custom-intercept-tooltip` and
   `.custom-hit-chance-tooltip` were a *third* tooltip family Stage 3 never saw, painting
   teal chrome, which made hover-transient information look like docked UI. They now share
   `--fv-overlay` + `--fv-radius-tooltip` with the other two. They keep their own border and
   left-aligned multi-row layout — unlike the plain map tooltip they carry tabulated combat
   numbers, and the border separates the header row from the rows beneath.
3. **`replay.css` stays cyan — but SPLIT BY STATE** (corrected in review round 2, below).
   The decision is written into the top of the file itself, not just here, because that is
   where someone "finishing the tokenisation" will be standing when they are tempted to undo
   it. Reasoning: the colour is doing a job — telling a viewer at a glance that they are
   watching rather than playing.

**Surface 4 — `.confirm` + `PlayerSettingsForm`.** Lands on **four pages** at once.

- Border was *already* `#496791`; only fill, radius and scrollbars moved.
- **Fill went to `--fv-well` (opaque), not the translucent `--fv-chrome-scs`.** A commit
  dialog sits over a busy map and has to stay readable — and `.confirm.warning` had already
  made exactly this choice on its own, so this follows the house precedent rather than
  inventing a third answer.
- The labelled OK/Cancel buttons *do* take the translucent fill: composited over
  `--fv-well` they land a step lighter than the dialog, which is the button affordance the
  old teal was providing.
- `PlayerSettingsForm` converted as a modal (not with the overlays it visually neighbours),
  taking the same opaque fill and `theme.radii.modal`.

**§2.3's inline-style problem was overstated — and is explicitly left alone.** The measured
figure is **19** inline colour sites across **5** values, not "16 inline `color:`" among 88.
They are overwhelmingly signals: amber `rgb(224,185,57)`, red `#ff7070`, accent blue
`rgb(106,195,255)`, plus `#DEFBFF` / `#bdbdbd` for text. Every one was chosen to read on
`black` and still reads on `--fv-well`, which is barely different in luminance — so the
reskin does not strand them, and §2.3's "or explicitly left alone" branch is taken, on
purpose, with the reasoning recorded in confirm.css.

**`system/*.js` borders converted — a deliberate deviation.** §4 stage 2 point 3 defers
these files to Stage 5 *on the grounds that their hex is "mostly semantic state signals"*.
That is true of their fills but not of nine hairline borders, which are plain chrome and
were the last legacy teal in the React tree. Converted borders only; `SystemActivation`'s
`$isWeapon` red is untouched, because that one **is** a signal — it tells you the menu you
opened belongs to a weapon.

**Result: zero legacy chrome hex remains in any live React source or any live stylesheet
rule on the game screen.**

#### Review round 2 (user, 2026-07-30) — the replay decision was too coarse

**Reported:** the replay UI still showed its cyan while *inactive* on the live game screen,
clashing with the converged chrome around it.

**Correct, and the round-1 decision was applied too broadly.** "Keep replay distinct" is an
argument about replay **mode**, but `replay.css` styles two states, and the markup
(client/UI/replayUI.js) makes the difference stark:

- `.replay-inactive` is a **single "Replay" button**. It lives in the LIVE game screen as a
  sibling of `#phaseheader` in the top bar. Cyan there signals nothing — you are not in
  replay — so next to the converged header it just read as something that had been missed.
- `.replay-active` is the floating transport panel, the speed cluster and the glyphs. That
  is where a viewer actually looks, and that is where the mode signal belongs.

So the file is now converged in one state and deliberately not in the other:

| State | Skin |
|---|---|
| `#replayUI` base — the pill visible while NOT replaying | **SCS**, matched to `#phaseheader` |
| `#replayUI.active` + everything under `.replay-active` | **cyan**, the mode signal |

**The split costs nothing structurally**, which is the tell that it was the right cut:
`#replayUI.active` *already* threw the base chrome away (`transparent` / no border / no
shadow), so the base rule was only ever visible in the inactive state to begin with. The
generic `#replayUI button` rule keeps its cyan for the active-state buttons and is overridden
for `.inactive` by a more specific selector, so source order is not load-bearing.

⚠️ The alpha trap applied here too — `opacity` 0.92 → 0.95, matched to `#phaseheader`, which
this bar now sits beside as an exact twin.

**General lesson, worth applying to any future "leave this one alone" call:** check whether
the surface has more than one state before exempting the whole stylesheet. An exemption
argued from one state does not automatically cover the others.

### Open decisions after this round

#### Review round 3 (user, 2026-07-30) — landing pages brought in scope

All three landing-page sites were approved for conversion, plus a height fix.

**`base.css .panel` converted** — the generic panel behind login, registration, profile,
faq and the other 13 `base.css` pages. This is the change that makes the *site*, not just
the game screen, read as one product. Its border was already `#496791`; only fill
(→ `--fv-panel`, the token the games.php redesign defined for exactly this and never wired)
and corners moved. `.subpanel` followed it square — a rounded box inside a squared one
reads as an oversight.

⚠️ **Its opacity went 0.9 → 1, which is moving the translucency rather than removing it.**
The old fill was opaque, so that 0.9 was the only thing letting the nebula backdrop through
— and it faded the *form text* along with the box. `--fv-panel` carries its own 0.92 alpha,
so the box still shows the backdrop at essentially the same strength while the text on top
goes fully crisp. Setting both would multiply them, per the `#phaseheader` trap.

This supersedes the earlier "converting this would undo a deliberate decision" caution:
gamesPanel.css's header recorded that the games.php redesign kept the page chrome
unchanged, but that was a *scope* boundary for that project, not a design finding. With the
game screen converged, the landing page was the last thing left looking 2011.

**Buttons converted.** `gamesNew.css` `.btn-fleet-test` / `.btn-view-ladder`, and
`gamesPanel.css` `.fv-btn--fleet` — **plus `.fv-btn--recent`, which was not asked for**:
it sits directly beside `--fleet` and their old borders were two shades of the same
grey-teal, so tokenising only one would have left a blue border next to a grey one, a
mismatch that did not exist before. `gamesNew.css`'s `.btn-recent-games` is deliberately
excluded — its `#69848f80` hairline is half-transparent and pairs with a translucent fill
to make it the quietest of the three; a flat token would harden it.

**Replay bar height matched to `#phaseheader`.** They sit side by side, so a 1px difference
reads as a wobble. The arithmetic has to be done by hand because the two are built
differently — `#phaseheader` is `height 25 + padding-top 8 + 2px borders = 35`, the replay
bar was `min-height 25 + padding-top 7 + 2 = 34`. Padding to 8. The ≤765px block already
matched (26 = 26) and was left alone.

**`--fv-chrome` / `--fv-line-chrome` are now HISTORICAL.** Nothing paints with the 2011
chrome any more. Both they and their `theme.js` twins are kept rather than deleted, and
their comments now say so — they are the documented revert path, so putting the old look
back does not mean re-deriving values from git history.

#### Review round 4 (user, 2026-07-30) — replay unified, HUD buttons regularised

**`replay.css` fully converged — the round-2 split is withdrawn.** The user reversed the
keep-cyan decision: being in replay is obvious enough from the transport controls and the
"Resume game" button without a separate palette announcing it. This file has now held three
positions (exempt → split by state → unified), and the header comment records all three so
the next person does not re-litigate it from scratch.

⚠️ **What mattered in the conversion is that the cyan was not flat — it encoded a
three-step hierarchy**, and that is the part worth preserving in any palette:

| Step | Was | Now | Job |
|---|---|---|---|
| resting transport glyphs | `#2c8090` | `--fv-text-dim` | present but quiet |
| speed cluster + hovers | `#49c4d4` | `--fv-accent` | reads as a *setting*, not a transport button |
| the running direction | `#d6f7fd` | `--fv-text-bright` | "which way is it playing", the thing you look for |

Collapsing those to one colour would have lost real information. They are still three
distinct steps, just expressed in the SCS palette.

**The three HUD button families now share one geometry.** They had drifted to *three* box
sizes and *three* content-scaling rules:

| | Was | Now |
|---|---|---|
| EW strip ×7 | 45×45 / 30×30, `font-size: 32px` | `theme.hud.btn` / `btnSmall` |
| FullScreen | **50**×45 / 30×30, `font-size: 28px` | same tokens |
| PlayerSettings | 45×45 / **26×28**, `font-size: 40px` | same tokens |

Two findings behind that:

- **The EW strip's `font-size` was a fossil.** Every button in it is a PNG
  background-image, so it has never rendered text — the font-size was doing nothing. It is
  the same "font-size scales the button" pattern the *other two* files genuinely suffered
  from, left behind here. Deleted rather than tokenised. Those icons scale with the **box**
  (`background-size: cover`), which is the behaviour the other two were moved to.
- **PlayerSettings' odd 26×28 phone box was a symptom, not the disease.** It got shrunk in
  round 1 because the squared button looked oversized — but the real culprit was the
  *glyph* (22px ⚙ in a 26px box is wall-to-wall), not the box. The fix now sits where the
  problem is: box matches its neighbours again, `theme.hud.glyphSmall` sizes the gear.

`theme.hud` also carries **two** content sizes on purpose — `icon` (58% of the box) for
inline SVG and `glyph` (71%) for text. An SVG fills its viewBox almost completely while a
glyph leaves leading inside its em box, so one number would render the gear visibly smaller
than the ⛶ icon. Both are pitched against the EW art, which fills its tile edge to edge.
**These two values are the tuning knobs** — nudge them, not the individual components.

**"FS" replaced with an icon.** Inline SVG four-corner brackets, not another PNG: no new
art file, crisp at any DPI, inherits the button's colour through `currentColor` (so it
would have followed the item-6 reskin for free), and sized off the box rather than a
font-size. It also removes the *reason* that button was 50px wide — two letters need more
room than one glyph. `FullScreen`'s `right` offset is now derived from
`theme.hud.btn + 10px` rather than the hard-coded `60px` / `40px`, which had already
drifted to a 15px/14px gap as its neighbour was resized.

#### ⚠️ Regression from round 4, and the verification gap that let it through

`PlayerSettings.js` was given `theme.hud.*` without the matching
`import theme from "../styled/theme"`. It reached the browser as
**`Uncaught ReferenceError: theme is not defined`**, which killed the whole UI bundle — so
`window.UIManager` was never defined and game.php only partially loaded. One-line fix.

**Why the checks missed it, which matters more than the bug:** every React edit in this
project was verified with `esbuild <files> --outdir=…`, which only **parses** each file in
isolation. It never resolves imports, so a file referencing an identifier it never imported
passes cleanly. Styled-components makes this worse than usual — `${theme.colors.x}`
interpolates at **module scope**, so a missing import is a hard failure at load rather than
a lazy one at render.

The check that actually holds is to **bundle** (`--bundle`, which resolves imports) and then
**evaluate** the bundle in node's `vm` against a shallow DOM stub, so module-scope code
runs. `UI.js` is vite's only entry point and all 43 React files are reachable from it, so
one bundle covers the whole tree — verified, not assumed.

One further trap, worth knowing because it nearly hid the fix: in such a harness, detect
with `e.name === 'ReferenceError'`, **not** `e instanceof ReferenceError`. The error is
constructed inside the vm context and inherits from that realm's constructor, so
`instanceof` is always false. The harness silently passed its own self-test until that was
corrected — self-test any checker against a deliberately broken module before trusting it.

### Stage 5 — cleanup ✅ *(2026-07-30)*

**Deleted:** `B5CGM.css` (349), `games.css` (63), `helper.css` (143) — **555 lines**.
Verified unreferenced first by fixed-string search across PHP/HTML *and* build config, and
verified git-tracked so the deletion is recoverable. The stylesheet set is now 14 files /
8,502 lines.

`helper.css`'s only referents were a commented-out `<link>` in game.php and `helper.php` —
whose own include site in game.php has been sitting inside an HTML comment, so neither the
partial nor its stylesheet has rendered in a long time. **`helper.php` itself was left in
place** (it carries a live `HelpManager` dependency and deleting it is a different
decision), but its `<link>` is now commented with a pointer to git history rather than left
silently aimed at a missing file.

⚠️ **`shipwindow.css` (1,307 lines) NOT deleted.** It remains gated behind the item-5
STAGE4-RETIRED "nothing deleted until live-stable" rider. That is not item 6's call.

### Round 5 (user, 2026-07-30) — the open list closed out

**1. `#iniTable`'s 15px — KEPT.** Looks right as-is; no reason to change it.

**2. `.confirm.multi-value-confirm` purple — KEPT.** Accepted as-is against the site
styling. 📌 Recorded intent for later: the user would like this dialog generalised so it can
serve **other weapons needing multi-input**, not just its current one. The bespoke purple is
fine until then — worth revisiting *with* that refactor rather than before it, since a
general-purpose multi-input dialog may want to look like the other `.confirm` variants.

**3. `gamesNew.css` buttons — the block was DELETED, not unified.** Investigating first
changed the answer, because none of the three rules was painting anything:

| Rule | Reality |
|---|---|
| `.btn-fleet-test` | only appearance in markup is inside a **commented-out** `<span>` in creategame.php |
| `.btn-view-ladder` | **zero** references anywhere |
| `.btn-recent-games` | **live — but as a JS hook, not styling** |

games.php renders `<button class="fv-btn fv-btn--recent btn-recent-games">` and
recentGames.js binds click/focus to that class. The *visual* styling comes from
`.fv-btn` + `.fv-btn--recent` in gamesPanel.css, which loads **after** gamesNew.css and sets
the full `border` shorthand — so the rule here was already completely overridden. Unifying
it would have been polishing something invisible. The class stays in the markup for JS.

**Retiring gamesNew.css itself: recommended AGAINST.** Not a vestigial file — measured, it
is foundational for all 12 pages that link it:

- both Google Fonts `@import`s (Orbitron, Bruno Ace SC) that `--fv-display` / `--fv-hero`
  depend on;
- the universal `* { font-family: Arial }` rule, which §2.2 flags as load-bearing and
  order-sensitive — moving it changes the cascade;
- base `body` and `a` styling;
- 54 class selectors, **24** referenced by the five doc pages and **33** by the app pages/JS
  (overlapping) — so it cannot simply be split doc-side/app-side.

Retirement means rehoming ~900 lines across 12 pages and re-establishing the cascade order,
which is a refactor with real regression risk and no visual payoff. A narrower win exists —
a dead-class sweep — but my scan was a substring heuristic over a subset of sources, so its
"12 unreferenced" figure is **not** trustworthy enough to bulk-delete on. That needs a
proper reference pass first.

**4. `shipwindow.css` DELETED** (1,307 lines) — the item-5 live-stable rider is lifted; the
React ship windows are stable on live. Removed with it: both commented `<link>`s (game.php,
gamelobby.php) and the dead `.shipwindow { position: fixed !important }` override that had
been sitting commented at the top of lobby.css.

✅ **The wider item-5 STAGE4-RETIRED sweep was EXECUTED 2026-07-31** (item 5's job, not
item 6's — recorded in SHIPWINDOW_REDESIGN_PLAN.md "Stage 4 deletion pass"). It covered
what was scoped here: three whole JS files (`UI/shipwindow.js`, `UI/flightwindow.js`,
`UI/systemInfo.js`), both commented HTML template blocks, the commented `<script>` tags,
and every commented `shipWindowManager.*` call site — **−2,904 lines**, and
`grep -rn STAGE4-RETIRED source/` is now empty.

**5. `ContainerRounded*` COLLAPSED.** `ContainerRounded`, `ContainerRoundedRightBottom` and
`ContainerRoundedRightSide` differed only in which corners they rounded; once item 6 squared
them they became three names for one behaviour, and names that actively lied. They are now a
single **`ContainerShadowed`** — `Container` plus the drop shadow, which is the only thing
that ever really distinguished them from the base.

It declares **no `border-radius` at all**, because square is `Container`'s default. That let
`theme.radii.pill` and `--fv-radius-pill` go too: a token pinned at 0 with nothing reading it
is noise. Reverting to pills now means restoring the three components from git history
(removed 2026-07-30) rather than flipping a token — a single shared component cannot express
three different corner sets, and pretending otherwise with a token would be a lie of the same
kind that was just removed.

The dead `/* Old version without audio */` block in EwButtons.js still names the old
component. Its comment now says explicitly that it is **not** revivable by uncommenting: it
also calls `.extend`, the styled-components v3 API removed in v4, so it needed porting
regardless.

### Round 6 (2026-07-30) — four EW buttons were still on the legacy teal

A completion sweep for the "zero legacy chrome hex remains in any live React source" claim
made in *Stage 4 surface 4* found it was **not quite true**: four of the seven EW strip
buttons — `LoSButton`, `HexButton`, `BgButton`, `SoundButton` — still hard-coded
`#587e8d` on their border.

**Why they were missed, and why it was nearly invisible:** the surface-1 pass converted the
buttons that re-declare *both* fill and border (`EBButton`, `FBButton`), because those were
the ones whose `#0a3340` fill visibly fought the new shell. These four re-declare only the
**border** — they take their fill from `MainButton` → `ContainerShadowed` → `Container`, so
they had already picked up `theme.colors.windowBg` for free and looked converted. Only the
1px edge stayed 2011, sitting directly beside three siblings painting `#496791`.

Fixed by pointing all four at `theme.colors.line`. The toggled branches (`limegreen`) are
untouched — they are signals, exactly as the surface-1 note says.

**Worth generalising:** *a partially-converted component reads as converted.* When a
component re-declares only one of the properties its parent supplies, the reskin check has
to be per-property, not per-component — looking at the screen would not have caught this.

`theme.colors.chromeBg` / `chromeLine` are now genuinely the only live references to the
legacy chrome values anywhere, and both are documented as the historical revert path
(round 3). Verified: zero `#587e8d` / `#0a3340` / `border-radius: 30px` outside them and the
`:root` in tokens.css.

⚠️ Needs `yarn build` — React source change, bundle not committed.

### Still open

- **`gamesNew.css` dead-class sweep** — plausible but needs a trustworthy reference scan
  first (see 3 above).
- **`.confirm.multi-value-confirm`** — revisit when it is generalised to other multi-input
  weapons (see 2 above).
- ~~**The item-5 STAGE4-RETIRED sweep**~~ — DONE 2026-07-31 (see 4 above).

### What Stage 4 inherits

Surfaces 2–5, in §4's order. Each is its own commit, each revertible alone. The shape of
the work is the same every time: **point the surface's literal hex at the tokens, then
retune the token** — the first half is zero-delta and the second half is the visible bit.

| # | Surface | Where | Notes |
|---|---|---|---|
| 2 | `#phaseheader` + commit buttons | tactical.css | small, always on screen, sets the tone. `--fv-radius-chrome` 8px → 0 |
| 3 | `#logcontainer` + tabs | tactical.css | largest legacy surface. Fold the 6 scrollbar copies onto `--fv-scroll-*` here |
| 4 | `.confirm` + `PlayerSettingsForm` | confirm.css, confirm.js, PlayerSettingsForm.js | **highest risk**: 4 pages, plus §2.3's 88 inline `style=` / 16 inline `color:`. `--fv-radius-modal` 5px → 0 |
| 5 | `replay.css` | replay.css | the `#7fe7f5` cyan outlier — decide whether replay *should* read as a distinct mode, and record whichever way it goes |

Out of scope throughout, per §2.4: the PNG sprite chrome (`#shipMovementUI`'s 25 movement
icons, the ok/cancel buttons, the EW action-button set). Those stay 2011 until someone
redraws them.
