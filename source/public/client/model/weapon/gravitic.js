"use strict";

var Gravitic = function Gravitic(json, ship) {
    Weapon.call(this, json, ship);
};
Gravitic.prototype = Object.create(Weapon.prototype);
Gravitic.prototype.constructor = Gravitic;

var GravitonPulsar = function GravitonPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
GravitonPulsar.prototype = Object.create(Pulse.prototype);
GravitonPulsar.prototype.constructor = GravitonPulsar;

GravitonPulsar.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.   

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GravitonPulsar.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GravitonPulsar.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GravitonPulsar.prototype.hasMaxBoost = function () {
    return true;
};

GravitonPulsar.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GraviticBolt = function GraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticBolt.prototype = Object.create(Gravitic.prototype);
GraviticBolt.prototype.constructor = GraviticBolt;

GraviticBolt.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed!
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GraviticBolt.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GraviticBolt.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GraviticBolt.prototype.hasMaxBoost = function () {
    return true;
};

GraviticBolt.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GravitonBeam = function GravitonBeam(json, ship) {
    Weapon.call(this, json, ship);
};
GravitonBeam.prototype = Object.create(Weapon.prototype);
GravitonBeam.prototype.constructor = GravitonBeam;

var LightGravitonBeam = function LightGravitonBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGravitonBeam.prototype = Object.create(Gravitic.prototype);
LightGravitonBeam.prototype.constructor = LightGravitonBeam;

var GraviticCannon = function GraviticCannon(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCannon.prototype = Object.create(Gravitic.prototype);
GraviticCannon.prototype.constructor = GraviticCannon;

var GraviticShifter = function GraviticShifter(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticShifter.prototype = Object.create(Gravitic.prototype);
GraviticShifter.prototype.constructor = GraviticShifter;

GraviticShifter.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
    var mod = 0;

    if (target.gravitic || target.factionAge >= 3) mod = -3; //-15% to hit gravitic and/or Ancient targets.    

    /* //Removed since OEW lock on allies enabled - DK 17.1.26    
    if(shooter.team == target.team){
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);        
        var rangePenalty = weaponManager.calculateRangePenalty(distance, this);
        mod += rangePenalty; //refund range penalty for friendly units since OEW lock on allies not possible.
    }
    */

    return mod;
};

var GravityNet = function GravityNet(json, ship) {
    Gravitic.call(this, json, ship);
};
GravityNet.prototype = Object.create(Gravitic.prototype);
GravityNet.prototype.constructor = GravityNet;

GravityNet.prototype.calculateSpecialHitChanceMod = function (shooter, target) {
    var mod = 0;

    if (target.gravitic || target.factionAge >= 3) mod = -3; //-15% to hit gravitic and/or Ancient targets.    

    /* //Removed since OEW lock on allies enabled - DK 17.1.26
    if(shooter.team == target.team){
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);        
        var rangePenalty = weaponManager.calculateRangePenalty(distance, this);
        mod += rangePenalty; //refund range penalty for friendly units since OEW lock on allies not possible.
    } 
    */

    return mod;
};

GravityNet.prototype.initializationUpdate = function () {
    if (gamedata.gamephase == 1 || gamedata.gamephase == 5) { //update weapon data field to show this gravity net's max movement distance or return to TBD
        this.data["Move Distance"] = this.moveDistance;
    }
    if (this.fireOrders.length > 0) {
        this.hextarget = true;
        this.ignoresLoS = false;
        if (this.fireOrders.length == 1) {
            if (!weaponManager.isSelectedWeapon(this)) {
                webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
            } else if (weaponManager.isSelectedWeapon(this) && this.target) {
                webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });//Remove any old sprites to prevent duplication.
                webglScene.customEvent("ShowTargetedHexagonInArc", { shooter: this.ship, target: this.target, system: this });
            }
        }
    } else {
        this.hextarget = false;
        this.ignoresLoS = false;
        if (this.target) {
            webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
        }
    }

    return this;
};

GravityNet.prototype.doMultipleFireOrders = function (shooter, target, system) {
    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 0) {
        return;
    }

    if (target.mine) { //Can't move mines.
        return;
    } 
    
    if (target.shipSizeClass > 3) { //Can't mvoe enormous or larger units.   
        return;
    }        

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //No called shots.     

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        if (chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'prefiring',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'gravitic',
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };
        this.target = target; //store current target to this gravity net object.       
        fireOrdersArray.push(fire); // Store each fire order

        webglScene.customEvent("ShowTargetedHexagonInArc", { shooter: shooter, target: target, system: this });
        this.hextarget = true; //switch gravNet from shipTarget mode to hexTarget mode.        
    }

    return fireOrdersArray; // Return all fire orders
};

GravityNet.prototype.doMultipleHexFireOrders = function (shooter, hexpos) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 1) {
        return;
    }
    var targetMoveHexValid = this.validateTargetMoveHex(hexpos, this.moveDistance);

    var fireOrdersArray = []; // Store multiple fire orders

    if (targetMoveHexValid) {
        for (var s = 0; s < shotsOnTarget; s++) {
            var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
            // Capture the target ship ID from the first fire order so BallisticIconContainer 
            // can draw the line starting from the target ship rather than the firing ship.
            var gravNetTargetId = (this.fireOrders.length > 0) ? this.fireOrders[0].targetid : -1;
            var fire = {
                id: fireid,
                type: 'prefiring',
                shooterid: shooter.id,
                targetid: -1,
                weaponid: this.id,
                calledid: -1,
                turn: gamedata.turn,
                firingMode: this.firingMode,
                shots: this.defaultShots,
                x: hexpos.q,
                y: hexpos.r,
                damageclass: 'gravNetMoveHex',
                notes: "split",
                gravNetTargetId: gravNetTargetId // ID of ship being moved, used as line start point
            };
            fireOrdersArray.push(fire);
        }
        webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
    }
    return fireOrdersArray; // Return all fire orders
};


GravityNet.prototype.validateTargetMoveHex = function (hexpos, maxmoverange) { //function to validate desired target movement hex, will check LOS from target ship to move hex and range and make sure no collisions occur.

    //get gravNetTargetHex to check range and LOS for gravNetTargetMovementHex
    //Target of grav net which will be used as shooter for grav net target hex.
    var valid = false; //default to false
    var gravNetTargetFireOrder = this.fireOrders[0];//get fireorder of grav net firing ship (So we can use it's hex as fireing hex), this should always be the first fire order
    if (gravNetTargetFireOrder) {	// check that the grav net firing ship set a fire order
        var targetShip = gamedata.getShip(gravNetTargetFireOrder.targetid);
        var targetShipHex = shipManager.getShipPosition(targetShip);
        var targetMoveHex = hexpos;
        var dist = targetShipHex.distanceTo(targetMoveHex);
        if (dist <= maxmoverange) {
            //var blockedHexes = weaponManager.getBlockedHexes();
            var blockedHexes = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.             
            var loSBlocked = mathlib.isLoSBlocked(targetShipHex, targetMoveHex, blockedHexes);
            if (!loSBlocked && !blockedHexes.some(blocked => blocked.q === targetMoveHex.q && blocked.r === targetMoveHex.r)) {//make sure hexpos is a not a blocked hex and LOS is not blocked      
                valid = true;
            }
        }
    }

    return valid;
};

GravityNet.prototype.checkFinished = function () {
    if (this.fireOrders.length > 1) return true;
    return false;
};

var LightGraviticBolt = function LightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGraviticBolt.prototype = Object.create(Gravitic.prototype);
LightGraviticBolt.prototype.constructor = LightGraviticBolt;

var UltraLightGraviticBolt = function UltraLightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
UltraLightGraviticBolt.prototype = Object.create(Gravitic.prototype);
UltraLightGraviticBolt.prototype.constructor = UltraLightGraviticBolt;


var GraviticLance = function (json, ship) {
    Weapon.call(this, json, ship);
};
GraviticLance.prototype = Object.create(Weapon.prototype);
GraviticLance.prototype.constructor = GraviticLance;

GraviticLance.prototype.initializationUpdate = function () {
    if (this.firingMode == 3) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }

    var ship = this.ship;
    if (gamedata.gamephase !== -2 && shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0) {
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
        this.data["Current Target"] = target.name;
    } else {
        delete this.data["Current Target"];
    }

    return this;
};

GraviticLance.prototype.doMultipleFireOrders = function (shooter, target, system) {

    /*var shotsOnTarget = this.guns; // Default guns initially.  We never want teh palyer to miss firing a shot for such a powerful weapon (and it can't intercept).
    if (this.fireOrders.length == 2) { // Two shots have been locked in, remove the first.
        this.fireOrders.splice(0, 1); // Remove the first fire order.
        shotsOnTarget--; //Reduce guns to 1, the one currently being retargeted!
    }else if(this.fireOrders.length == 1){
        shotsOnTarget--; //Reduce guns to 1, one shot is already locked and we don't want to target 3 :)        
    }
    */

    if (this.firingMode == 3 && this.fireOrders.length > 1) return;

    var shotsOnTarget = 1;
    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Grav Beams are raking, can never called shot.

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        if (chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Sweeping',
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };

        fireOrdersArray.push(fire); // Store each fire order
    }

    return fireOrdersArray; // Return all fire orders
};

GraviticLance.prototype.checkFinished = function () {
    if (this.firingMode == 3 && this.fireOrders.length > 1) return true;
    return false;
};

var GraviticCutter = function GraviticCutter(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCutter.prototype = Object.create(Gravitic.prototype);
GraviticCutter.prototype.constructor = GraviticCutter;

GraviticCutter.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    this.data["Weapon type"] = "Raking";
    this.data["Damage type"] = "Standard";

    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '10-28';
            break;
        case 1:
            this.data["Damage"] = '13-40';
            break;
        default:
            this.data["Damage"] = '10-28';
            break;
    }

    if (window.weaponManager.isLoaded(this)) {
        this.loadingtime = 2 + shipManager.power.getBoost(this);
        this.turnsloaded = 2 + shipManager.power.getBoost(this);
        this.normalload = 2 + shipManager.power.getBoost(this);
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    return this;
};

GraviticCutter.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};
GraviticCutter.prototype.hasMaxBoost = function () {
    return true;
};
GraviticCutter.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var HypergravitonBeam = function HypergravitonBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
HypergravitonBeam.prototype = Object.create(Gravitic.prototype);
HypergravitonBeam.prototype.constructor = HypergravitonBeam;

HypergravitonBeam.prototype.initBoostableInfo = function () {
    // Needed because it can change during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {

    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }
    return this;
};

HypergravitonBeam.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

var HypergravitonBlaster = function HypergravitonBlaster(json, ship) {
    Gravitic.call(this, json, ship);
    this.hasSpecialTargeting = true; //route target selection through doSpecialTargeting (transfer-target UI hook)
};
HypergravitonBlaster.prototype = Object.create(Gravitic.prototype);
HypergravitonBlaster.prototype.constructor = HypergravitonBlaster;

/* STAGE 4 - HBlasterList transfer-target ordering window.
 * Declares ONE plain normal/raking fire order against the chosen target (so it behaves
 * exactly like an ordinary Blaster shot to the rest of the client), but encodes the
 * INITIAL target + the ordered transfer-target list into the notes field as:
 *   HBT|<initialId>:<initialFlag>|<transferId>:<flag>|<transferId>:<flag>|...
 * The FIRST entry is always the initial target (so its structure-loss flag reaches the
 * server via getTransferFlagForShip); the server's getNextTransferTarget skips the fire
 * order's own targetid so the initial target is never consumed as a transfer hop. The
 * remaining entries are the player-ordered transfer queue. flag = transferOnStructure.
 *
 * The list + per-ship structure flags come from the confirm.hBlasterTransferList window.
 * The server-side beforeFiringOrderResolution parses + clears this notes payload, and is
 * authoritative for the per-hop range/arc geometry (isLegalTransferTarget, Stage 5b).
 *
 * Reached via the hasSpecialTargeting divert in weaponManager.targetShip() (first click)
 * or via reopenSpecialTargeting() (clicking the system icon when an order already exists,
 * to edit the list). Either way we replace any existing order rather than stacking one.
 *
 * The window is asynchronous, so doSpecialTargeting returns false (no synchronous
 * declaration); the window callback declares the order via declareTransferShot and then
 * replicates the post-declaration UI refresh the synchronous targetShip() path would do.
 */
/* Returns true if a fire order's notes encode a NON-EMPTY transfer queue (i.e. the order
 * actually carries transfer hops beyond the initial target). "HBT|<initial>:<flag>" alone
 * (initial target only, no hops) is NOT a transfer for slot-ownership purposes. */
HypergravitonBlaster.isTransferOrder = function (fire) {
    if (!fire || !fire.notes || fire.notes.indexOf('HBT') !== 0) return false;
    var segs = fire.notes.split('|');
    //segs[0]='HBT', segs[1]=initial target; a transfer queue needs at least segs[2].
    for (var i = 2; i < segs.length; i++) {
        if (segs[i] !== '') return true;
    }
    return false;
};

/* The OTHER Hypergraviton Blaster on $shooter (not $this) that currently holds the
 * transfer "slot" (a fire order with a non-empty transfer queue), or null. Mirrors the
 * server rule that only one Blaster per ship may transfer per turn. */
HypergravitonBlaster.prototype.getOtherTransferringBlaster = function (shooter) {
    if (!shooter || !shooter.systems) return null;
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (!sys || sys === this) continue;
        if (!(sys instanceof HypergravitonBlaster)) continue;
        var fire = weaponManager.getFiringOrder(shooter, sys);
        if (HypergravitonBlaster.isTransferOrder(fire)) return sys;
    }
    return null;
};

/* Scenario A (multiple Blasters selected at once, then target a ship): only ONE Blaster
 * may carry a transfer list, so only the BEST-loaded selected Blaster gets the window;
 * the rest fire normally. Returns true if $this is that best-loaded selected Blaster.
 * Only Blasters that can actually hit $target (in arc + range) are considered, so a more-
 * loaded Blaster that can't bear on this target doesn't steal the slot from one that can.
 * Priority: highest turnsloaded wins (a 2-turn-loaded beam beats a 1-turn one); ties
 * break on system id (stable/deterministic). */
HypergravitonBlaster.prototype.isPreferredSelectedBlaster = function (shooter, target) {
    var selected = gamedata.selectedSystems || [];
    var best = null;
    for (var i = 0; i < selected.length; i++) {
        var w = selected[i];
        if (!(w instanceof HypergravitonBlaster)) continue;
        if (w.ship && shooter && w.ship.id !== shooter.id) continue; //only this ship's selected Blasters
        //Only Blasters that can fire on this target compete for the transfer slot.
        if (target && !(weaponManager.isOnWeaponArc(shooter, target, w) && weaponManager.checkIsInRange(shooter, target, w))) continue;
        if (best === null) { best = w; continue; }
        var wLoad = parseInt(w.turnsloaded, 10) || 0;
        var bLoad = parseInt(best.turnsloaded, 10) || 0;
        if (wLoad > bLoad || (wLoad === bLoad && parseInt(w.id, 10) < parseInt(best.id, 10))) {
            best = w;
        }
    }
    return best === null || best === this;
};

HypergravitonBlaster.prototype.doSpecialTargeting = function (shooter, target, system) {
    var calledid = -1; //raking weapon, no called shots
    var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
    if (chance < 1) return false; //no declaration made

    //Scenario A: several Blasters selected together. Only the best-loaded one opens the
    //transfer window; the others fire a plain shot. (Also guards against stacking multiple
    //modals: if a transfer window is already open this targeting action, fire normally.)
    var aWindowIsOpen = (typeof $ === 'function') && $('.hBlasterTransfer').length > 0;
    if (aWindowIsOpen || !this.isPreferredSelectedBlaster(shooter, target)) {
        this.declareTransferShot(shooter, target, chance, [], false);
        return false;
    }

    //Scenario B: another Blaster on this ship already holds the transfer slot. Open the
    //window in "locked" mode (banner + Confirm List disabled — fire-normally/cancel only).
    var holder = this.getOtherTransferringBlaster(shooter);
    var lockInfo = holder ? { locked: true, holderName: this.blasterDisplayName(shooter, holder) } : null;

    //Scenario A disambiguation: when MORE THAN ONE Blaster on this ship is selected,
    //show this window's weapon id in the header so the player knows which Blaster it's for.
    var opts = (this.countSelectedBlasters(shooter) > 1) ? { showWeaponId: true } : null;

    this.openTransferWindow(shooter, target, chance, null, lockInfo, opts);
    return false; //declaration (if any) happens asynchronously inside the callback
};

/* Count of HypergravitonBlasters on $shooter currently in gamedata.selectedSystems. */
HypergravitonBlaster.prototype.countSelectedBlasters = function (shooter) {
    var selected = gamedata.selectedSystems || [];
    var n = 0;
    for (var i = 0; i < selected.length; i++) {
        var w = selected[i];
        if (!(w instanceof HypergravitonBlaster)) continue;
        if (w.ship && shooter && w.ship.id !== shooter.id) continue;
        n++;
    }
    return n;
};

/* A short human label for a Blaster on $shooter, used in the "slot taken" banner.
 * Prefers the system's displayName/name + a location word; falls back to "another Blaster". */
HypergravitonBlaster.prototype.blasterDisplayName = function (shooter, blaster) {
    if (!blaster) return 'another Blaster';
    var loc = '';
    switch (parseInt(blaster.location, 10)) {
        case 1: loc = 'Front '; break;
        case 2: loc = 'Aft '; break;
        case 3: case 31: case 32: loc = 'Port '; break;
        case 4: case 41: case 42: loc = 'Starboard '; break;
        default: loc = '';
    }
    return loc + 'Hypergraviton Blaster';
};

/* Re-opens the transfer window for an ALREADY-DECLARED Blaster order (player clicked the
 * system icon to edit). Reads the existing fire order, parses its notes back into the
 * saved order + checkbox states, and re-opens seeded from the same initial target.
 * Returns true if a window was opened (caller should then stop / not toggle selection). */
HypergravitonBlaster.prototype.reopenSpecialTargeting = function (shooter) {
    var fire = weaponManager.getFiringOrder(shooter, this);
    if (!fire) return false;

    var target = gamedata.getShip(fire.targetid);
    if (!target) return false;

    var chance = (typeof fire.chance !== 'undefined' && fire.chance !== null)
        ? fire.chance
        : window.weaponManager.calculateHitChange(shooter, target, this, -1).hitChance;

    //If ANOTHER Blaster now holds the transfer slot, re-open locked (transfer disabled).
    var holder = this.getOtherTransferringBlaster(shooter);
    var lockInfo = holder ? { locked: true, holderName: this.blasterDisplayName(shooter, holder) } : null;

    var preselect = this.parseTransferNotes(fire.notes, target.id);
    this.openTransferWindow(shooter, target, chance, preselect, lockInfo);
    return true;
};

/* Shared window-open + callback wiring for both first-declare and re-open. preselect is
 * null on a fresh declaration, or { queue, initialOnStructure } when re-editing. lockInfo
 * is null normally, or { locked:true, holderName } when another Blaster on the ship holds
 * the transfer slot (window opens with Confirm List disabled). opts is null normally, or
 * { showWeaponId:true } to append "(ID: X)" to the header (Scenario A disambiguation). */
HypergravitonBlaster.prototype.openTransferWindow = function (shooter, target, chance, preselect, lockInfo, opts) {
    var self = this;
    if (window.confirm && typeof window.confirm.hBlasterTransferList === 'function') {
        window.confirm.hBlasterTransferList(shooter, this, target, function (result) {
            if (result.cancelled) {
                //"Cancel Shot": remove any existing order + refresh, but only UNSELECT the
                //weapon if there actually was an order (so cancelling a never-declared shot
                //leaves the weapon selected for the player to retry).
                var hadOrder = !!weaponManager.getFiringOrder(shooter, self);
                weaponManager.removeFiringOrder(shooter, self);
                if (hadOrder) weaponManager.unSelectWeapon(shooter, self);
                webglScene.customEvent('SystemDataChanged', { ship: shooter, system: self });
                webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [self] });
                return;
            }
            self.declareTransferShot(shooter, target, chance, result.queue, result.initialOnStructure);
        }, preselect, lockInfo, opts);
    } else {
        //Fallback (window unavailable): declare a plain no-transfer shot so the
        //weapon still fires rather than silently doing nothing.
        this.declareTransferShot(shooter, target, chance, [], false);
    }
};

/* Parses an "HBT|initialId:flag|transferId:flag|..." notes string back into the window's
 * preselect shape: { queue:[{shipid,transferOnStructure}], initialOnStructure }. The first
 * entry (matching initialTargetId) becomes initialOnStructure; the rest become the queue.
 * Returns null if the notes aren't our format (so the window opens with fresh defaults). */
HypergravitonBlaster.prototype.parseTransferNotes = function (notes, initialTargetId) {
    if (!notes || notes.indexOf('HBT') !== 0) return null;
    var segments = notes.split('|');
    segments.shift(); //drop leading 'HBT'

    var result = { queue: [], initialOnStructure: false };
    for (var i = 0; i < segments.length; i++) {
        if (segments[i] === '') continue;
        var parts = segments[i].split(':');
        var shipid = parseInt(parts[0], 10);
        var flag = (parts.length > 1) && (parseInt(parts[1], 10) === 1);
        if (!shipid) continue;
        if (shipid === parseInt(initialTargetId, 10)) {
            result.initialOnStructure = flag;
        } else {
            result.queue.push({ shipid: shipid, transferOnStructure: flag });
        }
    }
    return result;
};

/* Builds + declares the Blaster's single normal/raking fire order against $target,
 * encoding the initial target's structure flag + the ordered transfer queue into notes,
 * then refreshes the targeting UI exactly as the synchronous targetShip() path does.
 * queue = [{ shipid, transferOnStructure }, ...] (transfer hops, already ordered).
 * initialOnStructure = the initial target's own "transfer on structure loss" flag.
 */
HypergravitonBlaster.prototype.declareTransferShot = function (shooter, target, chance, queue, initialOnStructure) {
    var calledid = -1; //raking weapon, no called shots

    //Replace any prior declaration (re-click to re-target / re-order).
    weaponManager.removeFiringOrder(shooter, this);

    //Initial target first (so its flag reaches getTransferFlagForShip; the server skips
    //it as a transfer hop), then the player-ordered transfer queue.
    var notes = "HBT|" + parseInt(target.id, 10) + ":" + (initialOnStructure ? 1 : 0);
    if (Array.isArray(queue)) {
        for (var i = 0; i < queue.length; i++) {
            var flag = queue[i].transferOnStructure ? 1 : 0;
            notes += "|" + parseInt(queue[i].shipid, 10) + ":" + flag;
        }
    }

    var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
    var damageClass = this.data["Weapon type"] ? this.data["Weapon type"].toLowerCase() : 'raking';
    var fire = {
        id: fireid,
        type: 'normal',
        shooterid: shooter.id,
        targetid: target.id,
        weaponid: this.id,
        calledid: calledid,
        turn: gamedata.turn,
        firingMode: this.firingMode,
        shots: 1,
        x: "null",
        y: "null",
        damageclass: damageClass,
        chance: chance,
        hitmod: 0,
        notes: notes
    };

    this.fireOrders.push(fire);

    //Mirror the post-declaration refresh from weaponManager.targetShip(): unselect
    //the weapon and notify the UI so the order's sprite/weapon-list/tooltip update.
    weaponManager.unSelectWeapon(shooter, this);
    webglScene.customEvent('SystemDataChanged', { ship: shooter, system: this });
    webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [this] });
};

HypergravitonBlaster.prototype.initBoostableInfo = function () {
    // Needed because it can change during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {

    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

	switch (this.turnsloaded) {
		case 1:
			this.data["Damage"] = '45 - 90';
			break;
		case 2:
			this.data["Damage"] = '90 - 180';
			break;
		default:
			this.data["Damage"] = '45 - 90';
			break;
	}    

    return this;
};

HypergravitonBlaster.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

var MedAntigravityBeam = function MedAntigravityBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
MedAntigravityBeam.prototype = Object.create(Gravitic.prototype);
MedAntigravityBeam.prototype.constructor = MedAntigravityBeam;

MedAntigravityBeam.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

MedAntigravityBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
    if (this.firingMode == 2 && this.fireOrders.length > 1) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        if (chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Sweeping',
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };

        fireOrdersArray.push(fire); // Store each fire order
    }

    return fireOrdersArray; // Return all fire orders
};

MedAntigravityBeam.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 1) return true;
    return false;
};

var AntigravityBeam = function AntigravityBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
AntigravityBeam.prototype = Object.create(Gravitic.prototype);
AntigravityBeam.prototype.constructor = AntigravityBeam;

AntigravityBeam.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

AntigravityBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
    if (this.firingMode == 2 && this.fireOrders.length > 2) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        if (chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Sweeping',
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };

        fireOrdersArray.push(fire); // Store each fire order
    }

    return fireOrdersArray; // Return all fire orders
};

AntigravityBeam.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 1) return true;
    return false;
};

var GraviticMine = function GraviticMine(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticMine.prototype = Object.create(Gravitic.prototype);
GraviticMine.prototype.constructor = GraviticMine;