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

/* =============================================================================
 * HyperplasmaCutter
 *
 * A ship may carry 2 or 3 of these weapons. Each has a pool of up to 10d10
 * (reduced by DiceLost criticals). The pools are ship-wide: dice from any
 * cutter may be freely allocated to any target in arc, or held for defensive
 * intercept. Arc is checked per-cutter so a 2-cutter ship with non-overlapping
 * arcs only sees the dice from the cutter(s) that can reach the target.
 *
 * NORMAL MODE  (1) — player clicks a target, enters how many dice to spend.
 *                    JS distributes across eligible cutters (lowest-ID first).
 *                    PHP Pass 1 captures the dice counts.
 *
 * SUSTAINED MODE (2) — all cutters, all dice, one target. Routes through
 *                      declareSustainedShot on the first turn.
 *                      On continuation turns the primary cutter fires via
 *                      continueSustainedShot — no dialog, auto-commits all dice.
 *                      isSustainedPrimary uses sustainedTarget as primary gate
 *                      so it remains reliable between polls when overloadshots
 *                      may temporarily read as 0.
 *                      Subordinate cutters are stowed so UI shows them grayed out.
 *
 * DEFENSIVE    — right-click on the cutter opens intercept dialog via
 *                doMultipleSelfIntercept. Blocked when sustained is active.
 * =========================================================================== */

var hpcSelfInterceptQueue  = [];
var hpcSelfInterceptTimer  = null;

var HyperplasmaCutter = function HyperplasmaCutter(json, ship) {
    Weapon.call(this, json, ship);
};
HyperplasmaCutter.prototype = Object.create(Weapon.prototype);
HyperplasmaCutter.prototype.constructor = HyperplasmaCutter;

HyperplasmaCutter.prototype.defensiveType = null;
HyperplasmaCutter.prototype.defensiveSystem = false;

/* --------------------------------------------------------------------------
 * Sustained-mode helpers
 * sustainedTarget is the most reliable indicator — it persists across polls
 * whereas overloadshots can temporarily read as 0 between server responses.
 * isSustainedPrimary also sets autoHit=true so calculateHitChange returns
 * 100% whenever this cutter is identified as the primary.
 * ------------------------------------------------------------------------ */

HyperplasmaCutter.prototype.isSustainedPrimary = function () {
    if (this.sustainedTarget && Object.keys(this.sustainedTarget).length > 0) {
        this.autoHit = true;
        return true;
    }
    this.autoHit = false;
    return false;
};

HyperplasmaCutter.prototype.isSustainedThisTurn = function () {
    if (this.isSustainedPrimary()) return true;
    if (this.overloadshots > 0) return true;

    if (this.ship) {
        for (var i in this.ship.systems) {
            var sys = this.ship.systems[i];
            if (!(sys instanceof HyperplasmaCutter) || sys.destroyed) continue;
            if (sys === this) continue;
            if (sys.sustainedTarget && Object.keys(sys.sustainedTarget).length > 0) return true;
            if (sys.overloadshots > 0) return true;
            for (var j = 0; j < sys.fireOrders.length; j++) {
                var fo = sys.fireOrders[j];
                if (fo.notes && fo.notes.indexOf('HPC-Sustained') >= 0 &&
                    (typeof gamedata === 'undefined' || fo.turn == gamedata.turn)) return true;
            }
        }
    }
    return false;
};

/* --------------------------------------------------------------------------
 * Pool helpers
 * ------------------------------------------------------------------------ */

HyperplasmaCutter.prototype.getEligibleCutters = function (shooter, target) {
    var cutters = [];
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (!(sys instanceof HyperplasmaCutter)) continue;
        if (sys.destroyed) continue;
        if (target && !weaponManager.isOnWeaponArc(shooter, target, sys)) continue;
        cutters.push(sys);
    }
    cutters.sort(function (a, b) { return parseInt(a.id, 10) - parseInt(b.id, 10); });
    return cutters;
};

HyperplasmaCutter.prototype.getRemainingDice = function () {
    var max  = (this.maxDice !== undefined) ? this.maxDice : 10;
    var used = 0;
    var currentTurn = (typeof gamedata !== 'undefined') ? gamedata.turn : -1;
    for (var i = 0; i < this.fireOrders.length; i++) {
        var fo = this.fireOrders[i];
        if ((fo.type === 'normal' || fo.type === 'selfIntercept') &&
            (currentTurn === -1 || fo.turn == currentTurn)) {
            used += fo.shots || 0;
        }
    }
    return Math.max(0, max - used);
};

HyperplasmaCutter.prototype.getShipRemainingDice = function (shooter, target) {
    var eligible = this.getEligibleCutters(shooter, target);
    var total = 0;
    for (var i = 0; i < eligible.length; i++) total += eligible[i].getRemainingDice();
    return total;
};

/* --------------------------------------------------------------------------
 * initializationUpdate
 * ------------------------------------------------------------------------ */
HyperplasmaCutter.prototype.initializationUpdate = function () {
    this.defaultShots = 0;
    if (this.maxDice === undefined || this.maxDice === null) this.maxDice = 10;

    var sustained     = this.isSustainedThisTurn();
    var isPrimary     = this.isSustainedPrimary(); // also sets this.autoHit
    var isSubordinate = sustained && !isPrimary;

    // Subordinate cutters are stowed so the UI shows them as grayed out.
    this.stowed = isSubordinate;

    // System data window
    if (isPrimary) {
        var targetId = Object.keys(this.sustainedTarget)[0];
        var tgt      = gamedata.getShip(targetId);
        this.data["Current Target"] = tgt ? tgt.name : "Ship #" + targetId;
        this.data["Status"] = "Sustained — Primary" + (this.overloadshots > 0 ? " (S" + this.overloadshots + ")" : "");
    } else if (isSubordinate) {
        this.data["Status"] = "Sustained — Subordinate (unavailable)";
        delete this.data["Current Target"];
    } else if (sustained) {
        this.data["Status"] = "Sustained (Pending)";
        delete this.data["Current Target"];
    } else {
        delete this.data["Status"];
        delete this.data["Current Target"];
    }

    if (!sustained && gamedata.gamephase == 3 && this.getRemainingDice() > 0 || this.fireOrders.length > 0) {
        this.data["Dice Remaining"] = this.getRemainingDice();
    } else {
        delete this.data["Dice Remaining"];
    }

    if (this.overloadshots > 0) {
        this.outputDisplay = "S" + this.overloadshots;
    } else {
        this.outputDisplay = "1/1";
    }

    return this;
};

/* --------------------------------------------------------------------------
 * Firing — Normal mode (1) and Sustained continuation
 * ------------------------------------------------------------------------ */
HyperplasmaCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {
    if (this.handlingInput) { this.handlingInput = false; return []; }

    // Continuation turn — primary cutter auto-commits sustained shot
    if (this.isSustainedPrimary()) {
        this.continueSustainedShot(shooter, target);
        return [];
    }

    // Block subordinate cutters during sustained sequence
    if (this.isSustainedThisTurn()) {
        confirm.warning("This cutter is committed to sustained mode and cannot fire independently. Use the primary cutter to continue or end the sustained shot.");
        return [];
    }

    // Block if a sustained shot is already committed this turn
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (!(sys instanceof HyperplasmaCutter) || sys.destroyed) continue;
        for (var j = 0; j < sys.fireOrders.length; j++) {
            var fo = sys.fireOrders[j];
            if (fo.turn == gamedata.turn && fo.notes && fo.notes.indexOf('HPC-Sustained') >= 0) {
                confirm.warning("A sustained shot is already committed. No further fire orders can be declared this turn.");
                return [];
            }
        }
    }

    // Sustained mode (first turn) routes to declareSustainedShot
    if (this.firingMode == 2) {
        this.declareSustainedShot(shooter, target);
        return [];
    }

    // Normal mode — allocate dice
    var eligible = this.getEligibleCutters(shooter, target);
    if (eligible.length === 0) return [];

    for (var i = 0; i < eligible.length; i++) {
        if (eligible[i] !== this) eligible[i].handlingInput = true;
    }

    var totalDice = this.getShipRemainingDice(shooter, target);
    if (totalDice <= 0) {
        confirm.error("No dice remaining for this target.");
        return [];
    }

    var chance = window.weaponManager.calculateHitChange(shooter, target, this, -1).hitChance;
    if (chance < 1) return [];

    confirm.askForMultipleValues(
        "Allocate d10s to this target (" + totalDice + " remaining in arc)",
        [{
            id:    'dice',
            label: 'Dice to allocate',
            max:   totalDice,
            min:   1,
            value: totalDice
        }],
        function (results) {
            var dice = parseInt(results['dice'], 10);
            if (isNaN(dice) || dice < 1) return;
            dice = Math.min(dice, totalDice);

            var remaining    = dice;
            var firedWeapons = [];

            for (var i = 0; i < eligible.length; i++) {
                var cutter     = eligible[i];
                var cutterDice = Math.min(cutter.getRemainingDice(), remaining);
                if (cutterDice <= 0) continue;

                var isPrimary = (firedWeapons.length === 0);
                var fireid    = shooter.id + "_" + cutter.id + "_" + (cutter.fireOrders.length + 1);
                var fire = {
                    id:          fireid,
                    type:        'normal',
                    shooterid:   shooter.id,
                    targetid:    target.id,
                    weaponid:    cutter.id,
                    calledid:    -1,
                    turn:        gamedata.turn,
                    firingMode:  cutter.firingMode,
                    shots:       isPrimary ? dice : cutterDice,
                    x:           "null",
                    y:           "null",
                    damageclass: isPrimary ? 'Sweeping' : 'HPC-subordinate',
                    chance:      chance,
                    hitmod:      0,
                    notes:       isPrimary ? '' : 'HPC-subordinate',
                    addToDB:     true
                };

                cutter.fireOrders.push(fire);
                remaining -= cutterDice;
                firedWeapons.push(cutter);

                webglScene.customEvent('SystemDataChanged', { ship: shooter, system: cutter });
                if (cutter.checkFinished()) weaponManager.unSelectWeapon(shooter, cutter);
                if (remaining <= 0) break;
            }

            if (firedWeapons.length > 0) {
                webglScene.customEvent('ShipTargeted', {
                    shooter: shooter,
                    target:  target,
                    weapons: [firedWeapons[0]]
                });
            }
        }
    );

    return [];
};

/* --------------------------------------------------------------------------
 * Sustained mode — first turn
 * ------------------------------------------------------------------------ */
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

    var poolSize = 0;
    for (var i = 0; i < all.length; i++) poolSize += all[i].getRemainingDice();

    var calledid = -1;
    var chance   = window.weaponManager.calculateHitChange(shooter, target, primary, calledid).hitChance;
    if (chance < 1) return;

    for (var i in all) weaponManager.removeFiringOrder(shooter, all[i]);

    for (var i = 0; i < all.length; i++) {
        if (all[i] !== primary) {
            all[i].handlingInput = true;
            weaponManager.unSelectWeapon(shooter, all[i]);
        }
    }

    var fireid = shooter.id + "_" + primary.id + "_" + (primary.fireOrders.length + 1);
    var fire = {
        id:         fireid,
        type:       'normal',
        shooterid:  shooter.id,
        targetid:   target.id,
        weaponid:   primary.id,
        calledid:   calledid,
        turn:       gamedata.turn,
        firingMode: 2,
        shots:      poolSize,
        x:          "null",
        y:          "null",
        damageclass:'Sweeping',
        chance:     chance,
        hitmod:     0,
        notes:      "HPC-Sustained",
        addToDB:    true
    };
    primary.fireOrders.push(fire);

    webglScene.customEvent('SystemDataChanged', { ship: shooter, system: primary });
    weaponManager.unSelectWeapon(shooter, primary);
    webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [primary] });
};

/* --------------------------------------------------------------------------
 * Sustained mode — continuation turns (N+1 and N+2)
 * No dialog — auto-commits all dice to the same target.
 * autoHit is set before calculateHitChange so the fire order list shows 100%.
 * chance is hardcoded to 100 on the fire order for the same reason.
 * ------------------------------------------------------------------------ */
HyperplasmaCutter.prototype.continueSustainedShot = function (shooter, target) {
    var sustainedTargetId = Object.keys(this.sustainedTarget)[0];
    if (String(target.id) !== String(sustainedTargetId)) {
        confirm.warning("In sustained mode you must fire at the same target as the previous turn: " +
            (gamedata.getShip(sustainedTargetId) ? gamedata.getShip(sustainedTargetId).name : "Ship #" + sustainedTargetId));
        return;
    }

    var all = [];
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (sys instanceof HyperplasmaCutter && !sys.destroyed) all.push(sys);
    }
    var poolSize = 0;
    for (var i = 0; i < all.length; i++) poolSize += (all[i].maxDice || 10);

    // Set autoHit before calculateHitChange so the confirmation display shows 100%
    this.autoHit = true;

    weaponManager.removeFiringOrder(shooter, this);

    var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
    var fire = {
        id:         fireid,
        type:       'normal',
        shooterid:  shooter.id,
        targetid:   target.id,
        weaponid:   this.id,
        calledid:   -1,
        turn:       gamedata.turn,
        firingMode: 2,
        shots:      poolSize,
        x:          "null",
        y:          "null",
        damageclass:'Sweeping',
        chance:     100,
        hitmod:     0,
        notes:      "HPC-Sustained",
        addToDB:    true
    };
    this.fireOrders.push(fire);

    webglScene.customEvent('SystemDataChanged', { ship: shooter, system: this });
    weaponManager.unSelectWeapon(shooter, this);
    webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [this] });
};

/* --------------------------------------------------------------------------
 * Defensive — right-click opens intercept dialog
 * Blocked entirely when sustained mode is active.
 * ------------------------------------------------------------------------ */
HyperplasmaCutter.prototype.checkSelfInterceptSystem = function () {
    if (this.isSustainedThisTurn()) return false;
    return this.getRemainingDice() > 0;
};

HyperplasmaCutter.prototype.doMultipleSelfIntercept = function (ship) {
    if (this.isSustainedThisTurn()) {
        confirm.warning("Defensive intercept is not available while in sustained mode.");
        return;
    }

    for (var q = 0; q < hpcSelfInterceptQueue.length; q++) {
        if (hpcSelfInterceptQueue[q].weapon === this) return;
    }
    hpcSelfInterceptQueue.push({ ship: ship, weapon: this });

    if (hpcSelfInterceptTimer !== null) return;
    hpcSelfInterceptTimer = window.setTimeout(openHpcSelfInterceptDialog, 0);
};

function openHpcSelfInterceptDialog() {
    hpcSelfInterceptTimer = null;
    var queued = hpcSelfInterceptQueue;
    hpcSelfInterceptQueue = [];
    if (queued.length === 0) return;

    queued.sort(function (a, b) { return parseInt(a.weapon.id, 10) - parseInt(b.weapon.id, 10); });

    var totalDice = 0;
    for (var i = 0; i < queued.length; i++) totalDice += queued[i].weapon.getRemainingDice();

    if (totalDice <= 0) {
        confirm.error("No dice remaining for defensive fire.");
        return;
    }

    confirm.askForMultipleValues(
        "Add defensive intercept shot (" + totalDice + " dice remaining — each die = -5% hit chance on one incoming shot)",
        [{
            id:    'dice',
            label: 'Dice for this intercept shot',
            max:   totalDice,
            min:   1,
            value: 1
        }],
        function (results) {
            var dice = parseInt(results['dice'], 10);
            if (isNaN(dice) || dice < 1) return;
            dice = Math.min(dice, totalDice);

            var targetEntry  = null;
            var targetCutter = null;

            for (var i = 0; i < queued.length; i++) {
                if (queued[i].weapon.getRemainingDice() >= dice) {
                    targetEntry  = queued[i];
                    targetCutter = queued[i].weapon;
                    break;
                }
            }

            if (!targetCutter) {
                var maxDice = 0;
                for (var i = 0; i < queued.length; i++) {
                    var rem = queued[i].weapon.getRemainingDice();
                    if (rem > maxDice) {
                        maxDice      = rem;
                        targetEntry  = queued[i];
                        targetCutter = queued[i].weapon;
                    }
                }
                dice = Math.min(dice, targetCutter.getRemainingDice());
            }

            if (!targetCutter || dice <= 0) return;

            var fireid = targetEntry.ship.id + '_' + targetCutter.id + '_intercept_' + (targetCutter.fireOrders.length + 1);
            targetCutter.fireOrders.unshift({
                id:         fireid,
                type:       'selfIntercept',
                shooterid:  targetEntry.ship.id,
                targetid:   targetEntry.ship.id,
                weaponid:   targetCutter.id,
                calledid:   -1,
                turn:       gamedata.turn,
                firingMode: targetCutter.firingMode,
                shots:      dice,
                x:          'null',
                y:          'null',
                addToDB:    true,
                damageclass:'plasma',
                notes:      'HPC-intercept'
            });

            webglScene.customEvent('SystemDataChanged', { ship: targetEntry.ship, system: targetCutter });
            if (targetCutter.checkFinished()) weaponManager.unSelectWeapon(targetEntry.ship, targetCutter);
        }
    );
}

/* --------------------------------------------------------------------------
 * Misc
 * ------------------------------------------------------------------------ */
HyperplasmaCutter.prototype.checkFinished = function () {
    if (this.isSustainedThisTurn()) return true;
    return this.getRemainingDice() <= 0;
};

/* =============================================================================
 * HyperplasmaMatrix
 *
 * Fighter flight weapon — each fighter IS a component of a single combined gun.
 * The entire flight fires as one shot in Flash mode (Plasma class).
 *
 * Damage: N * 2d6 + 12, where N = surviving fighters in the flight.
 * Range penalty: -5% per hex.
 * Self-immunity: flight is immune to own Flash splash when in the same hex.
 * Defensive: each surviving fighter generates an independent -10% intercept.
 *
 * When one fighter fires, PHP combines all flight orders into one primary shot.
 * On the JS side, doMultipleFireOrders submits the fire order normally and then
 * stows all sibling HyperplasmaMatrix weapons so they gray out in the UI.
 * =========================================================================== */

var HyperplasmaMatrix = function HyperplasmaMatrix(json, ship) {
    Weapon.call(this, json, ship);
};
HyperplasmaMatrix.prototype = Object.create(Weapon.prototype);
HyperplasmaMatrix.prototype.constructor = HyperplasmaMatrix;

// One fire order from any fighter in the flight is sufficient — PHP combines them.
HyperplasmaMatrix.prototype.checkFinished = function (fireOrders) {
    return fireOrders.length >= 1;
};

/* --------------------------------------------------------------------------
 * doMultipleFireOrders
 *
 * Called when the player clicks a target to fire. Submits the fire order via
 * the standard weapon path, then stows all sibling HyperplasmaMatrix weapons
 * (other fighters in the flight) and fires SystemDataChanged so the UI redraws
 * them as grayed out — reflecting that the whole flight fires as one gun.
 * -------------------------------------------------------------------------- */
HyperplasmaMatrix.prototype.doMultipleFireOrders = function (shooter, target, system) {
alert("HPM fired"); // ← add this line
    // Build and submit the fire order via the standard weapon mechanism
    var fireid = shooter.id + '_' + this.id + '_' + (this.fireOrders.length + 1);
    var fire = {
        id:         fireid,
        type:       'normal',
        shooterid:  shooter.id,
        targetid:   target.id,
        weaponid:   this.id,
        turn:       gamedata.turn,
        firingMode: this.firingMode,
        shots:      1,
        addToDB:    true
    };
    this.fireOrders.push(fire);

    webglScene.customEvent('SystemDataChanged', { ship: shooter, system: this });
    weaponManager.unSelectWeapon(shooter, this);
    webglScene.customEvent('ShipTargeted', { shooter: shooter, target: target, weapons: [this] });

    // Stow all sibling HyperplasmaMatrix weapons and redraw them
    if (shooter.flight && shooter.systems) {
        for (var i in shooter.systems) {
            var fighter = shooter.systems[i];
            if (!fighter || !fighter.systems) continue;
            for (var j in fighter.systems) {
                var sys = fighter.systems[j];
                if (!(sys instanceof HyperplasmaMatrix)) continue;
                if (sys.id === this.id) continue; // skip self
                sys.stowed = true;
                webglScene.customEvent('SystemDataChanged', { ship: shooter, system: sys });
            }
        }
    }

    return [fire];
};

var PlasmaDriver = function PlasmaDriver(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaDriver.prototype = Object.create(Weapon.prototype);
PlasmaDriver.prototype.constructor = PlasmaDriver;

var HyperplasmaStream = function HyperplasmaStream(json, ship) {
    Plasma.call(this, json, ship);
};
HyperplasmaStream.prototype = Object.create(Plasma.prototype);
HyperplasmaStream.prototype.constructor = HyperplasmaStream;

var FuserArray = function FuserArray(json, ship) {
    Plasma.call(this, json, ship);
};
FuserArray.prototype = Object.create(Weapon.prototype);
FuserArray.prototype.constructor = FuserArray;

FuserArray.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }

    return this;
};

FuserArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.firingMode == 2 && this.fireOrders.length >= this.guns) return; //one split shot per gun (guns is reduced by GunLost crits).

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
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

FuserArray.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length >= this.guns) return true; //one split shot per gun (guns is reduced by GunLost crits).
    return false;
};