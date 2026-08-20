"use strict";

var Plasma = function Plasma(json, ship) {
    Weapon.call(this, json, ship);
};
Plasma.prototype = Object.create(Weapon.prototype);
Plasma.prototype.constructor = Plasma;

var PlasmaAccelerator = function PlasmaAccelerator(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaAccelerator.prototype = Object.create(Plasma.prototype);
PlasmaAccelerator.prototype.constructor = PlasmaAccelerator;

var MagGun = function MagGun(json, ship) {
    Plasma.call(this, json, ship);
};
MagGun.prototype = Object.create(Plasma.prototype);
MagGun.prototype.constructor = MagGun;

var HeavyPlasma = function HeavyPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
HeavyPlasma.prototype = Object.create(Plasma.prototype);
HeavyPlasma.prototype.constructor = HeavyPlasma;

var MediumPlasma = function MediumPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
MediumPlasma.prototype = Object.create(Plasma.prototype);
MediumPlasma.prototype.constructor = MediumPlasma;

var LightPlasma = function LightPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasma.prototype = Object.create(Plasma.prototype);
LightPlasma.prototype.constructor = LightPlasma;

var PlasmaStream = function PlasmaStream(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaStream.prototype = Object.create(Plasma.prototype);
PlasmaStream.prototype.constructor = PlasmaStream;

var PlasmaTorch = function PlasmaTorch(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaTorch.prototype = Object.create(Plasma.prototype);
PlasmaTorch.prototype.constructor = PlasmaTorch;

var PairedPlasmaBlaster = function PairedPlasmaBlaster(json, ship) {
    Plasma.call(this, json, ship);
};
PairedPlasmaBlaster.prototype = Object.create(Plasma.prototype);
PairedPlasmaBlaster.prototype.constructor = PairedPlasmaBlaster;

var PlasmaGun = function PlasmaGun(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaGun.prototype = Object.create(Plasma.prototype);
PlasmaGun.prototype.constructor = PlasmaGun;

var RogolonLtPlasmaGun = function RogolonLtPlasmaGun(json, ship) {
    Plasma.call(this, json, ship);
};
RogolonLtPlasmaGun.prototype = Object.create(Plasma.prototype);
RogolonLtPlasmaGun.prototype.constructor = RogolonLtPlasmaGun;

var RogolonLtPlasmaCannon = function RogolonLtPlasmaCannon(json, ship) {
    Plasma.call(this, json, ship);
};
RogolonLtPlasmaCannon.prototype = Object.create(Plasma.prototype);
RogolonLtPlasmaCannon.prototype.constructor = RogolonLtPlasmaCannon;

var LightPlasmaAccelerator = function LightPlasmaAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
LightPlasmaAccelerator.prototype = Object.create(Weapon.prototype);
LightPlasmaAccelerator.prototype.constructor = LightPlasmaAccelerator;

var HeavyPlasmaBolter = function HeavyPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
HeavyPlasmaBolter.prototype = Object.create(Plasma.prototype);
HeavyPlasmaBolter.prototype.constructor = HeavyPlasmaBolter;

var MediumPlasmaBolter = function MediumPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
MediumPlasmaBolter.prototype = Object.create(Plasma.prototype);
MediumPlasmaBolter.prototype.constructor = MediumPlasmaBolter;

var LightPlasmaBolter = function LightPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasmaBolter.prototype = Object.create(Plasma.prototype);
LightPlasmaBolter.prototype.constructor = LightPlasmaBolter;

var LightPlasmaBolterFighter = function LightPlasmaBolterFighter(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasmaBolterFighter.prototype = Object.create(Plasma.prototype);
LightPlasmaBolterFighter.prototype.constructor = LightPlasmaBolterFighter;

var DualPlasmaCannon = function DualPlasmaCannon(json, ship) {
    Plasma.call(this, json, ship);
};
DualPlasmaCannon.prototype = Object.create(Plasma.prototype);
DualPlasmaCannon.prototype.constructor = DualPlasmaCannon;

var MegaPlasma = function MegaPlasma(json, ship) {
    Weapon.call(this, json, ship);
};
MegaPlasma.prototype = Object.create(Weapon.prototype);
MegaPlasma.prototype.constructor = MegaPlasma;

var PlasmaProjector = function PlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaProjector.prototype = Object.create(Weapon.prototype);
PlasmaProjector.prototype.constructor = PlasmaProjector;

var PlasmaBlast = function PlasmaBlast(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaBlast.prototype = Object.create(Weapon.prototype);
PlasmaBlast.prototype.constructor = PlasmaBlast;

var Fuser = function Fuser(json, ship) {
    Weapon.call(this, json, ship);
};
Fuser.prototype = Object.create(Weapon.prototype);
Fuser.prototype.constructor = Fuser;

var RangedFuser = function RangedFuser(json, ship) {
    Weapon.call(this, json, ship);
};
RangedFuser.prototype = Object.create(Weapon.prototype);
RangedFuser.prototype.constructor = RangedFuser;

var DualPlasmaStream = function DualPlasmaStream(json, ship) {
    Weapon.call(this, json, ship);
};
DualPlasmaStream.prototype = Object.create(Weapon.prototype);
DualPlasmaStream.prototype.constructor = DualPlasmaStream;


var PakmaraPlasmaWeb = function  PakmaraPlasmaWeb(json, ship) {
    Weapon.call(this, json, ship);
};
PakmaraPlasmaWeb.prototype = Object.create(Weapon.prototype);
PakmaraPlasmaWeb.prototype.constructor =  PakmaraPlasmaWeb;

PakmaraPlasmaWeb.prototype.initializationUpdate = function () {
    if(this.firingMode == 2){
        const rangeCrit = shipManager.criticals.countCriticalOnTurn(this, "ReducedRangeValue", gamedata.turn);
        if(rangeCrit > 0) this.range = 1;
        this.data['Range'] = this.range;   
    }    
	return this;
}	

PakmaraPlasmaWeb.prototype.hasMaxBoost = function () {
	return this.maxBoostLevel;
};

PakmaraPlasmaWeb.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

// GTS_Triad

var HyperplasmaCutter = function HyperplasmaCutter(json, ship) {
    Weapon.call(this, json, ship);
};
HyperplasmaCutter.prototype = Object.create(Weapon.prototype);
HyperplasmaCutter.prototype.constructor = HyperplasmaCutter;

HyperplasmaCutter.prototype.getRemainingDice = function () {
    //Calculate remaining dice fresh from actual fire orders.
    //Uses this.maxDice (sent via stripForJson) rather than hardcoded 10 so
    //DiceLost criticals correctly reduce the available pool.
    var max = (this.maxDice !== undefined) ? this.maxDice : 10;
    var used = 0;
    for (var i = 0; i < this.fireOrders.length; i++) {
        var fo = this.fireOrders[i];
        if (fo.type === 'normal' || fo.type === 'selfIntercept') {
            used += fo.shots || 0;
        }
    }
    return Math.max(0, max - used);
};

HyperplasmaCutter.prototype.initializationUpdate = function () {
    //Prevent engine's setSelfIntercept auto-assignment from consuming dice.
    //It creates one selfIntercept order per weapon using defaultShots - setting
    //this to 0 means those auto-orders use no dice from the pool.
    this.defaultShots = 0;
    //maxDice is sent from PHP via stripForJson. If the base Weapon constructor
    //doesn't copy it automatically from JSON, read it explicitly here so the
    //firing dialog always sees the correct reduced pool after DiceLost criticals.
    if (this.maxDice === undefined || this.maxDice === null) this.maxDice = 10;
    //Show sustain target when active (sustainedTarget sent via stripForJson)
    if (this.sustainedTarget && Object.keys(this.sustainedTarget).length > 0) {
        var targetId = Object.keys(this.sustainedTarget)[0];
        var target = gamedata.getShip(targetId);
        this.data["Current Target"] = target ? target.name : "Ship #" + targetId;
    } else {
        delete this.data["Current Target"];
    }

    if (gamedata.gamephase == 3 && this.getRemainingDice() > 0 || this.fireOrders.length > 0) {
        this.data["Dice Remaining"] = this.getRemainingDice();
    } else {
        delete this.data["Dice Remaining"];
    }

    //Override the display string the engine would derive from turnsloaded/normalload.
    //Without this, normalload=2 (needed for intercept eligibility) causes the icon
    //to show a 2-turn charging cycle. Lightning Cannon uses the same fix.
    if (this.overloadshots > 0) {
        this.outputDisplay = "S" + this.overloadshots;
    } else {
        this.outputDisplay = "1/1";
    }

    //COSMETIC: if this cutter has no fire orders of its own but a sibling cutter does,
    //mark this cutter's icon as fired so the player can see it was involved in the shot.
    //Check only during firing phase (phase 3) and results phase (phase 4/5).
    if (gamedata.gamephase >= 3) {
        var hasSiblingOrder = false;
        if (!weaponManager.hasFiringOrder(this.ship, this)) {
            for (var i in this.ship.systems) {
                var sys = this.ship.systems[i];
                if (sys === this || !(sys instanceof HyperplasmaCutter) || sys.destroyed) continue;
                if (weaponManager.hasFiringOrder(this.ship, sys)) {
                    hasSiblingOrder = true;
                    break;
                }
            }
            if (hasSiblingOrder) {
                //Push a minimal display-only fire order so hasFiringOrder returns true
                //for this cutter, making its icon show as fired.
                var displayOrder = {
                    id: this.ship.id + "_" + this.id + "_display",
                    type: "normal",
                    shooterid: this.ship.id,
                    targetid: -1,
                    weaponid: this.id,
                    turn: gamedata.turn,
                    firingMode: this.firingMode,
                    shots: 0,
                    rolled: false,
                    addToDB: false
                };
                //Only add if not already present
                var alreadyAdded = false;
                for (var j = 0; j < this.fireOrders.length; j++) {
                    if (this.fireOrders[j].id === displayOrder.id) { alreadyAdded = true; break; }
                }
                if (!alreadyAdded) this.fireOrders.push(displayOrder);
            }
        }
    }

    return this;
};

/* doMultipleFireOrders handles both Normal and Sustained modes.
 *
 * NORMAL MODE (Lightning Cannon pattern):
 * When multiple cutters are selected together, a single combined dialog lets the
 * player allocate dice from ALL selected cutters to this one target in one go.
 * The dialog shows one row per selected cutter. The result produces one fire order
 * per cutter (each carrying its allocated dice count), and PHP combines them
 * server-side via the isCombined flag - exactly like Lightning Cannon prongs.
 *
 * When only one cutter is selected, a simple single-cutter dialog opens instead.
 *
 * SUSTAINED MODE (Heavy Laser pattern):
 * All cutters must participate. Handled by declareSustainedShot.
 */
HyperplasmaCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {
    if (this.handlingInput) { this.handlingInput = false; return []; }

    //Check ALL HyperplasmaCutters on this ship for an active sustained shot this turn.
    //If any cutter has committed a sustained shot, NO cutter can fire again in any mode.
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (!(sys instanceof HyperplasmaCutter) || sys.destroyed) continue;
        for (var j = 0; j < sys.fireOrders.length; j++) {
            var fo = sys.fireOrders[j];
            if (fo.turn == gamedata.turn && fo.notes && fo.notes.indexOf('HPC-Sustained') >= 0) {
                if (this.firingMode != 2 || sys !== this) {
                    confirm.warning("A sustained shot is already committed. No further fire orders can be declared this turn.");
                    return [];
                }
            }
        }
    }

    if (this.firingMode == 2) {
        this.declareSustainedShot(shooter, target);
        return [];
    }

    //Collect all currently-selected HyperplasmaCutters on this ship.
    //This cutter is the first to run; siblings haven't been unselected yet.
    var selectedCutters = [];
    for (var i = 0; i < gamedata.selectedSystems.length; i++) {
        var sys = gamedata.selectedSystems[i];
        if (sys instanceof HyperplasmaCutter && sys.ship && sys.ship.id === shooter.id) {
            selectedCutters.push(sys);
        }
    }
    if (selectedCutters.indexOf(this) === -1) selectedCutters.push(this);

    //Mark all siblings as handlingInput so their own doMultipleFireOrders calls bail out.
    //This cutter alone drives the combined dialog for all of them.
    for (var i = 0; i < selectedCutters.length; i++) {
        if (selectedCutters[i] !== this) {
            selectedCutters[i].handlingInput = true;
        }
    }

    var calledid = -1;
    if (system && target.flight) {
        while (system.parentId > 0) system = shipManager.systems.getSystem(target, system.parentId);
        calledid = system.id;
    }

    var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
    if (chance < 1) return [];

    //Build one input row per selected cutter, defaulting to all remaining dice on each.
    var inputs = [];
    for (var i = 0; i < selectedCutters.length; i++) {
        var cutter = selectedCutters[i];
        var cutterDice = cutter.getRemainingDice();
        if (cutterDice <= 0) continue;
        inputs.push({
            id: cutter.id,
            label: cutter.displayName + " (" + cutterDice + " dice)",
            max: cutterDice,
            value: cutterDice
        });
    }

    if (inputs.length === 0) return [];

    var self = this;
    var dialogTitle = selectedCutters.length > 1
        ? "Allocate d10s from each Hyperplasma Cutter to this target"
        : "Allocate d10s for this shot (" + this.getRemainingDice() + " remaining)";

    confirm.askForMultipleValues(dialogTitle, inputs, function (results) {
        var weapons = [];

        for (var i = 0; i < selectedCutters.length; i++) {
            var cutter = selectedCutters[i];
            var dice = parseInt(results[cutter.id], 10);
            if (isNaN(dice) || dice < 1) continue;
            if (dice > cutter.getRemainingDice()) dice = cutter.getRemainingDice();

            var freshChance = window.weaponManager.calculateHitChange(shooter, target, cutter, calledid).hitChance;
            var fireid = shooter.id + "_" + cutter.id + "_" + (cutter.fireOrders.length + 1);
            var fire = {
                id: fireid,
                type: 'normal',
                shooterid: shooter.id,
                targetid: target.id,
                weaponid: cutter.id,
                calledid: calledid,
                turn: gamedata.turn,
                firingMode: cutter.firingMode,
                shots: dice,
                x: "null",
                y: "null",
                damageclass: 'Sweeping',
                chance: freshChance,
                hitmod: 0
            };
            cutter.fireOrders.push(fire);
            weapons.push(cutter);

            webglScene.customEvent('SystemDataChanged', { ship: shooter, system: cutter });
            if (cutter.checkFinished()) weaponManager.unSelectWeapon(shooter, cutter);
        }

        if (weapons.length > 0) {
            webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: weapons });
        }
    });

    return [];
};

/* Sustained mode: all cutters, all dice, one target.
 * Routes through the lowest-id cutter as primary so the engine's per-weapon
 * overload state is always anchored to the same physical weapon.
 * Sibling cutters are unselected and marked handlingInput - they get no fire order
 * of their own (the primary carries the full pool). PHP handles marking them as
 * isCombined during calculateHitBase so they show as having fired. */
HyperplasmaCutter.prototype.declareSustainedShot = function (shooter, target) {
    var all = [];
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (sys instanceof HyperplasmaCutter && !sys.destroyed) all.push(sys);
    }

    var primary = all[0];
    for (var i = 1; i < all.length; i++) {
        if (parseInt(all[i].id, 10) < parseInt(primary.id, 10)) primary = all[i];
    }

    if (primary.overloadturns > 0 && primary.overloadshots <= 0) {
        confirm.warning("The Hyperplasma Cutter array is cooling down and cannot fire this turn.");
        return;
    }

    var poolSize = 10 * all.length;
    var calledid = -1;
    var chance = window.weaponManager.calculateHitChange(shooter, target, primary, calledid).hitChance;
    if (chance < 1) return;

    for (var i in all) weaponManager.removeFiringOrder(shooter, all[i]);

    //Mark siblings as handled BEFORE pushing the fire order so when their own
    //doMultipleFireOrders runs, handlingInput is already true and they bail out
    //immediately without showing the dice dialog.
    for (var i = 0; i < all.length; i++) {
        if (all[i] !== primary) {
            all[i].handlingInput = true;
            weaponManager.unSelectWeapon(shooter, all[i]);
        }
    }

    var fireid = shooter.id + "_" + primary.id + "_" + (primary.fireOrders.length + 1);
    var fire = {
        id: fireid,
        type: 'normal',
        shooterid: shooter.id,
        targetid: target.id,
        weaponid: primary.id,
        calledid: calledid,
        turn: gamedata.turn,
        firingMode: 1,
        shots: poolSize,
        x: "null",
        y: "null",
        damageclass: 'Sweeping',
        chance: chance,
        hitmod: 0,
        notes: "HPC-Sustained"
    };
    primary.fireOrders.push(fire);

    webglScene.customEvent('SystemDataChanged', { ship: shooter, system: primary });
    weaponManager.unSelectWeapon(shooter, primary);
    webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [primary] });
};

HyperplasmaCutter.prototype.checkSelfInterceptSystem = function () {
    if (this.sustainedTarget && Object.keys(this.sustainedTarget).length > 0) return false;
    if (this.firingMode == 2) return false;
    return this.getRemainingDice() > 0;
};

/* OUT OF SCOPE for manual interception (user decision, 2026-08-19) - permanently, not deferred.
   This cutter's intercept rating is 1 point PER d10 allocated out of a dice pool the player splits
   in its own dialog, which is a different mechanic from "commit these guns to that shot" and is
   already well served by the self-intercept flow. It keeps that flow and the automation; the
   ballistics tooltip will not offer it. Unlike the Molecular Slicer, this is not a Stage 7 item. 
   Disabled for now - DK */
//HyperplasmaCutter.prototype.usesCustomInterceptAllocation = true;

HyperplasmaCutter.prototype.doMultipleSelfIntercept = function (ship) {
    //Each cutter handles its own intercept group independently.
    //The player selects whichever cutter(s) they want and clicks intercept -
    //each selected cutter opens its own dialog for its own dice allocation.
    //This cutter IS the group - no routing to a primary needed.

    //Bail out if this cutter already has a real intercept group assigned.
    for (var k = 0; k < this.fireOrders.length; k++) {
        if (this.fireOrders[k].type === 'selfIntercept' &&
            this.fireOrders[k].shots > 0) {
            return; //already declared for this cutter
        }
    }

    //Show this cutter's own available dice.
    var available = this.getRemainingDice();
    if (available === 0) return;

    var targetCutter = this;

    var inputs = [{
        id: targetCutter.id,
        label: targetCutter.displayName + ' (' + available + ' dice available, each = 5% reduction)',
        max: available,
        value: available
    }];

    confirm.askForMultipleValues(
        'Allocate d10s for ' + targetCutter.displayName + ' intercept group',
        inputs,
        function (results) {
            var allocated = parseInt(results[targetCutter.id], 10);
            if (isNaN(allocated) || allocated < 1) return;
            if (allocated > available) allocated = available;

            for (var d = 0; d < allocated; d++) {
                var fireid = ship.id + '_' + targetCutter.id + '_' + (targetCutter.fireOrders.length + 1);
                var fire = {
                    id: fireid,
                    type: 'selfIntercept',
                    shooterid: ship.id,
                    targetid: ship.id,
                    weaponid: targetCutter.id,
                    calledid: -1,
                    turn: gamedata.turn,
                    firingMode: targetCutter.firingMode,
                    shots: 1,
                    x: 'null',
                    y: 'null',
                    addToDB: true,
                    damageclass: targetCutter.data['Weapon type'].toLowerCase()
                };
                targetCutter.fireOrders.unshift(fire);
            }
            webglScene.customEvent('SystemDataChanged', { ship: ship, system: targetCutter });
        }
    );
};

HyperplasmaCutter.prototype.checkFinished = function () {
    if (this.firingMode == 2) return true;
    return this.getRemainingDice() <= 0;
};