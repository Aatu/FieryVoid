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
evictionPriorityFor() returns priority 10 for things whose phpclass contains the substring "shuttle". A name like Flyer does not contain "shuttle", so it would fall through to the class_exists probe and get treated as a fighter (priority 1000+). That's why line 469 has an explicit Flyer/FlyerProtectorate check. If your new class name doesn't contain "shuttle", add it to that line so it's evicted as a shuttle, not as a fighter.

6. Art (only if needed)
getImage() keys off $this->faction. If your faction string already matches an existing case you're done; otherwise add a case returning your [imagePath, iconPath]. (Setting $this->faction correctly in step 1 is what makes this work automatically.)

7. Client preload — automatic, nothing to do
game.php:109-112 calls shuttleClassForFactionName() over $factionShuttleMap, so once step 3 is done the blueprint preloads for any game containing that faction's carrier. No client wiring needed.
Test
Quick test before considering it done: add a carrier of the faction to a game, then surrender or save/reload — that's the deserialize path that exercises the autoload entry. If it survives a surrender, all four touchpoints are wired.