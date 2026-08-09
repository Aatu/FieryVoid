# Pre-Battle Damage & Fleet Damage Persistence — Implementation Plan

Status: **BUILT 2026-08-07, stages 0–6 complete. First browser play-through 2026-08-08 found
seven defects (§12, all fixed); a second pass added six refinements (§13, all done) — including
the temporary/marine critical filter and lobby editing of carried criticals; a third pass added
three more (§14) — terrain is never saved, removed criticals keep their row, and
PARAM-CARRYING criticals are now carried between battles. A fourth pass (§15) added ELEVEN
more, the largest of which retires §11 from "optional follow-up" to BUILT: criticals can now
be ADDED in the lobby from a per-class catalogue, with per-class limits. §15 also adds a
third payload bucket for MINES. A fifth pass (§16) added seven, the largest of which is a
FACTION RULES change rather than a feature fix: **§16.4, the Vree saucer's outer-structure
ring — a breached block no longer destroys the systems shown in it.** §16 also collapses a
flight's per-fighter critical sections into ONE flight-wide section (`REF_FLIGHT`). A sixth
pass (§17) narrowed the crit picker's "All" list from 58 classes to a curated 12
(`PreBattleDamage::$generalCriticals`) and gave every system a place to name its own extras
(`ShipSystem::$preBattleCriticals`) — which is also what finally gives FIGHTERS anything to
offer.**
Created 2026-08-07; revised same day (flight finding, crit-authoring deferred to §11, independent
damage/crit load toggles); revised again during implementation (**D8** — fighters are damaged,
never destroyed; a lost fighter shrinks `flightSize`).

**What has been verified (2026-08-07):**

* `php -l` clean on every edited server file; `source/autoload.php` regenerated (`PreBattleDamage`
  registered).
* `db/prebattleDamage.sql` applied locally; both tables create and FK-cascade off `tac_saved_list`.
* Scratch round trip: validator accepts 42 storable `Critical` subclasses and rejects 37
  (`oneturn` / `forInfo` / self-expiring / deny-listed); `sanitise` clamps, drops unknown system
  ids, drops out-of-range ordinals, rejects a bogus crit type; ordinals resolve by
  `array_values` position (1, 4, 7, 10, 13, 16 keys → ordinals 1–6).
* **All four D3 load combinations** of a fleet carrying both kinds produce the right *effective*
  payload, and `toEntries` on each would write exactly the chosen rows and no others.
* D8 holds end to end: no `tac_saved_damage` row for a fighter carries `destroyed = 1`, and a
  crafted POST setting `k` on an ordinal has it stripped.
* React tree bundles and evaluates (missing-import check, self-tested against a broken module);
  both legacy bundles regenerate and contain the two new files.
* Replay harness: **157 pass / 1 fail**, and that one game (4234) fails identically on a clean
  tree — a stale baseline entry, not a regression. The server changes are inert for existing games.

**Not yet done:** the manual Docker play-through (buy a damaged fleet → Ready → Deployment →
turn 1; save mid-battle → reload in a new lobby). `yarn build` is the user's step.

* **Part 1** — allocate **damage and destruction** to **bought** ships in `gamelobby.php` through
  the React ship window; persist to the game DB on Ready, and to the saved-fleet tables.
  **Criticals are not authored in the lobby in v1** — see §11.
* **Part 2** — a **Save Current Fleet** action in a live game (`game.php`) that captures damage
  **and criticals**, so the end state of one battle becomes the start state of the next.
* Criticals therefore travel one way in v1: *live game → saved fleet → lobby (displayed, carried)
  → game DB*. Nothing in the lobby invents one.
* Damage and criticals are **independently loadable** — a fleet saved from a battle can be reloaded
  pristine, damage-only, crits-only, or fully (D3).
* **§11 is a live spec, not a wish list.** Authoring criticals in the lobby is deferred, and v1 is
  built with its seams in place: the wire format, the validator, the buy-time write path, the client
  state primitive, the preview renderer and the UI section all exist and are exercised in v1. The
  follow-up is one endpoint, one accessor, and flipping one prop.

---

## 0. Decisions

| # | Decision | Source |
|---|---|---|
| D1 | **No gating rule.** Available to any player on their own bought ships in any lobby. Instead `onReadyClicked`'s confirm warns when the fleet contains pre-damaged units. | user |
| D2 | **Point cost unchanged** by damage or criticals. No touch to `calculateFleet`, the fleet checker, `tac_saved_list.points` or affordability checks. | user |
| D3 | **Loading a saved fleet asks — twice, independently.** Two checkboxes, "Include saved battle damage" and "Include saved critical effects", each defaulted on when the fleet actually carries that kind and hidden when it does not. All four combinations are valid: a fleet saved from a bloody battle can be reloaded pristine, damage-only, crits-only, or fully. | user |
| D4 | **No critical authoring in the lobby — for now.** No crit picker, no per-system crit catalogue, no `systemCriticals.php` endpoint. Criticals are only ever *captured* from a live game and *carried* through. **The rest of the plan is deliberately built with the seams for authoring left in place** — see §11, which is a buildable spec, not a wish. | user (revision) |
| D5 | Pre-battle rows are written at **`turn = 0`**. The lobby sits at turn 0 (`DBManager::createGame` inserts `turn 0, phase -2`; `Manager::changeTurn` bumps to 1 immediately after `BuyingGamePhase::advance`). Turn 0 renders in the lobby (`gamedata.turn === 0`, crit filter is `turn <= gamedata.turn`), makes `ShipSystem::isDestroyed`'s structure cascade (`currentTurn − 1`) fire from Deployment on, and never matches a `turn == gamedata.turn` combat-log/replay filter. | derived |
| D6 | **Flight damage is keyed by fighter ORDINAL (1…flightSize), not system id** — see §1.1. | derived, load-bearing |
| D7 | Part 2 does not save transient/informational criticals: anything `forInfo`, `oneturn`, or a Hangar-Ops/state marker (`DockedFighter`, `SplitLaunchedFighter`, `LaunchedThisTurn`, `LCVLaunchedThisTurn`, `HangarOperations`, `OrbitalRepairing`, `DisengagedFighter`). Those describe a moment in a battle, not a lasting wound. **Superseded twice:** §13.5 made `oneturn` an option (stamped at turn 1) rather than a ban, and **§14.3 removed the "param-carrying" clause** — `ReducedArcs` never carried a param at all and `DamageReductionReduced`'s param is now stored, so `param` is no longer always `NULL`. | derived |
| D8 | **A fighter is only ever DAMAGED, never destroyed — a lost fighter shrinks `flightSize`.** A dead fighter has no representation in B5W other than "not in the flight", and flight size is already freely adjustable in the lobby's Edit action. So: the fighter menu offers no Destroy control and health floors at 1; `k` is dropped from the `ftr` bucket at every sanitise boundary; and Part 2 saves the **surviving** flight size, numbering ordinals over survivors only. This **reverses** the earlier "§7.2 saves the original flightSize" note. Consequence to be aware of: point cost scales with flight size, so a shrunken flight reloads cheaper — that is correct (you are buying fewer craft), and does not conflict with D2, which is about *damage* not changing cost. | user, 2026-08-07 (revision 2) |

---

## 1. The finding that shaped this plan

### 1.1 In the lobby a flight is ONE sample fighter plus a number

Verified 2026-08-07 against the generated static JSON — **every one of the 10 Earth Alliance
flight blueprints reports `flightSize = 1` with exactly one entry in `systems`:**

```
ArmedMissileShuttleEA | flightSize=1 | systemCount=1 | keys=1
AuroraStarfury        | flightSize=1 | systemCount=1 | keys=1   …
```

Why: `FighterFlight::$flightSize` defaults to **1** ([FighterFlight.php:29](source/server/model/ships/FighterFlight.php#L29));
each flight class calls `$this->populate()` in its own constructor, and `populate()` is
incremental (`$toAdd = $this->flightSize − count($this->systems)`), so a freshly constructed
flight gets one fighter. `ShipLoader::getAllShipsStatic` ([shipLoader.php:332](source/server/controller/shipLoader.php#L332))
never raises `flightSize`, so the static blueprint the lobby fetches has one. `HyachDovoch` is
the sole class that sets `flightSize = 6` itself.

The N fighters are minted **server-side only**, by `populate()` after `flightSize` is applied:
`Manager::getShipsFromJSON` ([:1707](source/server/controller/Manager.php#L1707)) at buy time and
`DBManager::getTacShips` ([:2346](source/server/controller/DBManager.php#L2346)) on every game load.

**Consequences:**

* `DBManager::getSavedShips` not calling `populate()`
  ([:398](source/server/controller/DBManager.php#L398)) is **not a bug and needs no fix.** Nothing
  in the lobby populates, so a loaded saved flight behaves exactly like a bought one — one sample
  fighter + `flightSize` N — and plays as N because the server populates from the stored size.
  (An earlier draft of this plan called it a Stage-0 prerequisite. That was wrong.)
* **Per-fighter system ids do not exist in the lobby**, so per-fighter damage cannot be keyed by
  them. It is keyed by **fighter ordinal** and resolved server-side after `populate()`, by walking
  `array_values($ship->systems)` (the flight's `systems` array is keyed by `$fighter->id`, which
  `addSystem` sets from a running `autoid` that also consumes each subsystem — so it is 1, 1+k,
  1+2k… and NOT 1..N; use position, not key).
* This is also why the request said "click on the healbar of **sample** fighter" — the fighter
  menu must be **synthetic**: it renders `flightSize` rows from one blueprint.
* Bonus: ordinals are immune to [[arch_positional_system_id_trap]] — a flight-size change
  re-maps rather than mis-points.

### 1.2 Non-flight system ids ARE safe to key on

`BaseShip::add*System` assigns ids in pure constructor order, and the same constructor runs in
three places: the static blueprint the lobby holds, the buy-POST reconstruction
(`getShipsFromJSON`), and the game load. **Enhancements never add systems** (grep for
`addSystem` in `Enhancements.php` finds only a JSON helper), so nothing perturbs the numbering.
Damage on ordinary ships is therefore safely keyed by `systemid`.

### 1.3 The client-side extraction is much smaller than expected

`ajaxInterface.js` *and* `UI/confirm.js` are already loaded by **both**
[game.php:369](source/public/game.php#L369)/[:385](source/public/game.php#L385) and
[gamelobby.php:136](source/public/gamelobby.php#L136)/[:149](source/public/gamelobby.php#L149).
So `constructSavedShips` ([ajaxInterface.js:422](source/public/client/ajaxInterface.js#L422)),
`submitSavedFleet`, `getSavedFleets`, `loadSavedFleet`, `deleteSavedFleet`, `changeFleetPublic`
and `confirm.showSaveFleet` ([confirm.js:1620](source/public/client/UI/confirm.js#L1620)) are
**already callable from game.php today**. Only the thin `gamedata.*` UI wrappers live in
`gamelobby.js`, and game.php needs only the *save* half. See §7.

---

## 2. Survey: what exists

### Saved fleets
* Endpoints `saveFleet.php`, `loadSavedFleet.php`, `getSavedFleets.php`, `deleteSavedFleet.php`.
* [Manager.php:758](source/server/controller/Manager.php#L758) `submitSavedFleet` →
  `getSavedShipsFromJSON` ([:1007](source/server/controller/Manager.php#L1007)) →
  `DBManager::submitSavedList` / `submitSavedShip` / `submitSavedEnhancement` / `submitSavedAmmo`
  ([DBManager.php:230-319](source/server/controller/DBManager.php#L230-L319)).
* [Manager.php:875](source/server/controller/Manager.php#L875) `loadSavedFleet` →
  `getSavedFleet` / `getSavedShips` / `getSavedEnhancementsForShip` / `getSavedAmmoForShip`, then
  `Enhancements::setEnhancementOptions` and a `beforeTurn` sweep.
* Tables `tac_saved_list` / `_ship` / `_enh` / `_ammo`
  ([db/emptyDatabase.sql:461-544](db/emptyDatabase.sql#L461)), FK-cascaded off
  `tac_saved_list.id` and `tac_saved_ship.id`.

### Damage / criticals
* `tac_damage`, `tac_critical` ([db/emptyDatabase.sql:146-192](db/emptyDatabase.sql#L146)).
* Writers `DBManager::submitDamages` ([:1220](source/server/controller/DBManager.php#L1220)) and
  `submitCriticals` ([:908](source/server/controller/DBManager.php#L908)) — both
  **string-interpolated SQL**, and `submitDamages` runs a `tac_fireorder` back-fill whenever
  `fireorderid < 0`.
* Readers `getDamageForShips` ([:2585](source/server/controller/DBManager.php#L2585)) —
  `turn <= ?`; `getCriticalsForShips` ([:2617](source/server/controller/DBManager.php#L2617)) —
  and it does **`new $type(...)`** on the stored string. Turn-0 rows load on every turn.
* `ShipSystem::stripForJson` ([:105](source/server/model/systems/ShipSystem.php#L105)) folds
  damage older than `currentTurn − 1` into one synthetic entry. Nothing to change; but it means
  Part 2's summariser must **sum**, never count.
* `ShipSystem::$critData[$class] = description` ([:1191](source/server/model/systems/ShipSystem.php#L1191))
  is how crit text already reaches the client; `SystemInfo.getCriticals` reads it and falls back
  to the raw class name.

### Client rendering a preview must drive
* `damageManager.getDamage` = Σ(damage − armour) clamped to `maxhealth` ([damage.js:5](source/public/client/damage.js#L5)).
* `SystemIcon.getStructureLeft` ([:502](source/public/client/UI/reactJs/system/SystemIcon.js#L502));
  `getDestroyed` → `shipManager.systems.isDestroyed`, which **reads the server-computed
  `system.destroyed` boolean and does not re-derive from damage**
  ([systems.js:29](source/public/client/systems.js#L29)); `hasCriticals` → orange bar.
* `ShipSection` header bar ([:268](source/public/client/UI/reactJs/shipWindow/ShipSection.js#L268)).
* `FighterIcon` health bar ([:279](source/public/client/UI/reactJs/shipWindow/FighterIcon.js#L279));
  `FighterList` maps `ship.systems` 1:1 ([FighterList.js:28](source/public/client/UI/reactJs/shipWindow/FighterList.js#L28)).
* Client `ShipSystem` ctor copies `damage`/`criticals` off the JSON and defaults them to `[]`
  ([model/shipSystem.js:12](source/public/client/model/shipSystem.js#L12)).

### Lobby ship-window plumbing (from [[project_shipwindow_redesign]] Stage 3)
* Bought-vs-store test already exists: `ShipWindowManager.isLeftSide`
  ([:14](source/public/client/renderer/shipWindowManager.js#L14)) — in the lobby
  `ship.userid == 0` is the **store** blueprint.
* `SystemIcon.clickSystem` bails at `if (gamedata.waiting || gamedata.replay) return;`
  ([:188](source/public/client/UI/reactJs/system/SystemIcon.js#L188)); the lobby sets
  `waiting: true`, so `SystemInfoMenu` is never rendered there — the popup arrives only via
  hover/long-press. `showSystemInfoMenu` is called only from
  [PhaseStrategy.js:78-82](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L78).
* The lobby's uiEvents handler is the bootstrap at the **end of `gamelobby.js`**.
* ⚠️ Lobby fleet ships are `jQuery.extend` clones (`getShipByType`
  [:3152](source/public/client/gamelobby.js#L3152)) — **every `instanceof` fails**
  ([[arch_lobby_extend_clone_prototype_loss]]).
* `gamedata.turn === 0`, `gamephase === -2`, `waiting === true`; `window.staticShips` is
  **undefined** in the lobby ([[arch_gamelobby_static_ship_access]]).

### Buy submit path
`ajaxInterface.construcGamedata` ([:685](source/public/client/ajaxInterface.js#L685)) → POST
`gamedata.php` → `Manager::submitTacGamedata` → `getShipsFromJSON` → `BuyingGamePhase::process`
([:329](source/server/Phase/BuyingGamePhase.php#L329)), where `submitShip` mints the real
`tac_ship.id` ([:386](source/server/Phase/BuyingGamePhase.php#L386)) and enhancements / flight
size / ammo are written. **That is where damage and crit rows go.**

---

## 3. The one wire format

One compact per-ship payload serves the lobby state, the buy POST, the saved-fleet write, the
saved-fleet read and the Part-2 game save. Absent keys mean "nothing".

```js
preBattleDamage: {
  // ORDINARY SHIPS — keyed by systemid (§1.2)
  sys: {
    "12": { d: 6 },                  // 6 points of damage
    "14": { d: 8, k: 1 },            // damaged and destroyed
    "21": { k: 1 },                  // destroyed outright (d defaults to maxhealth)
    "33": { d: 3, c: { "OutputReduced1": 2 } }   // c only ever arrives from a saved fleet
  },
  // FLIGHTS — keyed by fighter ORDINAL 1..flightSize (§1.1). Damage lands on the fighter
  // unit itself, never its weapons. NO `k` here, ever (D8): a lost fighter shrinks
  // flightSize instead, so `d` caps at maxhealth − 1.
  ftr: { "1": { d: 6 }, "2": { d: 4 }, "4": { d: 2, c: { "GunLost": 1 } } }
}
```

* `d` integer total damage, `1 … maxhealth` (`1 … maxhealth − 1` in `ftr`)
* `k` 1 when destroyed — **`sys` only** (D8)
* `c` map of critical phpclass ⇒ count. In v1 **never produced by the lobby (D4)** — only by
  Part 2's summariser or read back from `tac_saved_crit`. The key exists in the format from day
  one precisely so §11 needs no format change; every consumer below already handles it.
* `p` (added §14.3) map of critical phpclass ⇒ **integer param**, present only for the classes
  in `PreBattleDamage::$paramCriticals` — the ones that keep their MAGNITUDE in the crit's own
  `param` rather than in a count. A **sibling map, not a richer `c` value**, so every existing
  consumer of `c` keeps counting and `p` is simply absent for the other 40-odd classes. `p`
  never outlives `c`: a key with no matching `c` key is pruned at every boundary.

```js
  sys: { "33": { d: 3, c: { "DamageReductionReduced": 1 }, p: { "DamageReductionReduced": 8 } } }
```

**Damage and criticals are independently separable** (D3). Everything that filters a payload does
it through one helper, `PreBattleDamage::filter($payload, $withDamage, $withCriticals)`:
`$withDamage = false` drops `d`/`k`, `$withCriticals = false` drops `c`, and entries left empty
are removed. Filtering happens **on the payload, not on the render** — see §4.7.

Expansion (server, one shared helper — §4.2):

* `d` ⇒ one `tac_damage` row: `turn 0`, `damage = d`, `armour 0`, `shields 0`,
  **`fireorderid = 0`** (not −1, which would trigger `submitDamages`' `tac_fireorder` lookup),
  `destroyed = k`, `undestroyed 0`, `pubnotes 'Pre-battle damage'`, `damageclass 'PreGame'`.
* `k` alone ⇒ same row with `damage = maxhealth`.
* each `c` entry ⇒ *count* `tac_critical` rows, `turn 0`, `turnend 0`, `param NULL`.

A single row per system, not per point: smallest thing that reproduces both the display and the
rules (`getTotalDamage` sums; `isDestroyed` reads the flag), and it is the shape `stripForJson`
would collapse a long history into anyway.

---

## 4. Server

### 4.1 New tables — `db/prebattleDamage.sql` + fold into `db/emptyDatabase.sql`

Both mirror `tac_saved_ammo` exactly (same key shape, same double FK, same cascade), so
`deleteSavedFleet` keeps working untouched.

```sql
CREATE TABLE `tac_saved_damage` (
  `listid`    INT(11)    NOT NULL,
  `shipid`    INT(11)    NOT NULL,          -- tac_saved_ship.id
  `kind`      TINYINT(1) NOT NULL DEFAULT 0,-- 0 = systemid, 1 = fighter ordinal
  `ref`       INT(11)    NOT NULL,          -- systemid | ordinal
  `damage`    INT(11)    NOT NULL DEFAULT 0,
  `destroyed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_dmg_ship` FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dmg_list` FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `tac_saved_crit` (
  `listid`   INT(11)      NOT NULL,
  `shipid`   INT(11)      NOT NULL,
  `kind`     TINYINT(1)   NOT NULL DEFAULT 0,
  `ref`      INT(11)      NOT NULL,
  `type`     VARCHAR(100) NOT NULL,
  `amount`   INT(11)      NOT NULL DEFAULT 1,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`,`type`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_crit_ship` FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_crit_list` FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

`kind` is what makes the ordinal scheme (D6) storable in the same table as system ids, without a
second pair of tables.

### 4.2 New shared helper `source/server/model/PreBattleDamage.php`

Autoloadable; holds **all** the rules so the buy path, the saved-fleet write, the saved-fleet
read and any future campaign tooling cannot drift.

```php
class PreBattleDamage {
    const TURN = 0, DAMAGECLASS = 'PreGame', PUBNOTES = 'Pre-battle damage';

    /* Validate + normalise a raw client payload → ['sys'=>[...], 'ftr'=>[...]].
       Drops anything invalid; never throws on bad client data. */
    public static function sanitise($ship, array $raw): array;

    /* True when $type is a real, storable Critical subclass (see below). */
    public static function isValidCriticalType(string $type): bool;

    /* Split a payload by kind (D3). Drops d/k when !$withDamage, c when !$withCriticals,
       then prunes entries left empty. THE single place that separation happens. */
    public static function filter(array $payload, bool $withDamage, bool $withCriticals): array;

    /* Does this payload contain each kind? Drives the two load checkboxes + messaging. */
    public static function contents(array $payload): array;   // ['damage'=>bool,'criticals'=>bool]

    /* Resolve a flight ordinal to the populated fighter, or null. Position-based:
       array_values($ship->systems)[$ordinal-1]. */
    public static function fighterByOrdinal($ship, int $ordinal);

    /* Expand a sanitised payload into DamageEntry[] + Critical[] for a KNOWN shipid. */
    public static function toEntries($ship, array $clean, $gameid, $shipid): array;

    /* Apply a sanitised payload to a live ship OBJECT (setDamage/setCritical + destroyed
       flags), so loadSavedFleet returns a correct payload with no extra DB round trip.
       Takes an already-FILTERED payload — it applies whatever it is given, so it needs no
       knowledge of the D3 toggles. */
    public static function applyToShip($ship, array $clean): void;

    /* {critClass => description} for just the classes present in $clean — sent with
       loadSavedFleet so the lobby's SystemInfo popup can name a carried critical. */
    public static function describeCriticals(array $clean): array;
}
```

**`sanitise` is the security boundary.** It must:

1. `sys` — resolve each key through `$ship->getSystemById()`; drop unknown ids.
   `ftr` — require `$ship instanceof FighterFlight`, ordinal `1 … flightSize`; drop the rest.
2. clamp `d` to `1 … maxhealth` (fighter `maxhealth` for the `ftr` branch); drop `d <= 0` unless `k`.
3. coerce `k` to 0/1.
4. **reject any critical type failing `isValidCriticalType`.** Not cosmetic:
   `DBManager::getCriticalsForShips` does `new $type(...)` on the stored string, so an
   unvalidated type is arbitrary class instantiation on every subsequent load of that game.
5. clamp each crit count (suggest ≤ 10).
6. drop the D7 exclusion set (belt-and-braces; Part 2 already filters).

`isValidCriticalType` = `class_exists($type) && is_subclass_of($type, 'Critical')` **plus** a
`static $storable` deny-list for the D7 classes. Because criticals arrive only from real battles
(D4), it must **not** be narrowed to a system's `possibleCriticals` table — genuine combat crits
like `AmmoExplosion` / `OSATThrusterCrit` / `LimpetBore` are applied by bespoke code and are absent
from those tables. `possibleCriticals` therefore stays `protected` and untouched; no new accessor,
no per-system catalogue, **no `systemCriticals.php` endpoint** (dropped with D4).

> **§11 seam.** Keep this function as the *storability* test and nothing more. Authoring adds a
> separate, narrower question — "what may a player *offer* to add to this system" — which is the
> catalogue's job, not this validator's. Do not fuse the two: narrowing `isValidCriticalType` to
> `possibleCriticals` would silently eat carried combat crits on every reload.

Add `is_subclass_of($type, 'Critical')` at the `getCriticalsForShips` read site too, as
defence-in-depth for rows already in the DB.

### 4.3 `BaseShip::$preBattleDamage` + `Manager::getShipsFromJSON`

`BaseShip` gains `public $preBattleDamage = array();` with the invariant documented on it:

> Read by **`BuyingGamePhase::process` only.** Every other phase ignores the field, so a client
> cannot inject damage mid-game.

Beside the existing whitelisted extras (`bulkBuy`, `movement->forced`) in
`getShipsFromJSON` ([:1626](source/server/controller/Manager.php#L1626)) and
`getSavedShipsFromJSON` ([:1007](source/server/controller/Manager.php#L1007)):

```php
$ship->preBattleDamage = $value["preBattleDamage"] ?? array();
```

⚠️ POST-side ships carry no notes/enhancements ([[arch_post_side_ship_reconstruction]]).
`sanitise` depends on neither — only `getSystemById`, `maxhealth`, and `flightSize`. Note that
`getShipsFromJSON` **already** calls `populate()` after setting `flightSize`
([:1707](source/server/controller/Manager.php#L1707)), so the fighters exist before ordinals are
resolved. Confirm that ordering survives any edit.

### 4.4 `BuyingGamePhase::process` — write the rows

Inside the `bulkBuy` loop, right after the enhancement block
([:392-399](source/server/Phase/BuyingGamePhase.php#L392)):

```php
$clean = PreBattleDamage::sanitise($savedShip, $ship->preBattleDamage ?? array());
if ($clean) {
    $parts = PreBattleDamage::toEntries($savedShip, $clean, $gameData->id, $id);
    if ($parts['damage'])    $dbManager->submitDamages($gameData->id, PreBattleDamage::TURN, $parts['damage']);
    if ($parts['criticals']) $dbManager->submitCriticals($gameData->id, $parts['criticals'], PreBattleDamage::TURN);
}
```

* Use `$savedShip` (the mine clone when `$ship->mine`) so bulk-bought mines each get their own rows.
* `submitCriticals` inserts only when `$critical->id < 1` — construct with `id = -1`.
* Both writers interpolate `pubnotes` / `param` unescaped. We pass only the two constants and
  `NULL`, so nothing user-supplied reaches SQL — comment it, so a future `param` feature escapes.

### 4.5 `DBManager` additions

Modelled on `submitSavedAmmo` / `getSavedAmmoForShip` (prepared, `ON DUPLICATE KEY UPDATE`):

* `submitSavedDamage($listid,$shipid,$kind,$ref,$damage,$destroyed)`
* `submitSavedCrit($listid,$shipid,$kind,$ref,$type,$amount)`
* `getSavedDamageForShip($shipid)` → `[[kind,ref,damage,destroyed], …]`
* `getSavedCritsForShip($shipid)` → `[[kind,ref,type,amount], …]`
* expose **`hasDamage` AND `hasCrits`** per row in `getSavedFleets` — two `EXISTS` subqueries
  against `tac_saved_damage` / `tac_saved_crit`, no extra round trip. They drive the two D3
  checkboxes independently (each is hidden when its flag is false) and the dropdown badge.

### 4.6 Saved fleets — write

`Manager::submitSavedFleet`'s ship loop ([:776](source/server/controller/Manager.php#L776)), after
the enhancement/ammo blocks:

```php
$clean = PreBattleDamage::sanitise($ship, $ship->preBattleDamage ?? array());
foreach (array('sys'=>0, 'ftr'=>1) as $bucket => $kind) {
    foreach (($clean[$bucket] ?? array()) as $ref => $e) {
        if (!empty($e['d']) || !empty($e['k']))
            $dbManager->submitSavedDamage($listId, $shipId, $kind, $ref, $e['d'] ?? 0, $e['k'] ?? 0);
        foreach (($e['c'] ?? array()) as $type => $n)
            $dbManager->submitSavedCrit($listId, $shipId, $kind, $ref, $type, $n);
    }
}
```

Note `submitSavedFleet` runs `getSavedShipsFromJSON`, which **already** populates flights
([:1030](source/server/controller/Manager.php#L1030)) — so ordinal validation works here too.

### 4.7 Saved fleets — read

`Manager::loadSavedFleet` gains **`$includeDamage` and `$includeCriticals`** (both from new
optional keys in `loadSavedFleet.php`'s POST body, both defaulting true) and, per ship, after
enhancements + ammo and before the `beforeTurn` sweep:

```php
$full = PreBattleDamage::sanitiseSavedRows(
    $ship,
    $dbManager->getSavedDamageForShip($ship->id),
    $dbManager->getSavedCritsForShip($ship->id)
);                                                    // wire format, validated against THIS ship
$available = PreBattleDamage::contents($full);        // what the fleet HAS — for messaging only
$clean     = PreBattleDamage::filter($full, $includeDamage, $includeCriticals);

$ship->preBattleDamage = $clean;                      // the EFFECTIVE payload (see warning)
$ship->preBattleAvailable = $available;               // display-only, never submitted
PreBattleDamage::applyToShip($ship, $clean);
```

⚠️ **The toggle must prune the payload, not just the preview.** `$ship->preBattleDamage` is what
the client carries and re-POSTs at buy time, so if a declined kind stayed in it, it would be
written to `tac_damage` / `tac_critical` anyway and the toggle would be a lie. Hence `filter`
runs *before* the payload is handed over, and `applyToShip` simply renders whatever survived.
`preBattleAvailable` carries the "this fleet also had criticals you chose not to load" fact
separately, and `construcGamedata` must never copy it.

**Ordinal validation caveat:** `getSavedShips` leaves the flight at one fighter (§1.1, correctly
so). Ordinals therefore validate against **`$ship->flightSize`**, not against
`count($ship->systems)` — and `applyToShip` cannot touch fighter objects that do not exist. For
flights, `applyToShip` instead stashes the ordinal map on the ship for the **client** to render
against its synthetic fighter rows (§5.2). Ordinary ships get real `damage`/`criticals` entries.

Also return, in the response:
* `preBattleDamage` per ship — the **filtered** payload;
* `preBattleAvailable` per ship — `{damage, criticals}` booleans for what was on offer;
* `critDesc` — `PreBattleDamage::describeCriticals()` output for the crit classes actually
  applied, so `SystemInfo` names them (it already falls back to the raw class name if absent);
* `hasDamage` and `hasCrits` on the `list` block.

---

## 5. Client

### 5.1 New legacy file `source/public/client/battleDamage.js` → `window.battleDamage`

Add its `<script>` to the debug lists in **both** `gamelobby.php` and `game.php` so
`scripts/bundle-legacy.js` picks it up automatically.

```
battleDamage = {
  get(ship)                       // wire-format payload, lazily created on ship.preBattleDamage
  setSystem(ship, systemid, entry)
  setFighter(ship, ordinal, entry)
  setCriticals(ship, kind, ref, critMap)   // §11 SEAM — writes the `c` key. Used in v1 only by
                                           // doLoadFleet (carrying a loaded fleet's crits); the
                                           // picker calls exactly this and nothing else.
  clear(ship)                     // flight-size change / phpclass change
  isEmpty(payload)
  contents(payload)               // {damage, criticals} — mirrors the PHP twin
  filter(payload, withDmg, withCrit)       // mirrors PreBattleDamage::filter
  fleetHasDamage()                // any own bought ship carries damage or crits → Ready warning (D1)

  applyToShip(ship)               // render the preview (below)
  revertShip(ship)                // strip PreGame entries, then re-apply from the payload
  fighterHealth(ship, ordinal)    // maxhealth − d, for the synthetic fighter rows
  fighterCriticals(ship, ordinal) // `c` for that ordinal — read-only display in v1
  summariseShip(ship)             // LIVE game ship → wire format (Part 2, §7.2)
}
```

`setCriticals`, `contents` and `filter` exist in v1 with real callers (load-carry, the two D3
toggles, the Ready warning). Nothing in §11 needs to add a state primitive — it only adds a
*catalogue fetch* and a *UI section* that call `setCriticals`.

**`applyToShip`** — for each `sys` entry push
`{ id:-1, shipid, systemid, turn:0, damage:d, armour:0, shields:0, fireorderid:0, destroyed:k,
undestroyed:0, pubnotes:'Pre-battle damage', damageclass:'PreGame' }` onto `system.damage`, and
one `{ id:-1, shipid, systemid, phpclass:type, turn:0, turnend:0, param:null }` per crit count
onto `system.criticals`. Then:

* `system.destroyed = k || (d >= system.maxhealth)`;
* mirror `ShipSystem::isDestroyed`'s structure rule — for each destroyed structure system, set
  `destroyed = true` on every system in that location. **Duck-type on `name === 'structure'`,
  never `instanceof Structure`** ([[arch_lobby_extend_clone_prototype_loss]]);
* `system.critData[type] = critDesc[type] || type`.

`ftr` entries are **not** written into system arrays (there is only one fighter object in the
lobby); they are read directly by the synthetic fighter UI via `fighterHealth`.

⚠️ **Shared references.** Client system fields share refs across same-phpclass instances
([[arch_client_system_shared_reference]]). `damage` / `criticals` / `critData` must be **replaced
with fresh copies** before the first write (`system.damage = (system.damage || []).slice()`), or
damaging one Omega damages every Omega in the fleet. This is the single most likely bug in the
feature — Stage 2's exit test is explicitly two identical ships.

### 5.2 React

**`system/ApplyDamageMenu.js`** (new). Blue chrome from `PowerCapacitor.js` (`#215a7a` header,
`rgba(16,26,38,0.9)` body, `theme.colors.line` hairlines, 24×18 `ActionButton`). Input + wheel +
ticker pattern from `MinorThoughtPulsarMenu.js` ([:254-271](source/public/client/UI/reactJs/system/MinorThoughtPulsarMenu.js#L254),
[:342-360](source/public/client/UI/reactJs/system/MinorThoughtPulsarMenu.js#L342)).

```
┌ Apply Damage ────────────────────────────┐
│ Structure   [−] [  8 ] [+]   ☐ Destroy   │
├ Critical Effects ────────────────────────┤   ← rendered only when crits are present
│ Output reduced by 1  (x2)                │      read-only in v1; §11 makes this section
│ Partial burnout                          │      editable in place
└──────────────────────────────────────────┘
```

* Field shows **remaining health** (`maxhealth − d`), clamped `0 … maxhealth`, starts at
  `maxhealth`; hitting 0 auto-ticks Destroy. Wheel up heals, wheel down damages. Keyboard entry
  strips non-digits to 0.
* Destroy ticked ⇒ `k = 1`, field forced to 0 and disabled.
* **No crit *picker* (D4)**, but there is a **"Critical Effects" section** listing whatever this
  system carries from a loaded fleet, read-only, using `critDesc` for the text and `(xN)` for
  counts. Criticals also still show through the existing `SystemInfo` crit block and the orange
  health bar.
  > **§11 seam.** Build this as its own component, `CriticalEffectsSection`, taking
  > `{ ship, kind, ref, crits, editable }` and rendering rows from a `[{type, label, count}]`
  > array. In v1 it is always called with `editable={false}` and derives its rows from the
  > payload. §11 passes `editable` plus a longer row list from the catalogue, and each row gains
  > the checkbox + ticker; the section's position, styling and data flow do not move.
* Every change → `battleDamage.setSystem` → `battleDamage.applyToShip(ship)` →
  `window.shipWindowManagerReact.update()` (repaints icons/bars/section headers) + `forceUpdate`.

**`shipWindow/FighterDamageMenu.js`** (new). Opened by clicking the `FighterIcon` health bar
([:279](source/public/client/UI/reactJs/shipWindow/FighterIcon.js#L279)) — add an `onClick` that
relays a new `FighterDamageClicked` uiEvent, gated to lobby + bought + own ship, with
`stopPropagation` so it does not also trip the fighter hover/long-press handlers.

It renders **`flightSize` synthetic rows** from the one sample fighter (§1.1):

```
┌ Fighter Damage ──────────┐
│ Fighter 1  [−][ 6 ][+] /6│
│ Fighter 2  [−][ 6 ][+] /6│
│  … up to flightSize      │
│ [ Apply Fighter 1 to all]│
└──────────────────────────┘
```

Rows read/write `ftr[ordinal]`; `maxhealth` comes from the sample fighter. **No Destroy control
(D8)** — health floors at 1, and a lost fighter is expressed by lowering `flightSize` in the
lobby's existing Edit action. A row whose ordinal carries criticals renders the same read-only
`CriticalEffectsSection` inline beneath it (so §11 extends flights for free). Propagate copies
fighter 1's whole entry to all other ordinals (same idea as `MinorThoughtPulsarMenu.propagate`) —
**and, when §11 lands, its criticals too; the propagate helper is written over the whole entry
object, not over `d`/`k` fields, so that needs no change.** Because the lobby draws only one
fighter card, the flight window's single health bar shows **flight-level** state:
`Σ(maxhealth − d) / (flightSize × maxhealth)`, with the total as its text.

**Gating** — in `SystemInfoButtons.js`:

```js
export const canApplyPreBattleDamage = (ship, system) =>
    gamedata.gamephase === -2 && ship.userid != 0 && !isPseudoSystem(system);
```

Add it to `canDoAnything` ([:759](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L759))
and `hasStyledMenu` ([:858](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L858)),
and render `<ApplyDamageMenu>` in `render()` ([:543](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L543)).
Every other `can*` predicate already requires a positive `gamephase`, so this cannot change any
game.php menu.

`SystemIcon.clickSystem` — the `waiting` bail ([:188](source/public/client/UI/reactJs/system/SystemIcon.js#L188))
and the destroyed bail ([:191](source/public/client/UI/reactJs/system/SystemIcon.js#L191)) must
both let this one case through (you must be able to *un*-destroy what you just destroyed):

```js
if ((gamedata.waiting || gamedata.replay) && !canApplyPreBattleDamage(ship, system)) return;
```

Import it from `SystemInfoButtons`, as `UI.js` already does for `canDoAnything`.

**Lobby uiEvents bootstrap** (end of `gamelobby.js`): `SystemClicked` currently always calls
`showInfo`. Change to — if `uiManager.canShowSystemInfoMenu(ship, system)` →
`showSystemInfoMenu` (mirroring [PhaseStrategy.js:78-82](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L78)),
else `showInfo`. Add a `FighterDamageClicked` case plus a new
`UIManager.showFighterDamageMenu` reusing the `#systemInfoReact` root.

### 5.3 Invalidation

* Bought flight's **flight size changed** ⇒ `battleDamage.clear(ship)` + warn once (ordinals
  beyond the new size would silently vanish; clearing is honest).
* Lobby **Edit** (`getShipByType` re-clone) ⇒ carry the payload across when the phpclass is
  unchanged, else clear.
* **Copy** ship ⇒ deep-clone the payload (never share the object).
* **Remove** ⇒ nothing (payload dies with the ship).

---

## 6. Lobby: Ready warning, save, load

* **Ready warning (D1)** — `gamedata.onReadyClicked`
  ([:3213](source/public/client/gamelobby.js#L3213)): when `battleDamage.fleetHasDamage()`,
  `"Are you sure you wish to ready your fleet?<br><br><b>NOTE: this fleet includes units with
  pre-battle damage and/or critical effects.</b>"`. `confirm.confirm` already renders HTML.
* **Buy POST** — `construcGamedata` ([:685](source/public/client/ajaxInterface.js#L685)), inside
  the `ship.userid === gamedata.thisplayer` block:
  `if (!battleDamage.isEmpty(ship.preBattleDamage)) newShip.preBattleDamage = ship.preBattleDamage;`
* **Save** — same one-liner in `constructSavedShips` ([:422](source/public/client/ajaxInterface.js#L422)).
* **Load (D3)** — the load confirms in `populateFleetDropdown`
  ([:3324](source/public/client/gamelobby.js#L3324)) and `loadSavedFleetById`
  ([:3474](source/public/client/gamelobby.js#L3474)) gain **two** checkboxes:

  ```
  Load your 'Second Line' fleet?
    ☑ Include saved battle damage        (shown only when hasDamage)
    ☑ Include saved critical effects     (shown only when hasCrits)
  ```

  `confirm.confirm` takes an HTML message, so the boxes go in the same way
  `confirm.showSaveFleet` injects its public-fleet checkbox
  ([confirm.js:1637](source/public/client/UI/confirm.js#L1637)) — but read them **inside** the OK
  callback, before `$(".confirm").remove()` runs, since that is what `doSaveFleet` does and the
  ordering is easy to get wrong. Neither flag present ⇒ send neither ⇒ server defaults apply.
  The choices flow into `ajaxInterface.loadSavedFleet(listId, opts, cb)` — an **options object**,
  not two positional booleans, so §11 or a future third kind does not re-sign the function. Badge
  fleets carrying either kind in the dropdown next to the padlock.
* **`doLoadFleet`** ([:3519](source/public/client/gamelobby.js#L3519)): after `new Ship(listShip)`,
  keep `listShip.preBattleDamage` (already filtered server-side) on the ship, stash `critDesc`,
  and call `battleDamage.applyToShip(ship)` before `gamedata.updateFleet(ship)`. Do **not**
  re-filter client-side — one filter, server-side, or the two can disagree.
* **Declined-kind notice** — when `preBattleAvailable` reports a kind the player skipped, show one
  `confirm.warning` after the load ("Fleet loaded. Saved critical effects were not applied."), so
  a mis-click is obvious rather than silent.
* **Bought-ship row** — append a "(damaged)" marker in `updateFleet`
  ([:352](source/public/client/gamelobby.js#L352)) so damage is visible without opening a window.

---

## 7. Part 2: Save Current Fleet from a live game

### 7.1 Extract only the SAVE half

Per §1.3 the shared plumbing is already on both pages, and **game.php never loads a fleet.** So:
put `saveCurrentFleet()` (plus its confirm dialog and result toast) in a new
`client/savedFleets.js`; have `gamelobby.js`'s `onSaveClicked`/`doSaveFleet`
([:3248](source/public/client/gamelobby.js#L3248)) delegate to it; leave the load/dropdown/delete
UI in `gamelobby.js` where its `cachedFleets` / `fleetDropdownList` / `fleetDropdownButton` DOM
closures live. Roughly a fifth of the diff of a full extraction, and nothing in game.php goes
near the dropdown. Revisit only if "load a fleet mid-game" is ever wanted.

### 7.2 `constructSavedShips` needs three fixes for game.php

It is lobby-shaped today ([:422](source/public/client/ajaxInterface.js#L422)):

1. **Points** — the loop filters `lship.slot != gamedata.selectedSlot`, and `selectedSlot` is
   `null` in game.php ([gamedata.js:23](source/public/client/gamedata.js#L23)) ⇒ points would save
   as 0. Fall back to `gamedata.isMyShip(lship)` when `selectedSlot` is null, and use base
   `pointCost` (D2: damage is not a discount).
2. **Skip dead / departed units** — filter `shipManager.isDestroyed(ship)`, and skip mid-battle
   artefacts that make no sense in a fleet list: launched-fighter "Split" rows, spent mines, and
   Chameleon phantom sheets (negative ids — [[project_chameleon_sensors]]).
3. **Flight size** — save the **surviving** flight size (D8): `battleDamage.survivingFlightSize`,
   i.e. fighters that are neither destroyed, dropped out, docked nor split off. Emit their damage
   as **ordinals numbered over the survivors** — position among the living, not the live system
   id and not the position in the original flight. That is what makes the payload
   flightSize-relative and reload-safe. A flight with no survivors is skipped entirely (it is
   already caught by the `shipManager.isDestroyed(ship)` filter in point 2).

Then:

```js
var dmg = battleDamage.summariseShip(ship);
if (!battleDamage.isEmpty(dmg)) newShip.preBattleDamage = dmg;
```

`summariseShip` collapses a live game's many `DamageEntry` rows:
`d = Math.min(maxhealth, Σ(damage − armour))`, `k = shipManager.systems.isDestroyed(ship,system)`,
`c` = counts of in-effect criticals by phpclass, filtered through the **D7 exclusion list**. Keep
that list in one exported constant and note in a comment on both sides that it mirrors
`PreBattleDamage`'s PHP deny-list ([[project_dev_roadmap]] item 13 — mirror-drift as a principle).

⚠️ `stripForJson` has already aggregated pre-`currentTurn−1` damage into one synthetic entry. The
**total is preserved**, so `summariseShip` must sum, never count.

### 7.3 The SAVE FLEET tab in `#logcontainer`

[game.php:737-793](source/public/game.php#L737): add
`<div id="fleetSaveTab" data-select="#fleetsave" class="logUiEntry"><span>SAVE FLEET</span></div>`
next to `declarationsTab`, plus a `<div id="fleetsave" class="logPanelEntry" style="display:none;">`
panel. The generic `data-select` handler already drives LOG / INFO / DECLARATIONS / CHAT.

Panel: a short explanation ("Saves your surviving ships, their enhancements, ammo, and their
current battle damage and critical effects as a reusable fleet list. Load it from the game lobby
to continue a campaign."), a Save button wired to `savedFleets.saveCurrentFleet()`, and a summary
line of what will be saved (N ships; M destroyed and excluded). Style from
`styles/tokens.css` only — **no new `:root` block** ([[project_visual_unification]]).

Availability: own ships only, any phase (it reads no orders), and deliberately **including
FINISHED games and replay** — that is exactly the "reload from the end of the previous battle"
case. Gate on "the viewer has ships in this game", not on phase.

---

## 8. Stages & exit criteria

| Stage | Content | Exit test |
|---|---|---|
| **0** | `db/prebattleDamage.sql` + `emptyDatabase.sql` fold-in; `PreBattleDamage.php`; DBManager CRUD; `is_subclass_of` guard at `getCriticalsForShips` | Tables create; a scratch PHP script round-trips a saved fleet carrying both `sys` and `ftr` damage; `php -l` on `/usr/src/current/…` (**not** `/usr/src/fieryvoid` — [[howto_docker_db_access]]) |
| **1** | `battleDamage.js` state + `applyToShip` / `revertShip` | Console-set damage in the lobby renders: health bars, orange crit bars, destroyed icons, section structure bar, structure cascade. **Two identical ships: damaging one leaves the other untouched** |
| **2** | `ApplyDamageMenu` + gating + lobby `SystemClicked` → menu | Clicking a bought ship's system opens the blue menu; store (left) windows and all of game.php unchanged; wheel/keyboard/tickers work; Destroy round-trips; un-destroy works |
| **3** | `FighterDamageMenu` + health-bar click + propagate + flight-level bar | A bought flight's fighters take damage individually and propagate; **health cannot be driven below 1 and no Destroy control exists (D8)**; lobby fighter hover/long-press still suppressed (round 3 of [[project_shipwindow_redesign]]) |
| **4** | Buy POST → `BuyingGamePhase` write; Ready warning; §5.3 invalidation | Ready a damaged fleet in a scratch game; Deployment shows it; `tac_damage`/`tac_critical` hold turn-0 rows; **a flight of 4 gets damage on the right 4 fighters**; a crafted POST with a bogus crit type is rejected; the same POST in phase 1 writes nothing |
| **5** | Saved-fleet write + read + `filter`/`contents` + the two D3 checkboxes + dropdown badge + `critDesc` + declined-kind notice | **All four load combinations** of a fleet carrying both kinds: both / damage-only / crits-only / neither — and in each case the ships that get READIED write exactly the chosen rows to `tac_damage`/`tac_critical` and no others (the payload-not-preview rule). A size-9 flight reloads with the right ordinals damaged; a carried critical shows its description, not its class name |
| **6** | Part 2: `savedFleets.js`, `constructSavedShips` fixes, SAVE FLEET tab | Save mid-battle, load in a new lobby, state matches the source game; destroyed ships absent; **a flight that lost 2 of 6 reloads as a flight of 4 whose ordinals carry the survivors' damage (D8)**; criticals survive the round trip; lobby save/load unchanged |

---

## 9. Risk register

| Risk | Mitigation |
|---|---|
| **Arbitrary class instantiation** via `tac_critical.type` (`new $type(...)` in `getCriticalsForShips`) | `isValidCriticalType` at every write boundary + `is_subclass_of` at the read site |
| **Shared client system references** ([[arch_client_system_shared_reference]]) — damaging one Omega damages all Omegas | Replace `damage`/`criticals`/`critData` with fresh copies before first write; two-identical-ships exit test in Stage 1 |
| **`instanceof` on lobby clones** ([[arch_lobby_extend_clone_prototype_loss]]) | Duck-type: `name === 'structure'`, `Array.isArray(system.systems)`, `ship.userid != 0` |
| **Flight ordinals mis-resolved** — `$ship->systems` is keyed by `$fighter->id` (1, 1+k, 1+2k…), **not** 1..N | `fighterByOrdinal` uses `array_values(...)` position, and only ever runs after `populate()`. Stage 4's "flight of 4" test is the gate |
| Positional system ids ([[arch_positional_system_id_trap]]) | Ordinals for flights; for ships, ids are pure ctor order and enhancements never add systems (§1.2). Every read path re-`sanitise`s against the freshly built ship, so a stale id is dropped, not misapplied |
| Damage injected in a later phase | Field read **only** by `BuyingGamePhase::process`; invariant documented on `BaseShip::$preBattleDamage` |
| Replay / combat log confused by turn-0 rows | Turn 0 never matches a `turn == gamedata.turn` filter; verify a replay of a pre-damaged game plays from turn 1 with no phantom hits |
| Unescaped `pubnotes`/`param` in the two DB writers | Only fixed constants and `NULL` pass today; comment the constraint |
| Live LiteSpeed memory ceiling ([[reference_fv_live_litespeed]]) | Nothing in v1 enumerates ship classes — the endpoint that would have (`systemCriticals.php`) is deferred to §11, which specifies one class per request. No `getAllShipsStatic(null)` anywhere ([[arch_static_generator_streaming]]) |
| A future third "kind" of saved state re-signing the load API | `loadSavedFleet(listId, opts, cb)` takes an options object, and `filter`/`contents` are kind-agnostic helpers rather than two hard-coded booleans at each call site |
| No automated coverage for damage/crit resolution | The replay harness does not cover damage rolls and its baseline is stale ([[project_replay_harness]]). Run `check` anyway to prove the server changes are inert for existing games; the real gate is manual Docker play-through |

---

## 10. Out of scope

* **Authoring criticals in the lobby** — deferred by D4, spec retained in **§11**.
* ~~Param-carrying criticals~~ — **now in scope, see §14.3.** `DamageReductionReduced` is saved
  with its integer param; `ReducedArcs` turned out never to have had one.
* Damage to a fighter's individual weapons (the fighter menu damages the craft, not its guns).
* Point-cost discounting for damaged ships — explicitly rejected (D2).
* Pre-battle ammo depletion — `tac_saved_ammo` already carries ammo; no lobby editor for it.
* Loading a saved fleet *into* a live game — nothing in Part 2 needs it (§7.1).
* Adding `populate()` to `DBManager::getSavedShips` — **investigated and rejected**: §1.1 shows it
  is not a bug, and populating there would make the lobby's saved flights inconsistent with its
  bought ones.

---

## 11. OPTIONAL FOLLOW-UP — authoring criticals in the lobby

Deferred by D4, **not abandoned.** Everything below is additive: no table, no wire-format key, no
`PreBattleDamage` method signature and no client state primitive changes. The seams are marked
"§11 seam" in §4.2, §5.1 and §5.2.

### 11.1 What already exists after v1

| Need | Status after v1 |
|---|---|
| A place in the payload for crits | ✅ `c: {critClass: count}` (§3) |
| Server validation + storage | ✅ `isValidCriticalType`, `tac_saved_crit`, `toEntries` write `turn 0 / turnend 0 / param NULL` |
| Write to `tac_critical` at buy | ✅ `BuyingGamePhase::process` (§4.4) already submits `$parts['criticals']` |
| Client state write | ✅ `battleDamage.setCriticals(ship, kind, ref, critMap)` (§5.1) |
| Preview render (orange bar, `SystemInfo` crit block, `critData` text) | ✅ `applyToShip` (§5.1) |
| Save/load round trip + the D3 toggle | ✅ §4.6 / §4.7 |
| A UI section in the right place | ✅ `CriticalEffectsSection`, `editable={false}` (§5.2) |
| **Missing: the list of crits a system may be offered** | ❌ this is the only real work |
| **Missing: making the section editable** | ❌ checkbox + ticker per row |

So the follow-up is **one endpoint + one accessor + flipping `editable`**.

### 11.2 The catalogue

`ShipSystem::$possibleCriticals` is `protected` and keyed `roll => critClass | array(critClass,…)`
([ShipSystem.php:32](source/server/model/systems/ShipSystem.php#L32)). It is in **neither** the
static ship JSON (`ShipCompactor::compactShipObject` json_encodes the object, so non-public
properties are dropped) **nor** `stripForJson`. Add a public accessor that exposes only the derived
list, leaving the table itself protected:

```php
public function getPossibleCriticalTypes(){      // flatten + dedupe
    $out = array();
    foreach ($this->possibleCriticals as $v)
        foreach ((is_array($v) ? $v : array($v)) as $c) $out[$c] = true;
    return array_keys($out);
}
```

**Deliver it via a new endpoint, `source/public/systemCriticals.php` — NOT via the static JSON.**
The static tree is already ~116 MB and every lobby visitor fetches a whole faction file
(Earth Alliance alone is 4.3 MB); per-system crit lists would add roughly 3 % for data that is
only read when a menu is opened. Measured sizes and the reasoning behind pre-compression are in
[ShipCompactor::precompressBrotli](source/server/lib/ShipCompactor.php#L156).

* **Request** `POST {"phpclass":"EAOmega","flightSize":6}`
* **Response**
  ```json
  { "phpclass":"EAOmega",
    "systems": { "12":["OutputReduced1","DamageReductionRemoved"], "13":[] },
    "fighters": { "1":["GunLost"] },
    "meta": { "OutputReduced1": {"desc":"Output reduced by 1"} } }
  ```
* `Manager::getSystemCriticals($phpclass, $flightSize)`: resolve the class through **`ShipLoader`,
  never `new $phpclass` on a raw client string**; set `flightSize` + `populate()` before walking a
  flight (§1.1) and key fighters by **ordinal**, matching the `ftr` bucket; run
  `getPossibleCriticalTypes()` per system, minus the D7 deny-list; build `meta` by instantiating
  each named `Critical` once and reading `->description` — the same source `critData` uses.
* Cache in APCu keyed `<db>_syscrits_<phpclass>_<flightSize>_<deployVersion>`, matching the
  existing convention ([[arch_gamedata_polling_cache]]). Client caches per phpclass+flightSize in
  a plain object: one request per ship class per session.
* ⚠️ Build **one** ship class per request. Never `getAllShipsStatic(null)` — that is the documented
  cause of the deploy 503 on the live LiteSpeed workers
  ([[arch_static_generator_streaming]], [[reference_fv_live_litespeed]]).

### 11.3 UI change

`CriticalEffectsSection` gains `editable={true}` when
`canApplyPreBattleDamage(ship, system) && catalogue.loaded`. Rows become the union of "carried"
and "offerable", each with a checkbox (tick ⇒ count 1) and the same `[−][N][+]` ticker + wheel +
keyboard control the damage field uses. Every change calls `battleDamage.setCriticals`, which
already triggers `applyToShip` + `shipWindowManagerReact.update()`. Fetch the catalogue lazily on
first menu open for a phpclass, and render the section read-only until it arrives.

### 11.4 Decisions to revisit when building

* **Offer only `possibleCriticals`, or every storable `Critical`?** The narrow list is more
  faithful to B5W; the wide list would let a scenario author reproduce anything a real battle can
  produce (`AmmoExplosion`, `OSATThrusterCrit`, `LimpetBore` are applied by bespoke code and are
  absent from every `possibleCriticals` table). Suggest narrow by default with an "all effects"
  expander. **Whichever is chosen, it constrains only what the picker OFFERS — never
  `isValidCriticalType`** (§4.2 seam).
* **`oneturn` criticals** — excluded by D7 because a one-turn crit stamped at `turn 0` sets
  `turnend = turn + 1 = 1` and expires as turn 1 begins, i.e. it would silently do nothing. If
  authoring them is ever wanted ("this system starts turn 1 offline"), stamp `turn = 1` instead of
  0 for that class only, and re-check `getCriticalsForShips`' `ForcedOfflineOneTurn` special case.
* ~~**Param-carrying criticals** (D7) need a per-class param editor and escaping in
  `DBManager::submitCriticals`~~ — **done in §14.3, and both premises were wrong.**
  `submitCriticals` already runs `param` through `DBEscape`
  ([:1028](source/server/controller/DBManager.php#L1028)), and `ReducedArcs` has no param to
  edit (the restricted arc lives on the weapon blueprint). Only `DamageReductionReduced` needed
  an editor, and it got the plain integer ticker the count already had.
* **Consistency with damage** — a critical on a system whose health is untouched is legal in B5W
  (crits come from hit rolls, not thresholds), so no cross-validation is wanted. Worth stating in
  the UI so it does not read as a bug.

---

## 12. Browser play-through findings, 2026-08-08

The first real play-through found seven defects. All seven are fixed; the root cause of the
first (and worst) is worth reading before touching any payload that crosses PHP.

### 12.1 ⭐ THE BUG: an empty PHP `array()` becomes a JSON ARRAY, and an array drops named keys

**Symptom.** Damage authored in the lobby rendered perfectly, but reached neither
`tac_saved_damage` (saving a fleet) nor `tac_damage` (readying into a game). Both paths, silently.

**Why it took a while to find.** Every component tested clean in isolation:
`PreBattleDamage::sanitise` / `toEntries` round-tripped a Primus payload correctly; the client
half produced the right `{sys:{"3":{d:5}}}`; `constructSavedShips` and `construcGamedata` both
attached it. The loss happened *between* them, in `JSON.stringify`.

**Root cause.** `BaseShip::$preBattleDamage = array()` is a public property, so it rides every
static ship blueprint the lobby clones — and PHP's empty `array()` encodes as the JSON **array**
`[]`, not `{}`. `battleDamage.get()` normalised the two *buckets* from array back to object
(trap 5 in the original build) but not the payload itself, so it stayed an Array. Hanging a
named property off an Array works perfectly in memory:

```js
const a = [];  a.sys = { "3": { d: 5 } };
a.sys                                  // { "3": { d: 5 } }   <- the preview reads this
JSON.stringify({ preBattleDamage: a }) // {"preBattleDamage":[]}   <- the POST sends this
```

`JSON.stringify` serialises **only the indexed elements** of an array. So the preview was right
and the wire was empty, in both save paths, with no error anywhere.

**Fix, in three places:**

* `battleDamage.get()` normalises the payload itself (`toPlainObject`), then its buckets.
* `doLoadFleet` runs the server's payload through `toPlainObject` too — an empty one comes back
  from `Manager::loadSavedFleet` as `[]` for exactly the same reason.
* `ShipCompactor::compactShip` adds `preBattleDamage` / `preBattleAvailable` to
  `$serverOnlyShipKeys`, so blueprints stop carrying them at all (~40 bytes x 2,554 ships, and
  it removes the trap at source). Needs a static regen to take effect; the client fix does not.

**The general rule:** any field that is `array()` server-side and an object client-side arrives
as `[]` when empty. Normalise it on the client the first time you touch it, and keep it off the
blueprint if the blueprint has no use for it.

### 12.2 The SAVE FLEET tab never appeared in game.php

`savedFleets.js`'s bootstrap ran `refreshSavePanel()` at DOM-ready, when `gamedata.ships` is
still empty — so "the viewer has ships in this game" was false and the tab hid itself. The only
other refresh trigger was the panel's own `onshow`, which cannot fire while the tab is hidden.
Now also called from `gamedata.parseServerData`, which is the single funnel for both the inline
bootstrap (`game.php` `$(window).on("load")`, i.e. after DOM-ready) and every poll — so the
"N units will be saved" line stays honest as units die, too.

### 12.3 Structure is now damageable from the section health bar

A section's Structure is deliberately filtered out of the icon grid (`filterStructure`) — the
header bar **is** its icon — so there was no way to click it. `ShipSection` now relays the
ordinary `SystemClicked` with the Structure system, gated on `canApplyPreBattleDamage`, and the
existing lobby handler opens the same `ApplyDamageMenu`. A completed long-press (the arc
gesture) sets `ignoreNextClick` so the touch that asked for the arc does not also open the menu.

`battleDamage.cascadeStructure` was rewritten to mirror `ShipSystem::isDestroyed` rule for rule:

| rule | before | now |
|---|---|---|
| system falls with its block | by `system.location` | by `getStructureLocation()` — `structureHomeLocation` when set |
| `structureHomeLocation` as an ARRAY | not handled | falls only when EVERY listed block is gone |
| `survivesStructureDestruction` | not handled | survives, primary loss included |
| non-primary Structure | fell with any structure | falls with PRIMARY only |

⚠️ **`survivesStructureDestruction` must stay `protected`.** Making it public to reach the client
put `survivesStructureDestruction:false` on **every ammo entry of every gamedata poll** — a
launcher's `missileArray` holds raw weapon objects that are `json_encode`d whole rather than
through `ShipSystem::stripForJson`, which is exactly what the comments on `$alwaysHideFireOrders`
and `$hideFireOrdersFromEnemies` in `weapon.php` warn about. The replay harness caught it: 7
FAILs, all of them `survivesStructureDestruction: added (false)` under `missileArray`. It now
reaches the client through `getSurvivesStructureDestruction()` +
`ShipCompactor::annotateSystems`, i.e. the **static blueprint only**, written only when true.
(Also needs a static regen; until then the lobby preview treats shield projections like any
other system, which is what it did before.)

### 12.4 The wheel scrolled the page

React 18 registers ONE `wheel` listener per root container and registers it **passive** (with
`touchstart` / `touchmove`), so `e.preventDefault()` inside an `onWheel` prop is a no-op: the
ticker stepped AND the page scrolled. Fixed with `helpers/nonPassiveWheel.js`, a callback ref
that attaches a native `wheel` listener with `{ passive: false }`; build it once (constructor or
a cache keyed by row), never inline in `render()`.

⚠️ `MinorThoughtPulsarMenu` and `SelfRepairList` have the same latent bug in their own tickers.
Not touched — reported, not fixed.

### 12.5 Saved-fleet dialogs restyled

Save / Load / the result notice now share a `.fleetDialog` skin in `confirm.css`: the games.php
window language (dark well, spaced uppercase title over one hairline rule, left-aligned body,
labelled buttons bottom-right) instead of the 2011 centred text + 30px ok.png/cancel.png icons.
Scoped to that family — no other dialog changes. Built by `confirm.fleetDialogShell`; the
buttons keep their `.confirmok`/`.confirmcancel` classes (every caller binds to those) and take
their text from `data-label`. `confirm.fleetNotice` replaces `confirm.warning` for the fleet
result messages, deliberately separate so reskinning this family cannot reskin every warning in
the game. **Not `--fv-display`**: Orbitron is `@import`ed by `gamesNew.css`, which gamelobby.php
links and game.php does not — and Save Current Fleet runs on both.

### 12.6 "Load Fleet by #ID" did nothing on a phone

A bare `<input>` with other focusable fields further down the page gives mobile keyboards a
**Next** action key, which moves focus to the chat panel instead of emitting the Enter keydown
the handler was waiting for. Fixed with all four of: a single-input `<form>` (its action key
becomes GO/submit), `enterkeyhint="go"`, `inputmode="numeric"` + `pattern="[0-9]*"` for the
numeric pad, and an explicit **Load** button as the path that cannot be argued with. The form's
`submit` and the input's Enter keydown both call one idempotent `submitFleetId()`, which blurs
the field first so the on-screen keyboard is gone before the confirm opens.

### 12.7 Verification

* `php -l` clean; replay harness back to its known baseline — **154 pass / 1 fail**, that one
  (game 4234) being the stale entry that fails on a clean tree too.
* Scratch PHP: the flag is absent from a raw `json_encode` (so out of live gamedata), present as
  `true` on 3 Consortium systems after compaction, absent from all 32 Primus systems;
  `preBattleDamage`/`preBattleAvailable` gone from the blueprint; a structure-destroy payload
  still expands to the right turn-0 row.
* Node harness over the real `battleDamage.js` + `ajaxInterface.js`: with the blueprint's `[]`
  in place, the save payload now carries `preBattleDamage.sys` through `JSON.stringify`.
* Cascade table above verified against a synthetic ship covering all four rules.
* React tree bundles and evaluates; `ShipSection`, `ApplyDamageMenu` and `FighterDamageMenu`
  render under `renderToString` (module-scope evaluation alone would NOT have caught a missing
  import here — the new identifiers are used inside handlers, not at module scope).
* `nonPassiveWheel` unit-tested: one `addEventListener('wheel', fn, {passive:false})`, no rebind
  on a repeat ref, removal on unmount.

**Still outstanding: the user's own browser re-test, `yarn build`, and a static ship regen**
(the two `ShipCompactor` changes are inert until then; nothing else depends on it).

---

## 13. Refinement round, 2026-08-08 (second play-through)

Items 1, 2, 4, 5 and 6 of §12 were confirmed cleared. Six refinements followed.

### 13.1 SAVE FLEET tab sat on top of the LOG tab

Every tab in `#logUI` is `position: absolute` with an explicit `left`, so a new one with
none defaults to `left: auto` and lands on `#logTab`. `#fleetSaveTab` is now the right-most
tab at `left: 520px` (DECLARATIONS at 416 + the row's 104px step), plus `left: 75%` in both
`max-width` breakpoints.

`refreshSavePanel` also stopped using `tab.toggle()`: jQuery's `show()` writes an INLINE
`display` when a stylesheet rule is what's hiding the element, and the mobile breakpoint
hides every `.logUiEntry` unless `#logcontainer` is `.large` — so an inline `display:block`
would have pinned this one tab open in the collapsed state. It sets `display: ''` / `'none'`
directly instead.

### 13.2 The "(damaged)" marker didn't survive a fleet-list rebuild

It was written in `updateFleet` ONLY, and `constructFleetList` rewrites every row from
`gamedata.ships` on each slot select / remove / edit — so the marker vanished at the first
refresh. Replaced by a **broken-heart badge** (`fa-heart-crack`, the same icon the saved-fleet
dropdown already uses for a fleet carrying damage) ahead of the unit's name, built by ONE
helper, `gamedata.damagedShipBadge(ship)`, called from BOTH row builders. It is re-derived
from the ship's payload every time, so it cannot be lost again — the payload always survived,
it was only the markup that didn't.

`gamedata.refreshDamagedBadge(ship)` repaints a single row, and both React damage menus call
it from `refresh()`, so the badge appears and disappears as the player edits.

### 13.3 Un-ticking Destroy reset the system to full health

`setDestroyed(false)` wrote `{d: 0, k: 0}`. `ApplyDamageMenu` now keeps
`this.healthBeforeDestroy` — component state, deliberately not in the payload, because it is
UI undo state and the wire format has to keep meaning exactly one thing. Stashed both when
Destroy is ticked and when the ticker/keyboard passes through 0 (which auto-destroys), so
either route back gives the dialled-in value; full health only when it was never amended.

### 13.4 The explicit Load button is gone

The `<form>` was doing the real work all along: an input inside its OWN single-field form
gets **implicit submission**, so a phone keyboard offers Go/Enter instead of the "Next" that
was walking focus down to the chat panel — no submit button required for that. Button and its
CSS removed; `enterkeyhint="go"` + `inputmode="numeric"` + the `submit`/Enter handlers stay.

### 13.5 ⭐ Temporary and marine criticals — the filter, and how to edit it

**What was actually happening.** All seven marine/boarding criticals (`Sabotage`,
`SabotageElite`, `CaptureShip`, `CaptureShipElite`, `RescueMission`, `RescueMissionElite`,
`DefenderLost`) force `forInfo = true` in their own constructors, and 16 of 17 one-turn
criticals are `oneturn` or self-expiring — so `isValidCriticalType` was already refusing all
of them. **`tmphitreduction` was the one real leak**: neither `forInfo` nor self-expiring, it
tested storable and would have been carried between battles.

**What changed.**

1. **The deny list is now explicit and documented on both sides** — `PreBattleDamage::$denyList`
   (PHP, the enforcing one) and `battleDamage.EXCLUDED_CRITICALS` (JS, which stops the client
   offering something the server would drop). The marine classes are named there even though
   `forInfo` already catches them, so a future boarding critical that forgets `forInfo` cannot
   leak, and `tmphitreduction` is banned.

   > **To ban a critical from saved fleets:** add its class name to **BOTH** lists.
   > **To allow one again:** remove it from **BOTH**. Nothing else needs touching — every
   > write path (buy POST, fleet save, fleet load) runs through `isValidCriticalType`. A name
   > in only one list is a bug in the other.

2. **`isValidCriticalType` is now the storability test and nothing more** (the seam §4.2 asked
   for): deny list + `forInfo`. The one-turn question moved out to
   **`isTransientCriticalType`**, which answers a different question — *when* to stamp it.

3. **Temporary criticals are stamped at `TRANSIENT_TURN = 1`, not turn 0.** At turn 0 the base
   `Critical` constructor sets `turnend = 1` and `getCriticalsForShips`' `turnend >= fetchTurn`
   test drops it the instant turn 1 begins — a carried one-turn effect would have done nothing
   at all. At turn 1 its `turnend` is 2, so it IS in effect for the first turn of the next
   battle. (Plan §11.4 named this as the fix if it was ever wanted; user decision 2026-08-08.)

4. **A checkbox in game.php's Save Fleet dialog**, `#fleetTransientCritsCheckbox`, **unticked
   by default** — a wound is worth carrying, a one-turn effect usually is not. It flows
   `savedFleets.doSaveCurrentFleet` → `submitSavedFleet(…, opts)` →
   `constructSavedShips(…, opts)` → `battleDamage.summariseShip(ship, opts)` →
   `summariseCriticals(system, opts)`. Offered only when `gamedata.gamephase !== -2`: the
   lobby has no battle to have produced a one-turn critical, so there the question has no
   answer.

Verified end to end: a payload mixing all four groups expands to `OutputReduced1` ×2 at turn
0, `ForcedOfflineOneTurn` at turn 1/turnend 2, and drops `Sabotage`, `CaptureShipElite` and
`tmphitreduction`. The JS twin returns exactly the same sets with and without the flag, and
`setCriticals` refuses a deny-listed class on write, so the lobby editor cannot reintroduce one.

### 13.6 Carried criticals are editable in the lobby

`CriticalEffectsSection` gained `editable`: a `[−][N][+]` ticker (with a non-passive wheel)
and a `×` on each row, all writing through `battleDamage.setCriticals` — the single client-side
door criticals enter and leave a payload by, so the preview, the buy POST and the saved-fleet
write follow with no extra wiring. Dropping a count to 0 removes the effect; removing the last
one clears the entry. Live in both `ApplyDamageMenu` and `FighterDamageMenu` (per ordinal).

Still **no ADD** — inventing a critical from nothing needs the per-class catalogue endpoint
§11.2 specifies. Amending and removing what a battle actually produced needs none of it, which
is why this half ships and that half does not.

A one-turn row is labelled **"(turn 1 only)"** so it doesn't read as a lasting wound. That flag
rides a new `critTransient` map on the `loadSavedFleet` response (`PreBattleDamage::transientCriticals`,
same probe `isTransientCriticalType` uses, so the label and the turn the row is written at
cannot disagree) → `ship.preBattleCritTransient`.

### 13.7 Verification

`php -l` clean; replay harness **154 pass / 1 fail** (game 4234, stale on a clean tree);
React tree bundles, evaluates, and all three changed components render under `renderToString`
with the editable crit rows and the "(turn 1 only)" tag present; PHP and JS crit-filter tests
agree class for class.

---

## 14. Refinement round, 2026-08-08 (third pass)

Three user requests. The third turned out to rest on a wrong premise in this very plan.

### 14.1 Terrain is never written into a saved fleet

**Symptom.** A player who places terrain themselves (asteroid field, moon, jump gate) owns
that unit — it is bought into their slot and carries their `userid` and `team` — so every
saved fleet picked it up and re-bought it in the next lobby.

Map terrain belongs to `userid -5` and was already excluded by `isSaveableFleetShip`'s
ownership test; player-placed terrain was not, and nothing else in the filter catches it.
One line in the single funnel, ahead of the lobby early-return so it applies on **both**
pages (a fleet list is ships, in the lobby as much as mid-battle):

```js
if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
```

`gamedata.isTerrain` (`shipSizeClass == 5 || userid == -5`) exists on both pages —
[gamedata.js:236](source/public/client/gamedata.js#L236) and, independently,
[gamelobby.js:3856](source/public/client/gamelobby.js#L3856). Because `isSaveableFleetShip`
also drives the points loop and `savedFleets.js`'s "N units will be saved" line, ships and
points stay consistent with no other change.

⚠️ **Fleets already saved keep their terrain.** The filter is on the WRITE path only — a
load-side filter would drop units from a list whose stored `points` still counted them.
Re-saving an affected fleet cleans it.

### 14.2 A removed critical keeps its row

Dropping a critical to 0 (or hitting ×) took it out of the payload, and the row went with
it — an edit undoable only by reloading the whole fleet. The row now stays, empty and
dimmed, with the ticker and × still on it, so it can be put straight back.

**Where the memory lives is the whole design.** Three candidates, and only one works:

| candidate | verdict |
|---|---|
| in the payload | **no** — `preBattleDamage` has to keep meaning exactly one thing: what this unit carries into battle. A "removed but rememberable" key would be state the buy POST must then learn to ignore |
| on the React component | **no** — `ApplyDamageMenu` is torn down every time the menu closes, so "I changed my mind" would only work while it happened to still be open |
| **on the ship** | **yes** — `battleDamage.critMemory(ship, kind, ref)`, an ordered list of class names per target under `ship.preBattleCritSeen`. Survives closing and reopening the menu; dies with the ship object, so a fleet reload or a lobby Edit correctly starts clean |

It is never submitted: `construcGamedata` and `constructSavedShips` copy `preBattleDamage`
and nothing else. `battleDamage.clear()` and the flight-size invalidation in `onShipRebuilt`
delete it alongside the payload.

`CriticalEffectsSection` therefore stores nothing of its own. `displayRows` feeds the live
row types into `rememberCriticals` and draws the returned ordered list through
`rowForType`, which rebuilds each row **from the class name alone** — count and param
re-read from the payload (0 when removed), wording from `critLabel`, transience from
`ship.preBattleCritTransient`. Order is first-seen, which is also an improvement on the
payload's own order: deleting and re-adding an object key moves it to the end.

### 14.3 ⭐ Param-carrying criticals ARE storable — the deny-list entry was wrong

The plan banned `ReducedArcs` and `DamageReductionReduced` together as "param-carrying
(param is always stored NULL, so these would be meaningless)". Reading the code:

* **`ReducedArcs` carries no param at all.** The restricted arc lives on the weapon
  INSTANCE in the ship blueprint — `Weapon::$reducedArcStart` / `setArcRestriction`
  ([weapon.php:259](source/server/model/weapons/weapon.php#L259)) — and the crit is only
  the flag that says "locked to it" (`effectCriticals`
  [:657](source/server/model/weapons/weapon.php#L657)). `testArcRestriction` constructs it
  with six arguments and no param ([:696](source/server/model/weapons/weapon.php#L696)). A
  jammed turret therefore rebuilds perfectly on the next battle's ship with `param NULL`.
  **It was banned for a property it does not have.**
* **`DamageReductionReduced` genuinely carries one** — a Phased Gravitic Torpedo banks one
  crit per hit with the whole reduction in `param`
  ([torpedo.php:505](source/server/model/weapons/torpedo.php#L505)). It is the ONLY
  non-`forInfo` param-carrying critical in the game (the seven marine/boarding classes carry
  a boarding-pod info ARRAY and are `forInfo` + deny-listed).

Both are now carried. The mechanism:

**The collapse.** Every consumer of `DamageReductionReduced` SUMS the params rather than
counting the crits — `ShipSystem::sumCriticalParam`
([:1305](source/server/model/systems/ShipSystem.php#L1305)) behind
`ThoughtShield`/`ThirdspaceShield::getRatingReduction` and `getDefensiveDamageMod`, plus the
client's own `paramTotal` loops in [systems.js:1195](source/public/client/systems.js#L1195)
and `SystemInfo`'s `PARAM_SUM_CRIT_DESCRIPTIONS`. So N crits on a system are saved as **ONE
row carrying the TOTAL**: exactly equivalent for every consumer, and it gives the lobby a
single number to edit instead of a meaningless count. `sanitiseEntry` forces `c = 1` for a
param class and the client's `summariseCriticals` sums on the way in.

**The allow-list, not a free-for-all.** `PreBattleDamage::$paramCriticals` (PHP) and
`battleDamage.PARAM_CRITICALS` (JS) are a new mirrored pair, edited exactly like the deny
list. Only add a class whose param is a **plain integer** and whose instances **sum**.

**Storage.** `tac_saved_crit` gains `param INT(11) DEFAULT NULL` (`db/prebattleDamage.sql`
carries an idempotent `ADD COLUMN IF NOT EXISTS` for existing installs). **INT, not
`tac_critical`'s `varchar(200)`** — only integers are ever allowed through, and a narrow
column is one more thing stopping an array param from getting here. Rows written before the
column existed read back as `NULL`; `sanitiseSavedRows` pads to five columns so both shapes
load.

**Security.** `DBManager::submitCriticals` already escapes `param` via `DBEscape`
([:1028](source/server/controller/DBManager.php#L1028)) — the plan's warning that it
interpolates raw was out of date. `sanitiseParam` additionally rejects arrays/objects,
rejects non-numerics, and clamps to `MAX_CRIT_PARAM = 100`, so what reaches SQL is a bounded
integer or `NULL`.

**Description.** `describeCriticals` is keyed by CLASS and so *cannot* describe these — the
same class sits on two systems with different params, and `getDescription()` reads the param
("Damage reduction reduced by 8"); a probe would say "by 0". Param classes are therefore
**omitted** from that map and the client builds their label from the payload
(`battleDamage.critLabel`). Server-side `applyToShip` reads `critData` off the crit it just
built rather than off the probe, for the same reason.

**UI.** In the editor a param row's ticker edits the **param**, and its label drops the
trailing number so the two do not both show it: `Damage reduction reduced by [-][ 8 ][+][x]`.
Driving it to 0 removes the effect (§14.2 keeps the row). A param class arriving with no
usable param is dropped at every boundary — its magnitude *is* the effect, so storing it at
0 would carry a wound that does nothing.

### 14.4 Verification

* `php -l` clean on `PreBattleDamage.php`, `DBManager.php`, `Manager.php`.
* **Scratch PHP, 24 assertions:** both classes now storable and neither transient; the marine
  / `tmphitreduction` / `ForcedOfflineForTurns` bans intact; param collapse to `c=1`; a param
  class with no param dropped; clamp at 100; an ARRAY param rejected; `p` with no `c` pruned;
  all three D3 filter combinations carry `p` with `c` and drop it with `c`; `toEntries`
  produces one `DamageReductionReduced` at turn 0 with `param 8` and two `ReducedArcs` with
  `NULL`; `sanitiseSavedRows` round-trips 5-column AND legacy 4-column rows; `applyToShip`
  writes `critData = "Damage reduction reduced by 8"`; `describeCriticals` omits the param
  class.
* **DB round trip through the real DBManager:** `submitSavedCrit` stores and `ON DUPLICATE KEY
  UPDATE`s the param, an ordinary crit stores genuine SQL `NULL` (not 0), `getSavedCritsForShip`
  reads five columns, `deleteSavedFleet` still cascades.
* **Node harness over the real `battleDamage.js` + `ajaxInterface.js`, 34 assertions:** JS
  registry mirrors PHP; `setCriticals` collapse/clamp/drop; `filter` carries `p`; three phasing
  crits summing to one row of 12; params summing to nothing dropping the row; `forInfo` /
  deny-listed / transient still filtered; `applyToShip` param + label; `p` surviving
  `JSON.stringify` (the §12.1 trap); and the terrain filter — player-placed terrain refused in
  BOTH the lobby and mid-battle, ordinary ships unaffected.
* **React, 25 assertions:** tree bundles and evaluates; all three changed components render
  under `renderToString`; removing a crit leaves an empty row **in its original position**;
  `+` from zero puts it back; the param ticker edits the param not the count; emptying every
  row still draws all three; **a fresh component instance (menu reopened) still draws them**;
  another system starts clean; the memory is keyed per target and absent from the payload.
* **Replay harness: 154 pass / 1 fail** — the known stale game 4234, which fails identically
  on a clean tree.

**Outstanding: the user's browser test, `yarn build`, and (still) a static ship regen** for the
two §12 `ShipCompactor` changes. `db/prebattleDamage.sql` must be applied to any DB that
already has `tac_saved_crit`; it was applied locally.

---

## 15. Refinement round, 2026-08-08 (fourth pass)

Eleven user requests. The big one is **§11 — critical AUTHORING in the lobby — which is now
BUILT**, so D4 is retired. The other structural change is a **third payload bucket for MINES**.

### 15.1 A THIRD BUCKET: `mne`, for bulk-bought mines

A bulk mine purchase is ONE lobby object carrying `bulkBuy = N`, which `BuyingGamePhase`
mints into N separate `tac_ship` rows — **exactly the "one object plus a number" shape a
flight has** (§1.1). So mines get ordinals, not system ids, for the same reason flights do.

```js
mne: { "1": { d: 3 }, "2": { d: 6 } }        // ordinal 1..bulkBuy
```

* **STRUCTURE ONLY.** Every other system on a mine is `isTargetable = false`, and a mine
  cannot carry a critical worth taking into the next battle — so the entry is `{d}` and
  nothing else (`sanitiseEntry`'s new `$noCriticals` argument strips `c`/`p`).
* **Damaged, never destroyed**, exactly like a fighter (D8): health floors at 1 and a mine
  you lost is one fewer in the bulk.
* `PreBattleDamage::BUCKETS` (and `battleDamage.BUCKETS`) is now **the** list every loop
  walks — `filter`, `contents`, `isEmpty`, `criticalTypesIn`, `sanitiseSavedRows`,
  `toEntries`, and the client twins. Adding a fourth kind is one line, not six greps.
* `toEntries` gained a `$mineOrdinal` argument; `BuyingGamePhase` passes `$m + 1` from its
  bulk loop, so each copy gets only its own row. Null for everything else, which skips the
  bucket entirely.
* **UI: `shipWindow/MineDamageMenu.js`** (new), a sibling of `FighterDamageMenu` — one row
  per copy, opened from the compact window's structure bar. `canApplyPreBattleDamage` now
  excludes mines (`!ship.mine`) and the new `canApplyMineDamage` gates the bar instead, so
  a mine has no per-system menu at all. A mine whose structure is **1** (most proximity
  mines) renders nothing: any damage would destroy it, and a destroyed mine is one you did
  not buy.

### 15.2 Mines are saved and reloaded as BULKS — and the count is finally stored

Two bugs, one cause.

**`tac_saved_ship` had nowhere to put `bulkBuy`.** The client POSTed it and
`getSavedShipsFromJSON` read it, and then `submitSavedShip` dropped it on the floor — so a
saved fleet of ten mines reloaded as **one**. Fixed with a `bulkbuy INT(11) NOT NULL
DEFAULT 1` column (idempotent `ADD COLUMN IF NOT EXISTS` in `db/prebattleDamage.sql`),
written by `submitSavedShip` and read back by `getSavedShips` (mines only — every other
unit is one row per unit and stays that way).

**Saving out of a live game wrote one row per mine.** Mid-battle each mine IS its own ship,
so the survivors saved as N separate units and reloaded as N separate lines instead of the
bulk they were bought as. `ajaxInterface.groupSaveableShips` now regroups live mines by
phpclass into one row, and `summariseGroup` renumbers each copy's structure damage as its
ordinal in that bulk. **Grouped only in a live game** — in the lobby they are already bulk
rows, and merging two separate purchases of the same class would fuse two lines of the
player's own fleet list.

Also fixed in the same funnel: the saved-fleet **points** loop counted a bulk mine row
ONCE, so a fleet's stored `points` was short by `(bulkBuy − 1)` units — and that figure is
what the affordability check compares against on load.

### 15.3 ⭐ §11 IS BUILT — criticals can be added in the lobby

D4 ("no critical authoring in the lobby, for now") is retired. Everything §11.1 predicted
was already in place; what landed is exactly what it said would be needed — one endpoint,
one accessor, and flipping a prop.

* **`ShipSystem::getPossibleCriticalTypes()`** — flattens and dedupes the protected
  `$possibleCriticals` table. The table itself stays protected.
* **`Manager::getSystemCriticals($phpclass, $flightSize)`** + **`source/public/systemCriticals.php`**
  — ONE ship class per request, resolved through `ShipLoader::getShipsByClass` (never
  `new $phpclass` on a client string, never `getAllShipsStatic(null)`), APCu-cached under
  the deploy-versioned prefix.
* **`PreBattleDamage::offerableCriticalTypes($system)`** is the **narrow** question — what
  a player may INVENT here. ⚠️ It is deliberately NOT `isValidCriticalType`, which is the
  wider STORABILITY test; narrowing that one would eat carried combat crits on reload
  (§4.2's seam, and it still holds).
* `allCriticalTypes()` reads the class names out of `cricialClasses.php` — the one file
  that declares them — because the generated classmap autoloader means
  `get_declared_classes()` only ever holds the handful already touched this request.
* **UI**: `CriticalEffectsSection` gained a `<select>` picker plus an **"All"** switch
  (§11.4's "narrow by default with an all-effects expander"). Adding is
  `setValue(row, 1)` — the same door every ticker already used, so the preview, the buy
  POST and the saved-fleet write all follow with no new wiring.
  ⚠️ The picker appears as soon as the CATALOGUE arrives, not when it has something to
  offer: a fighter's own hit chart lists no criticals at all, so gating on that would hide
  the "All" switch that is the only route to them.

### 15.4 Per-class critical LIMITS

Some effects can only sensibly apply once. `PreBattleDamage::$critLimits` /
`battleDamage.CRIT_LIMITS` are a **new mirrored pair, edited exactly like the deny list**;
anything not named there keeps `MAX_CRIT_COUNT`. The server clamps in `sanitiseEntry`, the
client in `setCriticals`, and the editor's `[+]` goes dead at the ceiling.

**How the first pass was derived**, so a future edit can argue with it: every entry is a
critical the game reads as a **flag** — the code asks `if ($system->hasCritical('X'))`,
never multiplies by the count, and the class carries no `outputMod`. Everything left off
either multiplies by its count (`ReducedIniative`, `GunLost`, `ArmorReduced`,
`PenaltyToHit`, `HalfEfficiency`'s `round($used/($crits+1))` …) or stacks through
`effectCriticals`' `outputMod` sum (the `OutputReduced` family, `FieldFluctuations`), so
capping those would change the rules rather than tidy them. The two burnouts and
`OutputHalved` are judgement calls: they stack arithmetically, but "efficiency halved"
twice is not something a B5W sheet can say.

### 15.5 ⭐ `survivesStructureDestruction` never reached the client at all

**Symptom (user report, game 4283):** systems with the special structure-cascade properties
"are not working properly".

**Investigated first.** Both mechanisms were checked against the real data before touching
anything:

* the **array `structureHomeLocation`** path is CORRECT and already lives in the static
  bundle — a Vorlon Light Cruiser's Lightning Cannons (`home = [1, 32]`) fall only when
  BOTH the Front and Port-Aft structures are gone, in server and lobby preview alike;
* the Xill in game 4283 also behaves correctly in both: its only pre-battle rows are the
  two Port structures, and exactly the two Port guns fall;
* **the real defect is `survivesStructureDestruction`: `ssd` is `undefined` on every system
  of every static ship file.** §12.3 delivered it via `ShipCompactor::annotateSystems` —
  correctly — but that only takes effect after a **static ship regen**, which has not been
  run. So on any tree whose bundle is older than that change, a shield projection goes dark
  with its section and nothing says why.

**Fix: stop depending on the bundle's age.** The `systemCriticals.php` response §15.3 adds
is built from a freshly constructed ship, so it carries `ssd` per system as well as the
crit catalogue. `battleDamage.survivesStructureDestruction(ship, system)` asks the
blueprint first and the catalogue second, and `applyToShip` kicks the (de-duplicated,
one-per-ship-class-per-session) fetch and repaints when it lands. The `annotateSystems`
route stays — after a regen the flag is there immediately and no request is needed.

### 15.6 The other seven

| # | Change |
|---|---|
| 1 | **`ApplyDamageMenu` names the system**: `Twin Array #14 [−][ 8 ][+] ☐ Destroy` instead of `Structure / 4`. With several menus open, or on a hull carrying six Twin Arrays, the only thing telling them apart was which icon you had clicked. The id rides along because it is what the payload, `tac_damage` and the blueprint are all keyed by. |
| 2 | **A lobby flight's health bar reads FIGHTER 1**, not the flight total. §5.2's flight-level bar was on a card that is otherwise drawn as a single craft, so "48 / 48" read as a bug. The flight-wide figure stays where it means something — the caption of the per-ordinal menu the bar opens. |
| 4 | **Save Fleet's default name is `<game name> T<turn>`** in a live game (the lobby keeps "Unnamed Fleet" — it has no game name). It is the only thing that tells two saves of the same fleet apart in the dropdown. The value is HTML-escaped into the attribute: it is now user-supplied text in a string that gets parsed as HTML. |
| 7 | **The Ready confirm's pre-battle note is restyled.** It sits inside `confirm.confirm`'s `.confirm.error` shell — 16px bold `#c94b1d` — which shouted the whole sentence. Only `NOTE:` is a warning now (`.prebattle-note-label`, red); the rest is body text on the shared tokens. |
| 8 | **A second SAVE FLEET beside the fleet loader**, so it is reachable without scrolling the whole buy panel on a phone. Same `.savebutton` hook, so the one handler drives both. The three controls in that row (Load a Fleet / Save Fleet / Ready) are now one EQUAL-WIDTH set at 150px — the dropdown's `min-width: 160px` against its own `width: 150px` had quietly made it 10px wider than the Ready beside it. |
| 9 | **Small screens: the top row now matches the bottom of the buy panel.** `gamesNew.css` collapses every `.btn-*-lobby` to auto-width 0.9em at 600px, but `.readybutton-top` is deliberately independent of those classes and kept its fixed 150×27 — so the page showed two visibly different Ready buttons. Same numbers now, in `lobby.css`. |
| 10b | **The `×` on a critical row is gone.** It did exactly what ticking the count to 0 does, and two controls for one action read as two actions. A row dropped to zero still stays on screen (§14.2). The damage block also gained a **`Damage` section header** in the same style as `Critical Effects` (`CritSectionHeader`, exported from that file so the two cannot drift). |
| 11 | **Clicking a lobby ship window's background no longer raises the old ship-level popup** — the pre-redesign hit-chart/notes tooltip, which this window has had dedicated buttons for since Stage 1. It landed on top of whatever damage menu was open. Suppressed in `ShipWindow.onShipClick` rather than in the lobby's event handler, so the click still CLOSES an open menu, which is what clicking the backdrop should do. game.php is untouched — there the popup is the only route to that information. |

Also fixed in passing: **`loadSavedFleet.php` never returned `critTransient`.** Manager has
always produced it, but the response dropped it, so `doLoadFleet`'s third argument was
always empty and no carried one-turn effect was ever tagged "(turn 1 only)" — §13.6's label
had never actually appeared.

### 15.7 Verification

* `php -l` clean on every edited server file.
* **Scratch PHP, 31 assertions**: crit limits clamp (`ReducedArcs` → 1, `OutputReduced1`
  untouched); the whole `mne` lifecycle (ordinals validated against `bulkBuy`, damage
  capped one short of destruction, `k` and criticals stripped, out-of-range ordinals
  dropped, `contents`/`isEmpty`/`filter` seeing the new bucket); `toEntries` emitting ONE
  row per mine ordinal against the mine's Structure and nothing without a `$mineOrdinal`;
  `sanitiseSavedRows` round-tripping kind 2; the catalogue (storable-filtered `all`, per
  system `crits`, `meta` carrying limit/param/transient); and ordinary ships unchanged.
* **Catalogue endpoint**: builds for `Omega` (36 systems with entries), rejects a crafted
  class name and an unknown class, keys a flight by ordinal, and **flags
  `ThirdspaceAttackCraft` system 12 as `ssd`** — the flag the static bundle is missing.
  Both maps are `(object)` cast so a 0-based system-id map cannot arrive as a JSON array.
* **Node harness over the real `battleDamage.js` + `ajaxInterface.js`, 31 assertions**: the
  mine bucket end to end including surviving `JSON.stringify` (the §12.1 trap); crit limits
  and the catalogue override; **the `ssd` cascade — a shield falls without the flag and
  survives with it, while an ordinary system in the same block still falls**; live-mine
  regrouping (three mines → one row of `bulkBuy 3` with damage renumbered 1..3, the
  undamaged copy absent, the ordinary ship untouched); the lobby keeping two separate
  purchases; and the points fix. The JS limit table is asserted **key-for-key against the
  PHP one**.
* **React, 32 assertions**: the tree bundles and every changed component renders under
  `renderToString` — which caught a real bug this round (`ship` used in
  `CriticalEffectsSection.render` without being destructured, invisible to a parse check).
  Asserted: the damage row shows the displayName and the id; the `Damage` header; no `×`;
  the limit-1 ticker's ceiling; the picker hidden before the catalogue and present after,
  offering the hit-chart classes and not the already-drawn ones; the mine menu's one row
  per copy with no Destroy and no Critical Effects; a 1-structure mine rendering nothing;
  and **the fighter bar reading `4 / 6` (fighter 1) rather than the flight total**.
* **Replay harness: 154 pass / 1 fail** — the known stale game 4234, which fails identically
  on a clean tree. Unchanged from the last round, i.e. no payload-shape regression.
* Node `--check` on every edited legacy JS file.

**Outstanding: the user's browser test, `yarn build`, and (still) a static ship regen** —
though §15.5 means the regen is now an optimisation rather than a correctness dependency.
`db/prebattleDamage.sql` must be applied to any DB that already has these tables (it now
also adds `tac_saved_ship.bulkbuy`); it has been applied locally.

---

## 16. Refinement round, 2026-08-08 (fifth pass)

Seven requests. The structural one is **§16.4 — the Vree saucer's outer-structure ring**,
which is a rules change to a whole faction rather than a bug in this feature; it landed
here because pre-battle damage is what made it visible.

### 16.1 The "All" switch stretched the menu to 500px

**Symptom.** Ticking **All** in Critical Effects made `ApplyDamageMenu` "really wide".

**Cause.** A `<select>` takes its intrinsic width from its **longest option**, and the only
ancestor these menus have is `SystemInfoMenu`'s absolutely-positioned tooltip, which is
**shrink-to-fit** (capped at 500px). So swapping a system's handful of hit-chart criticals
for every storable class in the game handed the layout a much longer string to size to, and
it took it — right up to the ceiling.

`flex: 1` and `min-width: 0` on the select were already there and are not the fix: they let
a flex item **shrink**, they do not stop it **asking**. What clamps a max-content
contribution is a `max-width` on the box itself. Both menus now carry one
(`ApplyDamageMenu`'s `Container`, `FighterDamageMenu`'s `Tooltip`, 300px), the section
carries `min-width: 0; max-width: 100%`, and a long critical label wraps
(`overflow-wrap: anywhere`) instead of widening the row. The dropdown *popup* still opens
at its natural width, so nothing is lost by clipping the closed control.

### 16.2 "Destroy" and "All" sat off-centre in their rows

A default `<input type="checkbox">` carries the UA's own `margin: 3px 3px 3px 4px` plus its
own intrinsic box, so its **margin box** is several pixels taller than the word beside it.
`align-items: center` then faithfully centres two items of different heights and the ink
ends up misaligned against the tickers and the value field.

One exported pair, `CheckBox` + `CheckText` (in `CriticalEffectsSection.js`, beside
`CritSectionHeader`, so the two labels cannot drift): margin zeroed, box fixed at 12x12,
text at `line-height: 1`. Both items are then the same height and centring is exact.

### 16.3 The top SAVE FLEET lost its border

`gamelobby.php`'s second SAVE FLEET carried `class="btn savebutton savebutton-top"`.
**`gamesNew.css` is linked AFTER `lobby.css`**, and its `.btn { border: none;
display: inline-block; }` has the *same specificity* as `.savebutton-top`, so it won:
that button rendered with no border and without the `inline-flex` centring, while its twin
at the bottom of the buy panel — which pairs `.btn` with the **later** `.btn-primary-lobby`
in the same file — kept both. Fixed by dropping `btn` from the class list, which is what
`.readybutton-top` beside it has always done; `.savebutton-top` already declares everything
`.btn` was providing. The row stays a 150px equal-width set (§15.6 item 8) on purpose.

### 16.4 ⭐ THE VREE SAUCER RULE — systems survive their structure block

**User report (game 4285):** a Xill's Antiproton Guns were destroyed along with the two
Port structures, and should not have been.

**Investigated before changing anything.** Everything the code was doing matched what the
ship file says, and both mechanisms the report named turned out to be healthy:

* the **array `structureHomeLocation`** path is correct — a Vorlon Light Cruiser's
  `home = [1, 32]` cannons survive Front alone and fall only when Port-Aft goes too, while
  the `[1, 42]` pair stays up;
* **`ssd` DID reach the static bundle** after the user's regen (`Mindriders`, `The System`
  and `Thirdspace` carry it), so §15.5's delivery is done;
* the Xill's guns simply have **no structure home at all** — each is bound to exactly one
  quarter (`#12 -> 31`, `#14 -> 32`) — so destroying both Port blocks correctly took both
  Port guns.

**The real answer is a faction rule, not a home-location fix** (user, 2026-08-08): on a Vree
saucer the six "Outer Structure" blocks are a **ring around** the disc, not the compartment
the systems sit in. So a breached block does **not** destroy what is shown in it — which
makes what the system's structure home *is* academic there, unlike the Vorlon and Kirishiac
hulls where the home is the whole point.

Implemented on the **hull**, not on thirty systems one at a time:

| piece | where |
|---|---|
| `protected $systemsSurviveStructureLoss = false;` | `BaseShip` (`ShipClasses.php`) |
| stamps every non-Structure system it mounts | `BaseShip::addSystem` — every `addXSystem` variant routes through it |
| `ShipSystem::setSurvivesStructureDestruction()` | a **setter**, so the property stays `protected` |
| `= true` | `VreeCapital`, `VreeHCV`, and `Tyllz` (the one Vree hull that descends from neither) |

Stamping at **construction** is what makes it free everywhere: the flag is baked into the
blueprint, so it rides the static ship bundle (`ShipCompactor::annotateSystems`), the
crit-catalogue endpoint's `ssd`, the lobby's damage preview and every live ship, from one
line per hull class. **No client change was needed** — `battleDamage.survivesStructureDestruction`
already asks the blueprint and then the catalogue, and the catalogue is built from a freshly
constructed ship, so the preview is right even before the next static regen.

⚠️ **`$systemsSurviveStructureLoss` is PROTECTED on purpose.** A public ship property would
ride every ship of every gamedata poll to serve the ~1% of hulls that set it, and nothing on
the client needs the hull-level flag — the per-system one already has two delivery routes.
This is the same trap `$survivesStructureDestruction` itself documents (§12.3).

Structures are skipped: a Structure's own destruction rule is the PRIMARY-structure test,
and ship destruction still keys off the primary block alone, so a Vree saucer dies exactly
when it did before.

### 16.5 ONE Critical Effects section for a flight

`FighterDamageMenu` drew a full Critical Effects section — header, rows, picker and "All"
switch — **under every fighter row**. On a flight of six that is six of everything, for six
craft that are identical by construction. There is now **one**, under all the health rows.

It addresses a new reference, **`battleDamage.REF_FLIGHT` (0 — ordinals are 1-based, so it
cannot collide)**, handled inside `battleDamage` rather than in the component, so the single
door criticals enter a payload by stays a single door:

* `getEntry(ship, KIND_FIGHTER, REF_FLIGHT)` returns a **synthetic** union — every class any
  ordinal carries, at the highest count/param any of them carries it at. Nothing is written
  to a `"0"` key; the payload shape is unchanged.
* `setCriticals(…, REF_FLIGHT, …)` fans out to every ordinal through the ordinary
  per-ordinal path, so the deny list, the per-class limits and the param collapse all still
  apply. Each ordinal's **damage** is untouched — that is what the rows above are for.
* `offerableCriticals(…, REF_FLIGHT, …)` is offered ordinal 1's catalogue list.

The trade, stated plainly: a fleet saved out of a real battle can carry different criticals
on different craft; the section shows the worst of them, and editing levels them. That is
what "apply to the flight" has to mean on a card the lobby draws as a single craft.

Consequence: **"Apply Fighter 1 to all" now copies damage only.** It used to copy the whole
entry so that lobby crit authoring would propagate for free (§5.2) — with criticals authored
flight-wide that would do nothing at best, and on a loaded fleet where only the *other* craft
were crit it would silently wipe them.

### 16.6 A saved fleet with mines is refused when the scenario has none

'Allow Mines' is a per-scenario rule and a saved fleet outlives the game it came from, so a
fleet built where mines were allowed happily carried its bulks into a lobby whose buy panel
never offers them (`constructStore` skips mines on the same test). `doLoadFleet` — the one
funnel both load paths come through — now refuses the **whole** load with
*"Saved fleet contains units not available for this scenario"*.

Refusing wholesale rather than dropping the offending units is deliberate: the fleet's stored
`points` counted them, and the affordability check has already approved that figure, so a
partial load would put a fleet on the table that the player never saved.

### 16.7 The damaged badge is a wrench

`fa-heart-crack` -> **`fa-screwdriver-wrench`** in both places that draw it
(`gamedata.damagedShipBadge` for a bought unit, and the saved-fleet dropdown) — a wound a
unit is carrying *into* a battle reads as "needs repair", not as a death. Font Awesome 6.5 is
already linked on both pages, so it inherits the existing badge colour and size. Change both
or neither; the comment on each says so.

### 16.8 Verification

* `php -l` clean on `ShipClasses.php`, `ShipSystem.php`, `Tyllz.php`, `gamelobby.php`.
* **Scratch PHP, 13 assertions:** the Xill in game 4285's exact state — both Port structures
  destroyed, **both Port Antiproton Guns alive**, the structures themselves still destroyed;
  every non-Structure system on the hull flagged and **every Structure not**; a gun destroyed
  *outright* still destroyed (the flag stops the cascade, not direct damage); `Vaarka`,
  `Xixx`, `Tyllz` and `Xorr` fully flagged; **an Omega untouched (0 systems flagged)**; the
  Vorlon LC's array-home cascade still firing and its `[1,42]` pair still surviving; shield
  projections keeping their own hard-coded flag.
* **Catalogue endpoint:** `Manager::getSystemCriticals('Xill', 1)` returns `ssd: true` on all
  19 non-Structure systems, so the lobby preview is correct with no static regen.
* **Replay harness: 154 pass / 1 fail** — the known stale game 4234, which fails identically
  on a clean tree. Unchanged, i.e. the protected flag genuinely stays out of every payload
  (this is the harness's own worked example — see §12.3).
* **React, 20 assertions** under `renderToString` (bundled first, so a missing import throws
  — self-tested against a deliberately broken import): the damage menu's system name and
  `Destroy` in its own element; a `max-width` rule emitted; **exactly ONE "Critical Effects"
  header on a flight of six, drawn after the last fighter row**, showing a critical only one
  ordinal carries; the union taking the highest count; a REF_FLIGHT write reaching every
  ordinal and a REF_FLIGHT clear clearing every ordinal, both leaving per-fighter damage
  intact; **no `"0"` key ever written into the `ftr` bucket**; propagate copying damage while
  leaving another ordinal's critical alone; the mine menu still one row per copy with no
  Critical Effects section.
* `node --check` on every edited legacy JS file.

**Outstanding: the user's browser test, `yarn build`, and a static ship regen** — the regen
now also picks up the Vree flags, though §15.5's catalogue route means the lobby is correct
without it.

---

## 17. Refinement round, 2026-08-08 (sixth pass)

Four requests, all against the crit editor §15.3 built. The structural one is §17.1: the
"All" switch was answering the wrong question.

### 17.1 ⭐ "All" offered 58 criticals; now it offers 12, and systems can add their own

**Symptom (user).** "The 'All' crits checklist is too extensive — certain crits only make
sense for certain systems." Correct: `allCriticalTypes()` scraped every storable class out
of `cricialClasses.php`, so a Reactor was offered *Turret jammed* and an Engine was offered
*Stored ammunition exploded*. Fifty-eight entries is a list, not a picker.

**The second half of the same problem: fighters had NOTHING.** `Fighter::$possibleCriticals`
is empty — a fighter is destroyed rather than critted in play — so the narrow list was blank
for a flight and "All" was the *only* route to any effect at all. Curating "All" without
fixing that would have left flights with no criticals whatsoever.

So the two halves are one change: **narrow the general list, and give each system a place to
name its own extras.**

⭐ **"All" WIDENS the system's own list — it does not replace it.** The first cut had
`battleDamage.offerableCriticals` return the general list *instead of* the per-target one
when the switch was ticked, which was wrong in the obvious way: ticking "All" on a weapon
**hid Gun Lost and Turret jammed**, the two effects most worth authoring there (user report,
same day). It now returns the union, the system's own effects first, deduped — so the switch
can only ever add. Done client-side, where both lists are already in hand; the catalogue
keeps serving them separately because they answer different questions.

**`PreBattleDamage::$generalCriticals`** — a hand-curated array, meant to be edited. Twelve
entries, and the derivation is written on it so a future edit can argue with it: every one is
read **generically**, by code that does not know which system it is looking at. The
`OutputReduced` ladder, `OutputHalved(OneTurn)`, `PartialBurnout` and `SevereBurnout` all
work through `outputMod` / `outputModPercentage`, which `ShipSystem::effectCriticals` sums
for every system; `ForcedOfflineOneTurn` is read by `ShipSystem::isOfflineOnTurn`, likewise
generic. Everything left off is **scoped** to a weapon (`GunLost`, `ReducedArcs`,
`AmmoExplosion`…), to the C&C (`ReducedIniative`, `PenaltyToHit`, `ProfileIncreased` — all
read as `$CnC->hasCritical(...)`), to a thruster (`HalfEfficiency`), to a shield
(`DamageReduction*`) or to one faction's hull (`TendrilDestroyed`, `ShadowPilotPain`).
`OutputReduced` and `OutputReducedOneTurn` are absent for a different reason: they carry no
`outputMod` of their own, so authoring one stores a wound that does nothing.

**`ShipSystem::$preBattleCriticals`** — a new per-system FLAT list of extra classes the
editor may offer here, on top of the hit chart:

```php
protected $preBattleCriticals = array('AmmoExplosion', 'ReducedArcs');
```

It fills the two gaps `$possibleCriticals` cannot: effects real battles produce through
bespoke code that no hit chart lists, and systems with no hit chart at all. `ShipSystem::getPreBattleCriticalTypes()`
returns hit chart ∪ extras, and `PreBattleDamage::offerableCriticalTypes()` reads that.
`getPossibleCriticalTypes()` keeps meaning exactly "what this system's hit chart can roll" —
the two questions are different and only one of them is a rules statement.

**Seeded on `Fighter`, and only with what is verified to work:**

```php
protected $preBattleCriticals = array(
    'ReducedIniative', 'ReducedIniativeOneTurn', 'Uncontrolled', 'tmpinidown');
```

Those four are exactly what `ShipClasses::getIniModifier` reads off `getSampleFighter()` for
a flight (−10 permanent, −10 one turn, −15 one turn, −5 one turn). A flight has no C&C, so
the usual ship-wide criticals genuinely do nothing on one — which is why the list is short
and why the comment tells the next editor to `grep -rn 'hasCritical("Foo"' source/server`
and check the RECEIVER before adding a fifth.

⚠️ They are read off the **sample fighter (ordinal 1) only** — which is independently why
§16.5's flight-wide editor writes criticals to every ordinal rather than per craft.

⚠️ **Neither list is the storability test.** `isValidCriticalType` stays wide; narrowing it
would eat carried combat crits on reload (§4.2's seam, still holding after two rounds of
pressure). And there is **no new mirror pair** — both lists are server-side only and reach
the client through the catalogue endpoint, which the client already treats as authoritative.

⚠️ **The catalogue is APCu-cached** under the deploy-versioned prefix, and the deploy version
is the *mtime of the legacy bundle*. Editing a `$preBattleCriticals` table in PHP alone does
not bump it, so a browser can keep seeing the old list for up to an hour; `yarn build`
rewrites the bundle and orphans the cache, which is the normal path.

### 17.2 An added critical was labelled by its class name

`battleDamage.critLabel` consulted `critDesc` — the server's `describeCriticals` map — and
then fell straight through to the raw class name. `critDesc` only ever holds the classes a
LOADED FLEET actually carried, so a critical the player had just **added** in the lobby had
never been in it: the row read `OutputReduced1` while the dropdown it came from read "Output
altered by -1".

`critLabel` now tries three sources in order: `critDesc`, then the **catalogue's `meta`**
(which names every class the picker can offer), then the class name. `pickerLabel` delegates
to it as well, so the option and the row it becomes cannot disagree.

### 17.3 The checkbox nudge that survived §16.2

`base.css` carries a **global** `input[type="checkbox"] { position: relative; top: 2px; }`
that shifts every checkbox in the app down two pixels. `input[type=…]` is specificity 0,1,1;
a styled-components class is 0,1,0 — so the global rule beat §16.2's fix and the offset
survived it. `&[type='checkbox'] { top: 0 }` inside the styled component is 0,2,1 and takes
it back, as an explicit value rather than an `!important` so the ordinary rule keeps applying
to every other checkbox on the page.

Worth remembering generally: **a bare styled-components class loses to any `[attr]`-qualified
global selector.** Anything in `base.css` written as `tag[type=…]` outranks a styled
component unless the component qualifies itself the same way.

### 17.4 A system destroyed in the lobby could not be un-destroyed

`SystemIcon.clickSystem` had the exception right all along — its destroyed bail lets
`canApplyPreBattleDamage` through precisely so you can take back what you just destroyed.
But `SystemIcon.render` returns **early**, before that, with a bar-only `<System>` carrying
**no `onClick` at all**, so the click never reached the handler. The two guards disagreed and
the render won.

The early return now also passes `canApplyPreBattleDamage`, matching `clickableWhenDestroyed`
(the destroyed Kirishiac Orbital, which has always kept its interactive render for the same
kind of reason). Structure was never affected — it is clicked through the section header bar,
which has no destroyed gate.

One consequence handled in the same edit: the interactive render draws its health bar from
`getStructureLeft`, which reads *damage*. A system destroyed by the **structure cascade**
carries no damage of its own, so it would have drawn a FULL bar behind the destroyed blur.
The bar is now explicitly 0 when destroyed, matching the bar-only render it replaces.

### 17.5 Verification

* `php -l` clean on `PreBattleDamage.php`, `ShipSystem.php`, `fighter.php`.
* **Scratch PHP, 20 assertions:** the general list is 12 entries, all storable, all carrying
  a genuinely generic effect (asserted by probing `outputMod`/`outputModPercentage`, with
  `ForcedOfflineOneTurn` allowed through by name); no weapon- or C&C-scoped effect leaked in;
  the two no-op `OutputReduced`/`OutputReducedOneTurn` are absent; an ordinary system still
  offers exactly its hit chart; **an Aurora fighter now offers the four verified flight-scoped
  criticals**; the catalogue keys all 3 ordinals with identical lists and names every one of
  them in `meta`; an authored fighter crit survives `sanitise` and expands to a real turn-0
  `Critical`, while a transient one is stamped at `TRANSIENT_TURN`.
* **React/Node, 36 assertions** (all of §16.8's, plus): **"All" ticked returns a strict
  SUPERSET of unticked** — the system's own `GunLost`/`ReducedArcs`/`AmmoExplosion` still
  present, the general ones added on top, a class in both lists appearing once, and the same
  through a flight's REF_FLIGHT branch (self-tested: reverting to the replace behaviour
  fails three of them); an added crit whose
  class only the CATALOGUE knows renders its description and not its class name; `critDesc`
  still wins when it has the class; an unknown class still degrades to its own name; the
  `[type=checkbox]`-qualified `top: 0` rule is emitted; **a destroyed lobby system keeps its
  interactive render while the same system in a real game keeps the bar-only one**. That last
  pair was self-tested by reverting the fix — it fails without it.
* **Replay harness: 154 pass / 1 fail** — the known stale game 4234. The new
  `$preBattleCriticals` is `protected`, so nothing reached a payload.
* `node --check` on every edited legacy JS file.
