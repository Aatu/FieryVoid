"use strict";

window.weaponManager = {
    mouseoverTimer: null,
    mouseOutTimer: null,
    mouseoverSystem: null,
    currentSystem: null,
    currentShip: null,
    ramWarning: false,

    getWeaponCurrentLoading: function getWeaponCurrentLoading(weapon) {
        return weapon.turnsloaded;
    },

    onModeClicked: function onModeClicked(ship, system) {
        //throw new Error("Route trough phase strategy to get selected ship");
        if (!system) return;

        if (gamedata.gamephase != 3 && !system.ballistic && !system.preFires) return;

        if (gamedata.gamephase != 1 && system.ballistic) return;

        if (gamedata.gamephase != 5 && system.preFires) return;

        if (weaponManager.hasFiringOrder(ship, system) && !system.multiModeSplit) return;

        if (gamedata.isMyShip(ship)) {

            system.changeFiringMode();

            webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });

        }
    },

    onSetModeClicked: function onSetModeClicked(ship, system, mode) {
        if (!system) return;

        if (gamedata.gamephase != 3 && !system.ballistic && !system.preFires) return;

        if (gamedata.gamephase != 1 && system.ballistic) return;

        if (gamedata.gamephase != 5 && system.preFires) return;

        if (weaponManager.hasFiringOrder(ship, system) && !system.multiModeSplit) return;

        if (gamedata.isMyShip(ship)) {
            system.setFiringMode(mode);
            webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
        }
    },

    onSetModeAllClicked: function onSetModeAllClicked(ship, system, mode) {
        if (!system) return;

        if (gamedata.gamephase != 3 && !system.ballistic && !system.preFires) return;
        if (gamedata.gamephase != 1 && system.ballistic) return;
        if (gamedata.gamephase != 5 && system.preFires) return;

        var modeSet = mode;
        //set this mode on ALL similar weapons that aren't declared and can change firing mode
        var allWeapons = [];
        if (ship.flight) {
            allWeapons = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            allWeapons = ship.systems.filter(system => system.weapon);
        }
        //group by BASE displayName so paired Kirishiac weapons ('...A'/'...B') count as one type
        var baseName = weaponManager.stripPairingSuffix(system.displayName);
        var similarWeapons = new Array();
        for (var i = 0; i < allWeapons.length; i++) {
            if (baseName === weaponManager.stripPairingSuffix(allWeapons[i].displayName)) {
                if (system.weapon) {
                    similarWeapons.push(allWeapons[i]);
                }
            }
        }

        for (var i = 0; i < similarWeapons.length; i++) {
            var weapon = similarWeapons[i];

            if (weaponManager.hasFiringOrder(ship, weapon) && !weapon.multiModeSplit) continue;

            if (weapon.firingMode == modeSet) continue;
            //Replicate canChangeFiringMode logic
            if (!((gamedata.gamephase === 1 && weapon.ballistic) || (gamedata.gamephase === 5 && weapon.preFires) || (gamedata.gamephase === 3 && !weapon.ballistic && !weapon.preFires))) continue;

            //Check if mode exists for this weapon
            if (weapon.firingModes[modeSet]) {
                weapon.setFiringMode(modeSet);
            }
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    },

    cancelFire: function cancelFire(ship, system) {
        weaponManager.removeFiringOrder(ship, system);
        ballistics.updateList();
        gamedata.shipStatusChanged(ship);
    },

    unSelectWeapon: function unSelectWeapon(ship, weapon) {
        for (var i = gamedata.selectedSystems.length - 1; i >= 0; i--) {
            if (gamedata.selectedSystems[i] == weapon) {
                gamedata.selectedSystems.splice(i, 1);
            }
            /* Cleaned 19.8.25 - DK		
            if (weapon.duoWeapon) {
                for (var j in weapon.weapons) {
                    var subweapon = weapon.weapons[j];

                    weaponManager.unSelectWeapon(ship, subweapon);
                }
            }
            */
        }

        //JUMP_POINTS_PLAN.md Stage 2b: a pending vortex declaration belongs to the engine that is
        //SELECTED, so deselecting it abandons the transaction exactly as clicking away on the map
        //does. Hooked here rather than at the weapon-list icon because this is the choke point
        //every deselect route funnels through - the icon toggle, selecting another ship, and the
        //phase teardown sweep alike. No-op for every other weapon and whenever nothing is pending.
        if (window.UI && UI.vortexFacing) UI.vortexFacing.closeForWeapon(weapon);

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
    },

    checkConflictingFireOrder: function checkConflictingFireOrder(ship, weapon, alert) {
        var p = ship;
        if (ship.flight) {
            p = shipManager.systems.getFighterBySystem(ship, weapon.id);
        }

        for (var i in p.systems) {
            var system = p.systems[i];
            if (system.id == weapon.id) continue; //can't conflict itself

            if (weaponManager.hasFiringOrder(ship, system)) {
                /*I make it so guns prevent guns from firing, but not missiles - and vice versa*/
                if ((weapon.exclusive || system.exclusive) && weapon.ballistic == system.ballistic) {
                    if (alert) confirm.error("You cannot fire <b>" + weapon.displayName + "</b> and <b>" + system.displayName + "</b> together!");
                    return true;
                }
            }
        }

        return false;
    },

    //Smallest angular separation (0-180 deg) between two units as seen from a common observer.
    //Used by the firing-link "within N degrees" rule (Vree linked primaries).
    getBearingSeparation: function getBearingSeparation(observer, unitA, unitB) {
        var a = mathlib.getCompassHeadingOfShip(observer, unitA);
        var b = mathlib.getCompassHeadingOfShip(observer, unitB);
        return weaponManager.getHeadingSeparation(a, b);
    },

    //Smallest angular separation (0-180 deg) between two compass headings.
    getHeadingSeparation: function getHeadingSeparation(a, b) {
        var diff = Math.abs(a - b) % 360;
        return diff > 180 ? 360 - diff : diff;
    },

    //Compass heading, as seen from $firingUnit, of whatever a fire order is AIMED AT — a target
    //ship (targetid > 0) or the target HEX of a hex-targeted order (targetid -1 with x/y set,
    //e.g. Antimatter Shredder mode 1). Null if the order aims at neither (self-intercept, or a
    //target ship that is no longer in gamedata).
    //
    //This is what lets the firing-link rule treat a hex order like any other: a turret is one
    //mount, so where its hex-targeting weapon is pointed constrains its mate exactly as a ship
    //target would, and vice versa. Before this, hex orders were skipped entirely and a Vree
    //turret's Shredder and Cannon could point in completely opposite directions.
    getFireOrderBearing: function getFireOrderBearing(firingUnit, fo) {
        if (!fo) return null;
        if (fo.targetid > 0) {
            var target = gamedata.getShip(fo.targetid);
            return target ? mathlib.getCompassHeadingOfShip(firingUnit, target) : null;
        }
        if (fo.x !== undefined && fo.x !== null && fo.y !== undefined && fo.y !== null) {
            return mathlib.getCompassHeadingOfPoint(
                shipManager.getShipPosition(firingUnit), new hexagon.Offset(fo.x, fo.y));
        }
        return null;
    },

    //Human-readable name for what a fire order is aimed at, for the "blocked" message.
    getFireOrderAimLabel: function getFireOrderAimLabel(fo) {
        if (!fo) return 'its paired target';
        if (fo.targetid > 0) return (gamedata.getShip(fo.targetid) || {}).name || 'its paired target';
        if (fo.x !== undefined && fo.y !== undefined) return 'the hex at ' + fo.x + ',' + fo.y;
        return 'its paired target';
    },

    //Firing-link ("linkedFiringGroup") enforcement, evaluated AT TARGETING TIME (not at commit).
    //Weapons sharing a non-null linkedFiringGroup ON THE SAME PARENT UNIT constrain each other:
    //once a sibling in the group has declared a fire order this turn, the others are limited by it.
    //  - linkedFiringSpread == null : all members must fire at the SAME target unit (Thunderbolt's
    //                                 twin missile racks).
    //  - linkedFiringSpread == N    : each member's target must lie within N degrees of every
    //                                 already-targeted sibling's target, measured from the firing
    //                                 unit (Vree linked primaries = 60; their 360-degree turrets
    //                                 mean weapon arcs can't express this restriction themselves).
    //Scope is per-fighter for flights (each fighter's group is independent, so different fighters in
    //a flight may pick different targets) and whole-ship otherwise. The FIRST member to declare is
    //always free.
    //
    //HEX ORDERS COUNT, in both directions (angular-spread groups only): a hex-targeted weapon on the
    //turret (Antimatter Shredder mode 1) both CONSTRAINS its mate and IS CONSTRAINED by it — the
    //turret is one physical mount, so it can only point one way. Pass `candidateHex` (a hexagon
    //Offset) instead of `candidateTarget` when the candidate is a hex. Self-intercept orders, and
    //orders whose target ship has vanished, still never constrain (no resolvable bearing).
    //The same-unit rule (spread == null, Thunderbolt twin racks) is deliberately left ship-only —
    //those groups contain no hex weapons and "same target UNIT" has no meaning for a hex.
    //
    //Returns null when the candidate is allowed, or an HTML reason string when blocked.
    getLinkedFiringBlock: function getLinkedFiringBlock(firingUnit, weapon, candidateTarget, candidateHex) {
        if (!weapon.linkedFiringGroup) return null;

        var siblings;
        if (firingUnit.flight) {
            var fighter = shipManager.systems.getFighterBySystem(firingUnit, weapon.id);
            siblings = fighter ? fighter.systems : [];
        } else {
            siblings = firingUnit.systems;
        }

        var spread = weapon.linkedFiringSpread;
        var byHex = (candidateHex !== undefined && candidateHex !== null);

        //Bearing of the candidate aim point, measured from the firing unit — the common currency
        //the spread rule compares in, whether the candidate is a ship or a hex.
        var candidateBearing = null;
        if (spread != null) {
            candidateBearing = byHex
                ? mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(firingUnit), candidateHex)
                : mathlib.getCompassHeadingOfShip(firingUnit, candidateTarget);
        }

        for (var i in siblings) {
            var sib = siblings[i];
            if (!sib || sib.id == weapon.id) continue; //can't link to itself
            if (sib.linkedFiringGroup !== weapon.linkedFiringGroup) continue;

            for (var j in sib.fireOrders) {
                var fo = sib.fireOrders[j];
                if (fo.turn != gamedata.turn || fo.rolled) continue;

                if (spread == null || spread === undefined) {
                    //Same-target-UNIT rule: ship orders only, and only a ship candidate can satisfy
                    //or violate it — "the same target UNIT" is meaningless for a hex, so a hex
                    //candidate is left unconstrained here rather than blocked outright. (No such
                    //group contains a hex weapon today; this just keeps the rule honest.)
                    if (byHex || !(fo.targetid > 0)) continue;
                    if (fo.targetid == candidateTarget.id) continue; //same unit is always allowed
                    var lockedName = weaponManager.getFireOrderAimLabel(fo);
                    return "<b>" + weapon.displayName + "</b> must fire at the same target as its paired weapon (<b>" + lockedName + "</b>).";
                }

                //Angular-spread rule: any order with a resolvable bearing constrains, hex included.
                if (!byHex && fo.targetid == candidateTarget.id) continue; //same unit is always allowed
                var sibBearing = weaponManager.getFireOrderBearing(firingUnit, fo);
                if (sibBearing === null || candidateBearing === null) continue;

                if (weaponManager.getHeadingSeparation(candidateBearing, sibBearing) > spread) {
                    var aimLabel = weaponManager.getFireOrderAimLabel(fo);
                    return "<b>" + weapon.displayName + "</b>" + (byHex ? "'s target hex" : "'s target")
                        + " must be within " + spread + "° of its paired weapon’s target (<b>" + aimLabel + "</b>).";
                }
            }
        }
        return null;
    },

    //Bearing (from $firingUnit) of the first live order declared this turn by a system — ship
    //target OR target hex. Null if the system has no such order.
    getFirstLiveOrderBearing: function getFirstLiveOrderBearing(firingUnit, system) {
        if (!system || !system.fireOrders) return null;
        for (var j in system.fireOrders) {
            var fo = system.fireOrders[j];
            if (fo.turn != gamedata.turn || fo.rolled) continue;
            var bearing = weaponManager.getFireOrderBearing(firingUnit, fo);
            if (bearing !== null) return bearing;
        }
        return null;
    },

    //The BEARING this weapon's linked group is committed to this turn — from THIS weapon's own
    //declared order if it has one, otherwise from a sibling's (same linkedFiringGroup, same parent
    //unit). Null if no member has declared. For an angular-spread group this is the centre of the
    //weapon's reduced allowed arc. Used by ShipIcon.showWeaponArc to draw the reduced arc on hover -
    //both for a sibling-locked weapon AND for a weapon that has itself locked a firing order. (Groups
    //are two-weapon turrets in practice, so the first committed member is the relevant one.)
    //A bearing rather than a target ship, so a hex-targeted order (Antimatter Shredder mode 1)
    //centres the wedge just as a ship target does.
    getLinkedGroupDeclaredBearing: function getLinkedGroupDeclaredBearing(firingUnit, weapon) {
        if (!weapon.linkedFiringGroup) return null;

        //Prefer this weapon's own committed order, so a locked weapon shows the wedge on its own aim.
        var own = weaponManager.getFirstLiveOrderBearing(firingUnit, weapon);
        if (own !== null) return own;

        var siblings;
        if (firingUnit.flight) {
            var fighter = shipManager.systems.getFighterBySystem(firingUnit, weapon.id);
            siblings = fighter ? fighter.systems : [];
        } else {
            siblings = firingUnit.systems;
        }

        for (var i in siblings) {
            var sib = siblings[i];
            if (!sib || sib.id == weapon.id) continue;
            if (sib.linkedFiringGroup !== weapon.linkedFiringGroup) continue;
            var b = weaponManager.getFirstLiveOrderBearing(firingUnit, sib);
            if (b !== null) return b;
        }
        return null;
    },

    //Is this firing mode actually fireable? A mode whose entire fireControlArray
    //entry is null (e.g. AMMO_DUM Dummy Missiles) can never fire, so it must not
    //count as live ammo. Detect by null FC rather than by mode name so any future
    //unfireable ammo is handled automatically.
    isFireableMode: function isFireableMode(weapon, mode) {
        if (!weapon.fireControlArray) return true; //no per-mode FC info: assume fireable
        var fc = weapon.fireControlArray[mode];
        if (fc == null) return true; //no entry for this mode: don't exclude it
        //all three FC slots (fighter/<medium/<capital) null -> unfireable
        return !(fc[0] === null && fc[1] === null && fc[2] === null);
    },

    //Total fireable rounds available to an AmmoMagazine-fed weapon, across all of
    //its firing modes, drawn from the owning unit's ammoMagazine. Excludes modes
    //that can never fire (see isFireableMode). Returns null if this unit has no
    //magazine (weapon is not magazine-fed after all).
    getMagazineFireableAmmo: function getMagazineFireableAmmo(unit, weapon) {
        if (!unit || !unit.systems) return null;

        var magazine = null;
        for (var i in unit.systems) {
            if (unit.systems[i].name == "ammoMagazine") { magazine = unit.systems[i]; break; }
        }
        if (!magazine || !magazine.ammoCountArray) return null;

        var total = 0;
        for (var mode in weapon.firingModes) {
            if (!weaponManager.isFireableMode(weapon, mode)) continue; //skip dummy/unfireable modes
            var modeName = weapon.firingModes[mode];
            var count = magazine.ammoCountArray[modeName];
            if (typeof count === "number" && count > 0) total += count;
        }
        return total;
    },

    checkOutOfAmmo: function checkOutOfAmmo(ship, weapon, silent) {

        var p = ship;
        if (ship.flight) {
            p = shipManager.systems.getFighterBySystem(ship, weapon.id);
        } else {
            return weaponManager.checkOutOfAmmoShip(ship, weapon, silent);
        }

        if (weapon.hasOwnProperty("ammunition")) {
            if (weapon.ammunition > 0) {
                return false;
            } else {
                //confirm.error("This fighter gun is out of ammunition.");
                if (!silent) confirm.error("This weapon is out of ammunition.");
                return true;
            }
        }

        //modern AmmoMagazine-fed weapons: ammo lives centrally in the fighter's magazine
        if (weapon.checkAmmoMagazine) {
            var available = weaponManager.getMagazineFireableAmmo(p, weapon);
            if (available !== null && available <= 0) {
                if (!silent) confirm.error("This weapon is out of ammunition.");
                return true;
            }
            return false;
        }

        for (var i in p.systems) {
            var system = p.systems[i];
            if (system.id != weapon.id) continue;

            if (system.missileArray) {
                //any firing mode with rounds remaining means the rack can still fire
                for (var j in system.missileArray) {
                    var missile = system.missileArray[j];
                    if (missile.amount > 0) return false;
                }
                if (!silent) confirm.error("This missile rack is out of ammo.");
                return true;
            }
        }

        return false;
    },


    //checks whether a shipborne weapon has ran out of ammo
    checkOutOfAmmoShip: function checkOutOfAmmoShip(ship, weapon, silent) {
        if (ship.flight) {
            return weaponManager.checkOutOfAmmo(ship, weapon, silent);
        }

        if (weapon.hasOwnProperty("ammunition")) {
            if (weapon.ammunition > 0) {
                return false;
            } else {
                if (!silent) confirm.error("This weapon is out of ammunition.");
                return true;
            }
        }

        //modern AmmoMagazine-fed weapons: ammo lives centrally in the ship's magazine
        if (weapon.checkAmmoMagazine) {
            var available = weaponManager.getMagazineFireableAmmo(ship, weapon);
            if (available !== null && available <= 0) {
                if (!silent) confirm.error("This weapon is out of ammunition.");
                return true;
            }
            return false;
        }

        return false;
    },


    selectWeapon: function selectWeapon(ship, weapon) {
        if (weaponManager.checkOutOfAmmo(ship, weapon)) {
            return;
        }

        if (weaponManager.checkConflictingFireOrder(ship, weapon, alert)) {
            return;
        }

        if (!weaponManager.isLoaded(weapon))
            return;

        if (shipManager.power.isOffline(ship, weapon)) {
            return;
        }

        if (shipManager.systems.isDestroyed(ship, weapon)) {
            return;
        }

        if (weapon.stowed && weapon.stowedArcStart == null) return; //stowed weapons with a stowed arc set (Kirishiac Heavy Orbital) remain operational

        if (weapon.autoFireOnly) return; //this is auto-fire only weapon, should not be fired manually!

        //Spent & locked Gravitic Augmenter: already committed its order for the turn and is outside
        //that order's declaration phase — block re-selection from every path (icon click, select-all,
        //right-click) at this single chokepoint.
        if (typeof weapon.isSpentLocked === 'function' && weapon.isSpentLocked()) return;


        if (ship.shipSizeClass < 0) {
            for (var i = 0; i < ship.systems.length; i++) {
                for (var b = 0; i < ship.systems.systems; b++) {
                    if (ship.systems[i].systems[b].weapon) {
                        gamedata.selectedSystems.push(ship.systems[i].systems[b].weapon);
                        webglScene.customEvent('WeaponSelected', {
                            ship: ship,
                            weapon: ship.systems[i].systems[b].weapon
                        });
                    }
                }
            }
        }
        //⚠️ ORDER MATTERS, TWICE OVER.
        //
        //`WeaponSelected` goes FIRST because the phase strategies answer it by switching the
        //selected ship, and setSelectedShip -> deselectShip clears gamedata.selectedSystems on the
        //way out of the old one. Push before the event and the weapon the player just clicked is
        //swept away again whenever they click a weapon on a ship that was not already selected -
        //DK 6.25, "friendly fighter flight is selected unit". So the push stays after it.
        //
        //Which leaves every listener reading a selection that does NOT yet contain this weapon,
        //and that is what SystemDataChanged below is for. unSelectWeapon has always fired it AFTER
        //splicing, so the deselect path notified with correct state while the select path notified
        //with stale state - deselecting a weapon updated the tooltip's TARGETING list and
        //reselecting it did nothing until some other click came along (user report 2026-08-24).
        //Firing it here makes the two paths symmetric: state first, notification second.
        webglScene.customEvent('WeaponSelected', { ship: ship, weapon: weapon });
        gamedata.selectedSystems.push(weapon);
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
    },

    isSelectedWeapon: function isSelectedWeapon(weapon) {
        if ($.inArray(weapon, gamedata.selectedSystems) >= 0) return true;

        return false;
    },

    //For use if we allow targeting allies to toggle type of tooltips - DK
    hasShipWeaponsSelected: function hasShipWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === true;
            return system instanceof Weapon && system.hextarget !== true;
        });
    },

    //Paired Kirishiac-family weapons (Antigravity Beam, Hypergraviton Beam, Lightning Gun,
    //Proximity Launcher/Laser, Gravitic Augmenter, Phased Gravitic Torpedo, ...) append a
    //per-instance pairing letter to displayName, e.g. 'Antigravity Beam A' / 'Antigravity Beam B'.
    //"apply to all similar weapons" groups by displayName, so without stripping that suffix each
    //weapon only ever matched itself. Pairings are always a single trailing ' <UPPERCASE LETTER>'
    //(assigned as ' ' . $pairing in the server ship blueprints); no normal weapon displayName ends
    //in a lone capital, so this is safe. Returns the base name for grouping.
    stripPairingSuffix: function stripPairingSuffix(displayName) {
        if (typeof displayName !== 'string') return displayName;
        return displayName.replace(/ [A-Z]$/, '');
    },

    selectAllWeapons: function selectAllWeapons(ship, system, touchToggleOverride) {
        if (!gamedata.isMyShip(ship)) {
            return;
        }
        var array = [];
        var systems = [];
        if (ship.flight) {
            systems = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            systems = ship.systems.filter(system => system.weapon);
        }

        //group by BASE displayName so paired Kirishiac weapons ('...A'/'...B') count as one type
        var baseName = weaponManager.stripPairingSuffix(system.displayName);
        array = systems.filter(function (weapon) { return weaponManager.stripPairingSuffix(weapon.displayName) === baseName });

        var currentWasSelected = weaponManager.isSelectedWeapon(system); //all others affected weapons will have state set the same as current!

        if (touchToggleOverride === "forceSelect") {
            currentWasSelected = false; // Always select
        } else if (touchToggleOverride === "forceDeselect") {
            currentWasSelected = true; // Always unselect
        } else if (touchToggleOverride === true) {
            var selectable = array.filter(function (w) {
                if (w.destroyed) return false;
                if (gamedata.gamephase != 3 && !w.ballistic && !w.preFires) return false;
                if (gamedata.gamephase != 1 && w.ballistic) return false;
                if (gamedata.gamephase != 5 && w.preFires) return false;
                if (weaponManager.hasFiringOrder(ship, w) && !w.canSplitShots && !w.hasSpecialTargeting) return false;
                return true;
            });

            if (selectable.length > 0) {
                var allSelected = selectable.every(function (w) { return weaponManager.isSelectedWeapon(w); });
                currentWasSelected = allSelected;
            }
        }

        for (var i = 0; i < array.length; i++) {
            var system = array[i];

            if (gamedata.waiting) return;

            if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) {
                return;
            }

            if (system.destroyed) {
                continue;
            }


            if (system.weapon) {
                if (gamedata.gamephase != 3 && !system.ballistic && !system.preFires) continue; //improper at this moment
                if (gamedata.gamephase != 1 && system.ballistic) continue;	//improper at this moment
                if (gamedata.gamephase != 5 && system.preFires) continue;	//improper at this moment                
                if (weaponManager.hasFiringOrder(ship, system) && !system.canSplitShots && !system.hasSpecialTargeting) continue;//already declared, do not touch it! (special-targeting weapons may be re-clicked to edit)

                if (currentWasSelected) {//unselect
                    if (weaponManager.isSelectedWeapon(system)) weaponManager.unSelectWeapon(ship, system);
                } else {//select
                    if (!weaponManager.isSelectedWeapon(system)) weaponManager.selectWeapon(ship, system);
                }
            }

        }
    },

    // Build hover-tooltip text from a calculateHitChange result.
    // Returns '' when there is nothing to show.
    buildHitChanceTooltipText: function buildHitChanceTooltipText(result) {
        if (!result) return '';
        if (result.breakdownReason) return result.breakdownReason;
        if (!result.modifiers || result.modifiers.length === 0) return '';
        function fmtPct(value, key) {
            var pct = Math.round(value * 5 * 10) / 10; //one-decimal %, trailing .0 dropped by toString
            var sign = (pct > 0 && key !== 'base') ? '+' : ''; //base is the starting value, not a modifier; +0 shown as 0
            return sign + pct + '%';
        }
        var lines = ['Hit chance: ' + result.hitChance + '%'];
        for (var i = 0; i < result.modifiers.length; i++) {
            var m = result.modifiers[i];
            //A modifier may carry a value range (valueHigh/valueLow, d20 units) - e.g. the
            //cumulative split penalty spread across already-locked shots - rendered "high to low".
            if (typeof m.valueHigh === 'number' && typeof m.valueLow === 'number' && m.valueHigh !== m.valueLow) {
                lines.push('• ' + m.label + ': ' + fmtPct(m.valueHigh, m.key) + ' to ' + fmtPct(m.valueLow, m.key));
            } else {
                lines.push('• ' + m.label + ': ' + fmtPct(m.value, m.key));
            }
        }
        //Free-text footer that is not a percentage - boarding uses it to name the structure
        //section the unit will attach to.
        if (result.note) lines.push(result.note);
        return lines.join('\n');
    },

    // Attach the hit-chance hover-tooltip delegated handlers to a container.
    // We bind locally rather than on document because the parent ShipTooltip
    // element calls stopPropagation() on mouseover/out, which would otherwise
    // eat the bubbled event before it reaches document.
    attachHitChanceTooltipDelegation: function attachHitChanceTooltipDelegation($el) {
        $el.off('.hitchance')
           .on('mouseenter.hitchance touchstart.hitchance', '.hit-chance-tooltip', _showHitChanceTooltip)
           .on('mouseleave.hitchance touchend.hitchance touchmove.hitchance', '.hit-chance-tooltip', _hideHitChanceTooltip);
    },

    targetingShipTooltip: function targetingShipTooltip(selectedShip, ship, e, calledid) {
        //e.find(".shipname").html(ship.name);
        var f = $(".targeting", e);
        f.html("");

        weaponManager.attachHitChanceTooltipDelegation(f);

        if (gamedata.selectedSystems.length === 0) {
            return;
        }

        if (!(calledid > 0)) {
            //(calledid == null){
            var html = "";
            var section = weaponManager.getShipHittingSide(selectedShip, ship);

            for (var i = 0; i < section.length; i++) {
                switch (section[i]) {
                    case 1:
                        html += "-FORWARD-";
                        break;
                    case 2:
                        html += "-AFT-";
                        break;
                    case 3:
                        html += "-PORT-";
                        break;
                    case 4:
                        html += "-STARBORD-";
                        break;
                    case 31:
                        html += "-PORT.FWD-";
                        break;
                    case 32:
                        html += "-PORT.AFT-";
                        break;
                    case 41:
                        html += "-STBD.FWD-";
                        break;
                    case 42:
                        html += "-STBD.AFT-";
                        break;
                    default:
                        html += "-OTHER-";
                        break;
                }
            }
            $('<div><span class="weapon">' + html + '</span></div>').appendTo(f);
        }

        /* Boarding attaches to the section its ENTRY HEX EDGE points at, not to whichever arc
           the bearing happens to fall in, so name that one section. Without this the player was
           left to guess from the incoming-fire arcs above - and on a six-section base those list
           THREE candidates. Outside the calledid guard on purpose: a Sabotage boarding action is
           a called shot, and where it attaches still matters. */
        if (!ship.flight && !ship.mine && ship.shipSizeClass != 5) {
            var hasBoarding = false;
            for (var bw = 0; bw < gamedata.selectedSystems.length; bw++) {
                if (gamedata.selectedSystems[bw].isBoardingAction) { hasBoarding = true; break; }
            }
            if (hasBoarding) {
                var attach = weaponManager.getBoardingAttachInfo(selectedShip, ship);
                var attachHtml;
                if (attach.reason !== null) {
                    attachHtml = attach.certain
                        ? 'BOARDING ' + attach.label.toUpperCase() + ': ' + attach.reason
                        : 'BOARDING: no free section on target';
                } else if (attach.certain) {
                    attachHtml = 'BOARDING: attaching to ' + attach.label.toUpperCase();
                } else {
                    attachHtml = 'BOARDING: section rolled by server (two sections tie)';
                }
                $('<div><span class="weapon">' + attachHtml + '</span></div>').appendTo(f);
            }
        }

        //var blockedLosHex = weaponManager.getBlockedHexes(); //Are there any blocked hexes, no point checking if no.
        var blockedLosHex = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.        
        var loSBlocked = false; //Default to LoS not blocked.
        var skinDanceBlocked = null;
        // Attached pod logic
        var attachedUnitHidden = false;
        // Host targeting Pod restriction
        if (selectedShip.hasAttached && selectedShip.hasAttached[ship.id] !== undefined) {
            attachedUnitHidden = true; // Parent cannot target the attached pod
        }
        if (ship.attached && Object.keys(ship.attached).length > 0) {
            var hostId = Object.keys(ship.attached)[0];
            var podLocation = parseInt(ship.attached[hostId]);
            if (!isNaN(podLocation) && podLocation !== 0) {
                var hostShip = gamedata.getShip(hostId);
                var hostSections = weaponManager.getShipHittingSide(selectedShip, hostShip);
                if (hostShip) {
                    if (!hostSections.includes(podLocation)) {
                        attachedUnitHidden = true; // Pod is attached to a side not facing the shooter
                    }
                }
            }
        }


        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];
            var attachedWeaponHidden = false;
            var clawBlindSpot = false;

            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {

                    //Check for skin-dancing ships, these can't be targeted unless the shooter is also skin-dancing on same target.
                    if (gamedata.gamephase == 3) {
                        let sharedSkinDancing = false;
                        //Check if TARGET is skindancing
                        if (ship.skinDancing && Object.values(ship.skinDancing).includes(true)) {
                            for (const [targetID, value] of Object.entries(ship.skinDancing)) {
                                if (value === true && selectedShip.skinDancing && selectedShip.skinDancing[targetID] === true) {
                                    sharedSkinDancing = true;
                                    break;
                                }
                            }

                            if (!sharedSkinDancing) {
                                skinDanceBlocked = 'Target'; //Can't target a skin-dancing ship if shooter is not skindancing same Enormous unit
                            }
                        }

                        //Check if SHOOTER is skindancing
                        if (selectedShip.skinDancing && Object.values(selectedShip.skinDancing).includes(true)) {
                            var targetCompassHeading = mathlib.getCompassHeadingOfShip(selectedShip, ship);
                            var shooterFacing = shipManager.getShipHeadingAngle(selectedShip);
                            var targetBearing = mathlib.getAngleBetween(shooterFacing, targetCompassHeading, true);

                            //Restriction: If not shooting Host, AND in valid arc, AND not shared -> Block.
                            //Inverse (Allow): Shooting Host OR Side/Rear OR Shared.
                            if (!selectedShip.skinDancing[ship.id] && (targetBearing < 60 || targetBearing > 300) && !sharedSkinDancing) skinDanceBlocked = 'Shooter';
                        }
                    }
                    //New check to prevent attached ship from firing at it's host UNLESS it's a boarding weapon.    
                    //If selected ship is a flight, it cannot fire ANY non-boarding weapons at ANY target while attached.
                    if (selectedShip.attached && Object.keys(selectedShip.attached).length > 0) {
                        if (selectedShip.flight || selectedShip.attached[ship.id] !== undefined) {
                            if (!weapon.isBoardingAction) {
                                attachedWeaponHidden = true; // Prevent pods and ships from firing weapons at each others.
                            }
                        }
                        if (!weapon.isBoardingAction && weaponManager.isTargetInGrapplingClawBlindSpot(selectedShip, ship)) {
                            clawBlindSpot = true;
                        }
                    }




                    if (blockedLosHex.length > 0 && !loSBlocked) {
                        var sPosShooter = weaponManager.getFiringHex(selectedShip, weapon);
                        var sPosTarget = shipManager.getShipPosition(ship);
                        //If one weapon has blocked LoS, they all do so change value outside loop
                        loSBlocked = mathlib.isLoSBlocked(sPosShooter, sPosTarget, blockedLosHex);
                    }

                    if (weapon.ignoresLoS) loSBlocked = false;

                    var value = weapon.firingMode;
                    value = weapon.firingModes[value];
                    var keys = Object.keys(weapon.firingModes);

                    if (ship.Huge > 0 || attachedUnitHidden || attachedWeaponHidden || clawBlindSpot) { //Cannot Target larger terrain or POds that are attached to non-facing sides
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="cannotTarget"> Cannot Target</span></div>').appendTo(f);
                    } else if (loSBlocked) {
                        // LOS is blocked - only display the blocked message
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="losBlocked"> Line of Sight Blocked</span></div>').appendTo(f);
                    } else if (skinDanceBlocked !== null) {
                        // Can't target outside forward 120 degrees if skin-dancing
                        $('<div><span class="weapon">' + weapon.displayName + ': </span><span class="skinDanceBlocked">' + skinDanceBlocked + ' is Skin Dancing</span></div>').appendTo(f);
                    } else if (weapon.hextarget) {
                        // Don't show hit chance if targeting the hex.
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="hexTargeted"> Hex Targeted</span></div>').appendTo(f);
                    } else if (weaponManager.getLinkedFiringBlock(selectedShip, weapon, ship) != null) {
                        // Firing-linked weapon: this target is disallowed by a sibling's existing order
                        // (a different unit, or outside the group's angular spread). Shown while aiming
                        // so the restriction is visible before the click; the click itself is blocked
                        // with the full reason in targetShip.
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="cannotTarget"> Linked Firing</span></div>').appendTo(f);
                    } else {
                        // LOS is not blocked, not hex targeted, show normal hit chance info, check Sweeping weapons first.
                        if (calledid != null && !weaponManager.canWeaponCall(weapon)) {
                            $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="cannotCalled"> Cannot Called Shot</span></div>').appendTo(f);
                        } else {
                            var result = weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid);
                            var hitChance = result.hitChance;

                            var tooltipText = weaponManager.buildHitChanceTooltipText(result);
                            var chanceClass = (hitChance <= 0 ? 'negHitchange' : 'posHitchange');
                            if (tooltipText) chanceClass += ' hit-chance-tooltip';
                            var dataAttr = tooltipText ? ' data-tooltip="' + tooltipText + '"' : '';

                            if (keys.length > 1) {
                                $('<div><span class="weapon">' + weapon.displayName + ': <span class="firingMode"> (' + value + ')</span> - <span class="' + chanceClass + '"' + dataAttr + '>Approx: ' + hitChance + '%</span></div>').appendTo(f);
                            } else {
                                $('<div><span class="weapon">' + weapon.displayName + ': </span><span class="' + chanceClass + '"' + dataAttr + '>Approx: ' + hitChance + '%</span></div>').appendTo(f);
                            }
                        }
                    }
                } else {
                    $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="notInRange"> Not In Range</span></div>').appendTo(f);
                }
            } else {
                $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="notInArc"> Not In Arc </span></div>').appendTo(f);
            }
        }

    },


    canWeaponCall: function canWeaponCall(weapon) {
        //is this weapon eleigible for calling precision shot?...
        //Standard or Pulse, not Ballistic!
        //18 August 2022 (Geoffrey Stano) - With Marcin's input a new flag was created "overrideCallingRestrictions"
        //which can be used to specifically override the no ballistic called shots with the four lines below updated
        //		if (weapon.ballistic || weapon.hextarget) return false;
        if (weapon.hextarget) return false;
        if (weapon.overrideCallingRestrictions) return true; //weapon feature specifically overriden to allow called shot
        if (weapon.ballistic) return false; //ballistic weapons cannot do called shots
        if (weapon.damageType == 'Standard' || weapon.damageType == 'Pulse') return true;
        return false;
    },

    canCalledshot: function canCalledshot(target, system, shooter) {
        /*Marcin Sawicki, new version $outerSections-based - October 2017*/
        var sectionEligible = false; //section that system is mounted on is eligible for caled shots
        if (!shooter) return false;
        if (system.isTargetable != true) return false; //cannot be targeted by called shots under any conditions

        //a system may belong to ANOTHER section's structure block than the one it is displayed
        //on (structureHomeLocation - Kirishiac orbitals): it can only be hit from its HOME
        //block's facings, so match section arcs against the home location
        var sysLoc = (system.structureHomeLocation !== undefined && system.structureHomeLocation !== null) ? system.structureHomeLocation : system.location;

        if (target.flight) return true; //allow called shots at fighters (in effect it will affect particular fighter, not fighter system)

        //Added fragment below to allow Limpet Bore Torpedo to target any exterior system, no other weapon should meet criteria at this time - DK - 16 Apr 2024
        for (var i in gamedata.selectedSystems) {
            var shooterSystem = gamedata.selectedSystems[i];
            if (shooterSystem.weapon && shooterSystem.canTargetAllExtSections && (system.location != 0 || system.location == 0 && system.isPrimaryTargetable)) return true;
        }

        // If target is an attached ship (e.g. breaching pod), use the attachment location directly
        // instead of compass heading, which defaults to Front for same-hex speed-0 ships.
        if (shooter.attached && Object.keys(shooter.attached).length > 0) {
            var hostId = Object.keys(shooter.attached)[0];
            var attachedLocation = parseInt(shooter.attached[hostId]);
            if (!isNaN(attachedLocation) && attachedLocation !== 0) {
                // Only allow called shots against systems on the section the target is attached to
                if (sysLoc === attachedLocation) {
                    // Check if this section is eligible for called shots
                    for (var j = 0; j < target.outerSections.length; j++) {
                        if (target.outerSections[j].loc === attachedLocation && target.outerSections[j].call === true) {
                            return true;
                        }
                    }
                }
                // Also check primary-targetable systems (location 0) that overlap the attached section
                if (system.location === 0 && system.isPrimaryTargetable) {
                    return true;
                }
                return false; // System is not on the attached section
            }
        }

        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);
        var targetFacing = shipManager.getShipHeadingAngle(target);

        for (var i = 0; i < target.outerSections.length; i++) {
            var currSectionData = target.outerSections[i];
            var arcFrom = 0;
            var arcTo = 0;
            if (sysLoc == currSectionData.loc) {

                if (shipManager.movement.isRolled(target)) {
                    arcTo = mathlib.addToDirection(currSectionData.min, currSectionData.min * -2);
                    arcFrom = mathlib.addToDirection(currSectionData.max, currSectionData.max * -2);
                } else { //ship NOT rolled
                    arcFrom = currSectionData.min;
                    arcTo = currSectionData.max;
                }
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(arcFrom, targetFacing), mathlib.addToDirection(arcTo, targetFacing))) {
                    if (currSectionData.call == true) return true;
                }

                /*old version - not taking Rolled state into account
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(currSectionData.min, targetFacing), mathlib.addToDirection(currSectionData.max, targetFacing))) {
                    if (currSectionData.call == true) return true;
                }
                */
                sectionEligible = currSectionData.call;
            }
            //"loc" => $curr['loc'], "min" => $curr['min'], "max" => $curr['max'], "call" => $call
        }
        //options here: PRIMARY, incorrect facing of targeted section, section not eligible for called shots (eg. on MCVs)
        if (sysLoc > 0 && sectionEligible == true) {
            return false; //non-PRIMARY and eligible for called shots, but still here => must be out of arc!
        }
        //option here: section not normally eligible for target shots (PRIMARY or outer section on MCV)
        //check whether system is PRIMARY-targetable!
        if (system.isPrimaryTargetable != true) return false; //cannot be targeted under these conditions
        //check whether it's in arc
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(system.startArc, targetFacing), mathlib.addToDirection(system.endArc, targetFacing))) {
            return true;
        }
        return false;
    }, //endof function canCalledshot



    getTargetableThruster: function getTargetableThruster(shooter, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);

        //if (target.draziHCV){ //ALWAYS, not just for Drazi HCV layout!
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            return 1;
        }
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            return 2;
        }
        //}
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            return 3;
        }
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            return 4;
        }
    },

    isPosOnWeaponArc: function isPosOnWeaponArc(shooter, position, weapon) {
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);
        var targetCompassHeading = mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(shooter), position);

        //Some weapons like Transverse Drive have special arcs, refer to weapon function to check these separately.
        if (weapon.specialArcs) {
            var onArc = weapon.isPosOnSpecialArc(shooter, position);
            //confirm.error("Target hex is not in arc.");	          
            return onArc;
        }

        //A split-arc mount (Shadow Heavy Slicer) bears in EVERY one of its arcs, exactly as
        //isOnWeaponArc treats it for ship targets - and the live startArc/endArc is no guide to
        //which one, since changeFiringMode indexes startArcArray by firing mode and a split pair
        //sits at indices 0 and 1. Without this, picking intercept targets off incoming ballistics
        //and hex targeting both saw one arbitrary arc of the pair.
        if (weapon.splitArcs) {
            var multipleArcs = shipManager.systems.getMultipleArcs(shooter, weapon);
            if (multipleArcs.length) {
                return multipleArcs.some(function (arc) {
                    return mathlib.isInArc(targetCompassHeading,
                        mathlib.addToDirection(arc.start, shooterFacing),
                        mathlib.addToDirection(arc.end, shooterFacing));
                });
            }
        }

        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);

        return mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end);
    },

    isOnWeaponArc: function isOnWeaponArc(shooter, target, weapon) {
        if (weapon.splitArcs) return weaponManager.isOnWeaponArcMultiple(shooter, target, weapon);

        //console.log("is on arc");
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);
        var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);

        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
        var oPos = shipManager.getShipPosition(shooter);
        var tPos = shipManager.getShipPosition(target);

        /*if two ships are at same hex, then their relative position depends on THIS TURN Ini;
        and it should on PREVIOUS turn Ini... this may affect ability to launch missiles. 
        Hence at range 0 missile launch is always allowed, no matter the arc.
        */
        if (weapon.ballistic && oPos.equals(tPos)) return true;


        return mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end);
    },

    //Weapons like Shadow Heavy Slicer have two distinct arcs to check
    isOnWeaponArcMultiple: function isOnWeaponArcMultiple(shooter, target, weapon) {
        const shooterFacing = shipManager.getShipHeadingAngle(shooter);
        const targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);

        const oPos = shipManager.getShipPosition(shooter);
        const tPos = shipManager.getShipPosition(target);

        /* Range-0 ballistic exception */
        if (weapon.ballistic && oPos.equals(tPos)) return true;

        // Get all weapon arcs (already roll-corrected)
        const arcs = shipManager.systems.getMultipleArcs(shooter, weapon);

        // No arcs = cannot fire
        if (!arcs.length) return false;

        // Check against ANY arc
        for (let i = 0; i < arcs.length; i++) {
            const arc = arcs[i];

            const start = mathlib.addToDirection(arc.start, shooterFacing);
            const end = mathlib.addToDirection(arc.end, shooterFacing);

            if (mathlib.isInArc(targetCompassHeading, start, end)) {
                return true;
            }
        }

        return false;
    },

    isTargetInGrapplingClawBlindSpot: function isTargetInGrapplingClawBlindSpot(shooter, target) {
        if (shooter.flight) return false;
        if (!shooter.attached || Object.keys(shooter.attached).length === 0) return false;

        var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);

        for (var i in shooter.systems) {
            var system = shooter.systems[i];
            if (system.name === "GrapplingClaw" && !shipManager.systems.isDestroyed(shooter, system)) {
                // If this claw is currently attached to something
                if (system.hostShipId && system.hostShipId > 0) {
                    var arcs = shipManager.systems.getArcs(shooter, system);
                    var start = mathlib.addToDirection(arcs.start, shooterFacing);
                    var end = mathlib.addToDirection(arcs.end, shooterFacing);
                    if (mathlib.isInArc(targetCompassHeading, start, end)) {
                        return true;
                    }
                }
            }
        }
        return false;
    },

    calculateRangePenalty: function calculateRangePenalty(distance, weapon) {
        var rangePenalty = 0;

        if (weapon.specialRangeCalculation) {
            rangePenalty = weapon.calculateSpecialRangePenalty(distance);
        } else { //standard calculation
            rangePenalty = weapon.rangePenalty * distance;
        }
        return rangePenalty;
    },

    /*Marcin Sawicki, September 2019: simplified to using calculateHitChange instead*/
    calculataBallisticHitChange: function calculataBallisticHitChange(ball, calledid) {
        var shooter = gamedata.getShip(ball.shooterid);
        var weapon = shipManager.systems.getSystem(shooter, ball.weaponid);
        var target = gamedata.getShip(ball.targetid);
        return weaponManager.calculateHitChange(shooter, target, weapon, calledid).hitChance;
    },//endof calculataBallisticHitChange

    /* Kept only as the legacy entry point: it was always called as getInterception(ball) where
       ball carried .fireOrderId, and it never applied degradation or read the ORDER's firing mode.
       getDeclaredInterception does both. Returns d20 points - multiply by 5 for a percentage. */
    getInterception: function getInterception(ball) {
        if (!ball) return 0;
        return weaponManager.getDeclaredInterception(ball.fireOrderId, ball.weapon);
    },

    calculateBaseHitChange: function calculateBaseHitChange(target, base, shooter, weapon) {
        var jink = 0;
        var dew = 0;

        //if (target.flight && shooter) {
        if ((target.flight || target.jinkinglimit > 0) && shooter) {
            if (!shooter.flight) {
                jink = shipManager.movement.getJinking(target);
            } else {
                if (shooter) {
                    var sPosHex = shipManager.getShipPosition(shooter);
                    var tPosHex = shipManager.getShipPosition(target);

                    if ((weapon.ballistic) || (!sPosHex.equals(tPosHex)) || (shipManager.movement.getJinking(shooter) > 0)) { //same hex direct fire ignores jinking
                        jink = shipManager.movement.getJinking(target);
                    }
                }
            }

            if (weapon && weapon.ignoreJinking) { //some weapons do ignore Jinking completely
                jink = 0;
            }
        }

        if (!target.flight) {
            dew = ew.getDefensiveEW(target);
        }

        var bdew = 0;
        var sdew = 0;

        sdew = ew.getSupportedDEW(target);
        bdew = ew.getSupportedBDEW(target);

        if (shooter && shooter.flight && !weapon.ballistic) {
            dew = 0;
            bdew = 0;
            sdew = 0;
        }

        //advanced sensors: negates BDEW and SDEW, unless target is unit of advanced race
        if (shooter && (target.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors"))) {
            bdew = 0;
            sdew = 0;
        }

        //some weapons do ignore EW completely
        if (weapon && weapon.ignoreAllEW) {
            dew = 0;
            bdew = 0;
            sdew = 0;
        }

        //half-phasing target is more difficult to hit
        var halfphase = 0;
        if (shooter && weapon) { //consider half-phasing in calculations for a particular shot, but not in base profile
            if (shipManager.movement.isHalfPhased(target)) {
                halfphase = 4; //basic penalty induced by half-phasing target
                //ballistics double the penalty
                if (weapon && weapon.ballistic) halfphase = 8;
            }
            //if firing unit is itself half phasing, that's -10
            if (shooter && shipManager.movement.isHalfPhased(shooter)) halfphase = 10;
        }

        return base - dew - jink - bdew - sdew - halfphase;
    },


    /*calculate hit chance for ramming attack - different procedure*/
    /*also, it would be a bit different (simplified) from B5Wars original*/
    /* Refactored DK 2026: returns same shape as calculateHitChange so the
       targeting tooltip can show a per-modifier breakdown. */
    calculateRamChance: function calculateRamChance(shooter, target, weapon, calledid) {
        function makeResult(hitChance, opts) {
            opts = opts || {};
            return {
                hitChance: hitChance,
                autoHit: !!opts.autoHit,
                isOutOfRange: false,
                breakdownReason: opts.breakdownReason || null,
                modifiers: opts.modifiers || [],
                _otherDetail: []
            };
        }

        if (calledid > 0) return makeResult(0, { breakdownReason: 'Ramming: cannot called shot' });
        if ((!shooter.flight) && (target.flight)) return makeResult(0, { breakdownReason: 'Ramming: ship cannot ram a fighter' });

        var shooterHalfphased = shipManager.movement.isHalfPhased(shooter);
        var targetHalfphased = shipManager.movement.isHalfPhased(target);
        if (shooterHalfphased != targetHalfphased) return makeResult(0, { breakdownReason: 'Ramming: half-phase mismatch' });

        var modifiers = [];
        function pushIfNonZero(key, label, value) {
            if (value !== 0) modifiers.push({ key: key, label: label, value: value });
        }

        pushIfNonZero('base', 'Base Chance', 8); //40%
        if (target.Enormous)  pushIfNonZero('targetEnormous',  'Target Enormous',  6);
        if (shooter.Enormous) pushIfNonZero('shooterEnormous', 'Shooter Enormous', 6);

        if (!shooter.flight && (target.shipSizeClass >= 3) && (shooter.shipSizeClass < 3)) {
            pushIfNonZero('targetCapital', 'Target Capital',  2);
        }
        if ((shooter.shipSizeClass >= 3) && (target.shipSizeClass < 3)) {
            pushIfNonZero('subCapTarget',  'Sub-Capital Target', -2);
        }
        if (shooter.flight && !target.flight) {
            pushIfNonZero('fighterVsShip', 'Fighter vs Ship', 4);
        }

        var targetSpeed = Math.abs(shipManager.movement.getSpeed(target));
        var speedMod;
        switch (targetSpeed) {
            case 0: speedMod =  5; break;
            case 1: speedMod =  3; break;
            case 2:
            case 3: speedMod =  2; break;
            case 4:
            case 5: speedMod =  1; break;
            default: speedMod = -Math.ceil((targetSpeed - 5) / 5);
        }
        pushIfNonZero('targetSpeed',     'Target Speed',     speedMod);
        pushIfNonZero('shooterJinking',  'Shooter Jinking', -shipManager.movement.getJinking(shooter));
        pushIfNonZero('targetJinking',   'Target Jinking',  -shipManager.movement.getJinking(target));
        pushIfNonZero('fireControl',     'Fire Control',     weaponManager.getFireControl(target, weapon));

        //HK Targeting: penalty per hex the ramming unit moved this turn (speed == hexes moved).
        //Normally the class' own rangePenalty (-1/3); worsens to -1/2 when the flight is UNCONTROLLED
        //(command link severed). Gated to the three Orieni Hunter-Killer classes - mirrors calculateHitBaseRam.
        var ownSpeed = Math.abs(shipManager.movement.getSpeed(shooter));
        var hkClasses = ['HkShiningStar', 'HkShiningLight', 'hkBlazingStarGC'];
        var hkRangePenalty = weapon.rangePenalty;
        if (hkClasses.indexOf(shooter.phpclass) !== -1 && shipManager.movement.isUncontrolled(shooter)) {
            hkRangePenalty = 0.5; //-1/2 hexes when uncontrolled
        }
        pushIfNonZero('range',           'Targeting',    -(hkRangePenalty * ownSpeed));

        var sum = modifiers.reduce(function (s, m) { return s + m.value; }, 0);
        var hitChance = Math.round(sum * 5); //d20 -> d100
        return makeResult(hitChance, { modifiers: modifiers });
    }, //endof calculateRamChance

    /* ------------------------------------------------------------
       OLD calculateRamChance — kept for reference. Do not call.
       Replaced by the breakdown-returning version above. Behavior preserved.
       ------------------------------------------------------------
    calculateRamChance: function (shooter, target, weapon, calledid) {
        if (calledid > 0) return 0;//can't call ramming attack!
        if ((!shooter.flight) && (target.flight)) return 0;//ship has no chance to ram a fighter!
        var hitChance = 8; //base: 40%

        //half-phased and non-half-phased ship cannot ram each other
        var shooterHalfphased = shipManager.movement.isHalfPhased(shooter);
        var targetHalfphased = shipManager.movement.isHalfPhased(target);
        if (shooterHalfphased != targetHalfphased) return 0;

        if (target.Enormous) hitChance += 6;//+6 vs Enormous units
        if (shooter.Enormous) hitChance += 6;//+6 if ramming unit is Enormous

        if (!shooter.flight) { //upon re-reading - this bonus does not apply to fighters
            if ((target.shipSizeClass >= 3) && (shooter.shipSizeClass < 3)) hitChance += 2;//+2 if target is Capital and ramming unit is not
        }

        if ((shooter.shipSizeClass >= 3) && (target.shipSizeClass < 3)) hitChance -= 2;//-2 if shooter is Capital and rammed unit is not
        if ((shooter.flight) && (!target.flight)) hitChance += 4;//+4 for fighter trying to ram a ship
        var targetSpeed = Math.abs(shipManager.movement.getSpeed(target)); //I think speed cannot be negative, but just in case ;)
        switch (targetSpeed) {
            case 0: //+5 if the target is not moving.
                hitChance += 5;
                break;
            case 1://+3 if the target is moving speed 1.
                hitChance += 3;
                break;
            case 2://+2 if the target is moving speed 2 or 3.
            case 3:
                hitChance += 2;
                break;
            case 4://+1 if the target is moving speed 4 or 5.
            case 5:
                hitChance += 1;
                break;
            default: //this means >5; ‐1 for every 5 points of speed (or fraction thereof) that the target is moving faster than 5.
                hitChance -= Math.ceil((targetSpeed - 5) / 5);
        }
        //‐1 for every level of jinking the ramming or target unit is using
        hitChance -= shipManager.movement.getJinking(shooter);
        hitChance -= shipManager.movement.getJinking(target);

        //fire control: usually 0, but units specifically designed for ramming may have some bonus!
        hitChance += weaponManager.getFireControl(target, weapon);

        //range penalty - based on ramming units' speed (typical ramming has no range penalty, but HKs do!
        var ownSpeed = Math.abs(shipManager.movement.getSpeed(shooter));
        var rangePenalty = weapon.rangePenalty * ownSpeed;
        hitChance -= rangePenalty;

        hitChance = Math.round(hitChance * 5); //convert d20->d100
        return hitChance;
    }, //endof calculateRamChance
    ------------------------------------------------------------ */


    /* ------------------------------------------------------------------------
       BOARDING ATTACHMENT - front-end mirror of Marines::isAttachBlocked
       (source/server/model/weapons/specialWeapons.php).

       Limits are per STRUCTURE SECTION: two breaching pods per section, one
       grappling claw per section which it holds exclusively, and one attached
       craft in total on an LCV or OSAT. Without this mirror the player saw a
       healthy hit chance and the server silently cancelled the attachment.

       The SERVER rolls which section is hit, so these helpers only ever answer
       "is EVERY section this shooter could reach blocked". If some are free we
       must not pretend to know which one the server will pick.
       ------------------------------------------------------------------------ */

    //Section key for an EXISTING attachment. Mirrors BaseShip::getStructureSystem,
    //which falls back to Primary (0) for any location with no Structure of its own -
    //so an MCV's locations 1 and 2 are correctly the same section. No destroyed rule
    //here: the server resolves stored attachments the same way, breached or not.
    resolveAttachSection: function resolveAttachSection(target, location) {
        var loc = parseInt(location, 10);
        if (isNaN(loc) || loc === 0) return 0;

        var structure = shipManager.systems.getStructureSystem(target, loc);
        if (!structure) return 0;

        var structLoc = parseInt(structure.location, 10);
        return isNaN(structLoc) ? 0 : structLoc;
    },

    //Section the server would roll for a hit at this outer location. Mirrors
    //BaseShip::getHitSection, which redirects to Primary once the facing structure
    //was destroyed as of turn-1 - which is exactly what the client's destroyed flag
    //shows during order entry, since this turn's damage has not resolved yet.
    resolveAttachCandidate: function resolveAttachCandidate(target, location) {
        var loc = parseInt(location, 10);
        if (isNaN(loc) || loc === 0) return 0;

        var structure = shipManager.systems.getStructureSystem(target, loc);
        if (!structure) return 0;                                          //no Structure of its own
        if (shipManager.systems.isDestroyed(target, structure)) return 0;  //breached -> Primary

        return loc;
    },

    /* How many pods the PRIMARY section (0) can hold. On a hull with exterior Structures,
       pods reach Primary only through a breach - getHitSection redirects a hit to 0 exactly
       when the facing structure is destroyed - so its capacity is two per BREACHED exterior
       section, and zero while the hull is intact. A hull with no exterior structures (MCV,
       LCV, OSAT) keeps the flat two, because for those Primary IS the only section.
       Mirrors Marines::getPrimaryPodCap. */
    getPrimaryPodCap: function getPrimaryPodCap(target) {
        var exterior = 0;
        var breached = 0;

        for (var loc in target.structures) {
            if (parseInt(loc, 10) === 0) continue; //Primary itself is not a way in

            exterior++;
            var structure = shipManager.systems.getSystem(target, target.structures[loc]);
            if (structure && shipManager.systems.isDestroyed(target, structure)) breached++;
        }

        if (exterior === 0) return 2; //Primary is the hull's only section

        return 2 * breached;
    },

    //Slots this unit takes on its section: live pods for a flight, the whole
    //section for a claw ship.
    getAttachFootprint: function getAttachFootprint(unit) {
        if (!unit.flight) return 1;

        var live = 0;
        for (var i in unit.systems) {
            if (!shipManager.systems.isDestroyed(unit, unit.systems[i])) live++;
        }
        return live;
    },

    //An LCV or OSAT supports a single attached craft, pod flight or claw ship.
    isSingleAttachHull: function isSingleAttachHull(target) {
        return (target.hangarRequired === 'LCVs' || !!target.osat);
    },

    /* Hull-wide ceiling on attached pods, applied on top of the two-per-section rule, so a
       many-sectioned hull (a Vree saucer has six outer Structure blocks plus Primary) can
       never hold more than its size class allows. Enormous units and bases are exempt from
       the class table and take 12. Mirrors Marines::getHullPodCap. */
    getHullPodCap: function getHullPodCap(target) {
        if (target.base || target.Enormous) return 12;
        if (target.shipSizeClass >= 3) return 8;   //capital
        if (target.shipSizeClass == 2) return 4;   //HCV
        return 2;                                  //medium ship and smaller
    },

    //Opposite-ends pairing for the capital claw rule. Only 1<->2 and 3<->4 exist on
    //a non-base capital hull and Primary is never a valid partner; bases and Enormous
    //units skip this test entirely, exactly as the server does.
    isOppositeSection: function isOppositeSection(a, b) {
        a = parseInt(a, 10);
        b = parseInt(b, 10);
        if (!a || !b) return false;
        if (a === 1) return (b === 2);
        if (a === 2) return (b === 1);
        if (a === 3) return (b === 4);
        if (a === 4) return (b === 3);
        return false;
    },

    //Human-readable structure-section name, for telling the player where a boarder will land.
    //Same labels ShipInfo.js uses for already-attached units, plus Primary.
    getSectionLabel: function getSectionLabel(loc) {
        var labels = {
            0: "Primary", 1: "Forward", 2: "Aft",
            3: "Port", 31: "Port-Forward", 32: "Port-Aft",
            4: "Starboard", 41: "Starboard-Forward", 42: "Starboard-Aft"
        };
        var name = labels[parseInt(loc, 10)];
        return name ? name : "section " + loc;
    },

    //Mirror of BaseShip::getBearingOnUnit - the boarder's bearing relative to the target's own
    //facing, mirrored when the target is rolled exactly as the server does.
    getAttachRelativeBearing: function getAttachRelativeBearing(target, unit) {
        var bearing = mathlib.addToDirection(
            mathlib.getCompassHeadingOfShip(target, unit),
            -shipManager.getShipHeadingAngle(target));

        if (shipManager.movement.isRolled(target) && bearing !== 0) bearing = 360 - bearing;

        return Math.round(bearing);
    },

    /* Mirror of BaseShip::doGetAttachSectionBearing. A boarding unit attaches to the section
       whose arc is CENTRED on the hex edge it crossed, not merely to one that contains its
       bearing - so the player can see and plan the section rather than watch the server roll it.

       Returns the raw location, or null when two sections are equally centred on that edge (the
       server falls back to its profile-weighted roll there, and we must not claim to know which
       it picks). outerSections carries the same {loc, min, max} arcs as the server's
       getLocations(), minus Primary - which has no arc and is reached only via a breach. */
    getAttachLocation: function getAttachLocation(shooter, target) {
        if (!target.outerSections || target.outerSections.length === 0) return 0; //Primary-only hull

        var bearing = weaponManager.getAttachRelativeBearing(target, shooter);
        var best = null;
        var bestDist = null;
        var tied = false;

        for (var i = 0; i < target.outerSections.length; i++) {
            var arc = target.outerSections[i];
            if (!mathlib.isInArc(bearing, arc.min, arc.max)) continue;

            var dist = mathlib.getAngleDistance(bearing, mathlib.getArcCentre(arc.min, arc.max));

            if (bestDist === null || dist < bestDist - 0.001) {
                bestDist = dist;
                best = parseInt(arc.loc, 10);
                tied = false;
            } else if (dist < bestDist + 0.001 && parseInt(arc.loc, 10) !== best) {
                tied = true; //two distinct sections equally centred - the server rolls
            }
        }

        if (best === null || tied) return null;
        return best;
    },

    //The section a boarder actually lands on: the entry-edge location, then the breached-structure
    //-> Primary redirect. null when getAttachLocation could not commit to one.
    getAttachSection: function getAttachSection(shooter, target) {
        var loc = weaponManager.getAttachLocation(shooter, target);
        if (loc === null) return null;

        return weaponManager.resolveAttachCandidate(target, loc);
    },

    //Mirror of Marines::getSectionOccupancy. skipId excludes the unit being tested.
    //Destroyed attached units are skipped so a dead claw does not hold a section.
    //
    //NOTE: unlike the server this cannot see attachments DECLARED but not yet resolved this
    //turn - the client never learns the opponent's declarations. So every answer here is
    //occupancy as of the START of the turn; a section that fills up during resolution is
    //reported by the server in the combat log instead. See Marines::getPendingAttachments.
    getSectionOccupancy: function getSectionOccupancy(target, skipId) {
        var occ = { sections: {}, podTotal: 0, clawTotal: 0, unitTotal: 0, clawSections: [] };
        if (!target.hasAttached) return occ;

        for (var attachedId in target.hasAttached) {
            if (skipId !== undefined && attachedId == skipId) continue;

            var unit = gamedata.getShip(attachedId);
            if (!unit) continue;
            if (shipManager.isDestroyed(unit)) continue;

            var section = weaponManager.resolveAttachSection(target, target.hasAttached[attachedId]);
            if (!occ.sections[section]) occ.sections[section] = { pods: 0, claws: 0, units: 0 };

            occ.unitTotal++;
            occ.sections[section].units++;

            if (unit.flight) {
                var pods = weaponManager.getAttachFootprint(unit);
                occ.sections[section].pods += pods;
                occ.podTotal += pods;
            } else {
                occ.sections[section].claws++;
                occ.clawTotal++;
                occ.clawSections.push(section);
            }
        }

        return occ;
    },

    //Mirror of Marines::isAttachBlocked for ONE resolved section. Returns the reason
    //string if the attachment is refused, or null if it is allowed.
    getAttachBlockedReason: function getAttachBlockedReason(target, shooter, section, occ) {
        var here = occ.sections[section] || { pods: 0, claws: 0, units: 0 };
        var isClaw = !shooter.flight;

        //A. LCV / OSAT - one attached craft on the whole hull.
        if (weaponManager.isSingleAttachHull(target)) {
            if (occ.unitTotal >= 1) return 'This unit can only support a single attached craft.';
            return null;
        }

        //B. A claw ship holds its section exclusively - pods may never join it.
        //Deliberately asymmetric: a claw may still take a section holding pods.
        if (here.claws >= 1) return 'A Grappling Claw already holds this section.';

        if (isClaw) {
            //C. Whole-hull claw caps. Bases and Enormous units have no hull-wide cap -
            //one claw per section (rule B) is their only limit.
            if (!target.base && !target.Enormous) {
                if (target.shipSizeClass <= 2) { //medium ship or HCV
                    if (occ.clawTotal >= 1) return 'Only one vessel may grapple a medium ship or HCV.';
                } else { //capital
                    if (occ.clawTotal >= 2) return 'A capital ship can be grappled by two vessels only.';
                    if (occ.clawTotal === 1 && !weaponManager.isOppositeSection(occ.clawSections[0], section)) {
                        return 'Grappling vessels must attach to opposite ends.';
                    }
                }
            }
            return null;
        }

        var footprint = weaponManager.getAttachFootprint(shooter);

        //E. Pods: two per section, counting live pods across every attached flight.
        //Primary is the exception - two per BREACHED exterior section. See getPrimaryPodCap.
        var sectionCap = (section === 0) ? weaponManager.getPrimaryPodCap(target) : 2;

        if (here.pods + footprint > sectionCap) {
            return (sectionCap === 0)
                ? 'Breaching Pods can only reach the Primary section through a destroyed structure section.'
                : 'No room for more Breaching Pods on this section.';
        }

        //F. Hull-wide ceiling by size class, on top of the per-section rule.
        if (occ.podTotal + footprint > weaponManager.getHullPodCap(target)) {
            return 'This ship cannot support any more Breaching Pods.';
        }

        return null;
    },

    /* Where this unit will attach and whether it can. Single source of truth for the boarding
       hit chance, the targeting tooltip and the declaration-time warning.

       Returns { section, label, reason, certain } where:
         section  the resolved structure section, or null when the server will roll it
         label    that section's name, or null
         reason   why the attachment is refused, or null if it is allowed
         certain  true when we know the exact section, false when the server rolls between ties

       When the section is known we test only THAT section - the honest answer, and what lets
       the UI name it. When it ties we fall back to the old conservative test: refuse only if
       EVERY reachable section is blocked, because we must not pretend to know the server's roll. */
    getBoardingAttachInfo: function getBoardingAttachInfo(shooter, target) {
        var unknown = { section: null, label: null, reason: null, certain: false };

        //Targets nothing can attach to anyway - the callers refuse these separately, and terrain
        //has no outerSections to walk.
        if (!target || target.flight || target.mine || target.shipSizeClass == 5) return unknown;

        //An already-attached unit keeps the section it holds and is never refused.
        if (target.hasAttached && target.hasAttached[shooter.id] !== undefined) {
            var held = weaponManager.resolveAttachSection(target, target.hasAttached[shooter.id]);
            return { section: held, label: weaponManager.getSectionLabel(held), reason: null, certain: true };
        }

        var occ = weaponManager.getSectionOccupancy(target, shooter.id);
        var section = weaponManager.getAttachSection(shooter, target);

        if (section !== null) {
            return {
                section: section,
                label: weaponManager.getSectionLabel(section),
                reason: weaponManager.getAttachBlockedReason(target, shooter, section, occ),
                certain: true
            };
        }

        //Tie: the server rolls. Only assert a refusal if nothing at all is reachable.
        var candidates = target.outerSections ? weaponManager.getShipHittingSide(shooter, target) : null;
        if (!candidates || candidates.length === 0) candidates = [0];

        var seen = {};
        var lastReason = null;
        for (var i = 0; i < candidates.length; i++) {
            var candidate = weaponManager.resolveAttachCandidate(target, candidates[i]);
            if (seen[candidate]) continue; //two locations can collapse onto one section
            seen[candidate] = true;

            var reason = weaponManager.getAttachBlockedReason(target, shooter, candidate, occ);
            if (reason === null) return unknown; //at least one section is still free
            lastReason = reason;
        }

        return { section: null, label: null, reason: lastReason, certain: false };
    },

    //True when this unit cannot attach to the target at all. See getBoardingAttachInfo.
    isBoardingFullyBlocked: function isBoardingFullyBlocked(shooter, target) {
        return (weaponManager.getBoardingAttachInfo(shooter, target).reason !== null);
    },

    //calculate hit chance for Boarding Action - different procedure
    /* Refactored DK 2026: returns same shape as calculateHitChange so the
       targeting tooltip can show a per-modifier breakdown. */
    calculateBoardingAction: function calculateBoardingAction(shooter, target, weapon) {
        function makeResult(hitChance, opts) {
            opts = opts || {};
            return {
                hitChance: hitChance,
                autoHit: !!opts.autoHit,
                isOutOfRange: false,
                breakdownReason: opts.breakdownReason || null,
                modifiers: opts.modifiers || [],
                note: opts.note || null,
                _otherDetail: []
            };
        }

        if (target.flight)              return makeResult(0, { breakdownReason: 'Boarding: cannot board fighter' });
        if (target.shipSizeClass == 5)  return makeResult(0, { breakdownReason: 'Boarding: cannot board terrain' });
        if (target.mine)                return makeResult(0, { breakdownReason: 'Boarding: cannot board mine' });

        var attach = weaponManager.getBoardingAttachInfo(shooter, target);

        //hasAttached, not attached: target.attached is keyed by the TARGET's own host,
        //so this branch never fired and an attached unit showed a rolled chance instead
        //of the automatic hit the server has always given it.
        if (target.hasAttached[shooter.id] !== undefined) {
            return makeResult(100, { autoHit: true,
                breakdownReason: 'Boarding: already attached at ' + attach.label });
        }
        if (shipManager.movement.getJinking(shooter) > 0) {
            return makeResult(0, { breakdownReason: 'Boarding: cannot jink and attach' });
        }
        if (attach.reason !== null) {
            //Name the section when we know it, so the player can see WHERE the problem is and
            //re-route rather than just being told the attempt fails.
            return makeResult(0, { breakdownReason: attach.certain
                ? 'Boarding: ' + attach.label + ' - ' + attach.reason
                : 'Boarding: no free section on target' });
        }

        //The section is fixed by the hex edge this unit crossed, so tell the player which one.
        var attachNote = attach.certain
            ? 'Will attach to: ' + attach.label
            : 'Attach section rolled by server (two sections tie on this approach)';

        var modifiers = [];
        function pushIfNonZero(key, label, value) {
            if (value !== 0) modifiers.push({ key: key, label: label, value: value });
        }

        pushIfNonZero('base',        'Base Chance',  20); //100%
        pushIfNonZero('fireControl', 'Fire Control', weaponManager.getFireControl(target, weapon));

        var targetSpeed = Math.abs(shipManager.movement.getSpeed(target));
        var ownSpeed    = Math.abs(shipManager.movement.getSpeed(shooter));
        var speedDifference = Math.abs(targetSpeed - ownSpeed);
        var freeThrust = shooter.freethrust;

        if (shooter.flight) { //Breaching Pods
            if (speedDifference > freeThrust) {
                return makeResult(0, { breakdownReason: 'Boarding: insufficient thrust to match speed' });
            }
            if (targetSpeed > ownSpeed) {
                //Each point of speed difference equates to 10% chance to miss.
                pushIfNonZero('speedDifference', 'Speed Difference', -(speedDifference * 2));
            }
        } else { //Grapple Ships
            if (target.iniative > shooter.iniative) {
                return makeResult(0, { breakdownReason: 'Boarding: target won initiative' });
            }
            if (speedDifference > 0) {
                //Each point of speed difference equates to 5% chance to miss.
                pushIfNonZero('speedDifference', 'Speed Difference', -speedDifference);
                //Cannot attach to Enormous without auto-ramming; partial bonus offered.
                if (target.Enormous) pushIfNonZero('targetEnormous', 'Target Enormous', 2);
            }
        }

        var sum = modifiers.reduce(function (s, m) { return s + m.value; }, 0);
        var hitChance = Math.round(sum * 5); //d20 -> d100
        return makeResult(hitChance, { modifiers: modifiers, note: attachNote });
    }, //endof calculateBoardingAction

    /* ------------------------------------------------------------
       OLD calculateBoardingAction — kept for reference. Do not call.
       Replaced by the breakdown-returning version above. Behavior preserved.
       ------------------------------------------------------------
    calculateBoardingAction: function calculateBoardingAction(shooter, target, weapon) {
        if (target.flight || target.shipSizeClass == 5 || target.mine) return 0;//Cannot board fighters,  terrain, or mines!
        if (target.attached[shooter.id] !== undefined) return 100; // Pod attacking parent gets 100% chance to hit
        var jinking = shipManager.movement.getJinking(shooter); //Raider pods can jink, but can't attach at same time.
        if (jinking > 0) return 0;

        var hitChance = 20; //base: 100%

        //fire control: should be 0, but units specifically designed for boarding may have some bonus!
        hitChance += weaponManager.getFireControl(target, weapon);

        var targetSpeed = Math.abs(shipManager.movement.getSpeed(target)); //I think speed cannot be negative, but just in case ;)
        var ownSpeed = Math.abs(shipManager.movement.getSpeed(shooter));
        var speedDifference = Math.abs(targetSpeed - ownSpeed); //keep it a positive number.
        var freeThrust = shooter.freethrust;

        if (shooter.flight) {	//Breaching Pods.
            if (speedDifference > freeThrust) return 0;//Not enough thrust to compensate for speed difference, automatic miss.

            if (targetSpeed > ownSpeed) {//Target is moving faster, what are chances to attach?
                var speedChance = speedDifference * 2;//Each point of speed differnece equates to 10% chance to miss.
                var newHitchance = hitChance - speedChance;//Take current hitChance, and remove speed difference penalty.
                hitChance = Math.round(newHitchance * 5);//Convert to % value
                return hitChance;
            } else {
                hitChance = Math.round(hitChance * 5);	//Convert to % value
                return hitChance;
            }
        } else { //Grapple Ships
            if (target.iniative > shooter.iniative) return 0;//Cannot grapple ships which rolled equal or higher initiative than you.
            if (speedDifference > 0) {//Check Speed difference
                var speedChance = speedDifference;//Each point of speed difference equates to 5% chance to miss.
                var newHitchance = hitChance - speedChance;//Take current hitChance, and remove speed difference penalty.
                if (target.Enormous) newHitchance += 2;//You can't attach to Enormous Units without auto-ramming, but at least you get a bonus :)
                hitChance = Math.round(newHitchance * 5);//Convert to % value
                return hitChance;
            } else {
                hitChance = Math.round(hitChance * 5);	//Convert to % value
                return hitChance;
            }
        }

    }, //endof calculateBoardingAction
    ------------------------------------------------------------ */


    getFiringHex: function getFiringHex(shooter, weapon) {
        var sPosLaunch = null;

        if (weapon.hasSpecialLaunchHexCalculation) { //Does weapon have a different method of determining point of shot e.g. Proximity Laser?
            sPosLaunch = weapon.getFiringHex(shooter, weapon);
        } else {
            if (weapon.ballistic) {	 //standard ballistic calculation						
                sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn);
            } else { //Direct fire
                sPosLaunch = shipManager.getShipPosition(shooter);
            }
        }
        return sPosLaunch;
    },

    /* ============================================================
       calculateHitChange and its helpers (refactored DK 2026)
       Behavior preserved exactly; modifier accumulation broken out
       so the targeting tooltip can show a per-modifier breakdown.
       The pre-refactor monolithic version is preserved at the bottom
       of this block, commented out for reference.
       ============================================================ */

    // Mirrors calculateBaseHitChange but returns each component separately.
    computeBaseDefenceBreakdown: function computeBaseDefenceBreakdown(shooter, target, weapon, base) {
        var jink = 0;
        var dew = 0;
        var bdew = 0;
        var sdew = 0;

        if ((target.flight || target.jinkinglimit > 0) && shooter) {
            if (!shooter.flight) {
                jink = shipManager.movement.getJinking(target);
            } else {
                if (shooter) {
                    var sPosHex = shipManager.getShipPosition(shooter);
                    var tPosHex = shipManager.getShipPosition(target);
                    if ((weapon.ballistic) || (!sPosHex.equals(tPosHex)) || (shipManager.movement.getJinking(shooter) > 0)) {
                        jink = shipManager.movement.getJinking(target);
                    }
                }
            }
            if (weapon && weapon.ignoreJinking) {
                jink = 0;
            }
        }

        if (!target.flight) {
            dew = ew.getDefensiveEW(target);
        }

        sdew = ew.getSupportedDEW(target);
        bdew = ew.getSupportedBDEW(target);

        if (shooter && shooter.flight && !weapon.ballistic) {
            dew = 0;
            bdew = 0;
            sdew = 0;
        }

        if (shooter && (target.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors"))) {
            bdew = 0;
            sdew = 0;
        }

        if (weapon && weapon.ignoreAllEW) {
            dew = 0;
            bdew = 0;
            sdew = 0;
        }

        var halfphase = 0;
        if (shooter && weapon) {
            if (shipManager.movement.isHalfPhased(target)) {
                halfphase = 4;
                if (weapon && weapon.ballistic) halfphase = 8;
            }
            if (shooter && shipManager.movement.isHalfPhased(shooter)) halfphase = 10;
        }

        return {
            base: base,
            dew: dew,
            sdew: sdew,
            bdew: bdew,
            jinking: jink,
            halfPhase: halfphase,
            total: base - dew - jink - bdew - sdew - halfphase
        };
    },

    // Returns shooter's offensive EW: { oew, soew }. Handles fighter offensive bonus path.
    computeOEW: function computeOEW(shooter, target, weapon, sPosTarget) {
        var oew = 0;
        var soew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            soew = ew.getSupportedOEW(shooter, target);
            var dist = ew.getDistruptionEW(shooter);
            oew -= dist;
            if (oew < 1) soew = 0;
            if (oew < 0) oew = 0;
        }

        if (shooter.flight === true) {
            var firstFighter = shooter.systems[1];
            var OBcrit = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            var mdew = ew.getDetectMEW(shooter);
            oew = shooter.offensivebonus - OBcrit - (mdew * 2);

            if (weapon.ballistic) {
                var shooterLoSBlocked = false;
                var blockedLosHex = gamedata.blockedHexes;
                if (blockedLosHex && blockedLosHex.length > 0) {
                    var shooterPos = shipManager.getShipPosition(shooter);
                    shooterLoSBlocked = mathlib.isLoSBlocked(shooterPos, sPosTarget, blockedLosHex);
                }
                if ((!shooter.hasNavigator &&
                    !weaponManager.isOnWeaponArc(shooter, target, weapon)) ||
                    shooterLoSBlocked ||
                    Object.values(shooter.skinDancing).includes(true) ||
                    Object.values(shooter.skinDancing).includes("Failed")) {
                    oew = 0;
                }
            }
            oew = Math.max(0, oew);
            if (oew == 0) soew = 0;
        }

        return { oew: oew, soew: soew };
    },

    // Computes jammer + no-lock penalties as a combined pair.
    // Returns { jammermod, noLockMod, soewSuppressed, oewSuppressed }.
    // Caller applies suppression to the OEW result it already holds.
    computeJammerNoLock: function computeJammerNoLock(shooter, target, weapon, oew, distance, rangePenalty) {
        var noLockPenalty = 0;
        var noLockMod = 0;
        var jammermod = 0;
        var soewSuppressed = false;
        var oewSuppressed = false;

        if (oew < 0.5) {
            noLockPenalty = 1;
        } else if (oew < 1) {
            noLockPenalty = 0.5;
        }

        if (shooter.mine) noLockPenalty = 0; //mines assume lock; jammer may still apply

        jammermod = ew.getJammerValueFromTo(shooter, target);

        if (jammermod > 0) {
            soewSuppressed = true; //jammer negates SOEW
        }

        if (weapon.ignoreAllEW) {
            noLockPenalty = 0;
            jammermod = 0;
            oewSuppressed = true;
            soewSuppressed = true;
        }

        if (!weapon.noLockPenalty) {
            jammermod = 0;
            noLockPenalty = 0;
        }

        if ((jammermod > 0) || (noLockPenalty > 0)) {
            if (weapon.doubleRangeIfNoLock) { //e.g. Antimatter
                var modifiedDistance = distance * (1 + noLockPenalty);
                noLockMod = weaponManager.calculateRangePenalty(modifiedDistance, weapon) - rangePenalty;
                modifiedDistance = distance * (1 + jammermod);
                jammermod = weaponManager.calculateRangePenalty(modifiedDistance, weapon) - rangePenalty;
            } else {
                noLockMod = rangePenalty * noLockPenalty;
                jammermod = jammermod * rangePenalty;
            }
            jammermod = jammermod - noLockMod; //noLock cannot be overcome by sensors
            if (jammermod < 0) jammermod = 0;
        }

        //jammer and jinking do not stack
        if (target.flight) {
            var jinking = shipManager.movement.getJinking(target);
            if (jinking > jammermod) {
                jammermod = 0;
            } else {
                jammermod = jammermod - jinking;
            }
        }

        return { jammermod: jammermod, noLockMod: noLockMod, soewSuppressed: soewSuppressed, oewSuppressed: oewSuppressed };
    },

    // Returns weapon fire-control value, including HyachComputer bonus and ballistic LoS-blocked logic.
    // calledid (optional): a called shot at a system carrying fireControlIndexOverride (Kirishiac
    // Orbital: "targeted as if a fighter") uses that FC category instead of the target ship's.
    computeFireControl: function computeFireControl(shooter, target, weapon, sPosTarget, calledid) {
        var fcIndexOverride = null;
        if (calledid > 0) {
            var calledFCSystem = shipManager.systems.getSystem(target, calledid);
            if (calledFCSystem && calledFCSystem.fireControlIndexOverride != null) {
                fcIndexOverride = calledFCSystem.fireControlIndexOverride;
            }
        }

        var firecontrol = (fcIndexOverride != null) ? weapon.fireControl[fcIndexOverride] : weaponManager.getFireControl(target, weapon);
        if (target.mine && weapon.canShootMines) weapon.fireControl[1] = -4; //preserved as-is from old code; mutates next-call FC

        if (shipManager.hasSpecialAbility(shooter, "HyachComputer")) {
            var computer = shipManager.systems.getSystemByName(shooter, "hyachComputer");
            var FCIndex = (fcIndexOverride != null) ? fcIndexOverride : weaponManager.getFireControlIndex(target);
            var bonusfirecontrol = computer.getFCAllocated(FCIndex);
            firecontrol += bonusfirecontrol;
        }

        if (weapon.ballistic && (!shooter.flight) && !weapon.ignoresLoS) {
            if (!(firecontrol <= 0)) {
                var blockedLosHex = gamedata.blockedHexes;
                var shooterPos2 = shipManager.getShipPosition(shooter);
                var loSBlocked = mathlib.isLoSBlocked(shooterPos2, sPosTarget, blockedLosHex);
                if (loSBlocked) {
                    if (weapon instanceof AmmoMissileRackS) {
                        if (weapon.hasOwnProperty('basicFC') && Array.isArray(weapon.basicFC) && weapon.basicFC.length > 0) {
                            firecontrol -= weapon.basicFC[weaponManager.getFireControlIndex(target)];
                        }
                    } else {
                        firecontrol = 0;
                    }
                }
            }
        }

        return firecontrol;
    },

    // Returns the rest of the per-shot modifiers, broken into:
    //   defensiveSystems  (positive - subtracted from goal)
    //   calledShot        (signed - added to goal when calledid > 0)
    //   otherTotal        (signed sum of otherDetail entries)
    //   otherDetail       ([{label, value}, ...] for future tooltip expansion)
    computeShotModifiers: function computeShotModifiers(shooter, target, weapon, calledid, distance) {
        var defensiveSystems = target.getHitChangeMod(shooter, weapon);
        var calledShot = 0;
        var otherDetail = [];

        if (target.mine) {
            var mdew = ew.getDetectMEW(shooter);
            var mineBonusActual = Math.max(0, (mdew + shooter.minesweeperbonus) - distance - target.signature);
            if (mineBonusActual !== 0) otherDetail.push({ label: 'Mine Detection', value: mineBonusActual });
        }

        if (weapon.specialHitChanceCalculation) {
            var specialMod = weapon.calculateSpecialHitChanceMod(shooter, target, calledid);
            if (specialMod !== 0) otherDetail.push({ label: 'Weapon Special', value: specialMod });
        }

        if (shooter.flight === true) {
            if (!weapon.ignoreJinking) {
                var shooterJink = shipManager.movement.getJinking(shooter);
                if (shooterJink !== 0) otherDetail.push({ label: 'Shooter Jinking', value: -shooterJink });
            }
            if (shipManager.movement.hasCombatPivoted(shooter) && (!shooter.ignoreManoeuvreMods)) {
                otherDetail.push({ label: 'Combat Pivot', value: -1 });
            }
        } else {
            var rolling = (shooter.agile === true)
                ? shipManager.movement.hasRolled(shooter)
                : shipManager.movement.isRolling(shooter);
            if (rolling && !shooter.ignoreManoeuvreMods) {
                otherDetail.push({ label: 'Rolling', value: -3 });
            }
            if (shipManager.movement.hasPivotedForShooting(shooter) && !shooter.ignoreManoeuvreMods) {
                otherDetail.push({ label: 'Pivot for Shooting', value: -3 });
            }
            if (shooter.osat && shipManager.movement.hasTurned(shooter)) {
                otherDetail.push({ label: 'OSAT Turn', value: -1 });
            }
            if (shooter.toHitBonus !== 0) {
                otherDetail.push({ label: 'Crew Quality', value: shooter.toHitBonus });
            }
            if (!shooter.osat) {
                var cnC = shipManager.systems.getSystemByName(shooter, "cnC");
                var penaltyToHit = -shipManager.criticals.hasCritical(cnC, "PenaltyToHit");
                var tmpHitReduction = -shipManager.criticals.hasCritical(cnC, "tmphitreduction");
                var shadowPilotPain = -shipManager.criticals.hasCritical(cnC, "ShadowPilotPain");
                if (penaltyToHit !== 0) otherDetail.push({ label: 'C&C: Penalty to Hit', value: penaltyToHit });
                if (tmpHitReduction !== 0) otherDetail.push({ label: 'C&C: Temp Hit Reduction', value: tmpHitReduction });
                if (shadowPilotPain !== 0) otherDetail.push({ label: 'Shadow Pilot Pain', value: shadowPilotPain });
            }
        }

        if (calledid > 0) {
            var calledSystem = shipManager.systems.getSystem(target, calledid);
            if (calledSystem && calledSystem.targetProfile != null) {
                calledShot = 0; //flat fighter-style profile (Kirishiac Orbital) - no called-shot penalty or bonus; the profile replaces base defence in calculateHitChange
            } else {
                calledShot = weapon.calledShotMod;
                if (target.base) calledShot += weapon.calledShotMod; //double penalty vs bases
                if (calledSystem && calledSystem.calledShotBonus != null) calledShot += calledSystem.calledShotBonus;
            }
        }

        var ammo = weapon.getAmmo(null);
        if (ammo && ammo.hitChanceMod !== 0) {
            otherDetail.push({ label: 'Ammo', value: ammo.hitChanceMod });
        }

        var otherTotal = otherDetail.reduce(function (s, d) { return s + d.value; }, 0);
        return {
            defensiveSystems: defensiveSystems,
            calledShot: calledShot,
            otherTotal: otherTotal,
            otherDetail: otherDetail
        };
    },

    // New calculateHitChange. Returns:
    //   {
    //     hitChance: int,           // same number the old function returned
    //     autoHit: bool,
    //     isOutOfRange: bool,
    //     breakdownReason: string|null,  // set on early-return cases (Auto-hit, Out of range, etc.)
    //     modifiers: [{key,label,value}, ...],  // non-zero, ordered for tooltip
    //     _otherDetail: [{label,value}, ...]    // sub-breakdown of the 'Other' line
    //   }
    // Invariant: Math.round(sum(modifiers[*].value) / 20 * 100) === hitChance
    calculateHitChange: function calculateHitChange(shooter, target, weapon, calledid) {
        function makeResult(hitChance, opts) {
            opts = opts || {};
            return {
                hitChance: hitChance,
                autoHit: !!opts.autoHit,
                isOutOfRange: !!opts.isOutOfRange,
                breakdownReason: opts.breakdownReason || null,
                modifiers: [],
                _otherDetail: []
            };
        }

        if (weapon.autoHit) return makeResult(100, { autoHit: true, breakdownReason: 'Auto-hit' });

        if (weapon.isRammingAttack) {
            return weaponManager.calculateRamChance(shooter, target, weapon, calledid);
        }
        if (weapon.isBoardingAction) {
            return weaponManager.calculateBoardingAction(shooter, target, weapon);
        }

        //Sustained-overload weapons auto-hit/auto-miss based on previous turn target
        if (shipManager.power.isOverloading(shooter, weapon)) {
            if (weapon.sustainedTarget && Object.keys(weapon.sustainedTarget).length > 0) {
                if (weapon.firingMode !== 1) return makeResult(0, { breakdownReason: 'Sustained: wrong firing mode' });
                if (!weapon.sustainedTarget.hasOwnProperty(target.id)) {
                    return makeResult(0, { breakdownReason: 'Sustained: wrong target' });
                } else if (weapon.sustainedTarget[target.id] === 1) {
                    return makeResult(100, { autoHit: true, breakdownReason: 'Sustained: auto-hit' });
                }
            }
        }

        //Mass-driver-style weapons that require enormous immobile target
        if (weapon.targetsImmobile) {
            var ownSpeed = shipManager.movement.getSpeed(shooter);
            var targetSpeed = shipManager.movement.getSpeed(target);
            if (!target.Enormous || ownSpeed > 0 || targetSpeed > 0) return makeResult(0, { breakdownReason: 'Target not immobile' });
        }

        //Geometry: defence value + distance + sPosTarget (used by helpers for ballistics)
        var defence = 0;
        var distance = 0;
        var sPosTarget = null;
        if (weapon.ballistic) {
            var sPosLaunch = weaponManager.getFiringHex(shooter, weapon);
            sPosTarget = shipManager.getShipPosition(target);
            defence = weaponManager.getShipDefenceValuePos(sPosLaunch, target);
            distance = sPosLaunch.distanceTo(sPosTarget).toFixed(2);
        } else {
            defence = weaponManager.getShipDefenceValue(shooter, target);
            distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
        }

        //called shot at a system with a flat target profile ("targeted as if they were fighters" -
        //Kirishiac Orbitals): the system's own profile replaces the ship's bearing profile entirely
        //(computeShotModifiers skips the called-shot penalty/bonus for the same case)
        if (calledid > 0) {
            var calledProfileSystem = shipManager.systems.getSystem(target, calledid);
            if (calledProfileSystem && calledProfileSystem.targetProfile != null) {
                defence = calledProfileSystem.targetProfile;
            }
        }

        var maxDistance = Math.max(weapon.range, weapon.distanceRange);
        if ((maxDistance > 0) && (maxDistance < distance)) {
            return makeResult(0, { isOutOfRange: true, breakdownReason: 'Out of range' });
        }

        //Compute components via helpers
        var baseBreakdown = weaponManager.computeBaseDefenceBreakdown(shooter, target, weapon, defence);
        var ewLock = weaponManager.computeOEW(shooter, target, weapon, sPosTarget);
        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
        var jammer = weaponManager.computeJammerNoLock(shooter, target, weapon, ewLock.oew, distance, rangePenalty);
        if (jammer.oewSuppressed) { ewLock.oew = 0; ewLock.soew = 0; }
        else if (jammer.soewSuppressed) ewLock.soew = 0;
        var fireControl = weaponManager.computeFireControl(shooter, target, weapon, sPosTarget, calledid);
        var shotMods = weaponManager.computeShotModifiers(shooter, target, weapon, calledid, distance);

        //Goal: identical to old formula (baseDef - jammermod - noLockMod - rangePenalty + oew + soew + firecontrol + mod)
        //where mod = -defensiveSystems + calledShot + otherTotal
        var goal = baseBreakdown.total
                 - jammer.jammermod - jammer.noLockMod
                 - rangePenalty
                 + ewLock.oew + ewLock.soew + fireControl
                 - shotMods.defensiveSystems
                 + shotMods.calledShot
                 + shotMods.otherTotal;
        var hitChance = Math.round(goal * 5);

        //Build modifier list (zeros omitted)
        var modifiers = [];
        function pushIfNonZero(key, label, value) {
            if (value !== 0) modifiers.push({ key: key, label: label, value: value });
        }
        pushIfNonZero('base',             'Base Defense',      baseBreakdown.base);
        pushIfNonZero('fireControl',      'Fire Control',      fireControl);     
        pushIfNonZero('oew',              'OEW',               ewLock.oew);
        pushIfNonZero('soew',             'Supported OEW',     ewLock.soew);           
        pushIfNonZero('dew',              'DEW',               -baseBreakdown.dew);
        pushIfNonZero('sdew',             'SDEW',              -baseBreakdown.sdew);
        pushIfNonZero('bdew',             'BDEW',              -baseBreakdown.bdew);
        pushIfNonZero('targetJinking',    'Target Jinking',    -baseBreakdown.jinking);
        pushIfNonZero('halfPhase',        'Half-Phase',        -baseBreakdown.halfPhase);
        pushIfNonZero('range',            'Range',             -rangePenalty);
        pushIfNonZero('jammerNoLock',     'No Lock',            -(jammer.jammermod + jammer.noLockMod));
        pushIfNonZero('defensiveSystems', 'Defensive Systems', -shotMods.defensiveSystems);
        if (calledid > 0) pushIfNonZero('calledShot', 'Called Shot', shotMods.calledShot);
        pushIfNonZero('other',            'Other',             shotMods.otherTotal);

        //Dev-mode invariant: breakdown sum must reproduce hitChance
        var sumCheck = modifiers.reduce(function (s, m) { return s + m.value; }, 0);
        if (Math.round(sumCheck / 20 * 100) !== hitChance) {
            console.warn('calculateHitChange breakdown sum mismatch', { sumCheck: sumCheck, hitChance: hitChance, goal: goal });
        }

        return {
            hitChance: hitChance,
            autoHit: false,
            isOutOfRange: false,
            breakdownReason: null,
            modifiers: modifiers,
            _otherDetail: shotMods.otherDetail
        };
    },

    /* ------------------------------------------------------------
       OLD calculateHitChange — kept for reference. Do not call.
       Replaced by the helper-based version above. Behavior preserved.
       ------------------------------------------------------------
    calculateHitChange: function calculateHitChange(shooter, target, weapon, calledid) {

        if (weapon.autoHit) return 100; //Some weapons always hit, let's just show 100% chance to prevent confusion at firing. DK - 12 Apr 2024

        if (weapon.isRammingAttack) {
            return weaponManager.calculateRamChance(shooter, target, weapon, calledid);
        }
        if (weapon.isBoardingAction) {
            return weaponManager.calculateBoardingAction(shooter, target, weapon);
        }

        //New check for sustained weapons, to see if they will auto-hit/auto-miss targets from previous turn.  If conditions not true, normal routine.
        if (shipManager.power.isOverloading(shooter, weapon)) {
            if (weapon.sustainedTarget && Object.keys(weapon.sustainedTarget).length > 0) {
                if (weapon.firingMode !== 1) return 0;
                if (!weapon.sustainedTarget.hasOwnProperty(target.id)) {
                    return 0;
                } else if (weapon.sustainedTarget[target.id] === 1) {
                    return 100;
                }
            }
        }

        //Weapons like Mass Drivers have special criteria for targets and shooter speed etc.
        if (weapon.targetsImmobile) {
            var ownSpeed = shipManager.movement.getSpeed(shooter);
            var targetSpeed = shipManager.movement.getSpeed(target);
            if (!target.Enormous || ownSpeed > 0 || targetSpeed > 0) return 0;
        }

        var defence = 0;
        var distance = 0;
        if (weapon.ballistic) {
            var sPosLaunch = weaponManager.getFiringHex(shooter, weapon);
            var sPosTarget = shipManager.getShipPosition(target);
            defence = weaponManager.getShipDefenceValuePos(sPosLaunch, target);
            distance = sPosLaunch.distanceTo(sPosTarget).toFixed(2);
        } else {
            defence = weaponManager.getShipDefenceValue(shooter, target);
            distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
        }

        var maxDistance = Math.max(weapon.range, weapon.distanceRange);
        if ((maxDistance > 0) && (maxDistance < distance)) {
            return 0;
        }

        var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

        var soew = 0;
        var dist = 0;
        var oew = 0;
        var mdew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            soew = ew.getSupportedOEW(shooter, target);
            dist = ew.getDistruptionEW(shooter);
            oew -= dist;
            if (oew < 1) soew = 0;
            if (oew < 0) oew = 0;
        } else {
            oew = 0;
            soew = 0;
        }

        var mod = 0;

        if (target.mine) {
            mdew = ew.getDetectMEW(shooter);
            var mineBonus = (mdew + shooter.minesweeperbonus) - distance - target.signature;
            mod += Math.max(0, mineBonus);
        }

        mod -= target.getHitChangeMod(shooter, weapon);

        if (weapon.specialHitChanceCalculation) {
            mod += weapon.calculateSpecialHitChanceMod(shooter, target, calledid);
        }

        if (shooter.flight === true) {
            var firstFighter = shooter.systems[1];
            var OBcrit = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            mdew = ew.getDetectMEW(shooter);
            oew = shooter.offensivebonus - OBcrit - (mdew * 2);

            if (weapon.ballistic) {
                var shooterLoSBlocked = false;
                var blockedLosHex = gamedata.blockedHexes;
                if (blockedLosHex && blockedLosHex.length > 0) {
                    var shooterPos = shipManager.getShipPosition(shooter);
                    shooterLoSBlocked = mathlib.isLoSBlocked(shooterPos, sPosTarget, blockedLosHex);
                }
                if ((!shooter.hasNavigator &&
                    !weaponManager.isOnWeaponArc(shooter, target, weapon)) ||
                    shooterLoSBlocked ||
                    Object.values(shooter.skinDancing).includes(true) ||
                    Object.values(shooter.skinDancing).includes("Failed")) {
                    oew = 0;
                }
            }
            oew = Math.max(0, oew);
            if (oew == 0) soew = 0;

            if (!weapon.ignoreJinking) {
                mod -= shipManager.movement.getJinking(shooter);
            }

            if (shipManager.movement.hasCombatPivoted(shooter) && (!shooter.ignoreManoeuvreMods)) mod--;
        } else {
            if (shooter.agile === true) {
                if (shipManager.movement.hasRolled(shooter)) {
                    if (!shooter.ignoreManoeuvreMods) mod -= 3;
                }
            } else {
                if (shipManager.movement.isRolling(shooter)) {
                    if (!shooter.ignoreManoeuvreMods) mod -= 3;
                }
            }

            if (shipManager.movement.hasPivotedForShooting(shooter)) {
                if (!shooter.ignoreManoeuvreMods) mod -= 3;
            }

            if (shooter.osat && shipManager.movement.hasTurned(shooter)) {
                mod -= 1;
            }

            if (shooter.toHitBonus != 0) {
                mod += shooter.toHitBonus;
            }

            if (!shooter.osat) {
                mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "PenaltyToHit");
                mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "tmphitreduction");
                mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "ShadowPilotPain");
            }
        }
        if (calledid > 0) {
            mod += weapon.calledShotMod;
            if (target.base) mod += weapon.calledShotMod;
            var calledSystem = shipManager.systems.getSystem(target, calledid);
            if (calledSystem.calledShotBonus != null) mod += calledSystem.calledShotBonus;
        }

        var ammo = weapon.getAmmo(null);
        if (ammo) mod += ammo.hitChanceMod;

        var noLockPenalty = 0;
        var noLockMod = 0;
        if (oew < 0.5) {
            noLockPenalty = 1;
        } else if (oew < 1) {
            noLockPenalty = 0.5;
        }

        if (shooter.mine) noLockPenalty = 0;

        var jammermod = 0;
        jammermod = ew.getJammerValueFromTo(shooter, target);
        if (jammermod > 0) {
            soew = 0;
        }

        if (weapon.ignoreAllEW) {
            noLockPenalty = 0;
            jammermod = 0;
            oew = 0;
            soew = 0;
        }

        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
        if (!weapon.noLockPenalty) { jammermod = 0; noLockPenalty = 0; }
        if ((jammermod > 0) || (noLockPenalty > 0)) {
            if (weapon.doubleRangeIfNoLock) {
                var modifiedDistance = distance * (1 + noLockPenalty);
                noLockMod = weaponManager.calculateRangePenalty(modifiedDistance, weapon) - rangePenalty;
                modifiedDistance = distance * (1 + jammermod);
                jammermod = weaponManager.calculateRangePenalty(modifiedDistance, weapon) - rangePenalty;
            } else {
                noLockMod = rangePenalty * noLockPenalty;
                jammermod = jammermod * rangePenalty;
            }
            jammermod = jammermod - noLockMod;
            if (jammermod < 0) jammermod = 0;
        }

        if (target.flight) {
            var jinking = shipManager.movement.getJinking(target);
            if (jinking > jammermod) {
                jammermod = 0;
            } else {
                jammermod = jammermod - jinking;
            }
        }

        var firecontrol = weaponManager.getFireControl(target, weapon);
        if (target.mine && weapon.canShootMines) weapon.fireControl[1] = -4;

        if (shipManager.hasSpecialAbility(shooter, "HyachComputer")) {
            var bonusfirecontrol = 0;
            var computer = shipManager.systems.getSystemByName(shooter, "hyachComputer");
            var FCIndex = weaponManager.getFireControlIndex(target);
            bonusfirecontrol = computer.getFCAllocated(FCIndex);
            firecontrol += bonusfirecontrol;
        }

        if (weapon.ballistic && (!shooter.flight) && !weapon.ignoresLoS) {
            if (!(firecontrol <= 0)) {
                var loSBlocked = false;
                var blockedLosHex = gamedata.blockedHexes;
                var shooterPos2 = shipManager.getShipPosition(shooter);
                loSBlocked = mathlib.isLoSBlocked(shooterPos2, sPosTarget, blockedLosHex);

                if (loSBlocked) {
                    if (weapon instanceof AmmoMissileRackS) {
                        if (weapon.hasOwnProperty('basicFC') && Array.isArray(weapon.basicFC) && weapon.basicFC.length > 0) {
                            firecontrol -= weapon.basicFC[weaponManager.getFireControlIndex(target)];
                        }
                    } else {
                        firecontrol = 0;
                    }
                }
            }
        }

        var goal = baseDef - jammermod - noLockMod - rangePenalty + oew + soew + firecontrol + mod;
        var hitChance = Math.round(goal / 20 * 100);
        return hitChance;
    },
    ------------------------------------------------------------ */


    getFireControl: function getFireControl(target, weapon) {
        //NB: Gravitic Augmenter Mode 1 modifies fireControl SERVER-SIDE and force-sends the
        //altered value via weapon.php stripForJson (the generic isModified flag), so the values
        //here already include the augmenter mod — no client-side mirror needed.
        if (target.shipSizeClass > 1) {
            return weapon.fireControl[2];
        }
        if (target.shipSizeClass >= 0) {
            return weapon.fireControl[1];
        }
        if (target.mine == true) {
            return weapon.fireControl[1];
        }

        return weapon.fireControl[0];
    },

    getFireControlIndex: function getFireControlIndex(target) {
        if (target.shipSizeClass >= 2) return 2;
        if (target.shipSizeClass >= 0) return 1;
        if (target.shipSizeClass < 0) return 0;
    },


    // 'position' should be in HEX coordinate
    getShipDefenceValuePos: function getShipDefenceValuePos(position, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var targetPos = shipManager.getShipPosition(target);

        var shooterCompassHeading = mathlib.getCompassHeadingOfPoint(targetPos, position);

        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

        //console.log("ship degree: " +delta);
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            //console.log("hitting front 1");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            //console.log("hitting rear 2");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            //console.log("hitting port 3");
            return target.sideDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }

        return target.sideDefense;
    },

    getShipHittingSide: function getShipHittingSide(shooter, target) {
        //Marcin Sawicki, October 2017: new approach!
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var toReturn = [];

        for (var i = 0; i < target.outerSections.length; i++) {
            var currSectionData = target.outerSections[i];
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(currSectionData.min, targetFacing), mathlib.addToDirection(currSectionData.max, targetFacing))) {
                toReturn.push(currSectionData.loc);
            }
            //"loc" => $curr['loc'], "min" => $curr['min'], "max" => $curr['max'], "call" => $call
        }
        toReturn.sort();
        return toReturn;
    },


    getShipDefenceValue: function getShipDefenceValue(shooter, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);

        if (target.base) {
            return target.forwardDefense;
        }

        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

        //console.log("ship degree: " +delta);
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            //console.log("hitting front 1");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            //console.log("hitting rear 2");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            //console.log("hitting port 3");
            return target.sideDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }

        return target.sideDefense;
    },

    /* ==== MANUAL INTERCEPTION =================================================================
       Player-directed intercept assignment: select intercept-capable weapons in the Firing phase
       and click an incoming shot in the ship tooltip's INCOMING list to commit them to that shot.
       Automated interception (Firing::automateIntercept) still handles everything left unassigned.

       Every predicate here mirrors a server-side test in Firing::isLegalIntercept /
       Firing::validateManualIntercept. The server drops an illegal order silently - by design, so
       nothing leaks about hidden state - which means the client predicate has to be strict enough
       that a drop means a stale blueprint, not a legitimate click.

       Throughout, a "shot" is one entry from getAllBallisticsAgainst - {fireOrder, shooter, weapon}
       - optionally carrying .position, the launch hex the tooltip already resolved from the
       shooter's icon. ========================================================================= */

    /* Read a per-mode weapon flag WITHOUT changing the weapon's mode. The display path in
       ShipTooltipBallisticsMenu cycles changeFiringMode() to match an order, and weapon objects are
       shared between same-phpclass systems, so mutating one here would corrupt another's display.
       Falls back to the live field for weapons that have no array for that flag. */
    getModeFlag: function getModeFlag(weapon, flagName, mode) {
        if (!weapon) return undefined;
        var array = weapon[flagName + 'Array'];
        if (array && array[mode] !== undefined) return array[mode];
        return weapon[flagName];
    },

    /* Where an incoming shot is bearing FROM, in hex coordinates.
       Ballistic: the launch hex - what the server's getFiringHex resolves to, and what the tooltip
       already recorded as ball.position. Non-ballistic (a Sweeping shot): the server bears on the
       shooter's CURRENT hex, so a start-of-turn position would be the wrong answer. Moot while
       every Sweeping weapon in the game is uninterceptable, but it must not be inherited wrong. */
    getIncomingSourcePos: function getIncomingSourcePos(ball) {
        var shooter = ball.shooter || gamedata.getShip(ball.fireOrder.shooterid);
        if (!ball.weapon || !ball.weapon.ballistic) {
            return shooter ? shipManager.getShipPosition(shooter) : null;
        }
        if (ball.position) return new hexagon.Offset(ball.position);
        return shooter ? shipManager.movement.getPositionAtStartOfTurn(shooter) : null;
    },

    /* Which firing mode would this weapon intercept in? Mirrors MissileLauncher::switchModeForIntercept.
       The current mode wins when it already has a rating; otherwise ONLY a canModesIntercept weapon
       (an Interceptor-missile rack) is allowed to look at its other modes - that flag is exactly the
       gate Firing::getUnassignedInterceptors uses before calling switchModeForIntercept, and without
       it an ordinary multi-mode weapon would silently intercept in a mode it is not set to.
       Returns null when the weapon cannot intercept in any mode. */
    getInterceptModeFor: function getInterceptModeFor(weapon) {
        if (!weapon || !weapon.weapon) return null;
        if (weapon.intercept > 0) return weapon.firingMode;
        if (!weapon.canModesIntercept) return null;
        if (mathlib.arrayIsEmpty(weapon.interceptArray)) return null;

        var bestMode = null;
        var bestValue = 0;
        for (var mode in weapon.interceptArray) {
            var value = weapon.interceptArray[mode];
            if (value > bestValue) {
                bestValue = value;
                bestMode = parseInt(mode, 10);
            }
        }
        return bestValue > 0 ? bestMode : null;
    },

    /* Intercept rating (d20 points) this weapon carries in a given mode - read from interceptArray
       rather than by switching the weapon, for the shared-object reason above. */
    getInterceptRatingInMode: function getInterceptRatingInMode(weapon, mode) {
        if (!weapon) return 0;
        if (weapon.interceptArray && weapon.interceptArray[mode] !== undefined) return weapon.interceptArray[mode];
        return weapon.intercept || 0;
    },

    /* This turn's orders on one weapon, split by kind. selfIntercept is counted separately because
       it is a PERMISSION MARKER ("the automation may use me"), not a shot - the server's own gun
       arithmetic makes the same distinction. */
    countCurrentTurnOrders: function countCurrentTurnOrders(weapon) {
        var counts = { offensive: 0, intercept: 0, selfIntercept: 0 };
        if (!weapon || !weapon.fireOrders) return counts;
        for (var i = 0; i < weapon.fireOrders.length; i++) {
            var fire = weapon.fireOrders[i];
            if (fire.turn != gamedata.turn) continue;
            if (fire.type === 'selfIntercept') counts.selfIntercept++;
            else if (fire.type === 'intercept') counts.intercept++;
            else counts.offensive++;
        }
        return counts;
    },

    /* Does this shot admit interception AT ALL, independent of who might shoot at it?
       Returns null when it does, or a short reason for the disabled button's title.
       'uninterceptable' is deliberately NOT tested here: canInterceptUninterceptable is a property
       of the INTERCEPTOR, so an uninterceptable shot is a per-weapon question, answered in
       canInterceptBallistic. The row still reports it, from getInterceptDisabledReason. */
    getShotInterceptRefusal: function getShotInterceptRefusal(ball) {
        if (!ball || !ball.weapon || !ball.fireOrder) return 'Unknown shot';
        var mode = ball.fireOrder.firingMode;
        if (weaponManager.getModeFlag(ball.weapon, 'doNotIntercept', mode)) return 'Cannot be intercepted';
        if (weaponManager.getModeFlag(ball.weapon, 'hextarget', mode)) return 'Hex-targeted';
        if (!gamedata.getShip(ball.fireOrder.targetid)) return 'No target';
        return null;
    },

    /* Client mirror of AmmoMagazine::canDrawInterceptor. The server tracks rounds already committed
       to interception in a private per-request counter the client never sees, so count our own
       declared intercept orders for that mode instead. Non-magazine weapons pass trivially. */
    ammoAvailableForIntercept: function ammoAvailableForIntercept(ship, weapon, mode) {
        if (!weapon || !weapon.checkAmmoMagazine) return true; //not magazine-fed - nothing to check

        //The magazine sits on the unit that carries the weapon: the ship itself, or the individual
        //fighter of a flight. Same lookup by name that getMagazineFireableAmmo uses.
        var unit = ship;
        if (ship.flight) unit = shipManager.systems.getFighterBySystem(ship, weapon.id);
        if (!unit || !unit.systems) return false;

        var magazine = null;
        for (var i in unit.systems) {
            if (unit.systems[i].name == "ammoMagazine") { magazine = unit.systems[i]; break; }
        }
        //Magazine-fed weapon on a unit with no magazine: the server's getAmmoMagazine returns null
        //and canInterceptAtAll refuses, so refuse here too.
        if (!magazine || !magazine.ammoCountArray) return false;
        if (!(magazine.remainingAmmo > 0)) return false;

        var modeName = weapon.firingModes ? weapon.firingModes[mode] : null;
        if (!modeName) return false;

        //canDrawInterceptor compares the mode's round count against rounds already committed to
        //interception THIS request. That counter is private and server-side, so mirror it by
        //counting the intercept orders this unit has already declared for the same round.
        var held = magazine.ammoCountArray[modeName] || 0;
        var declared = 0;
        for (var s in unit.systems) {
            var other = unit.systems[s];
            if (!other.fireOrders || !other.checkAmmoMagazine || !other.firingModes) continue;
            for (var f = 0; f < other.fireOrders.length; f++) {
                var fire = other.fireOrders[f];
                if (fire.type !== 'intercept') continue;
                if (fire.turn != gamedata.turn) continue;
                if (other.firingModes[fire.firingMode] !== modeName) continue;
                declared++;
            }
        }

        return (held - declared) >= 1;
    },

    /* Same hex NOW and at the end of the previous turn - the fighter-escort rule the server applies
       in isLegalIntercept. shipManager.isEscorting tests the start-of-turn position ONLY, which is
       why it cannot be reused here: it would offer orders the server then drops. */
    sharesHexNowAndAtStartOfTurn: function sharesHexNowAndAtStartOfTurn(ship, other) {
        var nowSelf = shipManager.getShipPosition(ship);
        var nowOther = shipManager.getShipPosition(other);
        if (!nowSelf || !nowOther || !nowSelf.equals(nowOther)) return false;

        var wasSelf = shipManager.movement.getPositionAtStartOfTurn(ship);
        var wasOther = shipManager.movement.getPositionAtStartOfTurn(other);
        if (!wasSelf || !wasOther) return false;
        return wasSelf.equals(wasOther);
    },

    /* Standard $freeintercept geometry: the protected unit must lie roughly opposite the incoming
       shot - within 60 degrees either side of the bearing away from it. Mirrors the arc built in
       isLegalIntercept. Both bearings are taken as absolute compass headings rather than relative
       ones: the server's relative transform (facing offset plus the rolled-ship mirror) is applied
       identically to both, and the window is symmetric about the opposite bearing, so it cancels. */
    isBetweenShooterAndTarget: function isBetweenShooterAndTarget(ship, target, ball) {
        var sourcePos = weaponManager.getIncomingSourcePos(ball);
        if (!sourcePos) return false;

        var incomingHeading = mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(ship), sourcePos);
        var from = mathlib.addToDirection(incomingHeading, 120);
        var to = mathlib.addToDirection(from, 120);
        var targetHeading = mathlib.getCompassHeadingOfShip(ship, target);

        return mathlib.isInArc(targetHeading, from, to);
    },

    /* THE predicate. One weapon, one shot: may this weapon be hand-assigned to intercept it?
       Used by the button-enable test AND by the declaration loop, so the UI can never offer
       something the declaration would silently skip. */
    canInterceptBallistic: function canInterceptBallistic(ship, weapon, ball) {
        if (gamedata.gamephase !== 3) return false; //R1 - Firing phase only
        if (!ship || !weapon || !weapon.weapon || !ball || !ball.fireOrder) return false;
        if (!gamedata.isMyShip(ship)) return false;
        if (shipManager.isDestroyed(ship)) return false;
        if (!ship.flight && shipManager.isDisabled(ship)) return false;

        if (shipManager.systems.isDestroyed(ship, weapon)) return false;
        if (shipManager.power.isOffline(ship, weapon)) return false;
        if (weapon.stowed) return false; //docked Kirishiac Orbital - non-operational
        if (weapon.autoFireOnly) return false; //server drives these; the player never assigns them
        if (!weaponManager.isLoaded(weapon)) return false;

        if (weaponManager.getShotInterceptRefusal(ball) !== null) return false;

        var incoming = ball.weapon;
        var mode = ball.fireOrder.firingMode;
        if (weaponManager.getModeFlag(incoming, 'uninterceptable', mode) && !weapon.canInterceptUninterceptable) return false;
        if (weapon.ballisticIntercept && !incoming.ballistic) return false; //may only stop ballistics

        //Rating, in the mode the order will actually carry (R6).
        var interceptMode = weaponManager.getInterceptModeFor(weapon);
        if (interceptMode === null) return false;
        if (weaponManager.getInterceptRatingInMode(weapon, interceptMode) <= 0) return false;

        //Weapons that price interception per DIE out of a shared pool rather than per gun. The
        //generic gun accounting below is meaningless for them, so they answer for themselves:
        //  - Molecular Slicer  - implements the pair of hooks and spends a die or a block of set
        //                        damage per engagement (Stage 7).
        //  - Hyperplasma Cutter - permanently out of scope (§11.5); no hook, so still refused.
        if (weapon.usesCustomInterceptAllocation) {
            if (typeof weapon.canDeclareManualIntercept !== 'function') return false;
            if (!weapon.canDeclareManualIntercept(ship)) return false;
        }

        //Gun accounting. Skipped entirely for the custom-allocation weapons above - guns are not
        //the currency they spend, and ->guns is 1 on a Slicer, which would cap it at one order.
        var counts = weaponManager.countCurrentTurnOrders(weapon);
        if (weapon.usesCustomInterceptAllocation) {
            //answered above, by the weapon itself
        } else if (weapon.ballistic || weapon.preFires) {
            //R7 - these declare in an EARLIER phase, so their orders are already committed and
            //nothing may be wiped to make room: they qualify only if they have fired nothing.
            if (counts.offensive > 0) return false;
            if (counts.intercept >= weapon.guns) return false;
            if (!weaponManager.ammoAvailableForIntercept(ship, weapon, interceptMode)) return false;
        } else if (weapon.canSplitShots) {
            //R3 - one gun per manual intercept; the rest stay available to fire or be auto-assigned.
            if (counts.offensive + counts.intercept >= weapon.guns) return false;
        }
        //A non-split direct-fire weapon needs no cap here: the click wipes its own uncommitted
        //orders and re-declares (R4), exactly as targetShip does in the opposite direction.

        //Arc, measured from where the shot is coming from.
        var sourcePos = weaponManager.getIncomingSourcePos(ball);
        if (!sourcePos) return false;
        if (!weaponManager.isPosOnWeaponArc(ship, sourcePos, weapon)) return false;

        //Who is being shot at?
        var target = gamedata.getShip(ball.fireOrder.targetid);
        if (!target) return false;

        if (target.id !== ship.id) { //fire directed at a third party - only some weapons may step in
            if (ball.fireOrder.shooterid === ship.id) return false; //never intercept your own shot
            if (target.team !== ship.team) return false;

            if (ship.flight) {
                //Fighter escort: ballistics only, never fire aimed at another flight, and the two
                //must share a hex now AND have shared one at the end of last turn.
                if (!incoming.ballistic) return false;
                if (target.flight) return false;
                if (!weaponManager.sharesHexNowAndAtStartOfTurn(ship, target)) return false;
            } else {
                if (!weapon.freeintercept) return false;
                //freeinterceptspecial weapons decide third-party legality in their own server-side
                //canFreeInterceptShot, which has no client mirror. Rather than offer an order the
                //server may drop, leave those to the automation - they keep working, and every one
                //of them can still be hand-assigned to fire aimed at its OWN ship.
                if (weapon.freeinterceptspecial) return false;
                if (!weaponManager.isBetweenShooterAndTarget(ship, target, ball)) return false;
            }
        }

        return true;
    },

    /* The eligible part of the current selection, ordered the way the server ranks interceptors in
       compareInterceptAbility: best rating first, faster-recharging first on a tie. Client and
       server therefore agree on what "strongest" means when the greedy fill spends them. */
    getSelectedInterceptorsFor: function getSelectedInterceptorsFor(ship, ball) {
        var eligible = [];
        for (var i = 0; i < gamedata.selectedSystems.length; i++) {
            var weapon = gamedata.selectedSystems[i];
            if (weaponManager.canInterceptBallistic(ship, weapon, ball)) eligible.push(weapon);
        }

        eligible.sort(function (a, b) {
            var ra = weaponManager.getInterceptRatingInMode(a, weaponManager.getInterceptModeFor(a));
            var rb = weaponManager.getInterceptRatingInMode(b, weaponManager.getInterceptModeFor(b));
            if (ra !== rb) return rb - ra;
            var la = Math.max(a.loadingtime || 0, a.normalload || 0);
            var lb = Math.max(b.loadingtime || 0, b.normalload || 0);
            if (la !== lb) return la - lb;
            return a.id - b.id;
        });

        return eligible;
    },

    /* Why is the INTERCEPT button on this row disabled? Null when it is not. */
    getInterceptDisabledReason: function getInterceptDisabledReason(ship, ball) {
        var shotRefusal = weaponManager.getShotInterceptRefusal(ball);
        if (shotRefusal) return shotRefusal;

        if (weaponManager.getSelectedInterceptorsFor(ship, ball).length > 0) return null;

        //Nothing eligible - say which of the two reasons it is, since they call for opposite actions.
        //A custom-allocation weapon counts as an interceptor only if it implements the manual hooks
        //(the Slicer does; the Hyperplasma Cutter deliberately does not - §11.5).
        var anyInterceptorSelected = gamedata.selectedSystems.some(function (weapon) {
            if (weapon.usesCustomInterceptAllocation
                && typeof weapon.canDeclareManualIntercept !== 'function') return false;
            return weaponManager.getInterceptModeFor(weapon) !== null;
        });
        if (!anyInterceptorSelected) {
            var anyCustom = gamedata.selectedSystems.some(function (weapon) {
                return weapon.usesCustomInterceptAllocation
                    && typeof weapon.canDeclareManualIntercept !== 'function';
            });
            return anyCustom
                ? 'Use this weapon\'s own intercept declaration'
                : 'No interceptor selected';
        }

        var mode = ball.fireOrder.firingMode;
        if (weaponManager.getModeFlag(ball.weapon, 'uninterceptable', mode)) return 'Uninterceptable';
        return 'No selected weapon can reach this shot';
    },

    /* Interception this side has DECLARED against one shot, in d20 points (x5 for a percentage).
       Mirrors Weapon::getInterceptionMod, degradation included: each interceptor after the first is
       worth one point less, but only where degradation applies at all - it is switched off against
       ballistics and against noInterceptDegradation weapons, unless the weapon sets
       doInterceptDegradation (the Nexus Laser Missiles).

       This is DECLARED interception only. Automated assignment happens server-side after commit and
       is deliberately not previewed, and a teammate's uncommitted orders are not in this payload -
       hence the "Committed" wording on the row rather than "Interception". */
    getDeclaredInterception: function getDeclaredInterception(fireOrderId, interceptedWeapon) {
        var degrades = interceptedWeapon
            ? (interceptedWeapon.doInterceptDegradation
                || !(interceptedWeapon.ballistic || interceptedWeapon.noInterceptDegradation))
            : true;

        var total = 0;
        var prior = 0;

        for (var s in gamedata.ships) {
            var ship = gamedata.ships[s];
            var fires = weaponManager.getAllFireOrders(ship);
            for (var f in fires) {
                var fire = fires[f];
                if (fire.type !== 'intercept') continue;
                if (fire.turn != gamedata.turn) continue;
                if (fire.targetid != fireOrderId) continue;

                var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
                if (!weapon) continue;

                var rating = weaponManager.getInterceptRatingInMode(weapon, fire.firingMode);
                if (degrades) rating -= prior;
                total += Math.max(0, rating);
                prior++;
            }
        }

        return total;
    },

    /* Base hit chance of an incoming shot, BEFORE interception.

       A BALLISTIC in flight has no stored hit chance at all: tac_fireorder has no `chance` column
       (the client-side field of that name never leaves the browser), and the row's `needed` stays 0
       until the shot resolves. So the obvious `chance ?? needed` reads 0% for every missile on the
       board - which made every ballistic look already-suppressed and stopped the greedy fill dead
       before it declared anything. The INCOMING row has always shown a LIVE recompute for these, so
       anything that subtracts interception has to start from the same number.

       A direct-fire (Sweeping) order does carry its declared chance, so that is preferred where it
       exists - it is what the row's own "normal" branches print. */
    getIncomingShotHitChance: function getIncomingShotHitChance(ball) {
        if (!ball || !ball.fireOrder) return 0;

        if (ball.fireOrder.type === 'normal') {
            var stored = ball.fireOrder.chance ?? ball.fireOrder.needed;
            if (stored > 0) return stored;
        }

        var shooter = ball.shooter || gamedata.getShip(ball.fireOrder.shooterid);
        if (!shooter || !ball.weapon) return 0;

        //Same call - and therefore the same number - the row's headline and the grouping key use.
        return weaponManager.calculataBallisticHitChange({
            weaponid: ball.weapon.id,
            targetid: ball.fireOrder.targetid,
            shooterid: shooter.id
        });
    },

    /* Hit chance left on a shot once this side's declared interception is subtracted. NOT floored
       at 0 (user direction, 2026-08-20): the server does not floor 'needed - totalIntercept' for
       the roll either, and the overshoot is information the player is spending weapons to buy -
       "-25%" says "this shot is dead twice over, stop feeding it" where a floored 0% cannot.
       Every consumer must therefore cope with a negative. The greedy fill's `<= 0` test still
       advances correctly; the row formats a negative range with "to" so a leading minus cannot
       read as a second hyphen.

       The server behaves the same way: automateIntercept drops any shot whose
       'needed - totalIntercept' is <= 0, so an over-intercepted shot is simply gone. */
    getRemainingHitChance: function getRemainingHitChance(ball) {
        var base = weaponManager.getIncomingShotHitChance(ball);
        var committed = weaponManager.getDeclaredInterception(ball.fireOrder.id, ball.weapon) * 5;
        return base - committed;
    },

    /* Commit ONE weapon to ONE shot. Returns the number of orders created. */
    declareInterceptWith: function declareInterceptWith(ship, weapon, ball) {
        var mode = weaponManager.getInterceptModeFor(weapon);
        if (mode === null) return 0;

        //Some weapons price a DEFENSIVE shot in a fixed firing mode regardless of the mode they
        //are set to - their doMultipleSelfIntercept overrides stamp firingMode 1 for exactly this
        //reason (VorlonDischargeCannon bills 5 x firingMode of power PER ORDER, so a mode-3
        //intercept would be charged 15 instead of 5). A manual intercept is the same kind of shot
        //and has to be stamped the same way. Only ever narrows the mode, never the rating: every
        //weapon that overrides this carries a flat ->intercept with no interceptArray.
        if (typeof weapon.getInterceptOrderMode === 'function') mode = weapon.getInterceptOrderMode(mode);

        //Weapons with their own interception currency declare through their own hook - the
        //Molecular Slicer spends a damage die or a block of set damage, not a gun (Stage 7). It
        //owns everything below, INCLUDING whether a selfIntercept marker is spent or reused, so
        //this returns straight out rather than falling through to the generic path.
        if (typeof weapon.declareManualIntercept === 'function') {
            return weapon.declareManualIntercept(ship, ball, mode);
        }

        //A selfIntercept marker offers the weapon to the automation; a targeted order supersedes
        //that offer, so trade one for the other (same consent, a strictly more specific
        //commitment). removeSelfInterceptSingle re-prices split weapons via recalculateForIntercept.
        if (weaponManager.canRemInterceptSingle(ship, weapon)) {
            weaponManager.removeSelfInterceptSingle(ship, weapon);
        }

        var orders = 1;
        if (!weapon.canSplitShots) {
            //R4 - a non-split weapon commits every gun to the shot it was pointed at. Its own
            //uncommitted orders are cleared first so a second click retargets rather than stacks.
            //NOT for split weapons: removeFiringOrder would wipe their offensive shots too, and on
            //a multiModeSplit weapon it detours through removeAllMultiModeSplit.
            if (!weapon.ballistic && !weapon.preFires && !weapon.multiModeSplit) {
                weaponManager.removeFiringOrder(ship, weapon);
            }
            orders = weapon.guns;
        }

        var damageClass = (weapon.data && weapon.data["Weapon type"])
            ? weapon.data["Weapon type"].toLowerCase() : '';

        for (var s = 0; s < orders; s++) {
            weapon.fireOrders.push({
                id: ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1),
                type: 'intercept',
                shooterid: ship.id,
                //An intercept order's targetid is the id of the FIRE ORDER it is stopping - a
                //different id space from every other order type, where it is a unit id.
                targetid: ball.fireOrder.id,
                weaponid: weapon.id,
                calledid: -1,
                turn: gamedata.turn,
                firingMode: mode,
                shots: weapon.defaultShots,
                x: "null",
                y: "null",
                damageclass: damageClass
            });
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
        return orders;
    },

    /* Commit the whole eligible selection to ONE named shot - the expanded sub-row's INTERCEPT,
       where the player has been explicit about which shot they mean. */
    targetBallistic: function targetBallistic(ship, ball) {
        if (gamedata.gamephase !== 3) return 0;
        if (!ship || !ball) return 0;

        var declared = 0;
        var eligible = weaponManager.getSelectedInterceptorsFor(ship, ball);

        for (var i = 0; i < eligible.length; i++) {
            var weapon = eligible[i];
            //Re-test: an earlier weapon in this pass may have drawn the last interceptor round.
            if (!weaponManager.canInterceptBallistic(ship, weapon, ball)) continue;
            if (weaponManager.declareInterceptWith(ship, weapon, ball) > 0) {
                declared++;
                if (weaponManager.isWeaponSpentForIntercept(ship, weapon)) {
                    weaponManager.unSelectWeapon(ship, weapon);
                }
            }
        }

        if (declared > 0) gamedata.shipStatusChanged(ship);
        return declared;
    },

    /* A weapon with shots left stays selected so the player can keep spending it; one that is out
       is unselected, matching what targetShip does through checkFinished. */
    isWeaponSpentForIntercept: function isWeaponSpentForIntercept(ship, weapon) {
        //A custom-allocation weapon knows exactly when its pool is empty, and neither ->guns nor
        //the generic order count means anything to it (a Slicer's ->guns is 1, which would retire
        //it after a single engagement with most of its dice still in hand).
        if (typeof weapon.canDeclareManualIntercept === 'function') {
            return !weapon.canDeclareManualIntercept(ship);
        }
        if (!weapon.canSplitShots) return true; //committed every gun
        if (typeof weapon.checkFinished === 'function' && weapon.checkFinished()) return true;
        var counts = weaponManager.countCurrentTurnOrders(weapon);
        return (counts.offensive + counts.intercept) >= weapon.guns;
    },

    /* Greedy fill across the members of a grouped row (3x Missile). One click walks the eligible
       selection strongest-first and puts each weapon on the FOCUS shot - the first member still
       above 0% once declared interception is subtracted. When a member reaches 0% the focus moves
       on, so nothing is wasted on a shot that is already suppressed, and the click stops early if
       every member is suppressed. Allocation is at WEAPON granularity: a non-split weapon's guns
       all follow it onto the same shot (R4). */
    allocateIntercept: function allocateIntercept(ship, members) {
        if (gamedata.gamephase !== 3) return 0;
        if (!ship || !members || members.length === 0) return 0;

        var ordered = members.slice().sort(function (a, b) {
            return String(a.fireOrder.id).localeCompare(String(b.fireOrder.id), undefined, { numeric: true });
        });

        //One eligibility pass, against the first member: every member of a group shares shooter,
        //weapon, firing mode and geometry, so eligibility cannot differ between them.
        var eligible = weaponManager.getSelectedInterceptorsFor(ship, ordered[0]);
        var declared = 0;
        var focus = 0;

        for (var i = 0; i < eligible.length; i++) {
            while (focus < ordered.length && weaponManager.getRemainingHitChance(ordered[focus]) <= 0) {
                focus++;
            }
            if (focus >= ordered.length) break; //whole group suppressed - spend nothing more

            var weapon = eligible[i];
            var ball = ordered[focus];
            if (!weaponManager.canInterceptBallistic(ship, weapon, ball)) continue;

            if (weaponManager.declareInterceptWith(ship, weapon, ball) > 0) {
                declared++;
                if (weaponManager.isWeaponSpentForIntercept(ship, weapon)) {
                    weaponManager.unSelectWeapon(ship, weapon);
                }
            }
        }

        if (declared > 0) gamedata.shipStatusChanged(ship);
        return declared;
    },

    /* Does this weapon's whole contribution this turn consist of interception? True when it carries
       at least one manual 'intercept' order or 'selfIntercept' marker and nothing aimed at anyone.
       Drives the GREEN system-icon highlight that tells a defensive commitment apart from the
       orange "this weapon is shooting at something".

       Withdrawing a manual intercept is deliberately NOT offered in the ballistics tooltip: it is an
       ordinary fire order, so the ship window's existing remove button clears it like any other. */
    isInterceptOnly: function isInterceptOnly(ship, weapon) {
        if (!weapon || !weapon.weapon) return false;
        var counts = weaponManager.countCurrentTurnOrders(weapon);
        return counts.offensive === 0 && (counts.intercept + counts.selfIntercept) > 0;
    },

    /* May this weapon be SELECTED in the Firing phase purely so it can be hand-assigned to
       interception? The normal selection gate in SystemIcon allows a phase-3 click only on a
       direct-fire weapon; a missile rack loaded with Interceptor missiles is intercept-capable but
       ballistic, so without this it cannot be picked up at all (D3).

       Note this asks only "is it selectable"; whether it may take a PARTICULAR shot is
       canInterceptBallistic's question, and the two agree on every test they share. */
    canManuallyInterceptWith: function canManuallyInterceptWith(ship, weapon) {
        if (gamedata.gamephase !== 3) return false;
        if (!ship || !weapon || !weapon.weapon) return false;
        if (!gamedata.isMyShip(ship)) return false;
        if (shipManager.isAdrift(ship)) return false;
        if (shipManager.isDestroyed(ship)) return false;

        if (weapon.stowed) return false;
        if (weapon.autoFireOnly) return false;
        if (shipManager.systems.isDestroyed(ship, weapon)) return false;
        if (shipManager.power.isOffline(ship, weapon)) return false;
        if (!weaponManager.isLoaded(weapon)) return false;
        if (weapon.usesCustomInterceptAllocation) return false; //allocates per die, via its own dialog

        var mode = weaponManager.getInterceptModeFor(weapon);
        if (mode === null) return false;

        //R7 - a rack that launched in Initial Orders has fired; a rack that manually intercepts
        //cannot then launch. Its orders are already committed server-side, so nothing may be wiped
        //to make room.
        var counts = weaponManager.countCurrentTurnOrders(weapon);
        if (counts.offensive > 0) return false;
        if (weapon.canSplitShots) {
            if (counts.intercept >= weapon.guns) return false;
        } else if (counts.intercept > 0) {
            return false;
        }

        return weaponManager.ammoAvailableForIntercept(ship, weapon, mode);
    },

    canSelfIntercept: function canSelfIntercept(ship) {
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];
            var loadingTimeActual = Math.max(weapon.loadingtime, weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging 
            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && loadingTimeActual > 1) {
                return true;
            }
        }
        return false;
    },

    checkSelfIntercept: function checkSelfIntercept(ship) {
        var invalid = [];
        var valid = [];

        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (weaponManager.hasFiringOrder(ship, weapon)) {
                weaponManager.removeFiringOrder(ship, weapon);
            }

            var loadingTimeActual = Math.max(weapon.loadingtime, weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging
            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && loadingTimeActual > 1) {
                valid.push(weapon);
            } else invalid.push(weapon);
        }

        if (valid.length > 0) {
            weaponManager.confirmSelfIntercept(ship, valid, invalid, "Do you want to order the selected weapons to intercept incoming fire ?");
        }
    },

    confirmSelfIntercept: function confirmSelfIntercept(ship, valid, invalid, message) {
        confirm.confirmWithOptions(message, "Yessss", "Nope", function (response) {
            if (response) {
                weaponManager.setSelfIntercept(ship, valid);
                for (var i in invalid) {
                    weaponManager.unSelectWeapon(ship, invalid[i]);
                }
            }
        });
    },

    /*check whether a long-recharge weapon is eligible for interception*/
    canSelfInterceptSingle: function checkSelfIntercept(ship, weapon) {
        if (gamedata.gamephase != 3) return false;//declaration in firing phase only
        if (!weapon.weapon) return false;//only weapons can intercept ;)
        if (weapon.stowed) return false;//stowed (Kirishiac Orbital docked) - non-operational
        var loadingTimeActual = Math.max(weapon.loadingtime, weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging
        if ((weapon.intercept < 1) && !(weaponManager.canWeaponInterceptAtAll(weapon)) || (loadingTimeActual <= 1)) return false;//cannot intercept or quick to recharge anyway and will be auto-assigned
        if (weapon.ballistic && !(weaponManager.canWeaponInterceptAtAll(weapon))) return false;//no interception using ballistic weapons    
        if (weaponManager.hasFiringOrder(ship, weapon) && !weapon.canSplitShots) return false;//already declared and can't split shots.
        if (!weaponManager.isLoaded(weapon)) return false;//not ready to fire
        if (weapon.canSplitShots) {
            var canSelfIntercept = weapon.checkSelfInterceptSystem(); //Look to weapon itself now, to see if any special criteria should apply.
            if (!canSelfIntercept) return false;
        }
        return true;
    },

    onDeclareSelfInterceptSingle: function onDeclareSelfInterceptSingle(ship, weapon) {
        if (!weaponManager.canSelfInterceptSingle(ship, weapon)) return; //last check whether weapon is eligible for that!
        if (weapon.canSplitShots) { //Discharge Gun/Slicers use their own logic here, so diverge to their own methods.
            weapon.doMultipleSelfIntercept(ship);
            var outOfShots = weapon.checkFinished();
            if (outOfShots) weaponManager.unSelectWeapon(ship, weapon);
            return;
        }
        var fireid = ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
        var fire = {
            id: fireid,
            type: "selfIntercept",
            shooterid: ship.id,
            targetid: ship.id,
            weaponid: weapon.id,
            calledid: -1,
            turn: gamedata.turn,
            firingMode: weapon.firingMode,
            shots: weapon.defaultShots,
            x: "null",
            y: "null",
            addToDB: true,
            damageclass: weapon.data["Weapon type"].toLowerCase()
        };
        weapon.fireOrders.push(fire);
        weaponManager.unSelectWeapon(ship, weapon);
    },

    /*declare self-intercept for all similar undeclared weapons*/
    onDeclareSelfInterceptSingleAll: function onDeclareSelfInterceptSingleAll(ship, weapon) {
        var allWeapons = [];
        if (ship.flight) {
            allWeapons = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            allWeapons = ship.systems.filter(system => system.weapon);
        }
        //group by BASE displayName so paired Kirishiac weapons ('...A'/'...B') count as one type
        var baseName = weaponManager.stripPairingSuffix(weapon.displayName);
        var similarWeapons = new Array();
        for (var i = 0; i < allWeapons.length; i++) {
            if (baseName === weaponManager.stripPairingSuffix(allWeapons[i].displayName)) { //this will include this particular system too, of course
                similarWeapons.push(allWeapons[i]);
            }
        }
        for (var i = 0; i < similarWeapons.length; i++) {
            var otherWeapon = similarWeapons[i];
            weaponManager.onDeclareSelfInterceptSingle(ship, otherWeapon); //will check whether weapon is actually eligible for such declaration
        }
    },

    //Some split shots weapons add self-intercepts at beginning of fireorders, this lets us check if such an order exists - DK
    canRemInterceptSingle: function canRemInterceptSingle(ship, weapon) {
        if (gamedata.gamephase != 3) return false;//declaration in firing phase only
        if (!weapon.weapon) return false;//only weapons can intercept ;)        
        for (var i = 0; i < weapon.fireOrders.length; i++) {
            if (weapon.fireOrders[i].type == "selfIntercept") {
                return true; //An order found
            }
        }
        return false;
    },

    //Some split shots weapons add self-intercepts at beginning of fireorders, this lets us remove them without deleting all offensive orders - DK
    removeSelfInterceptSingle: function removeSelfInterceptSingle(ship, weapon) {
        for (var i = 0; i < weapon.fireOrders.length; i++) {
            if (weapon.fireOrders[i].type == "selfIntercept") {
                weapon.fireOrders.splice(i, 1);
                break; //we are only remove one order
            }
        }

        //On a weapon whose manual intercepts are PAID FOR by its markers (the Molecular Slicer -
        //MANUAL_INTERCEPTION_PLAN.md Stage 7), removing the last spare marker would strand an
        //engagement with nothing behind it. MolecularSlicerBeamL::getInterceptionMod pays out only
        //while engagements <= markers, so a stranded intercept order is worth exactly 0 - it would
        //still sit in the ship window looking committed. Withdraw it with the marker that bought it.
        if (typeof weapon.getSpareInterceptCapacity === 'function' && weapon.getSpareInterceptCapacity() < 0) {
            for (var j = weapon.fireOrders.length - 1; j >= 0; j--) {
                if (weapon.fireOrders[j].type == "intercept") {
                    weapon.fireOrders.splice(j, 1);
                    break; //one marker bought one engagement - withdraw one
                }
            }
        }

        weapon.recalculateForIntercept(false); //Slicers need this to adjust hit chance for other shots, perhaps other will in future too. 
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
    },


    setSelfIntercept: function setSelfIntercept(ship, valid) {
        for (var weapon in valid) {
            var weapon = valid[weapon];

            var fireid = ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);

            var fire = {
                id: fireid,
                type: "selfIntercept",
                shooterid: ship.id,
                targetid: ship.id,
                weaponid: weapon.id,
                calledid: -1,
                turn: gamedata.turn,
                firingMode: weapon.firingMode,
                shots: weapon.defaultShots,
                x: "null",
                y: "null",
                addToDB: true,
                damageclass: weapon.data["Weapon type"].toLowerCase()
            };

            weapon.fireOrders.push(fire);
            weaponManager.unSelectWeapon(ship, weapon);
        }
        //	gamedata.shipStatusChanged(ship);
    },


    canWeaponInterceptAtAll: function canWeaponInterceptAtAll(weapon) {
        var canIntercept = false;
        var loadingTimeActual = Math.max(weapon.loadingtime, weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging

        if (weapon.canModesIntercept && (loadingTimeActual > 1)) { //Could weapon have alternative modes with Intercept Rating, and would need to use Self Intercept?
            canIntercept = weapon.canWeaponInterceptAtAll(weapon);//Call to weapon function to check modes for intercept ratings.
        }

        return canIntercept;
    },


    //system is for called shot!
    targetShip: function targetShip(selectedShip, ship, system) {
        var debug = false;

        debug && console.log("weaponManager target ship", ship, system);

        if (shipManager.isDestroyed(selectedShip)) return;
        if (selectedShip.mine && ship.mine) return;  //Mine can't shoot mines.
        if (ship.Huge > 0) return; //Do not allow targeting of large muti-hex terrain.
        if (!selectedShip.flight && shipManager.isDisabled(selectedShip)) return;
        if (weaponManager.isHidden(selectedShip)) return; //Block invisible ships from firing where appropriate.

        //Uncontrolled-flight block. A remote-controlled Hunter-Killer flight whose command
        //link is severed (node shortfall or ELINT jamming) is driven entirely by the server -
        //it moves itself (drift/seek) and auto-rams a co-located enemy. The player has no
        //control, so declaring fire for it only produces a spurious DOUBLE attack (the server
        //rejects it in Firing::validateFireOrders anyway). Warn and block at declaration so the
        //player gets immediate feedback instead of a silently-dropped order.
        if (shipManager.movement.isUncontrolled(selectedShip)) {
            confirm.warning("This Hunter-Killer flight is UNCONTROLLED - It cannot be given fire orders.");
            return;
        }

        //Check for skin-dancing ships, these can't be targeted unless the shooter is also skin-dancing on same target, they also have their own rules about firing.
        if (gamedata.gamephase == 3) {
            if (!weaponManager.checkSkindancing(selectedShip, ship)) return; //Returns false if skin dancing conditions prevent firing at or from a skin dancing unit.
        }

        //var blockedLosHex = weaponManager.getBlockedHexes();
        var blockedLosHex = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.         
        var loSBlocked = false;

        var toUnselect = [];
        var splitTargeted = [];
        var linkedWarned = {}; //firing-link groups already warned about this click (one popup per group)
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            // Attachment firing restriction: Flights attached to anything, or non-flights targeting their host.
            if (selectedShip.attached && Object.keys(selectedShip.attached).length > 0) {
                if (selectedShip.flight || selectedShip.attached[ship.id] !== undefined) {
                    if (!weapon.isBoardingAction) {
                        continue;
                    }
                }
            }

            // Host targeting Pod restriction
            if (selectedShip.hasAttached && selectedShip.hasAttached[ship.id] !== undefined) {
                continue; // Parent cannot target the attached pod
            }

            if (weapon.isBoardingAction && weapon.firingMode == 2 && !system) {
                if (gamedata.rules.desperate === undefined || (gamedata.rules.desperate !== ship.team && gamedata.rules.desperate !== -1)) {
                    var html = "You cannot choose to Wreak Havoc unless Desperate scenario rules are in effect.";
                    confirm.warning(html);
                    return;
                }
            }

            //Boarding: refuse a target this unit cannot attach to, so the order is not burned on
            //an attachment the server will cancel. Names the section when the entry hex edge
            //fixes it, so the player knows to approach from a different side.
            if (weapon.isBoardingAction) {
                var attachInfo = weaponManager.getBoardingAttachInfo(selectedShip, ship);
                if (attachInfo.reason !== null) {
                    confirm.warning(attachInfo.certain
                        ? "This unit would attach to the " + attachInfo.label + " section of "
                          + ship.name + ". " + attachInfo.reason
                        : "There is no free section on " + ship.name + " for this unit to attach to.");
                    return;
                }
            }
            //Only need to check first weapon
            if (blockedLosHex && blockedLosHex.length > 0 && !loSBlocked) {
                var sPosShooter = weaponManager.getFiringHex(selectedShip, weapon);
                var sPosTarget = shipManager.getShipPosition(ship);

                loSBlocked = mathlib.isLoSBlocked(sPosShooter, sPosTarget, blockedLosHex);
            }

            if (loSBlocked && !weapon.ignoresLoS) continue;

            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon) || (weapon.stowed && weapon.stowedArcStart == null)) {
                debug && console.log("Weapon destroyed, not loaded, or stowed (Kirishiac Orbital docked)"); //a stowed arc set (Heavy Orbital) keeps the weapon operational
                continue;
            }

            if (weapon.hextarget) {
                debug && console.log("This weapon targets only hexagons");
                continue;
            }

            if (weapon.ballistic && gamedata.gamephase != 1) {
                debug && console.log("trying to fire in wrong phase for ballistic weapon");
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3 && (weapon.preFires && gamedata.gamephase != 5)) {
                debug && console.log("trying to fire in wrong phase for normal weapon");
                continue;
            }

            if (weapon.ballistic && system && (!weapon.overrideCallingRestrictions)) { //25.11.23 - Added last condition to allow Limpet Bore to make called shots as a ballsitic weapon.
                debug && console.log("trying to call shot with ballistic");
                continue;
            }

            if ((!system) && weapon.canOnlyCalledShot) { //25.11.23 - New statement to make sure Limpet Bore can ONLY make Called Shots.
                debug && console.log("trying to target ship with weapon that can only target systems");
                continue;
            }

            if (!gamedata.isMyOrTeamOneShip(ship) && weapon.weaponClass == 'Support') { //30.06.24 - New statement to stop players supporting enemies!
                debug && console.log("trying to target enemy ship with a support weapon");
                continue;
            }

            if (weaponManager.checkConflictingFireOrder(selectedShip, weapon, true)) {
                debug && console.log("has conflicting fire orders");
                for (var j = gamedata.selectedSystems.length - 1; j >= 0; j--) {
                    var sel_weapon = gamedata.selectedSystems[j];
                    weaponManager.removeFiringOrder(selectedShip, sel_weapon);
                    weaponManager.unSelectWeapon(selectedShip, sel_weapon);
                }
                return;
            }

            if (ship.flight && weapon.fireControl[0] === null) {
                debug && console.log("cant fire flight");
                continue;
            }

            if (!ship.flight && ship.shipSizeClass < 2 && weapon.fireControl[1] === null) {
                if (ship.mine && weapon.canShootMines) {
                    //Do nothing in certain circumstances e.g. Interceptors firing at mines.
                } else {
                    debug && console.log("can't fire small ships");
                    continue;
                }
            }

            if (ship.shipSizeClass >= 2 && weapon.fireControl[2] === null) {
                debug && console.log("can't fire big ships");
                continue;
            }

            var type = 'normal';
            if (weapon.ballistic) {
                type = 'ballistic';
            } else if (gamedata.gamephase == 5) {
                type = 'prefiring';
            }

            if (weapon.reinforceAmount != null) {
                if (!weapon.checkReinforcement(selectedShip, ship)) return;
            }

            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                debug && console.log("is on arc");
                if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {
                    debug && console.log("is in range");

                    //Firing-link guard: a linked weapon's target is constrained by any sibling in its
                    //group that has already declared (same target for Thunderbolt; within N degrees
                    //for Vree). Placed here - after the arc/range pass - so we never nag when this
                    //weapon couldn't have fired at the clicked unit anyway. The same-pass select-all
                    //case is fine: the first sibling declares, the rest are then measured against it.
                    var linkBlock = weaponManager.getLinkedFiringBlock(selectedShip, weapon, ship);
                    if (linkBlock) {
                        if (!linkedWarned[weapon.linkedFiringGroup]) {
                            linkedWarned[weapon.linkedFiringGroup] = true;
                            confirm.warning(linkBlock);
                        }
                        continue;
                    }

                    if (weapon.hasSpecialTargeting) {
                        //Weapons that need a bespoke targeting flow (e.g. Hypergraviton Blaster's
                        //transfer-target ordering window) handle their own fire-order creation in
                        //doSpecialTargeting. They declare a plain single normal/raking fire order,
                        //so they deliberately bypass the canSplitShots machinery (ballistic icons,
                        //split menus, etc.). Only unselect if an order was actually declared.
                        var declared = weapon.doSpecialTargeting(selectedShip, ship, system);
                        if (declared) {
                            toUnselect.push(weapon);
                            webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
                        }
                    } else if (weapon.canSplitShots) {
                        var fire = weapon.doMultipleFireOrders(selectedShip, ship, system);
                        if (!Array.isArray(fire)) fire = fire ? [fire] : []; // Ensure fire is an array or an empty one                       

                        if (fire.length === 0) continue;

                        weapon.fireOrders.push(...fire);
                        var finishedFiring = weapon.checkFinished(); //Split weapons should unselect after they've used all their shots.
                        if (finishedFiring) {
                            toUnselect.push(weapon); //Normal method
                        } else {
                            splitTargeted.push(weapon); //Not finished, to be added to toUnselect aray at correct time below. 	  
                        }
                        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
                    } else {
                        weaponManager.removeFiringOrder(selectedShip, weapon);
                        for (var s = 0; s < weapon.guns; s++) {
                            var fireid = selectedShip.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
                            var calledid = -1;

                            if (system) {
                                //check if weapon is eligible for called shot!
                                if (!weaponManager.canWeaponCall(weapon)) continue;

                                // When the system is a subsystem, make all damage go through
                                // the parent.
                                while (system.parentId > 0) {
                                    system = shipManager.systems.getSystem(ship, system.parentId);
                                }

                                calledid = system.id;
                            }

                            var damageClass = weapon.data["Weapon type"].toLowerCase();
                            var chance = weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid).hitChance;

                            if ((chance < 1) && (!weapon.ballistic)) {//now ballistics can be launched when hit chance is 0 or less - important for Packet Torpedo!
                                //debug && console.log("Can't fire, change < 0");
                                continue;
                            }

                            var fire = {
                                id: fireid,
                                type: type,
                                shooterid: selectedShip.id,
                                targetid: ship.id,
                                weaponid: weapon.id,
                                calledid: calledid,
                                turn: gamedata.turn,
                                firingMode: weapon.firingMode,
                                shots: weapon.defaultShots,
                                x: "null",
                                y: "null",
                                damageclass: damageClass,
                                chance: chance
                            };
                            //Hook for weapons that need to stamp custom per-shot data onto the
                            //order at creation (e.g. Gravitic Augmenter Mode 3 rotation notes).
                            if (typeof weapon.onFireOrderCreated === 'function') weapon.onFireOrderCreated(fire);
                            weapon.fireOrders.push(fire);
                            toUnselect.push(weapon);
                        }
                    }
                    //Marcin Sawicki: moving this statement so only weapons actually declared are unselected
                    //toUnselect.push(weapon);
                }
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        toUnselect.push(...splitTargeted); //We don't want to unselect, but want these weapons passed to onShipTargeted - DK 01.25
        webglScene.customEvent('ShipTargeted', { shooter: selectedShip, target: ship, weapons: toUnselect })

        //Reset Movement UI after moment of targeting, to prevent cancel of last Combat Pivot AFTER locking target! - DK 10.24
        if (gamedata.gamephase == 3 && selectedShip.flight) {
            webglScene.customEvent("ShipMovementChanged", { ship: ship }); //Redraw movement for Combat Pivots         	 	
        }

        //Add new warning for when people ignore tooltip and try to ram when they possibly shouldn't - DK 10/24
        //No warning for ships designed to ram or if desperate rules apply!
        if (weapon.isRammingAttack && !weapon.designedToRam) {
            // No warning for ships designed to ram or if desperate rules apply
            if (gamedata.rules.desperate === undefined ||
                (gamedata.rules.desperate !== ship.team && gamedata.rules.desperate !== -1)) {
                if (!weaponManager.ramWarning) {
                    var html = "WARNING - Ramming Attacks should only be used in scenarios where they are specifically permitted.";
                    weaponManager.ramWarning = true;
                    confirm.warning(html);
                }
            }
        }

    },


    checkIsInRange: function checkIsInRange(shooter, target, weapon) {

        if (weapon.name == "ProximityMine" && gamedata.gamephase == 5 && weapon.potentialTargets && weapon.potentialTargets[target.id] !== undefined) {
            return true;
        }

        var range = weapon.range;
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);

        var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");

		//GTS_Triad - Allowing Ancients to ignore stealth with ballistics (Triad Missile Rack and Phased Gravitic Torpedo)
        if (stealthSystem && distance > 5 && weapon.ballistic && target.flight && weapon.factionAge < 3) {
            return false;
        }

        if (range === 0) return true;

        var jammer = shipManager.systems.getSystemByName(target, "jammer");
        if (jammer || stealthSystem) {

            //			if (!shipManager.power.isOfflineOnTurn(target, jammer, (gamedata.turn-1) )){ //Amended this section to accommodate Hyach Stealth ships - DK 18.3.24
            /*Improved/Advanced Sensors effect*/
            var jammerValue = 0;
            if (jammer && (!shipManager.power.isOfflineOnTurn(target, jammer, (gamedata.turn - 1)))) {//Jammer exists and was enabled last turn.
                jammerValue = shipManager.systems.getOutput(target, jammer);
            }
            var stealthValue = 0;
            var stealthDistance = 12; //Default for ships
            if (shooter.flight) stealthDistance = 4; //Fighters
            if (shooter.base) stealthDistance = 24; //Bases

            if (stealthSystem && (distance > stealthDistance) && target.shipSizeClass >= 0) {
                stealthValue = shipManager.systems.getOutput(target, stealthSystem);
            }

            if (stealthValue > jammerValue) jammerValue = stealthValue;//larger value is used

            if (shipManager.hasSpecialAbility(shooter, "AdvancedSensors") || shipManager.systems.getSystemByName(shooter, "fighteradvsensors")) {
                jammerValue = 0; //negated
            } else if (shipManager.hasSpecialAbility(shooter, "ImprovedSensors") || shipManager.systems.getSystemByName(shooter, "fighterimprsensors")) {
                jammerValue = jammerValue * 0.5; //halved
            }
            range = range / (1 + jammerValue);
            //range = range / (shipManager.systems.getOutput(target, jammer)+1);
            //		}
        }

        return distance <= range;
    },


    targetHex: function targetHex(selectedShip, hexpos) {
        if (shipManager.isDestroyed(selectedShip)) return;
        if (!selectedShip.flight && shipManager.isDisabled(selectedShip)) return;
        var hidden = weaponManager.isHidden(selectedShip); //Block invisible ships from firing where appropriate.

        var toUnselect = Array();
        var splitTargeted = [];
        var linkedWarned = {}; //one firing-link warning per group, not per weapon
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            // Attachment firing restriction: Flights attached to anything cannot target hexes with non-boarding weapons.
            if (selectedShip.flight && selectedShip.attached && Object.keys(selectedShip.attached).length > 0) {
                if (!weapon.isBoardingAction) {
                    continue;
                }
            }

            //jumpEngine joins the exemptions: a concealed ship MAY open a jump point, and doing so
            //breaks its concealment (JUMP_POINTS_PLAN.md section 2.1, user ruling 2026-08-21). The
            //server writes the reveal at commit - ShadingField/CloakingDevice generateIndividualNotes
            //via JumpEngine::vortexRevealNotes - so the warning below is the player's only notice
            //before the fact.
            if (hidden && weapon.name !== 'TransverseDrive' && weapon.name !== 'MicroJumpSystem' && weapon.name !== 'jumpEngine') {
                var html = "You cannot fire weapons on a turn when you are stealthed.";
                confirm.warning(html);
                toUnselect.push(weapon);
                continue;
            }


            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon)) continue;

            if (shipManager.power.isOffline(selectedShip, weapon)) {
                toUnselect.push(weapon);
                continue;
            }

            if (!weapon.hextarget) {
                continue;
            }

            if (weapon.autoFireOnly) {
                continue;
            }

            if (weapon.ballistic && gamedata.gamephase != 1) {
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3 && (weapon.preFires && gamedata.gamephase != 5)) { //
                continue;
            }

            if (weaponManager.checkConflictingFireOrder(selectedShip, weapon)) {
                continue;
            }

            /* VORTEX DISRUPTOR - A HALF-PHASED SHADOW HULL CANNOT USE IT (user ruling
               2026-08-29). The server has always said so, but only by setting the hit chance to
               ZERO in VortexDisruptor::calculateHitBase - so the order was accepted, the weapon
               discharged, and the player found out at the end of the turn that they had spent a
               3-turn recharge on a shot that could not physically hit. Refused here instead, with a
               reason, and REPORTED rather than silently skipped for the same reason the vortex
               declaration below is: the hex was picked deliberately.
               ⚠️ The server keeps its hit chance of 0 - this is the courtesy, not the rule. */
            if (weapon.name === 'VortexDisruptor' && shipManager.movement.isHalfPhased(selectedShip)) {
                confirm.error("A half-phased ship cannot fire its <b>Vortex Disruptor</b> - the beam has "
                    + "nothing to bite on. Stay in phase to disrupt a jump point.");
                toUnselect.push(weapon);
                continue;
            }

            //Vortex declaration legality (JUMP_POINTS_PLAN.md section 2.1). Reported rather than
            //silently skipped: the player picked this hex deliberately, and the server would
            //otherwise drop the order without telling anyone. The weapon stays SELECTED so the
            //next right-click can try a different hex.
            if (weapon.name === 'jumpEngine') {
                //STAGE 5 - one vortex per ship, across turns. Told rather than silently dropped:
                //the server refuses a second opening (Firing::getVortexDeclarationBlock) and the
                //player would otherwise watch the order vanish at commit with no explanation.
                //MAINTAINING an open vortex is NOT done from the map - it is the Maintain toggle in
                //the Jump Engine's own menu (JumpEngineMenu), because it also has to take the ship
                //dark, which a hex-target gesture cannot do. Hence the pointer rather than a second
                //map path to the same order.
                if (shipManager.movement.getVortexHeldBy(selectedShip)) {
                    confirm.error("This ship is already holding a jump point open. Use <b>Maintain Vortex</b> "
                        + "on the Jump Engine to keep it open, or let it close before opening another.");
                    continue;
                }

                var vortexBlock = weaponManager.getVortexHexBlock(selectedShip, weapon, hexpos);
                if (vortexBlock) {
                    confirm.error(vortexBlock);
                    continue;
                }
                //Concealed ships are allowed through the isHidden guard above for this weapon only.
                //Say so before the order is built - the reveal is written server-side at commit and
                //there is no other signal until the next gamedata load.
                if (hidden) {
                    confirm.warning("Opening a jump point breaks your concealment: this ship will be "
                        + "revealed to every enemy, and loses its cloak/shading bonuses for the rest of the turn.");
                }
            }

            //Firing-link guard for HEX targeting — the mirror of the one in targetShip. A turret is
            //one mount, so a hex-targeting weapon (Antimatter Shredder mode 1) must aim within its
            //group's spread of whatever its mate has already declared, ship or hex alike. Without
            //this a jammed Vree turret could shell a hex behind the ship while its cannon fired
            //forward. Placed after the phase/conflict guards and before the arc test so we only
            //nag about weapons that were otherwise eligible.
            var linkBlock = weaponManager.getLinkedFiringBlock(selectedShip, weapon, null, hexpos);
            if (linkBlock) {
                if (!linkedWarned[weapon.linkedFiringGroup]) {
                    linkedWarned[weapon.linkedFiringGroup] = true;
                    confirm.warning(linkBlock);
                }
                continue;
            }

            var type = 'normal';
            if (weapon.ballistic) {
                type = 'ballistic';
            } else if (gamedata.gamephase == 5) {
                type = 'prefiring';
            }

            if (weaponManager.isPosOnWeaponArc(selectedShip, hexpos, weapon)) {

                //Check for Line of sight
                //var blockedLosHex = weaponManager.getBlockedHexes();
                var blockedLosHex = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.                 
                var loSBlocked = false;
                if (blockedLosHex && blockedLosHex.length > 0) {
                    var sPosShooter = weaponManager.getFiringHex(selectedShip, weapon);

                    loSBlocked = mathlib.isLoSBlocked(sPosShooter, hexpos, blockedLosHex);
                }

                if (loSBlocked && !weapon.ignoresLoS) {
                    confirm.error("No line of sight between firing ship and target hex.");
                    return; //End work if no line of sight.
                }

                if (weapon.range === 0 || shipManager.getShipPosition(selectedShip).distanceTo(hexpos) <= weapon.range) {

                    if (weapon.canSplitShots) {
                        var fire = weapon.doMultipleHexFireOrders(selectedShip, hexpos);
                        if (!Array.isArray(fire)) fire = fire ? [fire] : []; // Ensure fire is an array or an empty one                       
                        if (fire.length === 0) continue;

                        weapon.fireOrders.push(...fire);
                        var finishedFiring = weapon.checkFinished(); //Split weapons should unselect after they've used all their shots.
                        if (finishedFiring) {
                            toUnselect.push(weapon); //Normal method
                        } else {
                            splitTargeted.push(weapon); //Not finished, to be added to toUnselect aray at correct time below. 	  
                        }
                        webglScene.customEvent('SystemDataChanged', { ship: selectedShip, system: weapon });
                    } else if (weapon.name === 'jumpEngine') {
                        //JUMP_POINTS_PLAN.md Stage 2b. The vortex FACING is part of the
                        //declaration, so the order cannot be built here: open the on-map facing
                        //control and let its OK button build it. Async (callback), so we DON'T
                        //fall through to the synchronous order build below - the same shape as
                        //ShadowFighterBomb beneath this.
                        //The `continue` also skips the loop tail, so the OK callback owns
                        //unSelectWeapon and the HexTargeted event itself (see
                        //createJumpPointOrder). Clicking away instead simply never calls it,
                        //which is what makes discarding cost nothing to clean up.
                        weaponManager.queueJumpPointOrder(selectedShip, weapon, hexpos, type);
                        continue;
                    } else if (weapon.name === 'ShadowFighterBomb') {
                        //Stage S (S-f): the Fighter Bomb launches a player-chosen
                        //number of held integrated fighters (1..remaining in the
                        //ShadowHangar) at the target hex. Pop the standard numeric
                        //count picker (confirm.askForMultipleValues), then create the
                        //single fire order with shots = chosen count — the server
                        //(performBombLaunch) clamps to the held pool and launches that
                        //many. Async (callback), so we DON'T fall through to the
                        //synchronous order build / toUnselect below.
                        weaponManager.queueShadowFighterBombOrder(selectedShip, weapon, hexpos, type);
                        continue;
                    } else {

                        weaponManager.removeFiringOrder(selectedShip, weapon);
                        for (var s = 0; s < weapon.guns; s++) {

                            var fireid = selectedShip.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
                            var fire = {
                                id: fireid,
                                type: type,
                                shooterid: selectedShip.id,
                                targetid: -1,
                                weaponid: weapon.id,
                                calledid: -1,
                                turn: gamedata.turn,
                                firingMode: weapon.firingMode,
                                shots: weapon.defaultShots,
                                x: hexpos.q,
                                y: hexpos.r,
                                damageclass: weapon.data["Weapon type"].toLowerCase()
                            };
                            weapon.fireOrders.push(fire);
                        }

                        toUnselect.push(weapon);
                    }
                }
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        toUnselect.push(...splitTargeted); //We don't want to unselect, but want these weapons passed to onHexTargeted - DK 01.25
        webglScene.customEvent('HexTargeted', { shooter: selectedShip, hexagon: hexpos })
    },

    // Stage S (S-f): held integrated-fighter count the Fighter Bomb can launch.
    // Sums ShadowMediumFighterFlight entries (skipping cannotLaunch wrecks). Mirrors
    // the server pool sum in HangarOps::performBombLaunch.
    //
    // MULTI-BAY (shadowRegenBaseBomb): pass the firing $weapon — when it carries a
    // bombHangarIndex the pool is scoped to ITS bay only (the ShadowHangar whose
    // bombGroupIndex matches), since each bomb launches just its own 6. SINGLE-BAY
    // (shadowCruiserBomb): $weapon omitted / no index → sum across all ShadowHangars.
    //
    // $subtractPending (default false): when true, also nets off fighters already QUEUED
    // to launch this turn (pending bomb fire orders on the relevant bay's bomb) so a
    // DISPLAY like "Fighters available" drops immediately after a launch is queued. The
    // launch DIALOG must NOT pass this — it replaces prior orders, so it needs the full
    // pre-order pool as the max (otherwise re-editing an order double-discounts).
    shadowFighterBombPool: function shadowFighterBombPool(carrier, weapon, subtractPending) {
        var pool = 0;
        if (!carrier || !carrier.systems) return 0;
        var bayIndex = (weapon && weapon.bombHangarIndex !== undefined && weapon.bombHangarIndex !== null)
            ? parseInt(weapon.bombHangarIndex, 10) : null;
        for (var i in carrier.systems) {
            var sys = carrier.systems[i];
            if (!sys || !sys.isShadowHangar || !Array.isArray(sys.hangarUsage)) continue;
            // Scope to this bomb's own bay when a bombHangarIndex was given.
            if (bayIndex !== null && parseInt(sys.bombGroupIndex, 10) !== bayIndex) continue;
            sys.hangarUsage.forEach(function (e) {
                if (!e || e.phpclass !== 'ShadowMediumFighterFlight' || e.cannotLaunch) return;
                pool += parseInt(e.flightSize || 1, 10);
            });
        }

        // Net off fighters QUEUED to launch this turn so a display reflects the pending
        // order immediately (the held hangarUsage above doesn't shrink until the turn
        // resolves and the server drains it). Count each ShadowFighterBomb's pending
        // shots, scoped to the same bay (its bombHangarIndex matches $weapon's, or both
        // unindexed on a single-bay hull).
        if (subtractPending) {
            for (var j in carrier.systems) {
                var bsys = carrier.systems[j];
                if (!bsys || bsys.name !== 'ShadowFighterBomb' || !Array.isArray(bsys.fireOrders)) continue;
                var bIdx = (bsys.bombHangarIndex !== undefined && bsys.bombHangarIndex !== null)
                    ? parseInt(bsys.bombHangarIndex, 10) : null;
                if (bIdx !== bayIndex) continue;   //a bomb only draws from its own bay
                bsys.fireOrders.forEach(function (fo) {
                    if (!fo) return;
                    if (fo.turn != null && typeof gamedata !== 'undefined' && fo.turn != gamedata.turn) return;
                    pool -= Math.max(0, parseInt(fo.shots || 0, 10));
                });
            }
            pool = Math.max(0, pool);
        }

        // Stage S (S-d): STRUCTURE-BOX cap. Each integrated fighter in space binds a
        // marked structure box, so a carrier can never launch more than it has boxes
        // for. Clamp the offered pool to remaining structure (a combat-reduced carrier
        // with 4 structure but 6 held can only launch 4; the rest stay held until
        // SelfRepair restores structure). The server (HangarOps::performBombLaunch)
        // is the authoritative clamp — it also nets off fighters already in space; this
        // is a UX guard so the dialog doesn't offer a launch that gets silently trimmed.
        var struct = shipManager.systems.getStructureSystem(carrier, 0);
        if (struct) {
            var structRemaining = Math.max(0, parseInt(shipManager.systems.getRemainingHealth(struct), 10) || 0);
            pool = Math.min(pool, structRemaining);
        }
        return pool;
    },

    // Stage S (S-f): max fighters per flight a Fighter Bomb can spawn. Mirrors the
    // server HangarOps cap: min(class flight-size limit, fighters currently HELD).
    // The class limit for the only bomb-launched class (ShadowMediumFighterFlight,
    // jinking 8) is 9; clamped to $pool so a bay holding fewer (ShadowCruiser: 6)
    // never offers a per-flight size bigger than its stock. This is the MANUAL
    // per-flight ceiling; the AUTOMATIC split groups into smaller chunks (see below).
    shadowFighterBombFlightCap: function shadowFighterBombFlightCap(carrier, pool) {
        var classLimit = 9;
        var p = parseInt(pool, 10);
        if (!isNaN(p) && p > 0 && p < classLimit) return p;
        return classLimit;
    },

    // Preferred per-flight size for the AUTOMATIC split (user 2026-07-09). Mirrors
    // HangarOps::bombAutoSplitChunk (6). Distinct from the cap: the auto-split groups
    // fighters into flights of 6 with a trailing remainder (12→6+6, 15→6+6+3, 7→6+1,
    // ≤6→one flight); a manual override may still reach the cap (9). Clamped to $pool.
    shadowFighterBombAutoChunk: function shadowFighterBombAutoChunk(carrier, pool) {
        var chunk = 6;
        var p = parseInt(pool, 10);
        if (!isNaN(p) && p > 0 && p < chunk) return p;
        return chunk;
    },

    /* JUMP_POINTS_PLAN.md STAGE 2b - start a vortex declaration transaction.

       Raises the facing control and returns; NOTHING is committed here. PhaseStrategy relays
       VortexFacingRequested to UI.vortexFacing, anchors it to the hex, and registers the
       click-away discard. createJumpPointOrder below is reached only from the OK button.

       DEFAULT FACING IS ALWAYS 0 (east) - user ruling 2026-08-21. It was briefly derived from the
       declaring ship's heading (heading + 3, so the mouth pointed back at the ship and a
       straight-ahead projection was OK-and-done), but a fixed, predictable starting point that
       never depends on how the ship happens to be pointing is easier to read and to teach, and the
       turn buttons now flank the facing arrow so stepping round is cheap. */
    queueJumpPointOrder: function queueJumpPointOrder(ship, weapon, hexpos, type) {
        webglScene.customEvent('VortexFacingRequested', {
            ship: ship,
            weapon: weapon,
            hexpos: hexpos,
            type: type,
            facing: 0,
            onConfirm: function (facing) {
                weaponManager.createJumpPointOrder(ship, weapon, hexpos, type, facing);
            }
        });
    },

    /* The OK half of the transaction: build the declaration and do the work targetHex's loop tail
       would have done, since the `continue` up there skipped it.

       firingMode carries the FACING (mode = facing + 1) - that is the whole reason JumpEngine has
       seven functionally identical modes: it persists to tac_fireorder.firingmode, so the facing
       needs no schema change and no new column.

       Exactly ONE order, not one per weapon.guns: a ship may hold one vortex, and
       Firing::getVortexDeclarationBlock rejects every declaration after the first anyway. */
    createJumpPointOrder: function createJumpPointOrder(ship, weapon, hexpos, type, facing) {
        //Re-checked at OK, not just at targeting: a server poll can land between the two and move
        //a terrain unit's owner or reveal a unit that was not there when the hex was picked.
        var vortexBlock = weaponManager.getVortexHexBlock(ship, weapon, hexpos);
        if (vortexBlock) {
            confirm.error(vortexBlock);
            return;
        }

        weaponManager.removeFiringOrder(ship, weapon);

        var fireid = ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
        weapon.fireOrders.push({
            id: fireid,
            type: type,
            shooterid: ship.id,
            targetid: -1,
            weaponid: weapon.id,
            calledid: -1,
            turn: gamedata.turn,
            firingMode: facing + 1,
            shots: weapon.defaultShots,
            x: hexpos.q,
            y: hexpos.r,
            damageclass: weapon.data["Weapon type"].toLowerCase()
        });

        weaponManager.unSelectWeapon(ship, weapon);
        webglScene.customEvent('HexTargeted', { shooter: ship, hexagon: hexpos });
    },

    /* JUMP_POINTS_PLAN.md STAGE 5 - MAINTAINING is NOT here. It is a toggle in the Jump Engine's
       own system menu (JumpEngineMenu / JumpEngine.doActivate in model/system/baseSystems.js),
       because the declaration is only half of it: the ship also has to shut everything down, which
       is not something a right-click on a hex can do, and the two halves have to happen together or
       the vortex closes at the end of the turn anyway. The ORDER the toggle produces is identical -
       firing mode 7 at the vortex's own hex - so the server side is the same either way. */

    /* =============== JUMP_GATES_PLAN.md STAGE 3 - SIGNALLING A FIXED JUMP GATE ================

       ⭐ THIS IS NOT A TARGETING GESTURE, AND IT DELIBERATELY DOES NOT GO THROUGH targetHex.

       Everything above starts from "I have a ship selected and some of its weapons selected, and I
       have right-clicked a hex". A gate signal starts from clicking THE GATE, with no ship selected
       and none needed - which unit of mine is in range is never chosen and never matters (plan
       section 2.1) - and the weapon it declares on belongs to a unit the player does not own. Every
       gate in targetHex's loop (weapon selection, arc, range from the shooter, line of sight, the
       firing-link test) is either the wrong question or an outright wrong answer here, which is why
       this is its own short path from the Initial Orders tooltip.

       THIS TURN'S SIGNAL ORDER ON $gate, or null - "does a claim of mine stand on this gate?".

       ⚠️ SCOPED TO THIS TURN AND TO MODES 1-4, never a bare fireOrders check. A gate's engine
       accumulates every claim ever made on it across the game, and the ones from earlier turns are
       history that must not be mistaken for a live one - the same reason
       JumpEngine.removeVortexMaintainOrder is turn-scoped rather than going through
       removeFiringOrder. While Initial Orders are open the only current-turn order that can be here
       is one THIS client just made: TacGamedata::hideSystemFireOrders strips every phase-1 ballistic
       order from every payload, its author's included. */
    getGateSignalOrder: function getGateSignalOrder(gate) {
        var engine = gamedata.getGateJumpEngine(gate);
        if (!engine || !Array.isArray(engine.fireOrders)) return null;

        for (var i = 0; i < engine.fireOrders.length; i++) {
            var fire = engine.fireOrders[i];
            if (!fire || fire.turn != gamedata.turn) continue;
            var mode = parseInt(fire.firingMode, 10);
            if (isNaN(mode) || mode < 1 || mode > 4) continue;
            return fire;
        }

        return null;
    },

    /* Withdraw it again. Turn-scoped for the reason above - removeFiringOrder would take every
       claim this gate has ever carried with it. */
    removeGateSignalOrder: function removeGateSignalOrder(gate) {
        var engine = gamedata.getGateJumpEngine(gate);
        if (!engine || !Array.isArray(engine.fireOrders)) return;

        for (var i = engine.fireOrders.length - 1; i >= 0; i--) {
            var fire = engine.fireOrders[i];
            if (!fire || fire.turn != gamedata.turn) continue;
            var mode = parseInt(fire.firingMode, 10);
            if (isNaN(mode) || mode < 1 || mode > 4) continue;
            engine.fireOrders.splice(i, 1);
        }

        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - AND THE MANIFEST GOES WITH IT, exactly as
           ReinforcementEntry.withdraw() drops a ship exit's. A berth naming a gate whose claim
           has just been taken back is a booking for a doorway that will not exist, and leaving it
           standing would post a manifest for nothing - the server would refuse it (the gate is no
           longer in persistManifest's opener list) and the player would never be told.
           ⚠️ ONLY WHEN THE GATE IS NOT ALREADY HOLDING A DOORWAY. Cancelling THIS turn's claim on a
           gate whose exit is already open from an earlier turn takes nothing away - the berth
           still has a doorway to ride, and clearing it would quietly cancel a wave the player never
           asked to cancel.

           ⚠️ createGateSignalOrder BELOW IS ALSO A CALLER - it withdraws before it re-declares, to
           keep one claim per player per gate per turn. That path cannot lose a manifest silently:
           the two tooltip buttons are gated on noGateSignalYet so a live claim cannot be replaced
           without cancelling first, and the manifest dialog opens immediately afterwards, so an
           empty list would be on screen rather than merely true. */
        if (window.ReinforcementEntry && !shipManager.movement.getExitHeldBy(gate.id)) {
            ReinforcementEntry.clearGateManifest(gate);
        }

        webglScene.customEvent('SystemDataChanged', { ship: gate, system: engine });
        webglScene.customEvent('HexTargeted', { shooter: gate, hexagon: shipManager.getShipPosition(gate) });
    },

    /* START A SIGNAL TRANSACTION. Raises the duration panel and returns; NOTHING is declared here.
       PhaseStrategy relays GateSignalRequested to UI.gateSignal, anchors it to the gate's hex and
       registers the click-away discard. createGateSignalOrder below is reached only from SIGNAL.

       DEFAULT DURATION IS 1 TURN, clamped to the gate's cap. The shortest opening is the cheapest
       mistake - a jump point standing open for four turns is a door the enemy may also use (plan
       section 2.6: ANY unit may fly into a gate vortex), so the safe number is the default and
       longer is a deliberate choice. */
    queueGateSignalOrder: function queueGateSignalOrder(gate, exit) {
        if (!gamedata.canSignalJumpGate(gate)) return;
        //REINFORCEMENTS_PLAN.md Stage 8: an ARRIVAL claim needs the reinforcements rule to be on in
        //this game (it no longer needs anything of this player's to be waiting - user ruling
        //2026-09-02, see gamedata.canSignalJumpGateForArrival). Re-asked here as well as on the
        //tooltip button because a poll can land in between - and the server refuses the claim
        //outright (Firing::getGateSignalBlock), so an order built without it would be silently
        //dropped at commit.
        if (exit && !gamedata.canSignalJumpGateForArrival(gate)) return;

        var engine = gamedata.getGateJumpEngine(gate);
        var maxHold = shipManager.systems.getGateMaxHold(gate);

        webglScene.customEvent('GateSignalRequested', {
            gate: gate,
            engine: engine,
            hexpos: shipManager.getShipPosition(gate),
            hold: 1,                 //maxHold is never below 1, so the default never needs clamping
            maxHold: maxHold,
            /* REINFORCEMENTS_PLAN.md STAGE 8 - WHICH WAY THE DOORWAY FACES. The panel wears its blue
               livery and its own button label off this flag, and it rides the confirm through to the
               order's damageclass. Everything else about the transaction is identical: a gate holds
               ONE jump point, so entry and entrance are two answers to one question, never two claims. */
            exit: !!exit,
            onConfirm: function (hold) {
                weaponManager.createGateSignalOrder(gate, hold, exit);
            }
        });
    },

    /* THE SIGNAL HALF OF THE TRANSACTION: build the claim.

       The order is an ordinary ballistic hex-target FireOrder on the GATE's own Jump Engine, aimed
       at the GATE's own hex - and that single choice buys the whole pipeline for free: secrecy
       until Initial Orders close (hideSystemFireOrders strips every phase-1 ballistic order), the
       map marker, the replay, the combat-log line and the server-side legality check (plan
       section 3.3).

       firingMode CARRIES THE PROGRAMMED OPEN DURATION IN TURNS, 1-4 - not a facing. That is what
       markGate() re-purposes the modes for, and it persists to tac_fireorder.firingmode with no
       schema change. A gate's facing is fixed when the gate is placed and is never in the order.

       targetid CARRIES THE CLAIMING PLAYER, as their nearest qualifying unit, because tac_fireorder
       has no player column and the gate belongs to nobody in particular. ⚠️ IT IS A HINT, NEVER AN
       AUTHORITY: Firing::getGateSignalBlock re-derives the nearest unit from $gamedata->forPlayer
       and OVERWRITES this before the row is written, and TacGamedata::hideSystemFireOrders masks it
       for every viewer it does not belong to - because it is the only field that could name a
       signaller, and a signaller is never named (plan section 2.1, trap 4).

       Re-checked at SIGNAL rather than only at the tooltip: a server poll can land between opening
       the panel and pressing the button, and it can move the ships the range test reads. */
    createGateSignalOrder: function createGateSignalOrder(gate, hold, exit) {
        if (!gamedata.canSignalJumpGate(gate)) {
            confirm.error("This jump gate can no longer be signalled.");
            return;
        }

        //The message is about the RULE, not about the fleet: since 2026-09-02 a player may open an
        //arrival doorway with nothing of their own waiting behind it (a teammate's wave, or their
        //own on a later turn, can ride it), so the only way to reach this is a game that does not
        //have reinforcements at all.
        if (exit && !gamedata.canSignalJumpGateForArrival(gate)) {
            confirm.error("This game has no reinforcements, so a jump gate cannot be signalled to "
                + "open a way in from hyperspace.");
            return;
        }

        var engine = gamedata.getGateJumpEngine(gate);
        var source = gamedata.getGateSignalSource(gate);
        if (!engine || !source) return;

        var maxHold = shipManager.systems.getGateMaxHold(gate);
        hold = Math.max(1, Math.min(parseInt(hold, 10) || 1, maxHold));

        var hex = shipManager.getShipPosition(gate);

        weaponManager.removeGateSignalOrder(gate);   //one claim per player per gate per turn

        engine.fireOrders.push({
            id: gate.id + "_" + engine.id + "_" + (engine.fireOrders.length + 1),
            type: 'ballistic',
            shooterid: gate.id,
            targetid: source.id,
            weaponid: engine.id,
            calledid: -1,
            turn: gamedata.turn,
            firingMode: hold,
            shots: engine.defaultShots,
            x: hex.q,
            y: hex.r,
            /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE FLAVOUR IS THE damageclass, mirroring the way
               'jumpexit' tells a ship's exit from its entrance (§3.4). 'gateexit' asks the gate for
               a doorway IN: Firing::getGateSignalBlock judges it by the identical gate list plus two
               rules, and JumpEngine::resolveGateClaims spawns a SpawnJumpPointExit for it if the
               claim wins the contest. Anything else keeps the Phase 2 value ('jumppoint'), so an
               ordinary entrance claim is byte-identical to before. */
            damageclass: exit ? 'gateexit' : engine.data["Weapon type"].toLowerCase()
        });

        webglScene.customEvent('SystemDataChanged', { ship: gate, system: engine });
        webglScene.customEvent('HexTargeted', { shooter: gate, hexagon: hex });

        /* ⭐⭐ AND THE MANIFEST FOLLOWS IMMEDIATELY (user request 2026-08-28). Signalling a gate for
           arrival is only half a gesture: the claim opens the doorway and the manifest says who
           walks through it, and they are named in the same breath everywhere else in this feature
           (ReinforcementEntry.createExitOrder does exactly this). Without it the player would
           have to signal the gate, close the panel, then find "Manage Reinforcements" and pick the
           gate out of the list to do the other half - and a gate signalled with nobody on its
           manifest brings nothing through, silently.
           Guarded on the module: gateSignal.js has no load-order relationship with it. */
        if (exit && window.ReinforcementEntry) ReinforcementEntry.showGateManifest(gate);
    },

    // Stage S (S-f): open the Fighter Bomb launch dialog (count + auto-split toggle /
    // manual per-flight sizes), then emit the fire order(s) at the target hex. A launch
    // bigger than the flight-size cap must spawn multiple flights:
    //   - AUTO (checkbox on): ONE fire order with shots = total; the server splits it.
    //   - MANUAL (checkbox off): ONE fire order PER chosen flight, shots = that size.
    // The combat log groups the same-hex orders into one "Fighter Bomb" entry.
    queueShadowFighterBombOrder: function queueShadowFighterBombOrder(carrier, weapon, hexpos, type) {
        //A carrier that half-phased this turn is partly in hyperspace and cannot
        //form/expel its integrated fighters — the server (ShadowFighterBomb::fire)
        //rejects the launch too, so block the order here for immediate feedback.
        if (shipManager.movement.isHalfPhased(carrier)) {
            confirm.warning("Half-phased ships cannot launch fighters with the Fighter Bomb.");
            return;
        }
        //Multi-bay: scope the offered pool to THIS bomb's bay (weapon.bombHangarIndex).
        var pool = weaponManager.shadowFighterBombPool(carrier, weapon);
        if (pool <= 0) {
            confirm.warning("No integrated fighters left in this hangar to launch.");
            return;
        }
        var cap = weaponManager.shadowFighterBombFlightCap(carrier, pool);
        var chunk = weaponManager.shadowFighterBombAutoChunk(carrier, pool);

        confirm.shadowFighterBomb(carrier, pool, cap, chunk, function (result) {
            var sizes = (result && Array.isArray(result.sizes)) ? result.sizes : [];
            if (sizes.length === 0) return;

            // Replace any prior bomb order(s) on this weapon, then emit one per flight.
            weaponManager.removeFiringOrder(carrier, weapon);
            sizes.forEach(function (sz) {
                sz = parseInt(sz, 10);
                if (isNaN(sz) || sz < 1) return;
                var fireid = carrier.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
                weapon.fireOrders.push({
                    id: fireid,
                    type: type,
                    shooterid: carrier.id,
                    targetid: -1,
                    weaponid: weapon.id,
                    calledid: -1,
                    turn: gamedata.turn,
                    firingMode: weapon.firingMode,
                    shots: sz,                          //this flight's fighter count
                    x: hexpos.q,
                    y: hexpos.r,
                    damageclass: weapon.data["Weapon type"].toLowerCase()
                });
            });

            weaponManager.unSelectWeapon(carrier, weapon);
            webglScene.customEvent('SystemDataChanged', { ship: carrier, system: weapon });
            webglScene.customEvent('HexTargeted', { shooter: carrier, hexagon: hexpos });
            //Refresh the carrier's ShadowHangar tooltip(s) so their capacity line
            //immediately shows "(Launching N)" from this bomb order (S-f).
            weaponManager.refreshShadowHangarTooltips(carrier);
        });
    },

    // Stage S (S-f): recompute + redraw the ShadowHangar tooltip(s) on a carrier so
    // their projected "(Launching N)" capacity line reflects the current Fighter Bomb
    // fire order (placed or cleared). Called after a bomb order changes.
    refreshShadowHangarTooltips: function refreshShadowHangarTooltips(carrier) {
        if (!carrier || !carrier.systems) return;
        for (var i in carrier.systems) {
            var sys = carrier.systems[i];
            if (!sys || !sys.isShadowHangar) continue;
            if (typeof sys.refreshHangarTooltip === 'function') sys.refreshHangarTooltip();
            webglScene.customEvent('SystemDataChanged', { ship: carrier, system: sys });
        }
    },

    removeFiringOrder: function removeFiringOrder(ship, system) {
        if (system.multiModeSplit) { //Divert to weapon function for these specific weapons.
            system.removeAllMultiModeSplit(ship);
            return;
        }
        for (var i = system.fireOrders.length - 1; i >= 0; i--) {
            if (system.fireOrders[i].weaponid == system.id) {
                system.fireOrders.splice(i, 1);
            }
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });

        //Stage S (S-f): clearing a Fighter Bomb order frees its launching fighters —
        //refresh the carrier's ShadowHangar tooltip(s) so their "(Launching N)" line
        //disappears (the bomb order this method just removed no longer projects).
        if (system.name === 'ShadowFighterBomb') weaponManager.refreshShadowHangarTooltips(ship);

        if (gamedata.gamephase == 3 && ship.flight) webglScene.customEvent("ShipMovementChanged", { ship: ship }); //Redraw movement for Combat Pivots
    },

    removeFiringOrderMulti: function removeFiringOrderMulti(ship, system, target = null, button = false) {

        if (system.multiModeSplit) { //Divert to weapon function for these specific weapons.
            system.removeMultiModeSplit(ship, target);
            return;
        }
        if (weaponManager.hasFiringOrder(ship, system)) {
            // Remove the fire order for targeted ship
            if (button) {
                // When a tooltip button is pressed on targeted enemy ship, check for a matching fireOrder and remove
                for (var i = system.fireOrders.length - 1; i >= 0; i--) {
                    var fireOrder = system.fireOrders[i];
                    // Check if the fire order's target matches the provided target
                    if (fireOrder.targetid == target?.id) {
                        if (fireOrder.hitmod > 0) { //Slicers have cumulative hitmod on split shots, when a fireOrder is removed all orders are reclaculated.
                            system.recalculateFireOrders(ship, i);
                        }

                        system.fireOrders.splice(i, 1); // Remove the specific fire order
                        system.maxVariableShots++; // Increment your counter
                        webglScene.customEvent('SplitOrderRemoved', { shooter: ship, target: target });

                        break; // Exit the loop after removing one matching fire order and recalculating the rest (if required).
                    }
                }
            } else {
                // Default case: Remove only the LAST fire order. No need to adjust hitMod as it's the last order anyway.'
                var lastFireOrder = system.fireOrders[system.fireOrders.length - 1];
                if (lastFireOrder.weaponid == system.id && lastFireOrder.turn == gamedata.turn) {
                    system.fireOrders.pop(); // Remove the last firing order
                    system.maxVariableShots++; // Increment your counter
                    var targetShip = gamedata.getShip(lastFireOrder.targetid);
                    webglScene.customEvent('SplitOrderRemoved', { shooter: ship, target: targetShip });
                }
            }

        }

        // Trigger custom event to notify of system data changes - call ballisticIconContianer.consumeGamedata()
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });

        // Handle redraw for ship movement in combat phase
        if (gamedata.gamephase == 3 && ship.flight) {
            webglScene.customEvent("ShipMovementChanged", { ship: ship });
        }
    },


    removeFiringOrderAll: function removeFiringOrderAll(ship, system) { //remove firing orders for ALL similar weapons that have them
        if (!gamedata.isMyShip(ship)) {
            return;
        }
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) {
            return;
        }

        var array = [];
        var systems = [];
        if (ship.flight) {
            systems = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            systems = ship.systems.filter(system => system.weapon);
        }

        //group by BASE displayName so paired Kirishiac weapons ('...A'/'...B') count as one type
        var baseName = weaponManager.stripPairingSuffix(system.displayName);
        array = systems.filter(function (weapon) { return weaponManager.stripPairingSuffix(weapon.displayName) === baseName });

        for (var i = 0; i < array.length; i++) {
            var weapon = array[i];
            if (!weaponManager.hasFiringOrder(ship, weapon)) continue;//does not have any declared firing orders
            weaponManager.removeFiringOrder(ship, weapon);
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });

        if (gamedata.gamephase == 3 && ship.flight) webglScene.customEvent("ShipMovementChanged", { ship: ship }); //Redraw movement for Combat Pivots         
    },


    hasFiringOrder: function hasFiringOrder(ship, system) {
        for (var i in system.fireOrders) {
            var fire = system.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) {
                if ((gamedata.gamephase == 1 || gamedata.gamephase == 3) && system.ballistic || gamedata.gamephase == 3 && !system.ballistic || gamedata.gamephase == 5 && system.preFires) {
                    if (fire.type == "selfIntercept") {
                        return "self";
                    } else return true;
                }
            }
        }
        return false;
    },

    hasOrderForMode: function hasOrderForMode(system) {
        for (var i in system.fireOrders) {
            var fire = system.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) {
                if ((gamedata.gamephase == 1 || gamedata.gamephase == 3) && system.ballistic || gamedata.gamephase == 3 && !system.ballistic || gamedata.gamephase == 5 && system.preFires) {
                    if (fire.firingMode == system.firingMode) {
                        return true;
                    }
                }
            }
        }
        return false;
    },


    hasTargetedThisShip: function hasTargetedThisShip(target, system) {
        for (var i in system.fireOrders) {
            var fire = system.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled && fire.targetid == target.id) {
                if ((gamedata.gamephase == 1 || gamedata.gamephase == 3) && system.ballistic || gamedata.gamephase == 3 && !system.ballistic || gamedata.gamephase == 5 && system.preFires) {
                    return true;
                }
            }
        }
        return false;
    },

    shipHasFiringOrder: function shipHasFiringOrder(ship) {
        for (var i in ship.systems) {
            if (ship.flight) {
                var fighter = ship.systems[i];
                for (var a in fighter.systems) {
                    var system = fighter.systems[a];
                    if (system.weapon) {
                        var orders = weaponManager.getAllFireOrdersFromSystem(system);
                        if (orders.length > 0) return true;
                    }
                }
            } else {
                var system = ship.systems[i];
                if (system.weapon) {
                    var orders = weaponManager.getAllFireOrdersFromSystem(system);
                    if (orders.length > 0) return true;
                }
            }
        }
        return false;
    },


    canCombatTurn: function canCombatTurn(ship) {
        var fires = weaponManager.getAllFireOrders(ship);
        if (Object.values(ship.skinDancing).includes(true) || Object.values(ship.skinDancing).includes("Failed")) return false; //Cannot combat pivot while skindancing or after failure      
        for (var i in fires) {
            var fire = fires[i];
            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            //Added Persistent effect check below, as was preventing cancel moves when non-ballistic Plasma Web generated a plasma cloud in Intial Orders - DK 09.24 
            if (fire.turn == gamedata.turn && !fire.rolled && !weapon.ballistic && fire.notes != 'PersistentEffect') {
                return false;
            }
        }

        return true;
    },


    getFiringOrder: function getFiringOrder(ship, system) {
        var fires = weaponManager.getAllFireOrders(ship);
        for (var i in fires) {
            var fire = fires[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) return fire;
        }

        return false;
    },

    getCalledShotInfo: function getCalledShotInfo(ship, system) {
        var fire = weaponManager.getFiringOrder(ship, system);
        if (!fire || fire.calledid == null || fire.calledid == -1) return null;
        var targetShip = gamedata.getShip(fire.targetid);
        if (!targetShip) return null;
        var targetSystem = shipManager.systems.getSystem(targetShip, fire.calledid);
        if (!targetSystem) return null;
        return {
            targetShip: targetShip,
            targetSystem: targetSystem,
            calledId: fire.calledid
        };
    },


    getAllFireOrders: function getAllFireOrders(ship) {
        var fires = new Array();
        for (var i in ship.systems) {
            if (ship.flight) {
                var fighter = ship.systems[i];
                for (var a in fighter.systems) {
                    var system = fighter.systems[a];
                    var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
                    if (sysFires) fires = fires.concat(sysFires);
                }
            } else {
                var system = ship.systems[i];
                var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
                if (sysFires) fires = fires.concat(sysFires);
            }
        }
        return fires;
    },


    getAllBallisticsAgainst: function getAllBallisticsAgainst(ships, hex) {
        ships = [].concat(ships);

        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {

                var targetingShip = ships.some(function (ship) {
                    return ship.id === fire.targetid;
                });

                //TODO: show weapons targeted at hex
                //var targetingHex = fire.targetid === -1 && new hexagon.Offset(fire.x, fire.q).equals(hex);

                return targetingShip; // || targetingHex;
            }));
        }, []).filter(function (fire) {
            return fire.type === "ballistic" || (fire.type === "normal" && fire.damageclass === "Sweeping"); //Ballistics and Shadow Slicers
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid)
            };
        });
    },

    /*
    getAllHexTargetedBallistics: function getAllHexTargetedBallistics() { //that's all hex targeted weapons, not just ballistics
        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {
                return fire.targetid === -1;
            }));
        }, []).filter(function (fire) {
            return fire.rolled !== 0;
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shots: fireOrder.shots,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid)
            };
        })
    },
    */

    /* True once the SERVER has resolved this fire order, i.e. the dice have been rolled and
       shotshit/needed/damage all mean something. The four callers below use it to decide whether
       an order may be RENDERED AS A RESULT - a replay fire animation, a combat-log line.

       This replaces `fire.rolled !== 0`, which was right about server data and wrong about the
       client's own. An order the player has just declared is built as a plain object literal (see
       setSelfIntercept and the weapon models' equivalents) that carries no `rolled` key at all, so
       `undefined !== 0` passed the old test and the order was drawn as though it had been fired -
       with `shotshit` equally absent, printing the literal "NaN/2 shots hit" (user report
       2026-08-03).

       Those locally-built objects survive far longer than they look like they should: while a
       player waits, the APCu fast poll answers with a bare "{}" (gamedata.php), which
       parseServerData discards without reaching setShipsFromJson - so nothing replaces them until
       the server actually has news. Surrendering mid-wait then puts that same client into
       ReplayPhaseStrategy over its own never-resolved orders, which Firing::withdrawSurrenderedFireOrders
       has meanwhile withdrawn server-side. Re-fetching the turn already showed the correct log,
       which is why the entries vanished on stepping away and back; this closes the window before
       that fetch. Nothing about the hole is surrender-specific - any replay rendered over
       unsubmitted local state would have hit the same NaN.

       `> 0` rather than a bare truthy test, to match the server's own convention for the same
       question (Firing::fireWeapons and the Weapon::fire family gate on `$fire->rolled > 0`, and
       auto-hit weapons force `max(1, rolled)` purely to satisfy it). Verified equivalent to the
       old test on real data: across the local corpus every persisted order has `rolled` 0 or
       positive - never null, never negative - and no order with a recorded hit has `rolled` 0. */
    isResolvedFireOrder: function isResolvedFireOrder(fire) {
        return Number(fire.rolled) > 0;
    },

    getAllHexTargetedBallistics: function () {

        var results = [];
        var playerTeam = gamedata.getPlayerTeam();

        for (var s = 0; s < gamedata.ships.length; s++) {

            var shooter = gamedata.ships[s];
            var fires = weaponManager.getAllFireOrders(shooter);

            for (var f = 0; f < fires.length; f++) {

                var fireOrder = fires[f];

                if (fireOrder.targetid !== -1) continue;
                if (!weaponManager.isResolvedFireOrder(fireOrder)) continue;

                var weapon = shipManager.systems.getSystem(shooter, fireOrder.weaponid);

                if (weapon.alwaysHideFireOrders && shooter.team !== playerTeam) {
                    var hasSecondAttack = false;
                    for (var i in weapon.fireOrders) {
                        var otherBall = weapon.fireOrders[i];
                        if (otherBall.shooterid == shooter.id && otherBall.damageclass == "SecondAttack") {
                            hasSecondAttack = true;
                            break;
                        }
                    }
                    if (!hasSecondAttack) {
                        continue;
                    }
                }

                results.push({
                    id: fireOrder.id,
                    fireOrder: fireOrder,
                    shots: fireOrder.shots,
                    shooter: shooter,
                    weapon: weapon
                });
            }
        }

        return results;
    },


    /* Terrain collision return damage - bookkeeping, not an attack.

       A ship crashing into an asteroid or moon is resolved entirely by the TERRAIN's
       RammingAttack: it damages the ship (the "COLLISION!" order, which is what players want to
       see), then applies return damage to itself. That return damage is dealt while the fire
       order id is still -1, so DBManager::submitDamages has to reattach it by matching
       shooter/target/weapon - and the collision order targets the SHIP, so it never matches.
       RammingAttack::fire therefore invents this self-targeted 'AutoRam' order purely as
       something for the damage to hang off.

       Players read it as the asteroid shooting itself, alongside a collision entry that already
       reports the damage they care about, so it is kept out of both the combat log and the replay
       animation. Scoped to terrain shooters: a SHIP ramming an Enormous unit produces the same
       order shape and still shows its own return damage exactly as before. */
    isTerrainReturnDamage: function isTerrainReturnDamage(fire) {
        if (!fire || fire.damageclass !== "AutoRam") return false;
        var shooter = gamedata.getShip(fire.shooterid);
        return !!shooter && gamedata.isTerrain(shooter.shipSizeClass, shooter.userid);
    },

    getAllPreFireOrdersForDisplayingAgainst: function getAllPreFireOrdersForDisplayingAgainst(target) {
        //one reverse map for every order resolved against this target, instead of one full
        //fleet sweep per order - see the note above getDamagesCausedBy
        var damageIndex = weaponManager.buildDamageIndex(gamedata.ships);
        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {
                return fire.targetid === target.id && (fire.type === "prefiring");
            }));
        }, []).filter(function (fire) {
            if (weaponManager.isTerrainReturnDamage(fire)) return false;
            return weaponManager.isResolvedFireOrder(fire);
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shots: fireOrder.shots,
                hits: fireOrder.shotshit,
                firingMode: fireOrder.firingMode,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid),
                targetSystem: shipManager.systems.getSystem(target, fireOrder.calledid),
                damagesCaused: weaponManager.getDamagesCausedBy(fireOrder, null, null, damageIndex).reduce(function (damages, damage) {
                    return damages.concat(damage.damages);
                }, []).map(function (damage) {
                    return {
                        armour: damage.armour,
                        damage: damage.damage,
                        damageclass: damage.damageclass,
                        destroyed: damage.destroyed,
                        system: shipManager.systems.getSystem(gamedata.getShip(damage.shipid), damage.systemid)
                    };
                }),
            };
        }).sort(function (obj1, obj2) {
            if (obj1.weapon.priority !== obj2.weapon.priority) {
                return obj1.weapon.priority - obj2.weapon.priority;
            } else {
                var $val = obj1.shooter.id - obj2.shooter.id;
                if ($val === 0) $val = obj1.id - obj2.id;
                return $val;
            }
        });
    },

    // damageIndex is optional, and follows the same convention as logFireOrders: a caller that
    // asks about several targets in a row (the replay's fire pass, which now walks the fleet
    // twice - once for multi-target volleys, once for everything else) builds ONE index and
    // passes it in, so the fleet sweep happens once for the whole pass instead of once per
    // target. Callers that pass nothing keep the original per-call sweep.
    getAllFireOrdersForDisplayingAgainst: function getAllFireOrdersForDisplayingAgainst(target, damageIndex) {
        //one reverse map for every order resolved against this target, instead of one full
        //fleet sweep per order - see the note above getDamagesCausedBy
        if (!damageIndex) damageIndex = weaponManager.buildDamageIndex(gamedata.ships);
        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {
                return fire.targetid === target.id && (fire.type === "normal" || fire.type === "ballistic");
            }));
        }, []).filter(function (fire) {
            if (weaponManager.isTerrainReturnDamage(fire)) return false;
            return weaponManager.isResolvedFireOrder(fire);
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shots: fireOrder.shots,
                hits: fireOrder.shotshit,
                firingMode: fireOrder.firingMode,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid),
                targetSystem: shipManager.systems.getSystem(target, fireOrder.calledid),
                damagesCaused: weaponManager.getDamagesCausedBy(fireOrder, null, null, damageIndex).reduce(function (damages, damage) {
                    return damages.concat(damage.damages);
                }, []).map(function (damage) {
                    return {
                        armour: damage.armour,
                        damage: damage.damage,
                        damageclass: damage.damageclass,
                        destroyed: damage.destroyed,
                        system: shipManager.systems.getSystem(gamedata.getShip(damage.shipid), damage.systemid)
                    };
                }),
                intercepts: weaponManager.getInterceptingFiringOrders(fireOrder.id).map(function (intercept) {
                    var interceptShooter = gamedata.getShip(intercept.shooterid);
                    return {
                        fireOrder: intercept,
                        shooter: interceptShooter,
                        weapon: shipManager.systems.getSystem(interceptShooter, intercept.weaponid)
                    };
                })
            };
        }).sort(function (obj1, obj2) {
            if (obj1.weapon.priority !== obj2.weapon.priority) {
                return obj1.weapon.priority - obj2.weapon.priority;
            } else {
                var $val = obj1.shooter.id - obj2.shooter.id;
                if ($val === 0) $val = obj1.id - obj2.id;
                return $val;
            }
        });
    },

    getAllFireOrdersFromSystem: function getAllFireOrdersFromSystem(system) {
        if (!system.weapon) return;

        var fires = system.fireOrders;

        return fires;
    },

    /* Every intercept order committed against ONE fire order, by its DB id. Feeds the combat log's
       per-shot "intercepted by" list (getIncomingFireAgainst).

       `id` is a FIRE ORDER id, so only type 'intercept' can match it: an 'intercept' order's
       targetid is the id of the shot it is stopping, whereas a 'selfIntercept' order's targetid is
       the interceptor's OWN SHIP id (see weaponManager.setSelfIntercept and the
       doMultipleSelfIntercept overrides - all five set `targetid: ship.id`). The old condition also
       tested for selfIntercept here; that clause could never legitimately match and fired only when
       a tac_ship.id happened to collide with a tac_fireorder.id, listing an unrelated weapon as an
       interceptor of this shot. Same id-space confusion as the one fixed in
       Firing::automateIntercept's totals loop, and the server's own counters have always tested
       'intercept' alone. Nothing is lost: a selfIntercept marker that actually intercepted is given
       a REAL intercept order by Firing::automateIntercept, and that order is what belongs here. */
    getInterceptingFiringOrders: function getInterceptingFiringOrders(id) {
        var intercepts = Array();

        for (var a in gamedata.ships) {
            var ship = gamedata.ships[a];
            var fires = weaponManager.getAllFireOrders(ship);
            for (var i in fires) {
                var fire = fires[i];
                if (fire.type == "intercept" && fire.targetid == id && fire.turn == gamedata.turn) {
                    intercepts.push(fire);
                }
            }
        }

        return intercepts;
    },

    changeShots: function changeShots(ship, system, mod) {
        var fires = weaponManager.getAllFireOrders(ship);
        for (var i in fires) {
            var fire = fires[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) {
                if (gamedata.gamephase == 1 && system.ballistic || gamedata.gamephase == 3 && !system.ballistic || gamedata.gamephase == 5 && system.preFires) fire.shots += mod;
            }
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    },

    /* ---- damage lookup by fire order -------------------------------------------------
       getDamagesCausedBy answers "what did this shot do" by sweeping every ship, system and
       fighter subsystem for entries stamped with the fire order's id. That is fine for a
       one-off lookup, but the replay and log paths ask it once per fire order, so a turn with
       many shots re-walks the whole fleet hundreds of times. A caller that is about to process
       a BATCH of fire orders can build the reverse map once with buildDamageIndex() and pass it
       in; the sweep is kept intact as the fallback, so any caller that passes no index behaves
       exactly as before.

       An index is deliberately never cached across calls. gamedata.setShipsFromJson rebuilds
       ship objects in place on every poll (the ships ARRAY keeps its identity, so an array-keyed
       cache would silently go stale), and a couple of display paths re-point system.damage at a
       parent weapon's array (SystemIcon / shipwindow). Every index built here therefore lives
       and dies inside one synchronous pass over data that cannot change underneath it. */

    // Map key for a fire order id. The sweep compares with == so a numeric id and its string
    // form match; String() reproduces that for every id shape in play (ints from the server,
    // client-side composite strings like "12_3_1"). Real keys carry an "id:" prefix so the
    // nullish key can never collide with an id that stringifies to the same word - this codebase
    // does put the literal string "null" in fire orders (FireOrder.x/y). null and undefined
    // share one key because null == undefined is true.
    damageIndexKey: function damageIndexKey(fireorderid) {
        if (fireorderid === null || fireorderid === undefined) return "nullish";
        return "id:" + String(fireorderid);
    },

    // fire order id -> [{ship, damages}], ships in iteration order and each ship's entries in
    // the same order the sweep would have collected them (system order, own damage before
    // fighter-subsystem damage). One pass instead of one pass per fire order.
    buildDamageIndex: function buildDamageIndex(ships) {
        var shipsToIterate = ships || gamedata.ships;
        var index = new Map();

        function record(ship, d) {
            var key = weaponManager.damageIndexKey(d.fireorderid);
            var perShip = index.get(key);
            if (!perShip) {
                perShip = [];
                index.set(key, perShip);
            }
            // a ship is walked to completion before the next one starts, so all of its entries
            // for a given key are contiguous - the last bucket is this ship's if it has one
            var bucket = perShip.length > 0 ? perShip[perShip.length - 1] : null;
            if (!bucket || bucket.ship !== ship) {
                bucket = { ship: ship, damages: [] };
                perShip.push(bucket);
            }
            bucket.damages.push(d);
        }

        for (var i in shipsToIterate) {
            var ship = shipsToIterate[i];
            for (var a in ship.systems) {
                var system = ship.systems[a];
                for (var b in system.damage) record(ship, system.damage[b]);
                if (system.fighter) {
                    for (var c in system.systems) {
                        var fighterSystem = system.systems[c];
                        for (var e in fighterSystem.damage) record(ship, fighterSystem.damage[e]);
                    }
                }
            }
        }

        return index;
    },

    getDamagesCausedBy: function getDamagesCausedBy(fire, damages, ships = null, index = null) {

        if (!damages) {
            damages = [];
        }

        var matches;

        if (index) {
            matches = index.get(weaponManager.damageIndexKey(fire.id)) || [];
        } else {
            matches = [];
            var shipsToIterate = ships || gamedata.ships;

            for (var i in shipsToIterate) {
                var ship = shipsToIterate[i];
                var list = Array();

                for (var a in ship.systems) {
                    var system = ship.systems[a];
                    for (var b in system.damage) {
                        var d = system.damage[b];
                        if (d.fireorderid == fire.id) {
                            list.push(d);
                        }
                    }
                    // A flight carries its defensive systems on the individual craft, and a
                    // capacity-pool absorber (Shield Projection) records what it soaked as a damage
                    // entry on ITSELF. Those entries live one level down, so without this the combat
                    // log reported a fully absorbed shot against a flight as "damaged for 0". Same
                    // fighter recursion as shipManager.systems.getSystem.
                    if (system.fighter) {
                        for (var c in system.systems) {
                            var fighterSystem = system.systems[c];
                            for (var e in fighterSystem.damage) {
                                var fd = fighterSystem.damage[e];
                                if (fd.fireorderid == fire.id) {
                                    list.push(fd);
                                }
                            }
                        }
                    }
                }

                if (list.length > 0) matches.push({ ship: ship, damages: list });
            }
        }

        for (var m = 0; m < matches.length; m++) {
            var match = matches[m];
            var found = false;
            for (var f in damages) {
                var entry = damages[f];
                if (entry.ship.id == match.ship.id) {
                    found = true;
                    entry.damages = entry.damages.concat(match.damages);
                }
            }
            // copy: the caller owns what it gets back, and a shared index must stay intact for
            // the next fire order in the batch
            if (!found) damages.push({ ship: match.ship, damages: match.damages.slice() });
        }

        return damages;
    },

    isLoaded: function isLoaded(weapon) {
        return weapon.loadingtime <= weapon.turnsloaded || weapon.loadingtime <= weapon.overloadturns;
    },
    isLoadedAlternate: function isLoaded(weapon) {
        //check if ANY mode's loading time is satisfied
        var shortestLoad = 999;
        for (var currTime in weapon.loadingtimeArray) {
            if (shortestLoad > weapon.loadingtimeArray[currTime]) {
                shortestLoad = weapon.loadingtimeArray[currTime];
            }
        }
        return shortestLoad <= weapon.turnsloaded;
    },

    getFireOrderById: function getFireOrderById(id) {

        for (var i in gamedata.ships) {
            for (var a in gamedata.ships[i].fireOrders) {
                var fire = gamedata.ships[i].fireOrders[a];
                if (fire.id == id) return fire;
            }
        }

        return false;
    },

    getFiringWeapon: function getFiringWeapon(weapon, fire) {

        return weapon;
    },


    //Function called in Combat Log animation to check if a particular fireORder needs to use the full log message e.g. Reactor overlaods, Hyperspace jumps
    //shooter is optional - only the terrain-crash rule below needs it, and it is the ship the log
    //entry has already resolved from fire.shooterid.
    doShortLogText: function doShortLogText(fire, shooter) {
        const shortLogTypes = [
            //JumpVortex (JUMP_POINTS_PLAN.md Stage 6): a jump point opening or closing. Like every
            //other entry here it is a log line wearing a fire order's clothes - there is no shot,
            //no target and no damage - so the log prints its sentence alone rather than "firing 1x
            //Ramming Attack ... 1/1 shots hit" at a ship nobody shot at.
            "HyperspaceJump", "JumpFailure", "JumpVortex", "SelfDestruct", "ContainmentBreach",
            "Reactor", "Sabotage", "WreakHavoc", "Capture", "Rescue", "LimpetBore",
            "MagazineExplosion", "NoHangar", "TerrainCollision", "HalfPhase", "TranverseCrit", "Boarding",
            "InadequateHangar", "HkJamming"
        ];

        //A crash into Huge terrain (multi-hex asteroid, moon) reads like the small-asteroid
        //TerrainCollision above: the "COLLISION!" pubnotes and the damage, without the
        //"firing Ramming Attack ... 1/1 shots hit" wording, which describes the asteroid as
        //shooting at the ship it was run into. Gated on the shooter actually being terrain,
        //because the same damage class is reused for the auto-ram that follows a failed
        //skin-dance against a non-terrain Enormous unit - those orders carry no pubnotes, so
        //short text would print an empty entry.
        if (fire.damageclass === "TerrainCrash" && shooter
            && gamedata.isTerrain(shooter.shipSizeClass, shooter.userid)) {
            return true;
        }

        return shortLogTypes.includes(fire.damageclass);
    },

    /* JUMP_POINTS_PLAN.md STAGE 2 - the client half of Firing::getVortexDeclarationBlock. Returns
       null when hexpos is a legal site for a jump vortex, or the message to show the player when it
       is not. A vortex may be projected onto SHIPS, friendly or enemy; what it may not share a hex
       with is any part of a Terrain unit (which is also what a jump gate, and from Stage 3 another
       vortex, is) or an Enormous unit.

       Range is deliberately not tested here - targetHex's own weapon.range check already covers it
       and refuses out-of-range hexes the same way it does for every other weapon.

       The sweep reads gamedata.ships rather than the ready-made gamedata.blockedHexes because the
       two are not the same set: blockedHexes holds ENORMOUS units only, and the Stage 3 vortex is
       Terrain that is deliberately NOT Enormous (so it cannot ram units flying over it). */
    getVortexHexBlock: function getVortexHexBlock(ship, weapon, hexpos) {
        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!unit || unit.removed) continue;
            if (shipManager.isDestroyed(unit)) continue;

            var isTerrain = gamedata.isTerrain(unit.shipSizeClass, unit.userid);
            if (!isTerrain && !unit.Enormous) continue; //ordinary ships may sit in a vortex hex

            //Whole footprint, not just the centre hex: an irregular terrain shape carries
            //hexOffsets (rotated to its facing) and a moon carries a Huge radius. Same shape as
            //getBlockedHexes below and as RammingAttack::getTerrainOccupiedHexes on the server.
            var position = shipManager.getShipPosition(unit);
            var occupied = [position];

            if (unit.hexOffsets && unit.hexOffsets.length > 0) {
                var lastMove = shipManager.movement.getLastCommitedMove(unit) || 0;
                var facing = lastMove.facing || 0;
                for (var j in unit.hexOffsets) {
                    occupied.push(mathlib.getRotatedHex(position, unit.hexOffsets[j], facing));
                }
            } else if (unit.Huge > 0) {
                occupied.push(...mathlib.getNeighbouringHexes(position, unit.Huge));
            }

            for (var k in occupied) {
                if (occupied[k].q == hexpos.q && occupied[k].r == hexpos.r) {
                    return "A jump vortex cannot be opened in a hex occupied by <b>" + unit.name + "</b>.";
                }
            }
        }

        return null;
    },

    //Should have been replaced by gamedata.blockedHexes, but leaving just in case I've missed a call somewhere - DK 10.2.26
    getBlockedHexes: function getBlockedHexes() {
        var blockedHexes = [];

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (ship.Enormous && !shipManager.isDestroyed(ship)) { // Only enormous or Huge units block LoS.
                var position = shipManager.getShipPosition(ship);
                blockedHexes.push(position);

                if (ship.hexOffsets && ship.hexOffsets.length > 0) {
                    // Assuming ship.facing is available on client side ship object. 
                    // shipManager stores it, usually ship.facing property exists.
                    var lastMove = shipManager.movement.getLastCommitedMove(ship) || 0;
                    var facing = lastMove.facing || 0;

                    for (var j in ship.hexOffsets) {
                        var offset = ship.hexOffsets[j];

                        // Use getRotatedHex for accurate positioning
                        var newHex = mathlib.getRotatedHex(position, offset, facing);
                        blockedHexes.push(newHex);
                    }
                } else if (ship.Huge > 0) { // Occupies more than 1 hex
                    var neighbourHexes = mathlib.getNeighbouringHexes(position, ship.Huge);
                    // Add surrounding hexes directly
                    blockedHexes.push(...neighbourHexes);
                }
            }
        }

        return blockedHexes;
    },


    isHidden: function isHidden(ship) {
        if (ship.faction == "Torvalus Speculators") {
            var shadingField = shipManager.systems.getSystemByName(ship, "ShadingField");
            if (shadingField.active) {
                return true; //Shading Field active this turn, ship cannot fire.   If one Field active on fighters, all should be.
            }
        }

        if (shipManager.hasSpecialAbility(ship, "Cloaking")) {
            var cloakingDevice = shipManager.systems.getSystemByName(ship, "CloakingDevice");
            if (cloakingDevice.active) {
                //var html = "You cannot fire weapons on a turn when your Cloaking Device was active.";
                //confirm.warning(html);
                return true; //Cloaking Device active this turn, ship cannot fire.
            }
        }

        return false;
    },

    checkSkindancing: function checkSkindancing(selectedShip, ship) {
        // 0. Pre-calculate Shared Skindancing State
        let sharedSkinDancing = false;
        if (ship.skinDancing && Object.values(ship.skinDancing).includes(true)) {
            for (const [targetID, value] of Object.entries(ship.skinDancing)) {
                if (value === true && selectedShip.skinDancing && selectedShip.skinDancing[targetID] === true) {
                    sharedSkinDancing = true;
                    break;
                }
            }
        }
        // 1. Check if SHOOTER has skindanced (Failed OR Success)
        if (selectedShip.skinDancing) {
            const statusValues = Object.values(selectedShip.skinDancing);
            // Case A: Shooter FAILED -> Cannot fire at all.
            if (statusValues.includes("Failed")) {
                confirm.warning("You cannot fire weapons after an unsuccessful attempt to Skin Dance.");
                return false;
            }
            // Case B: Shooter SUCCEEDED -> Restrict targeting
            if (statusValues.includes(true)) {
                var targetCompassHeading = mathlib.getCompassHeadingOfShip(selectedShip, ship);
                var shooterFacing = shipManager.getShipHeadingAngle(selectedShip);
                var targetBearing = mathlib.getAngleBetween(shooterFacing, targetCompassHeading, true);
                // Allow firing if: Target is Host OR Target is in Side/Rear Arc OR Shared Target
                if (selectedShip.skinDancing[ship.id] !== true && (targetBearing < 60 || targetBearing > 300) && !sharedSkinDancing) {
                    return false;
                }
            }
        }
        // 2. Check if TARGET is skindancing (Protection from others)
        // If target is skindancing (and we haven't already confirmed we share it), we can't shoot.
        if (ship.skinDancing && Object.values(ship.skinDancing).includes(true)) {
            if (!sharedSkinDancing) {
                return false; //Can't target a skin-dancing ship if shooter is not skindancing same Enormous unit
            }
        }

        return true;
    },


    getAllFireOrdersForAllShipsForTurn: function getAllFireOrdersForAllShipsForTurn(turn, type) {
        var fires = [];
        var toReturn = false;

        gamedata.ships.forEach(function (ship) {
            fires = fires.concat(weaponManager.getAllFireOrders(ship));
        });

        fires = fires.filter(function (fireOrder) {
            return fireOrder.turn == turn;
        });

        if (type) {
            fires = fires.filter(function (fireOrder) {
                //attempt to show hex-targeted non-ballistics as well
                toReturn = false;
                if (fireOrder.type == type) {//Is ballistic generally.
                    toReturn = true;
                }
                //show hex-targeted direct fire as ballistics, too
                if ((!toReturn) && (type == 'ballistic') && (fireOrder.type == 'normal' || fireOrder.type == 'prefiring') && (fireOrder.targetid == -1)) {
                    toReturn = true;
                }
                //show split shot direct fire as ballistics, too
                if ((!toReturn) && (type == 'ballistic') && (fireOrder.type == 'normal' || fireOrder.type == 'prefiring') && (fireOrder.damageclass == "Sweeping")) {
                    toReturn = true;
                }
                return toReturn;
                //return fireOrder.type == type;
            });
        }

        return fires;
    },

    getAllFireOrdersForLogPrint: function getAllFireOrdersForLogPrint(ships, turn) {
        var fires = [];
        var toReturn = false;

        // Collect all fire orders from all given ships
        ships.forEach(function (ship) {
            fires = fires.concat(weaponManager.getAllFireOrdersLog(ship));
        });

        // ✅ Combined filter: only keep orders from the given turn that have been rolled
        fires = fires.filter(function (fireOrder) {
            return fireOrder.turn == turn && weaponManager.isResolvedFireOrder(fireOrder);
        });

        return fires;
    },

    getAllFireOrdersLog: function getAllFireOrdersLog(ship) {
        var fires = new Array();
        for (var i in ship.systems) {
            if (ship.flightSize > 0) { //We can't use ship.flight here, it's not variable passed by combatLog.js in data.ships
                var fighter = ship.systems[i];
                for (var a in fighter.systems) {
                    var system = fighter.systems[a];
                    var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
                    if (sysFires) fires = fires.concat(sysFires);
                }
            } else {
                var system = ship.systems[i];
                var sysFires = weaponManager.getAllFireOrdersFromSystemLog(system);
                if (sysFires) fires = fires.concat(sysFires);
            }
        }
        return fires;
    },

    getAllFireOrdersFromSystemLog: function getAllFireOrdersFromSystemLog(system) {
        if (!system.fireOrders) return;

        var fires = system.fireOrders;

        return fires;
    },

    hasHexWeaponsSelected: function hasHexWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });
    },


};

// Hover tooltip for hit-chance breakdown in targetingShipTooltip.
// Bound locally on the .targeting div by targetingShipTooltip itself, because
// the parent ShipTooltip element calls stopPropagation() on mouseover/out,
// which would prevent any document-level delegation from firing.
function _showHitChanceTooltip(e) {
    var tooltip = $('#custom-hit-chance-tooltip');
    if (!tooltip.length) {
        tooltip = $('<div id="custom-hit-chance-tooltip" class="custom-hit-chance-tooltip"></div>').appendTo('body');
    }
    var raw = String($(this).data('tooltip') || '');
    var lines = raw.split('\n');
    var $header = $('<div class="hctt-header"></div>').text(lines[0] || '');
    tooltip.empty().append($header);
    for (var i = 1; i < lines.length; i++) {
        tooltip.append($('<div class="hctt-row"></div>').text(lines[i]));
    }
    var rect = this.getBoundingClientRect();
    var topPos = rect.top - tooltip.outerHeight() - 5;
    if (topPos < 0) topPos = rect.bottom + 5;
    var leftPos = rect.left + rect.width / 2 - tooltip.outerWidth() / 2;
    if (leftPos < 0) leftPos = 5;
    if (leftPos + tooltip.outerWidth() > window.innerWidth) leftPos = window.innerWidth - tooltip.outerWidth() - 5;
    tooltip.css({ top: topPos + 'px', left: leftPos + 'px' }).show();
}
function _hideHitChanceTooltip() {
    $('#custom-hit-chance-tooltip').hide();
}
