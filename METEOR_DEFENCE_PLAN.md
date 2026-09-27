# Meteor Defence — declared defensive fire against Meteor Swarms

Lets a player **declare, during Initial Orders, which intercept-capable weapons will defend against
meteors this turn**. When the unit then enters a Meteor Swarm, the declared weapons reduce each
meteor's damage by their intercept rating. A declared weapon is committed for the whole turn: it cannot
fire or intercept in the Fire Phase, whether or not a meteor ever arrives.

Status: **PLANNED — nothing built; every decision settled.** Written 2026-09-27, after an automatic
version was built and then removed (§0.1).

## The rule (B5W)

> "Defensive weapons that face the direction of motion can be set up to defend against meteors during
> movement. This must be announced at the start of the turn, and if done, the weapons cannot be used
> for any other purpose (either offensive or defensive) during that turn — including defense against
> enemy weapons fire. If a weapon is defending against meteors, it must be allocated to a specific
> meteor strike, and will block only that meteor. Multiple defensive weapons can fire at the same meteor
> with no degradation. The decision to fire must be made at the moment the meteor is discovered to be
> inbound (you cannot wait until all movement is complete, then choose which meteors to shoot at). If
> weapons are targeted on a meteor, all damage caused by that meteor is reduced by the total
> interception rating of all weapons used against it."

FV's rating is the d20 figure, `getInterceptRating()` — a fifth of the percentage the client shows
(an intercept of −10% blocks 2 damage).

## Decisions

Settled (user, 2026-09-27):

| # | Question | Ruling |
|---|---|---|
| S1 | How does a player commit weapons? | **Per weapon, in Initial Orders** — the rules' "announced at the start of the turn" |
| S2 | Which weapons may be declared? | **Any valid interceptor, by the normal identification** — ships' guns, fighters' guns, and Interceptor missiles |
| S3 | What does defence do? | Reduces **the meteor's damage**, not its chance to hit. The meteor still strikes |
| S4 | Why not automatic? | An automatic choice in Pre-Firing either takes guns the player wanted in the Fire Phase, or gives the defence away free. See §0.1 |

Also settled (user, 2026-09-27 — all eight recommendations accepted as written):

| # | Question | Ruling |
|---|---|---|
| D1 | Is a declared weapon committed for the turn even if no meteor arrives? | **Yes** (the rules). Otherwise declaring everything is free insurance |
| D2 | How are declared guns shared between meteors? | **Automatically, best rating first, one meteor per gun, stopping once that meteor is fully cancelled** — so a swarm's later meteors, and a second swarm, still find defenders. A player-set priority is not worth the UI |
| D3 | Can the opponent see the declaration? | **Hidden from enemies until Pre-Firing resolves.** The combat log then reveals what defended. The owner always sees it |
| D4 | Does declaring cost a slow weapon its charge? | **Yes** — it falls out of `firedOnTurn` (§2.4) and matches a weapon that intercepted |
| D5 | A split-shot weapon (e.g. a Twin Array that can split) | **The whole weapon is committed** — all its guns defend, none fire |
| D6 | Which bearing decides "faces the direction of motion"? | **The unit's motion into the swarm's hex, relative to its facing** — not the centreline of the struck section. The rules say "direction of motion" |
| D7 | When is the button offered? | **Only when a Meteor Swarm is on the map** (`MeteorSwarm` or the Triad's `spawnMeteoroid`) |
| D8 | When is an Interceptor missile spent? | **Only when that gun is actually used against a meteor**, not when declared |

---

## 0. Where this starts from

### 0.1 History

An automatic version was built on 2026-09-27, taking defenders from
`Firing::getUnassignedInterceptors`, then removed the same day. Meteors resolve in **Pre-Firing**,
before anyone declares fire, so the automation spent guns the player meant to use in the Fire Phase.
Its allocation code — the motion bearing, best-first allocation, `canInterceptAtAll` per gun,
`fireDefensively` — was sound. Only the choice of defenders was wrong. The full diff is saved at
`c:\tmp\dust_meteor_with_defence.patch`, and §7 sketches the version this plan needs.

### 0.2 What already exists and can be reused

- **Phase-1 fire orders.** Ballistic launches are declared in Initial Orders, validated
  (`Firing::validateFireOrders`, [firing.php:8](source/server/handlers/firing.php#L8)) and written
  (`InitialOrdersGamePhase::process`, [InitialOrdersGamePhase.php:348](source/server/Phase/InitialOrdersGamePhase.php#L348)).
- **Marker orders.** `selfIntercept` is a no-target "permission" order a weapon carries. The client
  builds it in `weaponManager.onDeclareSelfInterceptSingle`
  ([weaponManager.js:3648](source/public/client/weaponManager.js#L3648)), with an "all similar
  weapons" variant on right-click. Meteor Defence copies this shape.
- **"This weapon is spoken for" is already one test.** `Weapon::firedOnTurn`
  ([weapon.php:873](source/server/model/weapons/weapon.php#L873)) is true for any non-`selfIntercept`
  order this turn. Because of that:
  - automated interception leaves the weapon alone (`getUnassignedInterceptors` →
    `isValidInterceptor`);
  - manual interception refuses it (`validateManualIntercept` counts it as an offensive order);
  - the weapon's loading resets as if it had fired
    ([weapon.php:1277](source/server/model/weapons/weapon.php#L1277)).
- **The meteor pipeline.** `RammingAttack::beforePreFiringOrderResolution`
  ([specialWeapons.php:2450](source/server/model/weapons/specialWeapons.php#L2450)) creates a
  `MeteoroidCollision` order per swarm entered. `resolveMeteors`
  ([specialWeapons.php:3247](source/server/model/weapons/specialWeapons.php#L3247)) rolls the swarm's
  chart and resolves each meteor as its own Standard hit through `getDamage`'s one-meteor branch
  ([specialWeapons.php:3156](source/server/model/weapons/specialWeapons.php#L3156)). **Defence slots
  in there, per meteor.**
- **Pre-Firing now saves criticals and individual notes**, so the side effects of firing defensively
  persist: a drawn Interceptor round (an `AmmoUsed` note) and a backlash critical (EM Pulsar, Surge
  Blaster, a boosted Repeater or Graviton Pulsar).

---

## 1. The design

### 1.1 The declaration

A new fire-order type, **`meteorDefence`**, made in Initial Orders:

| Field | Value |
|---|---|
| `type` | `meteorDefence` |
| `shooterid` / `targetid` | the unit's own id (as `selfIntercept`) |
| `weaponid` | the weapon |
| `calledid` | −1 |
| `firingMode` | the weapon's current mode; for an ammo launcher, its **Interceptor** mode (the client's `canWeaponInterceptAtAll` path) |
| `shots` | the weapon's `guns` — how many meteors it can take |
| `shotshit` | 0 when declared; **the meteors it actually blocked**, written in Pre-Firing (§1.3) |
| `x` / `y` | `"null"` |
| `damageclass` | `MeteorDefence` |

**Eligibility** is the normal intercept identification, with the consent rule removed, because the
declaration *is* the consent:

- own unit, not destroyed, not a docking rider or a jumping Ancient;
- a Weapon with `intercept >= 1`, or an ammo launcher with Interceptor rounds in its magazine;
- loaded, online, not destroyed, not stowed;
- no other order this turn — a missile rack cannot both launch and defend;
- a Meteor Swarm is on the map (D7).

### 1.2 What a declaration blocks

Everything, for the rest of the turn (D1). Most of this is free, because `firedOnTurn` is already the
gate for both kinds of interception and for loading. The pieces that are not free are traps T2, T5 and
T6 in §4.

### 1.3 Resolution in Pre-Firing

Per meteor, inside `getDamage`'s `MeteoroidCollision` branch — the same seam the removed code used:

1. The **candidates** are the weapons on the struck unit with a `meteorDefence` order this turn, and:
   - still not destroyed (an earlier collision this phase may have taken one out);
   - arc covering the motion bearing (D6, T9);
   - with a gun left (`shots − shotshit > 0`);
   - passing `canInterceptAtAll` (the Interceptor magazine check).
2. **Allocate best rating first, one gun per step, until this meteor's damage is cancelled** (D2).
   For each gun spent: add its rating, increment the marker's `shotshit`, and call
   `fireDefensively($gamedata, $meteorOrder)`, which draws the round and applies any backlash.
3. Damage = max(0, meteor damage − total rating). A fully cancelled meteor writes no damage entry.
4. Mark each marker used this way `->updated = true`. `PreFiringGamePhase::advance` already calls
   `updateFireOrders`, so what each weapon blocked persists, and **the marker itself is the per-turn
   gun ledger**. That replaces the removed version's static array.
5. The meteor order's pubnote says what defended, e.g. "Defensive fire against meteors: 3 weapons
   reduced meteor damage by 14".

The Triad's doubling still applies before defence ("all damage caused by that meteor is reduced").

### 1.4 Flights

A flight's declared fighter weapons defend the flight: its meteors are resolved against the flight, not
a named craft. One declaration per weapon, with right-click declaring the same weapon on every craft,
as `onDeclareSelfInterceptSingleAll` already does for flights.

---

## 2. Server changes

### 2.1 Persist it — [DBManager.php:1530](source/server/controller/DBManager.php#L1530)

`submitFireorders` writes **only** `ballistic` orders in phase 1. Admit `meteorDefence` in phase 1 when
`addToDB` is set, and **skip it in every other phase**. The client re-posts every order it holds in the
Fire Phase, and only non-new ballistics are skipped there today (T4). This is the same one-phase rule
`prefiring` has for phase 5.

### 2.2 Validate it — `Firing::validateFireOrders`

Add a `meteorDefence` branch, using the same reject-and-detach convention as the vortex declaration:

- phase 1 only (`$gamedata->phase`);
- the §1.1 eligibility, judged on the DB copy of the ship (T10);
- at most one per weapon — judged on the POSTed list, as `validateVortexDeclaration` does;
- a Meteor Swarm present in `$gamedata->ships`.

Add a **defence-in-depth check for phases 3 and 5**: drop any offensive or intercept order from a
weapon that carries a `meteorDefence` order this turn. The client should never send one (§3), but the
server is the authority.

### 2.3 Skip it everywhere a shot is gathered

`preparePreFiring`, `firePreFiringWeapons`, `prepareFiring` and `fireWeapons` exclude orders by type,
and **treat any type they do not know as a shot** (T5). Either add `meteorDefence` to each type test,
or — better — follow `isHyperspaceLogOrder` ([firing.php:1726](source/server/handlers/firing.php#L1726)),
which "four gathers consult", with a sibling `Firing::isDeclarationOnly($fire)`. Also:
- the replay harness's `tohit` gather ([replayHarness.php:432](tests/replay/replayHarness.php#L432));
- `combatLog.groupByShipAndWeapon` ([combatLog.js:952](source/public/client/combatLog.js#L952)),
  which skips `intercept` / `selfIntercept`.

### 2.4 Commit the weapon for the turn

- `firedOnTurn` already covers automated interception, manual interception and loading — no change.
- **Split-shot weapons:** `getUnassignedInterceptors`, `isValidInterceptor` and
  `validateManualIntercept` count orders against `guns`, so one marker would leave `guns − 1` free
  (T6). A `meteorDefence` order must consume every gun (D5).

### 2.5 Keep it visible to its owner, hidden from enemies — [TacGamedata.php](source/server/model/TacGamedata.php)

- The Fire Phase strip at [TacGamedata.php:1693](source/server/model/TacGamedata.php#L1693) removes
  **every** current-turn order on a non-ballistic weapon, for **every** viewer, owner included (T2).
  Exempt `meteorDefence`, as `jumpexit` already is. Otherwise the owner's client forgets the weapon is
  committed and offers it for fire.
- The Initial Orders strip at [TacGamedata.php:1720](source/server/model/TacGamedata.php#L1720) removes
  current-turn orders on **ballistic** weapons for every viewer (T3), and missile launchers are
  ballistic. Exempt the marker for the owner. The re-insert risk that strip guards against is already
  closed by §2.1.
- Add a rule hiding current-turn `meteorDefence` orders from non-allies in phases 1, 2 and 5 (D3).

### 2.6 Record the motion bearing (T9)

Put the removed `brg:N` note back on the `MeteoroidCollision` order: the unit's motion into the swarm's
hex, relative to its facing, measured **to the hex entered**. `checkForCollisions` already measures Dust
entries this way (`$hexEntries`); meteors need the bearing as well as the location.

---

## 3. Client changes

- **Eligibility and actions in `weaponManager.js`**: `canDeclareMeteorDefence(ship, weapon)`,
  `onDeclareMeteorDefence`, `onDeclareMeteorDefenceAll` (right-click: every similar weapon on the unit,
  every craft for a flight) and `removeMeteorDefence`. Modelled on the `selfIntercept` family
  ([weaponManager.js:3632-3738](source/public/client/weaponManager.js#L3632)), with
  `gamedata.gamephase === 1` in place of 3.
- **"Is a Meteor Swarm on the map?"**: a `gamedata` helper matching on phpclass (`MeteorSwarm`,
  `spawnMeteoroid`), as `BallisticIconContainer`'s `FIELD_TERRAIN_CLASSES` does. Don't rely on
  `isMeteoroid`: only `MeteorSwarm`'s static blueprint carries it (T13).
- **Buttons in `SystemInfoButtons.js`**: "Meteor Defence" and "Remove Meteor Defence", next to the
  self-intercept pair ([SystemInfoButtons.js:248](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L248),
  L577-578), plus the `canWeaponMenu` / any-button lists near L861 and L978. **Any `weaponManager` call
  reachable in the lobby needs the stub in `gamelobby.php`, or a phase guard** (T12).
- **Show the commitment.** `SystemIcon` should mark a declared weapon distinctly — not as "firing", which
  would mislead — and the Fire Phase must refuse to select it. `hasFiringOrder` probably already
  refuses; verify.
- **Combat log**: skip markers in `groupByShipAndWeapon` (§2.3). The meteor collision entry's pubnote
  carries the result. `doShortLogText` needs nothing.
- **Help**: a paragraph in the *Interception* section of `faq.php` (`#interception`). Add a line to the
  Meteor Swarm notes once it is built.

---

## 4. Traps found while planning

| # | Trap | Where |
|---|---|---|
| T1 | Phase 1 writes **only** `ballistic` orders; a new type is silently dropped | [DBManager.php:1530](source/server/controller/DBManager.php#L1530) |
| T2 | The Fire Phase payload strips every current-turn non-ballistic order **for the owner too**, so the owner's client would offer a committed weapon for fire | [TacGamedata.php:1693](source/server/model/TacGamedata.php#L1693) |
| T3 | The Initial Orders payload strips current-turn orders on **ballistic weapons** for everyone, missile racks included | [TacGamedata.php:1720](source/server/model/TacGamedata.php#L1720) |
| T4 | The client re-posts every order in the Fire Phase; without a phase-1-only rule the marker is inserted again | [DBManager.php:1527-1537](source/server/controller/DBManager.php#L1527) |
| T5 | The four firing gathers treat an unknown order type as a shot — a marker would roll to hit and "fire" | [firing.php:1477](source/server/handlers/firing.php#L1477), [:1561](source/server/handlers/firing.php#L1561), [:1736](source/server/handlers/firing.php#L1736), [:2061](source/server/handlers/firing.php#L2061) |
| T6 | Split-shot weapons count orders against `guns`, so one marker frees `guns − 1` for interception or fire | `getUnassignedInterceptors`, `isValidInterceptor`, `validateManualIntercept` |
| T7 | `fireDefensively` has side effects: rounds (notes), backlash (criticals), flak cannons scanning the intercepted order's shooter. The first two are now saved by Pre-Firing; the flak scan is safe, because a terrain order's weapon is `doNotIntercept` | the overrides listed in §0.2 |
| T8 | A declared slow weapon loses its charge next turn (`firedOnTurn`) — intended (D4), but say so in the help text | [weapon.php:1277](source/server/model/weapons/weapon.php#L1277) |
| T9 | For multi-hex terrain, `checkForCollisions` measures its original bearing to the terrain's **centre** hex. Meteor Swarms are single-hex today, but measure to the hex entered anyway | [specialWeapons.php:2823](source/server/model/weapons/specialWeapons.php#L2823) |
| T10 | Initial Orders validation sees the **DB** ship, and the POSTed orders separately. "One per weapon" must read the POSTed list, as the vortex validation does | memory `arch_post_side_ship_reconstruction` |
| T11 | Don't let the marker be created with id −1 at resolution time. It is written in phase 1, so Pre-Firing loads it with a real id and can update it | — |
| T12 | The lobby has its own inline `weaponManager` stub; a new call reachable there kills the lobby ship window | `gamelobby.php` |
| T13 | `isMeteoroid` reaches the client only through `MeteorSwarm`'s static blueprint; the spawned Triad class is not in the statics | `static/json/Terrain.json` |

---

## 5. Stages

Each stage leaves the game playable. Run the replay harness after each server stage; it should stay
byte-identical, because no game in the corpus carries the new order.

0. **Inert plumbing (server).**
   - The skip helper (§2.3), wired into all four gathers and the harness.
   - `submitFireorders` phase-1 admission and phase-1-only rule (§2.1).
   - The masking exemptions and enemy hiding (§2.5).
   - Nothing emits the order yet.
1. **Validation (server).** The `meteorDefence` branch and the phase 3/5 defence-in-depth drop (§2.2),
   plus split-shot gun consumption (§2.4). Test with a hand-built POST.
2. **Resolution (server).** Put back the motion bearing (§2.6) and marker-driven allocation (§1.3, §7),
   with usage recorded on the marker. Test with the scratch-script method: game 4425, synthetic moves,
   forced Dice, rolled back — see memory `project_dust_meteor_rules`.
3. **Declaration UI (client).** The buttons, eligibility, right-click-all, remove, the committed-weapon
   indicator, the Fire Phase refusal, and the lobby stub.
4. **Log and help.** Combat log grouping skip, `faq.php`, Meteor Swarm notes.
5. **Live test** (§6).

---

## 6. Test matrix

1. A ship declares two forward guns and flies into a swarm head-on: meteor damage is reduced, those
   guns can't be selected in the Fire Phase, and automated interception leaves them alone.
2. A declared gun whose arc doesn't cover the direction of motion: it doesn't defend, and is still
   committed (D1).
3. An undeclared gun: never used against meteors, fires normally.
4. A Korlyan Cancar declares both Class-D racks: a round is drawn only for guns actually used (D8),
   and the magazine count is right next turn.
5. A fighter flight declares its guns (right-click all): they defend the flight.
6. Two swarms in one turn: guns used on the first aren't available to the second.
7. A declared weapon destroyed by an earlier collision in the same Pre-Firing: it doesn't defend.
8. An EM Pulsar declared and used: its backlash critical persists.
9. Opponent's view: no markers in phases 1, 2 or 5; the result appears in the log after Pre-Firing.
10. A missile rack that launched in Initial Orders: the Meteor Defence button isn't offered.
11. No Meteor Swarm on the map: the button isn't offered.
12. Replay harness byte-identical; the replay of the test game shows the collision entry and its note,
    and no "shot" from a marker.

---

## 7. Resolution sketch

The removed `defendAgainstMeteor`, adapted to markers. Illustrative, not final.

```php
private function defendAgainstMeteor($target, $meteorOrder, $meteorDamage, $gamedata){
    $bearing = preg_match('/brg:(\d+)/', $meteorOrder->notes, $m) ? (int)$m[1] : 0;
    $guns = array();
    foreach (self::getMeteorDefenceMarkers($target, $gamedata->turn) as $marker) {   //ship systems, or each craft's for a flight
        $weapon = $target->getSystemById($marker->weaponid);
        if (!$weapon || $weapon->isDestroyed()) continue;
        $useSplitArcs = $weapon->splitArcs && !$weapon->isArcRestricted();
        if (!mathlib::isInAnyArc($bearing, $weapon->startArc, $weapon->endArc,
            $useSplitArcs ? ($weapon->startArcArray ?? []) : [], $useSplitArcs ? ($weapon->endArcArray ?? []) : [])) continue;
        $rating = $weapon->getInterceptRating($gamedata->turn);
        for ($g = $marker->shotshit; $g < $marker->shots; $g++) $guns[] = array('marker' => $marker, 'weapon' => $weapon, 'rating' => $rating);
    }
    usort($guns, function ($a, $b) { return $b['rating'] <=> $a['rating']; });

    $reduction = 0;
    foreach ($guns as $gun) {
        if ($reduction >= $meteorDamage) break;
        if (!$gun['weapon']->canInterceptAtAll($gamedata, $meteorOrder, $this->unit, $target, $target, $this)) continue;
        $reduction += $gun['rating'];
        $gun['marker']->shotshit++;           //the marker IS the ledger
        $gun['marker']->updated = true;       //persisted by PreFiringGamePhase's updateFireOrders
        $gun['weapon']->fireDefensively($gamedata, $meteorOrder);
    }
    return $reduction;
}
```

⚠️ Set a launcher to the marker's `firingMode` before `canInterceptAtAll` and `fireDefensively`, and put
its mode back afterwards. Both read the current mode to find the Interceptor round, as
`automateIntercept` does for manual intercept orders.
