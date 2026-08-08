# Pre-Battle Damage & Fleet Damage Persistence — Implementation Plan

Status: **BUILT 2026-08-07, stages 0–6 complete. First browser play-through 2026-08-08 found
seven defects (§12, all fixed); a second pass added six refinements (§13, all done) — including
the temporary/marine critical filter and lobby editing of carried criticals; a third pass added
three more (§14) — terrain is never saved, removed criticals keep their row, and
PARAM-CARRYING criticals are now carried between battles.**
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
