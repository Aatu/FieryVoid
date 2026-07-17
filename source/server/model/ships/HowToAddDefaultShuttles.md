# Adding faction defaults shuttles: 

1. Define the subclass in Shuttle.php
Subclass Shuttle and override setShuttleDefaults(). Set $this->phpclass, $this->shipClass, $this->faction, and any stat overrides. Use FlyerProtectorate (line 162) as the template:
If shuttle will be for a faction that has minesweepers with default minesweeping shuttles, add the minesweeping class to shuttle as well at the BOTTOM of the file (you'll see many examples already)

2. Register it in the autoload classmap — do not skip this (you will also need to register minesweeping version here if you added one) e.g.
'flyer' => '/server/model/ships/Shuttle.php',
'minesweepingflyer' => '/server/model/ships/Shuttle.php',

3. Add the faction → class entry in HangarOps
In HangarOps.php:202, add to $factionShuttleMap:
'Some Faction' => 'FlyerXYZ',

4. Add the tooltip display name
shuttleDisplayNameFor() — add a case 'FlyerXYZ': return 'Flyer';. Without it the hangar tooltip falls through to default and labels the craft "Shuttle".  This may be fine if it’s a generic shuttle unit for faction

5. Check eviction priority
evictionPriorityFor() returns priority 10 for things whose phpclass contains the substring "shuttle". A name like Flyer / Lifeboat / Yacht does not contain "shuttle", so it would fall through to the class_exists probe and get treated as a fighter (priority 1000+). This is now guarded by a `self::isDefaultShuttleClass($phpclass)` check (true for `Shuttle` and any subclass), so ANY class that extends Shuttle is kept — no per-class edit needed. NOTE: `isDefaultShuttleClass` uses `class_exists` + `is_subclass_of`, so this only works if the class actually resolves, which means (a) the autoload key in step 2 must be present, and (b) $this->phpclass in step 1 must EXACTLY equal the class name. A phpclass typo (e.g. class `EmperorsYacht` with `$this->phpclass = "EmperorYacht"`) breaks both this check AND crashes deserialize with `Class "X" not found`.

6. Art (only if needed)
getImage() keys off $this->faction. If your faction string already matches an existing case you're done; otherwise add a case returning your [imagePath, iconPath]. (Setting $this->faction correctly in step 1 is what makes this work automatically.)

7. Client preload — automatic FOR FACTION-MAP shuttles only
game.php's carrier-preload loop calls shuttleClassForFactionName() over $factionShuttleMap, so once step 3 is done the blueprint preloads for any game containing that faction's carrier. No client wiring needed. THIS DOES NOT COVER category-declared shuttles — see the section below.
Test
Quick test before considering it done: add a carrier of the faction to a game, then surrender or save/reload — that's the deserialize path that exercises the autoload entry. If it survives a surrender, all four touchpoints are wired.

---

# Category-declared (bespoke) shuttles — a DIFFERENT flow

Some shuttles are NOT in $factionShuttleMap. They auto-populate only into carriers that explicitly declare a matching category in `$this->fighters` (e.g. `$this->fighters = array("yacht"=>1);`). Examples: EmperorsYacht ('yacht'), Lifeboat ('lifeboats'), MedicalShuttle ('medical shuttles'), PresidentialShuttle ('presidential shuttle'). These are wired through `HangarOps::shuttlePhpclassForCategory($category)` — a switch keyed on the lowercased category string, NOT the faction map.

For these, the faction-map steps 3 & 7 above DON'T apply. Instead:

A. Steps 1 (subclass) and 2 (autoload key) are the SAME — still mandatory. Phpclass MUST equal the class name (see step 5 warning).

B. Add the category → phpclass case in `HangarOps::shuttlePhpclassForCategory()` (~line 429). This is what makes a `$ship->fighters` declaration populate the shuttle at game start.

C. Add the display-name case in `shuttleDisplayNameFor()` (same as step 4) — otherwise the in-game Hangar tooltip labels the docked craft "Shuttle".

D. Client preload is NOT automatic. game.php's carrier-preload loop additionally scans each carrier blueprint's `->fighters` categories through `shuttlePhpclassForCategory()` and preloads the resolved classes into window.staticShips. This is generic — a new category case added in step B is picked up automatically, so nothing to edit here PROVIDED step B is done. Symptom if the preload is missing: launching the shuttle shows "undefined" in the ini GUI because the client can't resolve staticShips[faction][phpclass]. (The blueprint files under the SHUTTLE's own $this->faction, and the launched flight reports that same faction, so the two always match — but $this->faction must be a real string.)

E. Client allow-list mirrors (only matters if a carrier uses a per-bay allowedFighterClasses gate, but keep them in sync anyway): add the phpclass to the `shuttleClasses` object in BOTH systems.js `excludesDefaultShuttles()` (~line 472) and gamedata.js `isDefaultShuttleEntry()` (~line 424). The gamedata.js one gates a firing-phase "you have unlaunched fighters" warning — a docked bespoke shuttle missing from it is misread as a real fighter and triggers the warning spuriously. Key by the PHPCLASS (e.g. "Lifeboat"), not the category ("lifeboats"). These are .js — they need `yarn watch:legacy` to take effect.

Test (category shuttle): add a carrier that declares the category, start a game, confirm (i) the shuttle shows in the Hangar system-info tooltip with its proper name, (ii) it survives to end of turn (eviction), (iii) LAUNCH it and confirm it's named correctly (not "undefined") in the ini GUI, (iv) advance a turn (deserialize path — catches phpclass/autoload mismatches).