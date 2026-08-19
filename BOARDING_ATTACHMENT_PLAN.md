# Boarding Attachment Rules — Implementation Plan

Scope: three related changes to Breaching Pods (`Marines`) and Grappling Claw ships, plus four
defects found while reading and an FAQ update.

1. Per-**structure-section** attachment limits — server + front end.
2. An opt-out so weapons that must never damage ships (Plasma Web etc.) stop spawning the
   automatic "hit the host too" fire order when they damage an attached pod, **and** a separate fix
   for the same block double-applying terrain collision damage.
3. Auto-ram when a unit fails to attach to an Enormous unit.
4. Four defects (§4) and the FAQ (§5).

Status: **AGREED — not started.** Build order and commit split at the bottom.

Remaining judgement calls, each with a default already encoded — change any of them before §1 is built
and the rest of the plan is unaffected:

| | Default encoded | Alternative |
|---|---|---|
| Claws on bases / Enormous | skip the hull-wide cap, 1 per section only (§1.2 branch C) | inherit the capital cap of 2 on opposite ends |
| Claw onto a section already holding pods | allowed (preserves today's behaviour) | blocked, making §1.2 branch B symmetric |
| Auto-ram trigger | any unattached "Attaches" unit co-located with an Enormous unit (§3) | only units whose attach attempt this turn actually failed |

---

## 0. What the code does today (verified, not assumed)

### Attachment state
`$ship->hasAttached[shooterId] = location` and the mirror `$shooter->attached[hostId] = location`
([ShipClasses.php:164](source/server/model/ships/ShipClasses.php#L164)). Both are rebuilt from CnC
`IndividualNote`s on every load in
[CnC::onIndividualNotesLoaded](source/server/model/systems/baseSystems.php#L3092), note format
`"shooterId=>location"` or `"shooterId=>location:facing"`. Both are serialised to **every** viewer
([ShipClasses.php:709-712](source/server/model/ships/ShipClasses.php#L709)) — no team masking — so
the client can compute occupancy for enemy hulls.

The **key** is the attaching *unit* (a whole `FighterFlight`, or a claw ship), never an individual pod.
Every breaching-pod flight in the game is `maxFlightSize = 2` (checked all 30 of them), so
"2 pods per section" is effectively "one full flight per section". **There is no way to record a
partial attachment** — a flight attaches whole or not at all. That constraint drives the LCV/OSAT
decision in §1.4.

### The current limits
| Site | Rule |
|---|---|
| [`Marines::checkAttachedAmount`](source/server/model/weapons/specialWeapons.php#L7845) | Refuses if a **claw ship** already holds the chosen location; then a whole-ship pod total keyed on `shipSizeClass`: `>3 → 12`, `3 → 8`, `2 → 4`, `1 → 2`, LCV/OSAT → blocked once 2+ pods present. **No per-section pod cap at all.** |
| [`Marines::checkMissionAmount`](source/server/model/weapons/specialWeapons.php#L7907) | Same size-class table, applied to marine *missions delivered this turn* (`Marines::$boardedThisTurn`). |
| [`GrapplingClaw::checkAttachmentLimits`](source/server/model/weapons/specialWeapons.php#L8369) | Refuses a second claw on the same section; then `sizeClass 1/2 → 1 claw total`, `3 → 2 claws and they must be on opposite sections`, `>3 → 1 per section`. |

`shipSizeClass == 4` is **never used** — grep returns nothing. `Enormous` is a per-ship boolean
(bases, Babylon 5, Explorer, Kraken…), and those hulls are `shipSizeClass 3`, so today they land in
the "8 pods / 2 opposite claws" branch. That is the one place the existing claw rules misfire: a
six-section base has locations 1/41/42/2/32/31, none of which match the hard-coded 1↔2 / 3↔4 opposite
pairs, so the "opposite ends" test silently passes and any two sections are accepted.

### Sections are already modelled properly — we just aren't using it
[`BaseShip::getStructureSystem($location)`](source/server/model/ships/ShipClasses.php#L2966) returns
the Structure at that location and **falls back to Primary (0) when the location has no Structure of
its own**. That single call is the whole rule engine:

| Hull | `getLocations()` | Structures present | Distinct sections | Pod cap |
|---|---|---|---|---|
| MediumShip | 1, 2 | Primary only (`addPrimarySystem(new Structure(...))`) | {0} | 2 |
| LCV / OSAT | 1,2 / 0 | Primary only | {0} | **1 unit — see §1.4** |
| HeavyCombatVessel | 1, 2 | Fwd + Aft + Primary | {1,2,0} | 4 (+2 primary) |
| BaseShip (capital) | 1,2,3,4 | F/A/P/S + Primary | {1,2,3,4,0} | 8 (+2 primary) |
| Stormfalcon (`BaseShipNoAft`) | 1,3,4 | Fwd/Port/Stbd + Primary | {1,3,4,0} | **6** (+2 primary) |
| StarBaseSixSections (Pirocia) | 1,41,42,2,32,31 | 6 outer rings + Primary | 6 sections | 12 (+2 primary) |
| StarBaseFiveSections | 5 sections | 5 + Primary | 5 | 10 (+2 primary) |

That reproduces every number in the spec, per hull, with no table to maintain — including the Drazi
Stormfalcon case, which no `shipSizeClass` table can express.

### Primary section (0)
`getHitSection()` already returns **0** when the facing structure was destroyed as of `turn-1`
([ShipClasses.php:3365-3368](source/server/model/ships/ShipClasses.php#L3365)). So "Primary is only
reachable once the facing exterior structure is gone" needs **no new code** — it just needs section 0
to be a normal key with a cap of 2, which the resolution above gives for free.

### Auto-ram
[`RammingAttack::beforePreFiringOrderResolution`](source/server/model/weapons/specialWeapons.php#L2450)
scans co-located Enormous units and creates ram fire orders. Line **2529** short-circuits it:

```php
if($shooter->hasSpecialAbility("Attaches") && !$shooter instanceof Terrain) continue;
//ignore pods/grapple ships for now as we assume they are attaching.
```

Pre-Firing runs a **whole phase before** the attach roll, hence the blanket assumption.

### The host-spill fire order (task 2's culprit)
[`Firing::fire`](source/server/handlers/firing.php#L1304): whenever the target is an *attached
FighterFlight*, it manufactures a second, auto-hit (`needed=100, rolled=1`) fire order against the
host and fires the same weapon at it. Only `ammunition` is saved/restored around the extra shot.

---

## 1. Attachment limits

### 1.1 Server: shared helpers

Add `public static` methods to **`Marines`**, not a new class. `Marines::$boardedThisTurn`,
`Marines::getAttachedPodCount` and `Marines::recordBoarding` are already the shared home — both
`GrapplingClaw` and the Trek `Transporter` ([customTrek.php:3019](source/server/model/weapons/customTrek.php#L3019))
call into it. Keeping it there means **no new autoload entry** and no `phpab` regen.

```php
//Section key for an attachment: the location of the Structure that actually covers it.
//getStructureSystem() falls back to Primary, so an MCV/LCV/OSAT collapses 1 and 2 onto 0
//and a Stormfalcon simply has no section 2. Null only if a hull has no Primary at all.
public static function resolveAttachSection($target, $location){
    $struct = $target->getStructureSystem((int)$location);
    return ($struct === null) ? 0 : (int)$struct->location;
}

//sectionKey => array('pods' => int, 'claws' => int, 'units' => int), plus hull totals
//('clawTotal', 'unitTotal', 'clawSections'). $skipId excludes the unit being tested.
public static function getSectionOccupancy($target, $gamedata, $skipId = -1){ ... }

//Slots this unit consumes on its section: live pods for a flight, 1 (the whole section) for a claw ship.
public static function getAttachFootprint($unit){ ... }

//Single-hull special case: an LCV or OSAT supports one attached craft, full stop.
public static function isSingleAttachHull($target){
    return ($target->hangarRequired == 'LCVs' || $target instanceof OSAT);
}

//The one gate both weapons call. $reason is filled with the pubnotes text.
public static function isAttachBlocked($target, $gamedata, $shooter, $location, &$reason){ ... }
```

### 1.2 `isAttachBlocked` — the rule, in order

```
section = resolveAttachSection(target, location)
occ     = getSectionOccupancy(target, gamedata, shooter->id)     // skips the shooter itself
isClaw  = !($shooter instanceof FighterFlight)

// A. LCV / OSAT — one attached craft on the whole hull, pod or claw
if isSingleAttachHull(target):
    if occ.unitTotal >= 1 -> BLOCK "This unit can only support a single attached craft."
    else -> ALLOW                       // a whole 2-pod flight still fits; see §1.4

// B. A claw ship holds its section exclusively — pods may never join it
if occ[section].claws >= 1 -> BLOCK "A Grappling Claw already holds this section."

if isClaw:
    // C. whole-hull claw caps (B5W)
    if target->base || target->Enormous:
        // no hull-wide cap; the per-section rule below is the only limit
    elif target->shipSizeClass <= 2:                       // medium ship or HCV
        if occ.clawTotal >= 1 -> BLOCK "Only one vessel may grapple a medium ship or HCV."
    else:                                                   // capital
        if occ.clawTotal >= 2 -> BLOCK "A capital ship can be grappled by two vessels only."
        if occ.clawTotal == 1 && !isOppositeSection(occ.clawSections[0], section)
                              -> BLOCK "Grappling vessels must attach to opposite ends."
    // D. one claw per section (already covered by B for the same-section case)
else:
    // E. pods: two per section, counting live pods across every attached flight
    if occ[section].pods + getAttachFootprint(shooter) > 2
                              -> BLOCK "No room for more Breaching Pods on this section."
```

`isOppositeSection` keeps the existing pairing exactly — `1↔2`, `3↔4`, and section `0` is never a
valid partner — because those are the only sections a non-base capital hull has. Bases and Enormous
units skip the pairing entirely via branch C, which also fixes the six-section misfire noted in §0.

**One asymmetry preserved deliberately:** branch B stops a *pod* joining a claw's section, but nothing
stops a *claw* attaching to a section that already holds pods — that is today's behaviour
(`checkAttachmentLimits` only ever counted non-flights) and your ruling named only the pod direction.
Making it symmetric is one extra line in branch C; say the word if you want it.

### 1.3 Server: rewire the two callers

- [`Marines::checkAttachedAmount`](source/server/model/weapons/specialWeapons.php#L7845) — keep the
  method and signature (one caller), replace the body with a call to `isAttachBlocked`, and push
  `$reason` into `$fireOrder->pubnotes` instead of the current generic string.
- [`GrapplingClaw::checkAttachmentLimits`](source/server/model/weapons/specialWeapons.php#L8369) —
  same. The size-class caps and the opposite-ends test move into `isAttachBlocked` branch C rather
  than being deleted.

`hasAttached` keeps storing the **raw** `chosenLocation`. Do not normalise it to the section key on
write — [PhaseStrategy.js:899](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L899)
positions the pod model from it and [ShipInfo.js:84](source/public/client/UI/reactJs/system/ShipInfo.js#L84)
labels it ("Port-Forward" etc.). Resolution happens at check time only, so **no data migration and no
note-format change** — games in flight keep working.

### 1.4 LCV / OSAT: "1 unit", not "1 pod" — SETTLED

The rule is one pod total. The data model cannot express a partially-attached flight, and every
breaching-pod flight is 2 pods — so a literal `cap = 1 pod` with footprint counting would mean **no
pod flight could ever attach to an LCV or OSAT**, which is not the intent.

**Ruling (agreed 2026-08-12):** cap the LCV/OSAT at **one attaching unit**, and cap
`checkMissionAmount` for those hulls at **1 mission**, so a 2-pod flight can attach but only one
marine contingent actually boards. Net effect matches the rule, using machinery that already exists.
(Today's code is looser still: it permits two separate 1-pod flights.)

### 1.5 Footprint on normal sections

Strict counting means a 1-pod flight sitting on a section blocks a full 2-pod flight from joining it,
even though one slot is free — again because partial attachment cannot be recorded. That is the
rules-correct outcome (never more than 2 pods on a section) and only arises with damaged flights.
Flagging it so it isn't a surprise in play.

### 1.6 Deliberate rule changes to sign off

| Hull | Old | New |
|---|---|---|
| LCV / OSAT | 2 pods across up to two flights | **1 attached craft**, 1 mission |
| MediumShip | 2 pods, unlimited per section | 2 pods (unchanged; now explicitly one section) |
| HCV | 4 pods anywhere | 2 fwd + 2 aft (+2 primary once exposed) |
| Capital | 8 pods anywhere | 2 per section (same total, now positional) |
| Capital claws | 2, opposite ends | unchanged |
| MCV / HCV claws | 1 | unchanged |
| Stormfalcon | 8 pods | **6** (3 sections) |
| Bases / Enormous | 12 pods; claw pairing silently no-op | 2 pods **per section**, 1 claw **per section** |

### 1.7 Mission cap

Re-derive `checkMissionAmount` as `2 × (number of distinct Structure locations, primary included)`,
with the LCV/OSAT override of 1 from §1.4 — same walk as `getSectionOccupancy`, so a Stormfalcon can't
allow 8 missions while only 6 pods can physically attach.

**Trek Transporter is out of scope.** It never attaches, and `checkMissionAmount` is private to
`Marines` — the Transporter only *contributes* to `Marines::$boardedThisTurn` and is not itself gated
by the cap. Nothing in this plan changes its behaviour.

### 1.8 Front end

[`weaponManager.calculateBoardingAction`](source/public/client/weaponManager.js#L1276) currently has
**no** limit checks at all — the player sees a healthy % and the server silently cancels the attach.
Add the mirror:

- Candidate sections come from `weaponManager.getShipHittingSide(shooter, target)`
  ([weaponManager.js:2131](source/public/client/weaponManager.js#L2131)), which reads
  `target.outerSections` — the client-side twin of `getLocations()`.
- Resolve each candidate with `shipManager.systems.getStructureSystem(target, loc)`
  ([systems.js:371](source/public/client/systems.js#L371), backed by the serialised `ship.structures`
  map built at [ShipClasses.php:2078](source/server/model/ships/ShipClasses.php#L2078)); `null` → 0.
  Mirror the destroyed-structure → 0 rule so the primary case matches the server.
- Walk `target.hasAttached`, resolve each entry the same way, count live pods via
  `gamedata.getShip(id).systems` + `shipManager.systems.isDestroyed`, and mark claw sections
  (attached unit is not a flight).
- Port the whole of §1.2 — the LCV/OSAT single-craft rule, the claw hull caps and opposite-ends test,
  the claw-holds-the-section rule, and the 2-pods-per-section rule. This is the
  "front-end check that prevents attachment if a Grapple Ship is already on the section" from the
  brief, and it now covers the claw-vs-claw cases too.
- If **every** candidate section is blocked → `makeResult(0, { breakdownReason: 'Boarding: no free section on target' })`.
  If only some are, leave the chance alone — the server rolls the section and we must not pretend to
  know which one it picks.
- Add the same test to [`weaponManager.targetShip`](source/public/client/weaponManager.js#L2424) so a
  fully-blocked target raises a `confirm.warning` at declaration instead of burning the order.

### 1.9 Same-turn attachments — FIXED after test game 4300

The first build of §1.2 counted occupancy from `hasAttached` alone, and `hasAttached` is only written
at **resolution** time, in `onDamagedSystem`. But
[`Firing::prepareFiring`](source/server/handlers/firing.php#L891) computes `needed` for **every** fire
order before `Firing::fireWeapons` resolves **any** of them — so every boarding order in a turn saw
the identical pre-turn occupancy and they all passed. Game 4300 turn 1: four Scion flights onto a
Primus landed 2 pods on section 3, 2 on section 1 and **4 on section 4**; two flights onto a Demos both
took section 1. Both hulls then sat at their hull-wide totals (8 and 4), so nothing could reach the
aft sections at all — the reported symptom.

Fix: `getSectionOccupancy` now also counts **pending** attachments —
`Marines::getPendingAttachments`, which walks the boarding fire orders of every `Attaches` unit at this
target and reserves a slot for each one `calculateHitBase` has already approved (`updated == true &&
needed > 0`; refusals set `needed = 0`, and a DB-loaded order has `updated == false`). One reservation
per unit, whatever the number of pods or claw mounts. Sections therefore go first-come-first-served in
fire-order order, which is the same order `prepareFiring` walks.

Consequences, all deliberate:
- **Reservations are optimistic.** A pod whose attach roll later misses still held the slot for that
  turn. The roll is not known until a whole phase later, and refusing at resolution instead would spend
  the marine contingent before telling the player there was no room.
- **No resolution-time re-check was added for `Marines`.** Each approval saw every earlier approval, so
  the full approved set already satisfies the caps — and therefore so does any subset of it. That is
  also why `GrapplingClaw::onDamagedSystem`'s existing re-check cannot now refuse a claw that passed at
  pre-firing.
- **The front end cannot mirror this.** It never learns the section the server rolls, and it cannot see
  the opponent's declarations at all, so `isBoardingFullyBlocked` stays a test of occupancy as of the
  *start* of the turn. A pod refused for a full section is told so in the combat log, exactly as the
  mission cap has always done.
- **Which section a flight gets is its APPROACH BEARING, not a roll.**
  [`doGetHitSectionBearing`](source/server/model/ships/ShipClasses.php#L3125) takes only the locations
  whose arc contains the boarder's relative bearing, and for a same-hex unit that bearing is its
  direction of travel — the hex edge it crossed to get there ([[arch_same_hex_bearing]]; observer is the
  *target*, so the pod is the one stood back). A boarder therefore picks its section by route, and the
  profile-weighted roll only fires when arcs genuinely overlap. Two flights compete for a section only
  when they fly in from the same hex — which is exactly what happened in 4300: 876515 and 876519 both
  entered from (1.7, 3.0), bearing 60°, starboard.

Reachable sections per hull, at the six hex-edge bearings (verified against the 4300 hulls):

| Hull | 0° | 60° | 120° | 180° | 240° | 300° |
|---|---|---|---|---|---|---|
| Capital (Primus), Enormous (Explorer) | 1 | 4 | 4 | 2 | 3 | 3 |
| HCV (Demos) | 1 | 1 | 2 | 2 | 2 | 1 |
| 6-section base (Kraken) | 1/41/31 | 1/41/42 | 41/42/2 | 42/2/32 | 2/32/31 | 1/32/31 |

So a capital's four sections are all reachable but not equally — port and starboard take two of the six
approach directions each, fore and aft one apiece. An **HCV has only two sections at all** (three
bearings each), so its cap is 2 fwd + 2 aft and no route reaches more. A multi-section base is
ambiguous three-deep at *every* hex-edge bearing, so there the roll really does choose.

**Not a defect after all** — an earlier draft of this section worried that the two pods of a flight file
separate fire orders and so could resolve to different sections. They cannot:
[`getHitSection`](source/server/model/ships/ShipClasses.php#L3355) caches its pick in
`activeHitLocations[shooterId]`, so the first order of a shooter fixes the section for every later one
in the same resolution. That is also why §1.11's resolver shares the same cache.

### 1.10 Grappling claws do not count against the pod mission cap — FIXED

Second defect from test game 4300. `GrapplingClaw::onDamagedSystem` appended to
`Marines::$boardedThisTurn`, the *breaching-pod* mission tally that `checkMissionAmount` gates on. A
Claweagle mounts **two** claws, so one grappling frigate on the bow spent two of a Demos's four
missions (`getMissionCap` = 4 for an HCV); add one pod flight and the allowance was gone, so the flight
that had legitimately attached to the still-free aft section was refused delivery with "Too many
Breaching Pods trying to deliver marines". Orders 497280/497281, turn 1.

**Ruling (2026-08-13): grappling vessels do not count against the pod cap.** Claw deliveries now record
into a separate `Marines::$clawBoardedThisTurn` via `Marines::recordClawBoarding()`, and
`getNewMissionsThisTurn` — hence `checkMissionAmount` — sees pod missions only. Nothing caps the claw
tally: how many claw missions can land is already bounded by the attachment limits (one grappling vessel
on a medium ship or HCV, two on a capital, one per section on a base) and by each claw's ammunition.

The other half of the ruling — a claw attaching alongside pods is allowed — needed no change. It is
§1.2's deliberate asymmetry: branch B stops a *pod* joining a claw's section, nothing stops a *claw*
taking a section that holds pods.

### 1.11 Boarding section = hex entry edge, not the arc roll — CHANGED

`getHitSection` takes every arc that *contains* the shooter's bearing and, where they overlap,
`doGetHitSectionBearing` rolls profile-weighted between them. On a six-section hull **every** hex-edge
bearing is ambiguous three ways (a Kraken: 0° → 1/41/31, 60° → 1/41/42, …), so which block a boarder
grabbed was a roll the player could neither predict nor plan around. With per-section limits a clash now
costs the whole attempt, and once §3 lands it rams the boarder into an Enormous base.

**Ruling (2026-08-13): a boarding unit attaches to the section whose arc is CENTRED on the hex edge it
crossed.** New [`BaseShip::getAttachSection`](source/server/model/ships/ShipClasses.php#L3371) +
`doGetAttachSectionBearing`, called by both `calculateHitBase`s in place of `getHitSection`; two generic
angle helpers (`mathlib::getArcCentre`, `mathlib::getAngleDistance`) support it. It shares
`activeHitLocations`, so a boarder's section, its own gunfire and both orders of a two-pod flight all
agree.

Why arc-centre rather than a hard-coded edge→location table: a same-hex bearing is always a multiple of
60° (hex facings and hex neighbours both are) and every section arc is centred on one of those bearings,
so the rule derives the mapping from `getLocations()` itself. Swept over all 22 hull classes × the six
hex-edge bearings:

- **0 changes** on any hull whose arcs do not overlap — there the containing arc is already the
  nearest-centred one. Capital, HCV, MediumShip, OSAT, Mine, StarBase, SixSidedShip, VorlonCapitalShip
  are all untouched.
- **35 bearings become deterministic**, including all six on `StarBaseSixSections` and `VreeCapital`,
  which now map exactly onto 1 / 41 / 42 / 2 / 32 / 31. Also fixes `SmallStarBaseFourSections`,
  `UnevenBaseFourSections`, `MindriderHCV` and `watchtower`.
- **13 bearings still tie** and fall through to the existing roll, on hulls where two sections are
  genuinely equidistant from the entry edge: `BaseShipNoAft` at 180° (no aft section, so both rear
  quarters are equally placed), `HeavyCombatVesselLeftRight` and `MediumShipLeftRight` at 0°/180°,
  `SmallStarBaseThreeSections` at 60°/180°/300°, `StarBaseFiveSections` at 300°, `MindriderCapital` at
  0°/180°, `MindriderHCV` at 0°. A five-section base cannot align to six hex edges, and the medium-hull
  ties are harmless because every location there collapses onto Primary anyway.

### 1.12 Front end names the section — BUILT

The point of §1.11 was player-facing clarity, so the client now mirrors the rule and says where the
unit will land. New in [weaponManager.js](source/public/client/weaponManager.js):

| | |
|---|---|
| `getAttachRelativeBearing` | mirror of `BaseShip::getBearingOnUnit`, roll mirror included |
| `getAttachLocation` | mirror of `doGetAttachSectionBearing` — nearest arc centre over `target.outerSections`, which carries the same `{loc,min,max}` arcs as `getLocations()` minus Primary; `null` on a tie |
| `getAttachSection` | adds the breached-structure → Primary redirect via the existing `resolveAttachCandidate` |
| `getBoardingAttachInfo` | the one resolver — returns `{section, label, reason, certain}` |
| `getSectionLabel` | "Forward" / "Starboard-Aft" / "Primary" …, the labels `ShipInfo.js` already uses |

plus `mathlib.getArcCentre` / `getAngleDistance` in [mathlib.js](source/public/client/mathlib.js).

`isBoardingFullyBlocked` now delegates to `getBoardingAttachInfo`, which is a real tightening: when the
section is known it tests **only that section** instead of asking "is every candidate blocked". The
conservative all-candidates sweep survives as the tie fallback. Three places surface it:

- **Targeting tooltip** — a `BOARDING: attaching to STARBOARD` line, added next to the existing
  incoming-fire arc list (which stays, because a claw ship's guns still use it). Deliberately outside
  the `calledid` guard: a Sabotage boarding action *is* a called shot and where it attaches still matters.
- **Hit-chance tooltip** — a new free-text `note` on the result (`makeResult`/`buildHitChanceTooltipText`
  render it after the percentage breakdown): `Will attach to: Starboard`. A refusal now names the section
  and the rule: `Boarding: Starboard - No room for more Breaching Pods on this section.` An
  already-attached unit reads `Boarding: already attached at Port`.
- **Declaration warning** — `targetShip` says which section it *would* have taken and why that failed,
  instead of the old flat "there is no free section".

On a tie it says `Attach section rolled by server (two sections tie on this approach)` rather than
guessing.

One asymmetry that cannot be closed: the client's `getSectionOccupancy` sees only attachments recorded
*before* this turn. It never learns the opponent's declarations, so a section that fills during
resolution is still reported by the server in the combat log — noted in the method.

**Verification.** Dumped all 22 hull classes' GUI arcs plus the server's answer per hex-edge bearing to
JSON, then ran the real JS in a `vm` against it: **132/132 hull-bearing pairs match the server**, angle
helpers and the relative-bearing formula (roll included) pass, and eight stubbed scenarios check the
exact strings (free section, full section, claw-held section, a *different* section full, already
attached, tie, tie-and-all-full, LCV). Repeated against the **minified** output — the shipped artifact —
with the bundler's own esbuild options: same 132/132. A whole-bundle evaluation found `THREE` as the only
undefined global, which is correct (it ships as its own `<script>`).

Also fixed while here: `doGetAttachSectionBearing` called `fillLocations`, which returns **null** if a
location has no Structure of its own, and then indexed `[0]` on it. Real hulls always have Structures so
it never fired in play, but a bare hull surfaced it immediately — now falls back to the raw
`getLocations` entry, whose `loc` and `profile` are the only fields `activeHitLocations` consumers read.

---

## 2. Host-spill fire order — two separate fixes

Both live in the same block, [`Firing::fire`](source/server/handlers/firing.php#L1304), but they are
independent bugs and get their own commits.

### 2a. Weapons that must never damage ships
Plasma Web's cloud damage ([plasma.php:1381](source/server/model/weapons/plasma.php#L1381)) creates a
`prefiring` fire order against every flight that moved through the hex — and an attached pod mirrors
its host's movement, so it is always in the hex whenever the host is. Result: a hex-targeted
anti-fighter weapon lands a full-damage automatic hit on a capital ship.

**The premise was half wrong — the spill was never reachable, because the cloud never saw the pod
at all.** Found while testing game 4301 (2026-08-13). An attached pod is in the host's hex, but the
movement rows [`MovementGamePhase`](source/server/Phase/MovementGamePhase.php#L281) writes for it are
*all* stamped type `attached` — it copies the host's whole plot under that one type. Every "did this
unit enter a NEW hex" test in the codebase, `checkForValidTargets` included, accepts only
`move`/`slipleft`/`slipright`, so an attached pod matches none of its own rows. Verified in 4301
turn 2: the G'Quan drove through the cloud at (0,0) carrying pod 876533 and **no** fire order was
created against it, while the unattached flight 876532 on the identical route was hit for 11. A DB
sweep of every `PersistentEffectPlasma` order ever resolved locally confirms it: all damage has only
ever landed on flights, never on a host.

So §2a's guard is correct but was **latent** — it only starts mattering once detection is fixed.

Two changes, both in [plasma.php](source/server/model/weapons/plasma.php):
- New `getHexTransitMovement($flight, $gamedata)`: when a flight is attached, `checkForValidTargets`
  walks the **host's** movement rows instead of the pod's mirror. Same hexes, real move types, so the
  pod is caught exactly when its host would have been. Confirmed against 4301 turn 2 — both flights
  are now picked up, and `doesSkipAttachedHostHit()` keeps the G'Quan out of it.
- `createFireOrders`' already-engaged guard did `return` where it meant `continue`. It is a *per
  target* test but it abandoned every remaining flight in the list, so a second Web clouding the same
  hex — or one flight entering the hex twice in a plot — silently spared everything after it.

Left alone deliberately: the same movement-type blindness exists at seven other sites
([AoE.php](source/server/model/weapons/AoE.php#L500) ×3 — mines,
[specialWeapons.php](source/server/model/weapons/specialWeapons.php#L2783) ×4,
[baseSystems.php](source/server/model/systems/baseSystems.php#L10504)). Attached pods are invisible to
all of them too. Whether a pod should trip a minefield its host drives through is a rules question, not
a bug fix, so it is not in this pass.

- Add `public $skipsAttachedHostHit = false;` to `Weapon` (`source/server/model/weapons/weapon.php`).
  **Do not** add it to `stripForJson` — it is server-only. (`ShipSystem::stripForJson` is
  explicit-field, so nothing picks it up implicitly, but re-run the replay harness anyway per the
  serialised-property rule.)
- Guard the block:
  `if ($target && empty($weapon->skipsAttachedHostHit) && !empty($target->attached) && $target instanceof FighterFlight)`
- Set `public $skipsAttachedHostHit = true;` on `PakmaraPlasmaWeb`.
- Also treat `$weapon->hextarget` as an implicit opt-out: the shooter aimed at a hex, never at the
  pod, so the spill is conceptually wrong for every such weapon rather than just this one.

### 2b. Duplicate terrain collision damage
The terrain-collision loop at
[specialWeapons.php:2472](source/server/model/weapons/specialWeapons.php#L2472) already creates a
**separate** collision order for the host itself. The spill then adds a second one derived from the
pod's order, so a host dragging a pod through an asteroid field takes collision damage **twice**.

Fix: skip the spill when `$fire->damageclass` is `'TerrainCollision'` or `'TerrainCrash'`. Independent
of 2a and worth its own commit so it can be reverted separately.

**Also latent, for the same reason as 2a — and now made real (2026-08-13).** An attached pod was never
in the collision list either: `RammingAttack::checkForCollisions` walks the pod's own rows and they are
all type `attached`, so no order was ever raised against it and there was never a second host order to
suppress. **Ruling: attached units DO collide with terrain**, so the detector now accepts an `attached`
row as a hex transit when the position changed from the previous row — an exactly equivalent test,
since move/slipleft/slipright are the only plotted types that move a unit. New
`RammingAttack::isHexTransit`, used by `checkForCollisions` (both the multi-hex and single-hex
branches) and by `getRamHitLocation` / `getTerrainReturnHitLocation`, which must travel with it or an
attached collider gets a default hit location.

Deliberately **not** switched to reading the host's rows the way the Plasma Web fix does. The hexes are
identical either way, but a mirrored row carries the pod's attachment facing offset, and that facing is
what picks which of the *pod's* sections the collision strikes.

This is what makes 2b's guard load-bearing: host and pod now each earn their own collision order from
the same loop, so without the `TerrainCollision`/`TerrainCrash` exclusion the host would take two.

Verified on 4301 turn 2 (G'Quan drives (1,0)→(0,0)→(-1,0) carrying pod 876533): the pod's four
`attached` rows now yield the same two transits as the host's two `move` rows, at the same hexes, with
the pod keeping facing 5 against the host's 3; an asteroid parked on (0,0) returns host + attached pod +
unattached flight. Replay harness 161 passed / 0 failed.

Edge case left alone: `isPhasedThroughTerrain` tests the *unit's own* faction, so a pod attached to a
half-phased Shadow ship collides while its host slips through. Rare, and arguably right — the pod is
not the thing that phased.

Not in scope but recorded: the spill re-enters `$weapon->fire()` a second time and only saves/restores
`ammunition`. Any weapon with other per-shot side effects (boarding records, sustained targets,
`alreadyEngaged` statics) would double-apply them.

---

## 3. Auto-ram on failed attachment

### Why it can't be fixed where the skip is
Phase order is Pre-Firing → Firing. `beforePreFiringOrderResolution` (where line 2529 lives) runs
*before* `calculateHitBase` even runs in that same function, let alone before the roll — see
[`Firing::preparePreFiring`](source/server/handlers/firing.php#L669) (hook loop first, hit chances at
line 724). The attach outcome is only known inside
[`Firing::fireWeapons`](source/server/handlers/firing.php#L1092), where `onDamagedSystem` writes
`$shooter->attached[$target->id]`. And ramming orders are resolved **first** in `fireWeapons`
(lines 1093-1153), so a ram created after the boarding roll cannot join that batch.

`criticalPhaseEffects` is too late: `Criticals::setCriticals` runs after `fireWeapons`
([FireGamePhase.php:18](source/server/Phase/FireGamePhase.php#L18)), and a fire order created there
would be persisted by line 40 but never resolved — it would sit in the DB and surface next turn.

### Approach: a dedicated late pass in the Fire phase
Add `Firing::createFailedAttachRamOrders($gamedata, $dbManager)`, called at the **end of
`fireWeapons`** — after the "fighters at ships" loop, before the jump-engine loop. Thread `$dbManager`
in by giving `fireWeapons` the same optional second parameter `prepareFiring` already has, and pass it
from `FireGamePhase::advance`.

Per shooter (`hasSpecialAbility("Attaches")`, not Terrain, **not destroyed**), for each unit in
`$gamedata->getShipsInDistance($shooter, 0)`:

```
skip if !$target->Enormous
skip if $target instanceof Terrain            (handled by the collision path)
skip if $target->isDestroyed() || not yet deployed || $targetID == $shooter->id
skip if isset($shooter->attached[$targetID])  ← the "properly attached units never ram" guard
skip if isset($shooter->skinDancing[$targetID])
skip if $ram->checkAlreadyRammed($targetID) or a ram order at this target already exists
```

Then build the ram order and resolve it immediately. Two proven precedents to copy exactly:

- [`AutomatedMovement::createAutomatedRamOrders`](source/server/handlers/AutomatedMovement.php#L675) —
  order construction plus the **`submitSingleFireorder` + clear `addToDB`** trick. That persistence
  matters: the ram's return damage lands on the *firing* unit and its `DamageEntry` captures
  `$fireOrder->id`; without a real id it is `-1` and the pod's destruction never links to a combat-log
  row. Cast the insert id to `int` (string-id trap).
- [`TransverseDrive`](source/server/model/weapons/supportWeapons.php#L1045) — creating a ram order
  mid-phase and calling `$rammingAttack->fire($gamedata, $fire)` directly, which is the "replicate
  ramming later" pattern you had in mind. One correction when copying: attach the order to the
  **shooter's own** `RammingAttack->fireOrders[]`, not to the initiating weapon's list (Transverse
  Drive files it under the jumping ship; it still persists because `getNewFireOrders` walks every ship,
  but it is misfiled).

For a `FighterFlight` shooter, match the existing pre-firing path rather than the HK one: a single
order with `calledid` = the last live fighter
([specialWeapons.php:2547-2553](source/server/model/weapons/specialWeapons.php#L2547)), `damageclass
'TerrainCrash'`. That keeps a failed attach and a failed skin-dance producing identical output.

Line 2529 in the Pre-Firing pass stays **exactly as it is** — that is what keeps the two paths from
double-ramming.

### Consequences to be aware of
- **Ram damage now lands after normal weapons fire, not before.** For a failed attach that is
  defensible (the attempt happened this phase), but it does mean a pod shot down earlier in the phase
  never rams — made explicit by the `isDestroyed()` guard.
- **Bases**: speed 0 ⇒ speed difference 0 ⇒ attach auto-succeeds ⇒ `attached` is set ⇒ skipped. Only a
  *blocked* attach (section full, advanced armour, Ancient) rams a base. Matches your ruling.
- **This ships more rams than before**, because a pod that never declared an attach (out of marines,
  drifted in) will now ram a co-located Enormous unit. That *is* the general auto-ram rule the blanket
  skip was hiding. If you want the narrow version instead — ram only if the unit *had* an attach fire
  order this turn that failed or was cancelled — it is one extra predicate over
  `$shooter->getAllFireOrders($gamedata->turn)` looking for a boarding weapon with
  `targetid == $targetID`. Defaulting to the broad rule.
- **Tasks 1 and 3 interact.** Tighter per-section limits mean more blocked attaches, so more auto-rams.
  Build 1 first, then 3, and test them together.

---

## 4. Defects to fix in this pass

| # | Where | Issue | Fix |
|---|---|---|---|
| D1 | [weaponManager.js:1292](source/public/client/weaponManager.js#L1292) | `target.attached[shooter.id]` should be `target.hasAttached[shooter.id]` — `target.attached` is keyed by the target's *own* host, so the "already attached ⇒ auto-hit" branch is dead. | One-word fix; folds into the §1.8 commit. Server already returns 100, so this only corrects the displayed %. |
| D2 | [GrapplingClaw::calculateHitBase:8291](source/server/model/weapons/specialWeapons.php#L8291) | No `isset($target->hasAttached[$shooter->id]) → needed = 100` branch (Marines has one at [7962](source/server/model/weapons/specialWeapons.php#L7962)), so an already-attached claw re-rolls its attach chance every turn and can "miss" while still attached. | Add the auto-hit branch, mirroring Marines. Guard it *before* the section checks so an attached claw is never blocked by its own occupancy. |
| D3 | [GrapplingClaw::calculateHitBase:8291](source/server/model/weapons/specialWeapons.php#L8291) | Missing the `Mine` / `Terrain` target guards Marines has at [7940](source/server/model/weapons/specialWeapons.php#L7940). | Copy the guard block across. |
| D4 | [firing.php:1304](source/server/handlers/firing.php#L1304) | Host spill double-applies terrain collision damage. | §2b — its own commit. |

The order-dependent `array_keys($sectionClaws)[0]` in the old opposite-sections branch disappears when
that logic moves into `isAttachBlocked` (§1.2), which reads `clawSections` explicitly.

---

## 5. FAQ update

[faq.php](source/public/faq.php), the `#boarding` section — do this **after** the code lands so the
text matches shipped behaviour.

- **Lines 231-233** — the pod-limit paragraph is the main rewrite. Replace the flat
  "12/8/4/2/1 by hull size" list with the per-section rule: two pods per structure section, so
  totals follow the hull's actual section count (a Drazi Stormfalcon takes 6, not 8); Primary is
  reachable only once the facing structure is destroyed; LCVs and OSATs support a single attached
  craft. Keep and extend the existing claw sentence: one grappling vessel per section, a section held
  by a claw admits no pods, one claw on a medium ship or HCV, two on a capital ship on opposite ends.
- **Line 237** — "any shot aimed at a pod will automatically hit the vessel it is attached to as well"
  needs the §2a caveat: hex-targeted and other never-hits-ships weapons (Plasma Web) damage the pod
  only.
- **New bullet** — auto-ram: a unit that ends its movement on an Enormous unit's hex and fails to
  attach (or does not attempt one) rams it instead, resolved at the end of the Firing Phase.
  Successfully attached units never ram their host.
- Check the fleet-selection allowance paragraph (lines 220-223) still reads correctly — the purchase
  allowance is unchanged by any of this, but it sits directly above the text being rewritten.

---

## 6. Testing & rollout

1. **Replay harness before and after**, using the stash-compare method — capture output,
   `git stash push -- source/`, re-run, `git stash pop`, `Compare-Object` with timings normalised.
   Byte-identical output = clean, regardless of the pre-existing FAIL count.
   `docker exec fieryvoid-php-1 php /usr/src/current/tests/replay/replayHarness.php check`
   - The **tohit** check *does* exercise `Marines::calculateHitBase` and
     `GrapplingClaw::calculateHitBase`, so §1 will show up there — expect legitimate diffs on any
     corpus game with pods attached, and re-record only after eyeballing them.
   - Damage/criticals resolution is **not** covered, so §3's ram pass gets no harness coverage. It
     needs a live Docker game.
2. **Manual matrix** (local Docker):
   - 2-pod flight onto an MCV, then a second flight → refused (one section).
   - 2 flights onto an HCV fwd + aft → both attach; a third → refused.
   - Capital: 4 sections filled, then primary after the forward structure is destroyed.
   - Stormfalcon: confirm 3 sections, no phantom "aft".
   - LCV and OSAT: one flight attaches, one mission delivered; a second craft refused.
   - Claw onto a medium ship, then a second claw → refused. Capital: two claws on opposite ends
     accepted, two adjacent refused, a third refused.
   - Six-section base: one claw per section, several accepted (previously the pairing test no-oped).
   - Pod onto a section already holding a claw → refused.
   - Already-attached claw → 100% shown and no re-roll (D2).
   - Plasma Web cloud over a host carrying an attached pod → pod damaged, host untouched, no spurious
     combat-log entry.
   - Host dragging a pod through asteroids → host takes collision damage **once** (D4).
   - Pod fails to attach to Babylon 5 (fill the section first) → auto-rams, and the *successfully*
     attached pods do not.
3. **Bundles**: `weaponManager.js` is legacy → rebuild with `scripts/fvbuild.ps1`. Never hand-edit or
   hand-commit `game.legacy.bundle.js`.

## 7. Commit split

1. `Add structure-section resolution helpers for boarding attachment`
2. `Limit breaching pods and claws by structure section` (server; §1.2–1.7, includes D2 and D3)
3. `Mirror boarding section limits in the front end` (§1.8, includes D1)
4. `Let weapons opt out of the attached-pod host hit` (§2a)
5. `Stop attached pods duplicating terrain collision damage on the host` (§2b / D4)
6. `Auto-ram Enormous units when attachment fails` (§3)
7. `Update Boarding Actions FAQ for new attachment rules` (§5)
