# Document Viewer ("DATA ARCHIVE") Plan

The Starter Guide, FAQ, Factions & Tiers, Ammo, Options & Enhancements and Fleet Checker
documents open as **one window over the current screen** instead of as new web pages.

The ask (2026-09-29):

1. Bring the documents up as an interactive window over the current screen, not as a new page.
2. Skin it as a large sci-fi information data panel in the site's colours, starting from `.lb-panel`.
3. Put the existing bookmarks in a list on the left. Picking one changes the main pane.
4. Add pictures to illustrate the sections. For now these are placeholders, each with a brief; the
   user collects the images and hands them over to be inserted (§5).
5. Review and redraft the content for brevity, clarity and usability without changing the meaning
   of any sentence (§6).

**Status: built and tested locally on 2026-09-29; uncommitted.** It needs no DB change, no
generator run and no new dependency. Three paths are new and untracked, so they must be `git add`ed
by name: `source/public/docs/`, `source/public/client/UI/docViewer.js` and
`source/public/styles/docViewer.css`.

---

## 1. What it is

One window with five tabs: Starter Guide, FAQ, Factions & Tiers, Ammo & Options and Fleet Checker.

- **Left rail.** A search box, then the document's entries under their group headings (each group
  folds). The open entry lists its `<h3>` sub-headings beneath it, and the list follows the reader's
  scroll position.
- **Main pane.** A breadcrumb (document // group), the entry title, `Entry NN / NN` and **Copy
  link**, then the entry itself, then previous / next.
- **Search** runs across all five documents at once and every word must match. Each hit shows a
  snippet, and the words are highlighted in the entry it opens.
- **Keyboard.** Esc closes the window, but first clears a filled search box or shuts the phone
  drawer. ↑/↓ walk the list and the results. Tab stays inside the window.
- **Phone (≤760px).** Full screen, with the list in a drawer behind a **Contents** button. Opening
  the window pushes one history entry, so the Back button closes it instead of leaving the game.
- Each document reopens at the entry last read in it (per browser tab, in sessionStorage), unless
  the link names an entry.

**How it opens.** A plain left-click on any link to one of the five pages (`faq.php`,
`./factions-tiers.php`, `/faq.php#ladder`) opens the window at that entry instead of leaving the
page. Ctrl-, Shift- and middle-click still open the real page in a new tab. An element with no
`href` names its target with `data-fvdoc="faq"` or `data-fvdoc="faq#interception"`. From
script: `fvDocs.open("faq", "ladder")`.

**The five pages still exist (PAGE MODE).** Each is now the same viewer filling the page, with
its document embedded, so old bookmarks, middle-clicks and links posted in Discord land on the
right entry. The address follows the reader (`factions-tiers.php#shadows`), so a copied URL is a
link to that entry.

## 2. File map

| File | Role |
|---|---|
| [client/UI/docViewer.js](source/public/client/UI/docViewer.js) | The viewer, as plain DOM with no jQuery or React, because it runs on pages built on different stacks. Exposes `window.fvDocs`. |
| [styles/docViewer.css](source/public/styles/docViewer.css) | All of its styling; every rule sits under `.fvd`. |
| [docs/starter-guide.html](source/public/docs/starter-guide.html) · [faq.html](source/public/docs/faq.html) · [factions-tiers.html](source/public/docs/factions-tiers.html) · [ammo-options.html](source/public/docs/ammo-options.html) · [fleet-checker.html](source/public/docs/fleet-checker.html) | **The words**, one file per document. Each opens with a comment explaining its format. |
| [docs/docPage.php](source/public/docs/docPage.php) | The PAGE MODE shell the five pages share: header, viewer mount, the document embedded in a `<template>`, and the disclaimer footer. |
| `starterGuide.php`, `faq.php`, `factions-tiers.php`, `ammo-options-enhancements.php`, `fleetchecker.php` | About 10 lines each: the old login check, `$fvDocKey`, and `include 'docs/docPage.php'`. |

Host pages. Only game.php's links changed:

| Page | How it loads the viewer | Links it catches |
|---|---|---|
| games.php | CSS + deferred script (AssetLoader) | Starter Guide chip; the FAQ, Factions & Tiers and Ammo resource links; the ladder window's `/faq.php#ladder` |
| gamelobby.php | CSS; script in the debug list, so it is bundled | The FAQ, Factions & Tiers and Ammo links; the Fleet Checker rules link |
| game.php | CSS; script in the debug list, so it is bundled | FLEET INFO → USEFUL LINKS buttons, now `data-fvdoc="faq"` / `"ammo"` / `"factions"` in place of `window.open(...)` |
| creategame.php | CSS + deferred script | The ladder window's FAQ link, which no longer navigates away from a half-filled form |
| profile.php | CSS + deferred script | "Setup guide & how it works" → `faq.php#notifications` |

Any other page gets the window by linking the CSS and loading the script; its existing links are
then caught with no other change.

## 3. How it is built, and why

- **Only the open entry is in the page.** Each document is parsed into inert `<template>`s, and an
  entry is cloned in when it is shown. This means no id inside a document can collide with a host
  page's ids (game.php looks hundreds up by id), and nothing in a hidden entry loads, which matters
  for the 71 ship images in Factions & Tiers.
- **`data-anchor`, never `id`,** marks the places a link can land, for the same reason. The
  documents contain no `id=`.
- **Loading.** The window fetches a document the first time it is needed, with `cache: "no-cache"`
  (the browser revalidates), so an edit is live on the next page load with no version string to
  bump. PAGE MODE reads the embedded `<template>` instead, so it makes no second request.
- **Links.** One capture-phase click listener on the document catches them, so a modal that stops
  propagation (the ladder window) or a jQuery handler cannot get between a link and the window.
- **Keyboard isolation (overlay).** Key events are stopped at the window's edge, and a window-level
  capture listener catches keys aimed outside it. Typing in the search box on game.php cannot fire
  map hotkeys, and Esc closes the archive rather than a lobby window underneath.
- **Scroll lock** via `html.fvd-lock`. The z-index is 2147483000, above everything.
- **Fonts.** Orbitron and Bruno Ace SC arrive with gamesNew.css, which game.php does not link;
  that is the logPanel.css trap. The viewer requests them itself the first time it opens on a page
  without gamesNew.css.
- **Skin.** Starts from the `.lb-panel` grammar (gradient, `#2a6b8f` frame, Orbitron head band,
  3px rail) and adds corner brackets, a faint HUD grid and one sweep across the head. Colours come
  from tokens.css. `.fvd *{font-family:inherit}` beats gamesNew.css's `* {font-family: Arial}`, and
  `.fvd ::selection` restores text selection, which game.php turns off.

## 4. Editing the documents

The comment at the top of each `docs/*.html` holds everything a writer needs. In short:

- Each `<section data-key data-title data-group>` is one entry in the list:
  - `data-key` is its link (`faq.php#ladder`);
  - `data-title` is its name;
  - `data-group` is the list heading it sits under (consecutive entries share one).

  Factions & Tiers also uses `data-alias`, which keeps old link names working.
- **Keep `data-key`s stable.** Pages and old Discord posts link to them: `faq.php#ladder` (the
  ladder window), `#notifications` (profile), `#interception`, and every faction key from the Tier
  Ratings board.
- An `<h3>` is a sub-heading, listed under the open entry. Putting `data-anchor="x"` on any element
  makes `page.php#x` land on it. **Never use `id=`.**
- Links to another document look like `href="factions-tiers.php#shadows"`; links within one
  document look like `href="#mines"`. Off-site links open in a new tab automatically.
- Building blocks, all styled:

  | Class | Use |
  |---|---|
  | `p.fvd-lede` | opening paragraph |
  | `ol.fvd-steps` | numbered steps |
  | `dl.fvd-defs` | term / meaning |
  | `div.fvd-note` (`--tip`, `--warn`, optional `span.fvd-note-label`) | callout |
  | `table.fvd-roll` (`caption`, `td.fvd-num`) | dice or data table |
  | `.fvd-cards > article.fvd-card` with a `dl.fvd-spec` | Effect / Cost / Limit / Notes cards |
  | `ul.fvd-flow` | phase chips |
  | `.fvd-tiers` | the tier board |
  | `figure.fvd-fig` | a picture |
  | `figure.fvd-ship` | a faction's ship art |
  | `span.fvd-c-entrance` / `exit` / `jumped` / `ok` / `fail` | coloured words |

- **Adding a faction** (the usual contributor edit):
  1. Copy a faction `<section>` in factions-tiers.html.
  2. Give it a new `data-key` and put it in the right `data-group`.
  3. If it has a tier, add it to the tier lists in the Tier Ratings entry.

  Its picture can be any ship image:
  `<figure class="fvd-ship"><img src="img/ships/gquan.png" alt=""><figcaption>G'Quan Heavy
  Cruiser</figcaption></figure>`.
- The documents are plain HTML with no PHP, so an edit needs no build and no regeneration step.
- ⚠️ **Merge note.** The words moved out of the five PHP pages. A contributor branch that edits
  `factions-tiers.php` (or any of the others) will conflict; its change belongs in the matching
  `docs/*.html` now.

## 5. Images

### 5.1 Putting one in

A placeholder is a `figure.fvd-fig--ph` that names its file in `data-img` and its brief in
`div.fvd-ph`. Drop `fvd-fig--ph`, `data-img` and `data-ratio`, and replace the brief with the
`<img>`. Keep `fvd-fig--side` if the figure has it.

```html
<!-- before -->
<figure class="fvd-fig fvd-fig--ph fvd-fig--side" data-img="img/docs/faq/ruler.jpg" data-ratio="4:3">
  <div class="fvd-ph">Screenshot: the Ruler Tool measuring from one hex to another, ...</div>
  <figcaption>Measuring range and line of sight.</figcaption>
</figure>

<!-- after -->
<figure class="fvd-fig fvd-fig--side">
  <img src="img/docs/faq/ruler.jpg" alt="The Ruler Tool measuring range and line of sight">
  <figcaption>Measuring range and line of sight.</figcaption>
</figure>
```

- **Size.** Full-width pictures display at up to 640px wide and side pictures at up to 320px.
  Supply them at twice that for sharp screens: **1280px wide** for full width, **640px wide** for
  side. The shape in the list below is a guide only; once in, a picture keeps its own proportions.
- **Format.** JPG at about 75% quality, ideally under ~250 KB each. A flat-colour UI screenshot may
  be smaller as PNG; any web format works if the extension is changed in both places.
- **Filenames are case-sensitive on live** (Linux). Use exactly the paths listed, all lower case,
  under `source/public/img/docs/`.
- **To replace an image later, use a new filename.** Images are served with a one-year cache, and
  these `<img>`s do not carry the `?v=` deploy buster, so a returning player would keep seeing a
  picture swapped under the same name.
- **Hiding the gaps.** Setting `SHOW_IMAGE_PLACEHOLDERS` to `false` at the top of docViewer.js hides
  every unfilled placeholder, caption included, for a deploy that goes out before all the pictures
  are in. Search never indexes the briefs.

### 5.2 Shot list: 37 pictures

Shape "side" means a smaller picture floated beside the text. A screenshot of a real game reads
better than an empty test map.

**Starter Guide**, in `img/docs/starter/`:

| # | File | Shape | Entry | What it should show |
|---|---|---|---|---|
| 1 | join-games.jpg | 16:9 | Joining a Game | The Games panel on the main page: Your Games, Join Games (with one or two open games listed) and the Actions column. |
| 2 | create-game.jpg | 16:9 | Creating a Game | Create Game, Step 1 (Game Options): the step tabs across the top, the game name and the background pictures. |
| 3 | fleet-selection.jpg | 16:9 | Selecting Your Fleet | Fleet Selection with a faction chosen: the Purchase Fleet store on the left, Your Fleet on the right with two or three ships bought. |
| 4 | buy-dialog.jpg | 3:4 side | Selecting Your Fleet | The buy window for one ship: the name box, an Enhancements section, and the total cost above the Buy button. |
| 5 | ship-tooltip.jpg | 3:4 side | Ship Tooltip & SCS | A selected ship's tooltip: name, defence ratings, turn cost / delay, initiative and speed. |
| 6 | scs.jpg | 16:9 | Ship Tooltip & SCS | A ship's SCS (an Omega, say) with healthy, damaged and powered-down systems, a weapon still charging, and the section header bars. |
| 7 | deployment.jpg | 16:9 | Deployment | The Deployment Phase: your deployment zone on the map, one ship placed, its green speed and facing arrows showing. |
| 8 | title-bar.jpg | 5:1 | The Game Turn | The title bar at the top of the battle screen, showing the phase name and the green commit tick. |
| 9 | ew-buttons.jpg | 4:3 side | Phase 1: Initial Orders | An enemy ship's tooltip in Initial Orders with its OEW + / − buttons, and your selected ship's EW allocations. |
| 10 | ballistic-launch.jpg | 16:9 | Phase 1: Initial Orders | A missile launch being targeted: launchers selected in the SCS, hit chances over an enemy ship, a red targeting reticle beside an earlier target. |
| 11 | movement-arrows.jpg | 16:9 | Phase 2: Movement Orders | A ship on its turn to move, surrounded by its green movement arrows (forward, slide, turn, pivot, roll), with the thrust window open at the bottom. |
| 12 | fire-orders.jpg | 16:9 | Phase 4: Firing Orders | Weapons selected (blue) in a ship's SCS, and their % chances to hit listed under an enemy ship's tooltip beside its target reticle. |
| 13 | firing-modes.jpg | 1:1 side | Phase 4: Firing Orders | Close-up: a multi-mode weapon (a Battle Laser) in the SCS, with its firing-mode letters at the top right of the icon. |

**FAQ**, in `img/docs/faq/`:

| # | File | Shape | Entry | What it should show |
|---|---|---|---|---|
| 14 | battle.jpg | 16:9 | General Notes | A striking battle shot: two fleets exchanging fire mid-game, weapon effects visible. |
| 15 | ship-window-controls.jpg | 16:9 | Hot Keys & Useful Controls | A ship window (right-click a unit) with its title bar, corner resize grip and the Hit Chart / Ship Art / Ship Stats / Notes buttons. |
| 16 | info-panel.jpg | 16:9 | Info Panel | The Info Panel at the bottom of the battle screen: the tab strip, with COMBAT LOG open on a turn's firing log. |
| 17 | ruler.jpg | 4:3 side | Ruler Tool | The Ruler Tool measuring from one hex to another, showing the distance and whether line of sight is blocked. |
| 18 | discord-dm.jpg | 4:3 side | Discord Turn Notifications | A turn-notification DM from the Fiery Void bot in Discord, naming the game, turn and phase, with its link. |
| 19 | save-fleet-tab.jpg | 16:7 | Battle Damage & Saving Fleets | The SAVE FLEET tab of the Info Panel: the summary line of units to be saved, the "temporary critical effects" tick box and Save Current Fleet. |
| 20 | apply-damage.jpg | 3:4 side | Battle Damage & Saving Fleets | The menu opened by clicking a system in Fleet Selection: its Damage section (remaining boxes, Destroy tick box) and a Critical Effects list. |
| 21 | boarding.jpg | 16:9 | Boarding Actions | Breaching pods attached to an enemy ship's section, with a pod's marine mission options (Capture / Sabotage / Rescue) showing. |
| 22 | called-shot.jpg | 4:3 side | Called Shots | An enemy ship's SCS with one of its systems picked as a called-shot target. |
| 23 | jump-point-marker.jpg | 16:7 | Delayed Deployment Slot | The blue Jump Point hexes marking where a delayed slot's units will arrive next turn. |
| 24 | elint.jpg | 16:9 | ELINT & Electronic Warfare | An ELINT ship allocating support EW (the SOEW / SDEW / BDEW buttons on a friendly ship), with EW lines drawn on the map. |
| 25 | hangar-launch.jpg | 16:9 | Hangar Operations | A carrier's tooltip menu with its Launch button, and the launch window listing the stored flights and shuttles. |
| 26 | incoming-list.jpg | 16:9 | Interception | A ship tooltip's INCOMING list in the Firing phase, one row's hit chance underlined in blue, ready to click to intercept. |
| 27 | jump-point.jpg | 16:9 | Jump Drives | The Jump Engine selected, its yellow reach overlay on the map, and a yellow "Jump Point Forming" hex with its facing arrow. |
| 28 | manage-reinforcements.jpg | 16:9 | Reinforcements | The Manage Reinforcements window in Initial Orders: a jump-capable ship chosen, its target hex set, the Jump Point Manifest with units ticked, speeds and carriers set. |
| 29 | minefield.jpg | 16:9 | Mines & Minesweeping | Deploy Minefield in the Deployment Phase: the chosen area on the map with mines scattered in it. |
| 30 | skin-dance.jpg | 4:3 side | Skin Dancing | A small, agile ship ending its move in the same hex as an Enormous unit (a base), about to skin dance. |
| 31 | terrain.jpg | 16:9 | Terrain | A map with asteroids, a moon, a dust cloud and a meteor swarm, and a ship's path passing close by. |
| 32 | damage-types.jpg | 16:7 | Weapon Damage Types | **A diagram, not a screenshot:** where each damage type lands. Standard hits one system; Raking hits several in one section; Piercing hits the facing section, Primary and the far section; Flash hits the target plus 25% to everything else in the hex. |

**Ammo & Options**, in `img/docs/ammo/`:

| # | File | Shape | Entry | What it should show |
|---|---|---|---|---|
| 33 | missile-buy.jpg | 16:9 | Missiles | The buy window for a missile ship (a Kor-Lyan or Earth Alliance missile cruiser) with its Ammo & Ordnance section listing the missiles for sale. |
| 34 | enhancements.jpg | 16:9 | About Enhancements | The Enhancements section of a ship's buy window with a couple selected (Elite Crew, Improved Sensors) and the total cost updated. |
| 35 | refit-menu.jpg | 16:9 | System Enhancements | The menu opened by clicking a weapon in Fleet Selection: the gold Enhancements section (Gunsights [−] [1] [+]) and the gold star on the refitted system's icon. |

**Fleet Checker**, in `img/docs/fleet/`:

| # | File | Shape | Entry | What it should show |
|---|---|---|---|---|
| 36 | fleet-check-report.jpg | 16:9 | General Notes | The Fleet Correctness Report (Your Fleet → Check) showing a mix of green OK and red FAILURE lines. |
| 37 | store-tags.jpg | 4:3 side | Variant Restrictions | Close-up of a few Store rows showing the rating tags after the ship names: (C), (U), (R 33%). |

Factions & Tiers needs none. Each faction already shows one of its own ships from `img/ships/`
(71 of them), and any of those can be swapped for better art by changing the `src`.

## 6. Content review

### 6.1 Size

| Document | Words before → after | Entries | Sub-headings | Pictures |
|---|---|---|---|---|
| Starter Guide | 4,820 → 3,885 (−19%) | 12 | 24 | 13 placeholders |
| FAQ | 17,546 → 16,272 (−7%) | 24 | 82 | 19 placeholders |
| Factions & Tiers | 31,228 → 28,237 (−10%) | 79 | 255 | 71 ship images |
| Ammo & Options | 4,031 → 3,711 (−8%) | 10 | 13 | 3 placeholders |
| Fleet Checker | 1,301 → 1,185 (−9%) | 5 | 6 | 2 placeholders |

The Starter Guide took the heaviest edit. The FAQ and Factions & Tiers are mostly rules detail, so
they were tightened sentence by sentence rather than cut.

### 6.2 Structure

- **Starter Guide.** Three groups: Getting Started / Know Your Ships / Playing a Turn. Click
  sequences are numbered steps, and each phase is its own entry.
- **FAQ.** Regrouped into General / Interface & Site / Rules & Mechanics / Legal. **Reinforcements
  is split out of Jump Drives** into its own entry (`faq.php#reinforcements`); old `#jump` links
  still land on Jump Drives.
- **Factions & Tiers.** The **Tier Ratings board moved to the top** (Overview), and every faction
  on it is a link. Each faction shows one of its ships. `data-alias` keeps old anchors working
  (`centauriwotcr`, `balosian`, `rogolon`, `craytan`).
- **Ammo & Options.** The missile tables gained a **"Fire control (ftr / med / cap)"** header (the
  arrays are fighter / medium / capital, confirmed by the Multiwarhead's fighter-only `+3/-/-`).
  Enhancements are Effect / Cost / Limit / Notes cards. **Poor Crew now sits beside Elite Crew**,
  and a new "About Enhancements" entry indexes the rest.
- **All documents.** A few cross-links were added where one document answers another's question.
  One sentence is new: the FAQ's General Notes and the guide's first entry point a newcomer at the
  Starter Guide / FAQ.

### 6.3 Facts brought up to date (these change meaning, so check them)

The old text described the site as it was. Where the UI has since changed, the text now describes
the UI as it is:

| Document | Was | Now |
|---|---|---|
| Starter Guide | "Register New Player Account" | "Register new account" |
| Starter Guide | "Game Description", "Your Active Games" | "Scenario Description", "Your Games" |
| Starter Guide | Creating a Game: a 42×32 map, slot coordinates, a Background dropdown, a "Create Game" button, "Base Assault / uncheck Set Map Boundaries", a "Reinforcement slot" | Defaults summarised; background pictures; **Confirm & Create Game**; "a bigger Map Template such as Base Assault, or No Boundaries"; **Delayed Deployment Slot** |
| Starter Guide | Fleet selection: "Save Fleet left of Ready", "bottom right" | Choose Faction, Show details, confirm the purchase, Your Fleet, Load a Fleet |
| Starter Guide | SCS structure readout | Header bar, e.g. `540/600 A8` |
| Starter Guide ⚠️ | "Fighters must be deployed on the map; FV doesn't allow ships to carry fighters" | "on the map, or docked inside a carrier's hangar" (Hangar Operations) |
| Starter Guide | Log wording | The **COMBAT LOG** tab |
| FAQ | Fleet check button | **Check** |
| FAQ | Two Save Fleet buttons | One, in Your Fleet |
| FAQ | Load a Fleet behaviour | Over the points limit is refused; mines in a no-mines game and units past the ISD are left out, and the rest loads |
| FAQ | Delayed slot setting | The **Deploys Turn** field |
| FAQ | Team dropdown shown only for some team counts | Shown at any team count |
| FAQ | USEFUL LINKS open a new tab | They open this window |
| FAQ | Notification link name | **Set-Up Discord Notifications** |
| Ammo & Options | "Extra Ordnance Resource"; "store list on the right" | **Ordnance Reserve**; the Store list |
| Factions & Tiers | Brakiri shields: "Most Abbai units…" | Brakiri (a copy-paste slip) |
| Factions & Tiers | "Yolu Theocracy" | **Yolu Confederation** |
| Factions & Tiers | Typos: Craytan, Sniper Cannon, Thought Shields, capacitor, Polaren, electromagnetic, arc | Fixed; a duplicated Particle Impeder bullet removed |
| Fleet Checker | "four categories" (six are listed) | The count is dropped |

"Geoff" stays in the Unbalanced list, verbatim.

### 6.4 Open questions: ruled on and applied (2026-09-29)

The user ruled on all eight, and each change was checked against the code.

1. **DIST arithmetic** (FAQ). Ruling: 1 point of DIST costs 3 EW, but it is still 1 point of DIST.
   - *Differences* now says "1 point of DIST (which costs 3 EW points) against 3 separate OEW locks
     reduces each by 0.33 points".
   - *ELINT* now says "by 1 for every 3 EW points allocated (1 point of DIST)".

   Code: DIST ÷ 3 (÷ 4 for ConstrainedEW), split between the locks.
2. **Markab Religious Fervor**: use the Fiery Void value. Factions now says **+10 initiative**, as
   Ammo already did (code: `iniativebonus += 10`).
3. **Elite Pilot cost** (Ammo): now **+40% of the craft's price (rounded up)**, the actual game cost
   (`ceil(0.4 × craft price)`). It said −10%.
4. **Fighter Improved Thrust** (Ammo): **Limit 1** added, matching the code.
5. **Refits vs the Walkers' Wide-Beam** (Ammo). The Young/Middleborn paragraph now names the one
   exception, the Wide-Beam Lightning Array refit for Ancient hulls only. It links to
   `factions-tiers.php#widebeam`, a new anchor on that sub-heading. Code: the registry defaults to
   ages 1–2; the two Wide-Beam entries say `ages => array(3)`.
6. **FAQ Terrain**: **Dust fields and dust clouds** and **Meteor Swarms** added, plus a
   meteor-strike table (`#meteorchart`), all from the rules as built:
   - Dust: speed/2 to the entry side's Structure; (speed − 10)/3 per fighter. Standard damage with
     no hit chart; armour applies, shields do not.
   - Meteors: a d20 chart by size, −1 at speed ≤ 4 and +1 at 13+. Each meteor does 1d10 + speed on
     the entry side's hit chart.
   - Neither blocks line of sight.

   Sources: `DustField`, `MeteorSwarm`, `RammingAttack::resolveMeteors` and `DustAndMeteorsRule`.
   Terrain links to Meteor Defence (a new `#meteordefence` anchor in Interception), and Meteor
   Defence links back.
7. **The damage / refit menu**: you click a system to choose damage or system enhancements.
   - Ammo's step now says **click** (it said hover / long-press).
   - The FAQ's damage step now mentions the gold Enhancements section.
   - Both dropped menu **titles** that no longer exist: "Apply Damage & Critical Effects" and "Add
     Enhancements & Damage". The title bar was removed on 2026-08-16; the menu is now just its
     Enhancements / Damage / Critical Effects sections.
   - Picture briefs 20 and 35 were reworded to match.
8. **SCS bar colours**: confirmed correct; no change.

## 7. Decisions taken while building

- **One window with five tabs,** rather than five windows, so search and cross-links can span
  documents.
- **The old URLs are kept** as the full-page viewer, with shareable `#entry` addresses and a Copy
  link button, so nothing already posted breaks.
- **Placeholders show by default,** so the gaps are easy to find; one switch hides them (§5.1).
- **Real ship art in Factions & Tiers** instead of 71 placeholders.
- **Resume, fonts, Back button.** Each document resumes the last-read entry; game.php fetches the
  display fonts only when the window first opens; on phones a history entry lets Back close the
  window.
- **The ladder window's FAQ link** on Create Game opens the window instead of leaving the form.

## 8. Traps

- **`id=` inside a document** collides with host-page ids the moment the window is dropped into
  game.php. Use `data-anchor`.
- **`.fvd` must stay `box-sizing: border-box`.** In PAGE MODE it is `height: 100%` plus padding; as
  content-box it pushed the disclaimer footer over the panel.
- **gamesNew.css's `* {font-family: Arial}`** beats inherited fonts, hence
  `.fvd *{font-family:inherit}`. game.php hides `::selection`, hence `.fvd ::selection`.
- **Headless testing.** A same-document `#hash` navigation through CDP `Page.navigate` stalls; set
  `location.hash` by eval instead. A CDP tap at phone width can miss if the page scrolls between
  measuring and tapping; use `element.click()` by eval.
- **The legacy bundles** in the working tree were rebuilt unminified (`FV_NO_MINIFY=1`, the normal
  watch state) and are never committed. `UI.bundle.js` was already modified before this work began
  and is not part of it.

## 9. Verification (2026-09-29)

Drivers `t1`–`t5.mjs` are in that session's scratchpad. They used CDP against the local Docker
site as player 210 via a planted session.

- **t1**, a static host with games.php's CSS stack: 11/11. Open from a link, list navigation,
  cross-document search, key isolation, highlighting, Esc (clear, then close), `data-fvdoc`,
  sub-headings, history Back closes it, phone drawer.
- **t2**, document validation: no duplicate keys, no broken internal or cross-document links, all
  71 ship images exist, no `id=`.
- **t3**, the real site: 17/17 with no page errors. Covered:
  - games.php: the FAQ link, Esc, the Starter Guide chip, and the ladder's `/faq.php#ladder` (the
    ladder window stays open underneath);
  - `faq.php#interception` in PAGE MODE, and a tab switch rewriting the URL to
    `factions-tiers.php#shadows` with its ship art loaded;
  - game.php (game 4429): the Factions button, no map hotkeys while open, fonts requested, hotkeys
    back after closing;
  - lobby 4382: the Factions and Fleet Checker links;
  - Create Game: the ladder link stays on the page;
  - Profile: the notifications link;
  - phone PAGE MODE: no sideways scroll.
- **t4**: 3/3 (PAGE MODE footer layout, phone overlay fills the screen, phone drawer picks an
  entry). **t5**: screenshots of the enhancement cards, the missile table, the FAQ dice tables and
  the phone cards, all laying out cleanly.
- `php -l` is clean on every edited page, and `node --check docViewer.js` passes. All new files
  are CRLF with no BOM.
- **After the §6.4 rulings:** t2 re-run with no problems (the new anchors and cross-links
  resolve). **t6** screenshots the new Terrain entry on desktop and phone (the meteor table scrolls
  inside its own frame on a phone; the page has no sideways scroll). Its Meteor Defence link lands
  on Interception at `#meteordefence`.

## 10. `EW::getDistruptionEW` fix (user request, 2026-09-29)

Found while checking the DIST wording (§6.4 item 1). It is not part of the viewer, but it went in
with this work.

**The bug** ([EW.php:348](source/server/handlers/EW.php#L348)) was a regression from 03dd6d765
(2024-08-25, "Add Constrained EW for Mindriders"). That commit's `else {` was only closed after
`$amount += $fdew`, and the extra braces were added at the end, so both `return`s sat **inside**
the `foreach`. On the server:

- the **first** ELINT within 30 hexes with LoS decided the result, from either team. Usually that
  meant a friendly ELINT returning 0, and any ELINT later in the ship list was ignored;
- a Mindrider (ConstrainedEW) ELINT's DIST never counted at all;
- with no qualifying ELINT, the function fell off the end and returned `null`.

The client twin, `ew.getDistruptionEW` ([ew.js:936](source/public/client/ew.js#L936)), was
always right. So the hit-chance preview and the server's resolution
([weapon.php:1803](source/server/model/weapons/weapon.php#L1803)) could disagree. The same
function feeds the SOEW reduction at [EW.php:299](source/server/handlers/EW.php#L299).

**The fix:** close the `else` before `$amount += $fdew`, and move the returns below the loop. Net
braces are unchanged and `php -l` is clean.

**Verification:**
- **Replay harness `check`:** 112 passed / 8 failed / 1 skipped. That is the seven known failures
  plus **4361**, which fails identically with EW.php reverted to HEAD (stash-and-compare). The
  local game has advanced to turn 2 since it was recorded; it needs a re-record, which is not
  this change's to do.
- **Coverage gap:** the local DB has **no DIST rows at all**, so the harness cannot exercise this.
- **Stub test instead.** A scratch test drives the function with stub ships (`distTest.php` in
  this session's scratchpad, run in the php container):

  | Case | Expected | HEAD | Fixed |
  |---|---|---|---|
  | A friendly ELINT listed first, enemy 3 DIST, 1 lock | 1 | 0 | 1 |
  | A Mindrider with 4 DIST, 1 lock | 1 | 0 | 1 |
  | Two enemy ELINTs with 3 DIST each, 2 locks | 1 | 0.5 | 1 |
  | The only ELINT is 31 hexes away | 0 | `null` | 0 |
  | One enemy ELINT with 6 DIST, 3 locks | 0.667 | 0.667 | 0.667 |

- ⚠️ The fix is in `master`'s working tree only. The `DouglasChanges` deploy copy needs it too.

## 11. Left for the user

- Test the viewer:
  - desktop and phone, on games.php, the lobby, Create Game and Profile;
  - in a running game, where typing in the search box must not move the map;
  - an old link such as `faq.php#ladder` pasted into the address bar.
- Read the Starter Guide through; it took the heaviest edit. Check the §6.3 changes and the new
  Terrain text.
- Supply the §5.2 pictures, or set `SHOW_IMAGE_PLACEHOLDERS = false` for a deploy that goes out
  before they are in.
- Commit, adding the three untracked paths by name. EW.php is a separate, gameplay-affecting
  change and may deserve its own commit.

## 12. Out of scope: noticed, not touched

- **The client `ew.getDistruptionEW` has no 30-hex range check** (the server has one). An enemy
  ELINT that ends movement more than 30 hexes from the shooter still counts in the hit-chance
  preview but not at resolution. It is a preview-only mismatch, much rarer than the bug fixed in
  §10.
- The Factions & Tiers ship images load the `.png`, not the `.webp` that `mass_optimizer.php`
  makes alongside it. They are lazy and one per entry, so this is minor.
- The five pages' login check sends a `Location` header without `exit`, as the originals did, so
  the page body still renders behind the redirect. It was kept as it was.
