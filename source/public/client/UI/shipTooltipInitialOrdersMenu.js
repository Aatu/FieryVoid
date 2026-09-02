"use strict";

window.ShipTooltipInitialOrdersMenu = function () {

    function ShipTooltipInitialOrdersMenu(selectedShip, targetedShip, turn, hexagon) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
        this.hexagon = hexagon;
    }

    ShipTooltipInitialOrdersMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipInitialOrdersMenu.buttons = [
        { className: "addCCEW", condition: [isSelf, notFlight, notMine], action: addCCEW, info: "Add CCEW (right-click: max)", supportsMaxClick: true },
        { className: "removeCCEW", condition: [isSelf, notFlight, notMine], action: removeCCEW, info: "Remove CCEW (right-click: clear)", supportsMaxClick: true },
        { className: "addOEW", condition: [notSelf, isEnemyEW, sourceNotFlight], action: getAddOEW('OEW'), info: "Add OEW (right-click: max)", supportsMaxClick: true },
        { className: "removeOEW", condition: [notSelf, isEnemyEW, sourceNotFlight], action: getRemoveOEW('OEW'), info: "Remove OEW (right-click: clear)", supportsMaxClick: true },
        { className: "addMDEW", condition: [isSelf, enemyMines], action: addMDEW, info: "Add Mine Detection (right-click: max)", supportsMaxClick: true },
        { className: "removeMDEW", condition: [isSelf, enemyMines], action: removeMDEW, info: "Remove Mine Detection (right-click: clear)", supportsMaxClick: true },
        { className: "addDIST", condition: [notSelf, isEnemyEW, isElint, notFlight, notMine, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('DIST'), info: "Add DIST (right-click: max)", supportsMaxClick: true },
        { className: "removeDIST", condition: [notSelf, isEnemyEW, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasDIST], action: getRemoveOEW('DIST'), info: "Remove DIST (right-click: clear)", supportsMaxClick: true },
        //Jamming: ELINT disrupts a remote-controlled fighter flight's command link (Orieni Hunter-Killers). Target IS a flight, so no notFlight gate.
        { className: "addJAM", condition: [notSelf, isEnemyEW, isElint, sourceNotFlight, targetIsRemoteControl, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('JAM'), info: "Add Jamming (right-click: max)", supportsMaxClick: true },
        { className: "removeJAM", condition: [notSelf, isElint, sourceNotFlight, targetIsRemoteControl, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasJAM], action: getRemoveOEW('JAM'), info: "Remove Jamming (right-click: clear)", supportsMaxClick: true },
        //{ className: "addOEW", condition: [notSelf, sourceNotFlight], action: addOEW, info: "Add OEW" },
        //{ className: "removeOEW", condition: [notSelf, sourceNotFlight], action: removeOEW, info: "Remove OEW" },
        //{ className: "addDIST", condition: [notSelf, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('DIST'), info: "Add DIST" },
        //{ className: "removeDIST", condition: [notSelf, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasDIST], action: getRemoveOEW('DIST'), info: "Remove DIST" },
        { className: "addSOEW", condition: [isFriendly, isElint, notFlight, notMine, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SOEW'), info: "Add SOEW", supportsMaxClick: true },
        { className: "removeSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSOEW], action: getRemoveOEW('SOEW'), info: "Remove SOEW (right-click: clear)", supportsMaxClick: true },
        { className: "addSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SDEW'), info: "Add SDEW (right-click: max)", supportsMaxClick: true },
        { className: "removeSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSDEW], action: getRemoveOEW('SDEW'), info: "Remove SDEW (right-click: clear)", supportsMaxClick: true },
        { className: "addBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: addBDEW, info: "Add BDEW (right-click: max)", supportsMaxClick: true },
        { className: "removeBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: removeBDEW, info: "Remove BDEW (right-click: clear)", supportsMaxClick: true },
        { className: "addDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW, enemyStealth], action: addDetectSEW, info: "Add Detect Stealth (right-click: max)", supportsMaxClick: true },
        { className: "removeDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW, enemyStealth], action: removeDetectSEW, info: "Remove Detect Stealth (right-click: clear)", supportsMaxClick: true },
        { className: "removeAllEW", condition: [isSelf, notFlight, notMine], action: removeAllEW, info: "Remove All EW" },
        { className: "targetWeapons", condition: [isEnemy, hasShipWeaponsSelected], action: targetWeapons, info: "Target selected weapons on ship" },
        { className: "targetWeaponsHex", condition: [hasOrderSource, hasHexWeaponsSelected], action: targetHexagon, info: "Target selected weapons on hexagon" },
        { className: "targetSuppWeapons", condition: [isFriendly, hasShipWeaponsSelected, FFWeaponSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        { className: "removeMultiOrder", condition: [hasOrderSource, hasShipWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" },
        /* ⭐ JUMP_GATES_PLAN.md Stage 3 - SIGNALLING A FIXED JUMP GATE, and these two are the ONLY
           entries in this array whose subject is a unit the player does not own.

           A gate is contested terrain: ANY player may signal ANY gate, including one the enemy
           bought (plan section 2.4 - there is no owner priority). Clicking a terrain gate in Initial
           Orders already lands on InitialPhaseStrategy.targetShip and builds this menu, so the
           button is an entry in an existing array and gamedata.isMyShip needs no exception at all -
           the real structural work was the SUBMIT PATH, on both sides (plan section 3.1).

           NO SHIP NEEDS TO BE SELECTED. Which of my units is within range is never chosen and never
           matters, so every condition below reads the TARGETED ship (the gate) and none of them
           touches this.selectedShip - which is routinely null here. */
        { className: "signalJumpGate", condition: [isJumpGate, canSignalGate, noGateSignalYet], action: signalJumpGate, info: "Signal Jump Gate for Departure" },
        /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE OTHER DIRECTION, AND IT IS A SECOND BUTTON
           RATHER THAN A TOGGLE INSIDE THE PANEL. A gate holds ONE jump point and it is one-way
           (plan section 2.6), so signalling for departure and signalling for arrival are two
           different orders for the same charge - and the arrival one is meaningless unless the
           player has something in hyperspace, which is a condition a button can simply not meet.
           A toggle would have to sit there greyed out on every gate in every game without the
           reinforcements rule; this way the feature is invisible until it applies, which is the
           same shape the two mutually-exclusive buttons above already have. */
        { className: "signalJumpGateArrival", condition: [isJumpGate, canSignalGateForArrival, noGateSignalYet], action: signalJumpGateForArrival, info: "Signal Gate for Arrival" },
        { className: "cancelJumpGateSignal", condition: [isJumpGate, hasGateSignal], action: cancelJumpGateSignal, info: "Cancel Gate Signal" }
    ];


    ShipTooltipInitialOrdersMenu.prototype.getAllButtons = function () {
        return ShipTooltipInitialOrdersMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    /* ⭐ IS THERE A UNIT TO ISSUE THIS ORDER *FROM*? (user report 2026-08-29.)

       this.selectedShip is ROUTINELY null in this phase - the two jump gate buttons below are
       built on exactly that, and a player whose whole fleet is still in hyperspace has nothing
       the auto-select could pick - and InitialPhaseStrategy.targetShip now deliberately passes
       null for a selected unit that is not on the board yet. Every EW entry above already fails
       closed on a null source through isSelf/isEnemyEW/isElint/isFriendly, but the two hex/split
       entries asked only about the WEAPON SELECTION, so they would have offered a button whose
       action calls weaponManager with a null shooter. */
    function hasOrderSource() {
        return !!this.selectedShip;
    }

    function hasShipWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === true;
            return system instanceof Weapon && system.hextarget !== true;
        });
    }

    function hasSplitWeaponFiringOrder() {
        return gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.canSplitShots && weaponManager.hasTargetedThisShip(this.targetedShip, system);
        }.bind(this)); // Bind `this` to the callback
    }

    function hasHexWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === false;
            return system instanceof Weapon && system.hextarget === true;
        });
    }

    function targetWeapons() {
        weaponManager.targetShip(this.selectedShip, this.targetedShip);
    }

    function targetHexagon() {
        weaponManager.targetHex(this.selectedShip, this.hexagon);
    }

    function removeFiringOrderMulti() {
        // Loop through selected systems and check for systems that have canSplitShots set to true
        gamedata.selectedSystems.forEach(function (system) {
            if (system.canSplitShots) {
                // Call weaponManager.removeFiringOrderMulti for each system that meets the condition
                weaponManager.removeFiringOrderMulti(this.selectedShip, system, this.targetedShip, true);
            }
        }, this); // Make sure to bind `this` so that `this.selectedShip` is correct
    }

    function FFWeaponSelected() {
        if (gamedata.rules && gamedata.rules.friendlyFire === 1) return true; //To let ballistics target

        return gamedata.selectedSystems.some(system => {
            return system.canTargetAllies === true || system.canTargetAll === true;
        });
    }

    function addSelfEW(ewType, isMaxClick) {
        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, null, ewType, this.turn);
            var before = ew.getEWLeft(this.selectedShip);
            if (!entry) {
                ew.assignEW(this.selectedShip, ewType);
            } else {
                ew.assignEW(this.selectedShip, entry);
            }
            if (!isMaxClick) return;
            if (ew.getEWLeft(this.selectedShip) >= before) return;
        } while (ew.getEWLeft(this.selectedShip) > 0);
    }

    function removeSelfEW(ewType, isMaxClick) {
        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, null, ewType, this.turn);
            if (!entry) return;
            ew.deassignEW(this.selectedShip, entry);
        } while (isMaxClick);
    }

    function addCCEW(isMaxClick) { addSelfEW.call(this, "CCEW", isMaxClick); }
    function removeCCEW(isMaxClick) { removeSelfEW.call(this, "CCEW", isMaxClick); }
    function addMDEW(isMaxClick) { addSelfEW.call(this, "Detect Mines", isMaxClick); }
    function removeMDEW(isMaxClick) { removeSelfEW.call(this, "Detect Mines", isMaxClick); }
    function addBDEW(isMaxClick) { addSelfEW.call(this, "BDEW", isMaxClick); }
    function removeBDEW(isMaxClick) { removeSelfEW.call(this, "BDEW", isMaxClick); }
    function addDetectSEW(isMaxClick) { addSelfEW.call(this, "Detect Stealth", isMaxClick); }
    function removeDetectSEW(isMaxClick) { removeSelfEW.call(this, "Detect Stealth", isMaxClick); }

    function removeAllEW() {
        ew.removeEW(this.selectedShip);
    }

    function getAddOEW(type) {
        return function (isMaxClick) {
            addOEW.call(this, type, isMaxClick);
        };
    }

    function addOEW(type, isMaxClick) {
        if (!type) {
            type = "OEW";
        }

        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);
            var before = ew.getEWLeft(this.selectedShip);
            if (!entry) {
                ew.AssignOEW(this.selectedShip, this.targetedShip, type);
            } else {
                ew.assignEW(this.selectedShip, entry);
            }
            if (!isMaxClick) return;
            if (ew.getEWLeft(this.selectedShip) >= before) return;
        } while (ew.getEWLeft(this.selectedShip) > 0);
    }

    function getRemoveOEW(type) {
        return function (isMaxClick) {
            removeOEW.call(this, type, isMaxClick);
        };
    }

    function removeOEW(type, isMaxClick) {
        if (!type) {
            type = "OEW";
        }

        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);
            if (!entry) return;
            ew.deassignEW(this.selectedShip, entry);
        } while (isMaxClick);
    }

    /* ---- JUMP_GATES_PLAN.md Stage 3: the fixed jump gate signal. See the button entries above.
       All four read this.targetedShip - the gate - and never this.selectedShip. */
    function isJumpGate() {
        return gamedata.isJumpGate(this.targetedShip);
    }

    //Phase, charge, open vortex, engine health AND "I have a unit within the gate's signal range"
    //- the client mirror of Firing::getGateSignalBlock. No line-of-sight test, deliberately.
    function canSignalGate() {
        return gamedata.canSignalJumpGate(this.targetedShip);
    }

    function hasGateSignal() {
        return !!weaponManager.getGateSignalOrder(this.targetedShip);
    }

    //A standing claim does not stop the gate being signallable, so the two buttons need this to be
    //mutually exclusive - otherwise both show and the player can declare twice.
    function noGateSignalYet() {
        return !weaponManager.getGateSignalOrder(this.targetedShip);
    }

    function signalJumpGate() {
        weaponManager.queueGateSignalOrder(this.targetedShip);
    }

    /* REINFORCEMENTS_PLAN.md STAGE 8 - everything canSignalGate needs, plus the reinforcements rule
       being on in this game. The client mirror of the one extra rule Firing::getGateSignalBlock
       applies to a 'gateexit' claim; gamedata owns the predicate so the two buttons cannot drift.

       ⭐ IT NO LONGER ASKS WHAT IS IN HYPERSPACE (user ruling 2026-09-02): a gate exit stands for
       its whole programmed hold and anybody may ride it, so this button is offered to a player with
       nothing of their own waiting. See gamedata.canSignalJumpGateForArrival for the reasoning. */
    function canSignalGateForArrival() {
        return gamedata.canSignalJumpGateForArrival(this.targetedShip);
    }

    //Same panel, blue livery, and the manifest dialog opens on Signal - see
    //weaponManager.createGateSignalOrder.
    function signalJumpGateForArrival() {
        weaponManager.queueGateSignalOrder(this.targetedShip, true);
    }

    /* Withdraw the claim - and TOGGLE THE BUTTON BACK, which is the point of the redraw.

       The two gate buttons are mutually exclusive on hasGateSignal/noGateSignalYet, so the moment
       the order is gone the OTHER one is the correct button - but nothing re-evaluates a menu's
       conditions on its own. The tooltip SURVIVES this click (it swallows mousedown/mouseup, which
       is why the click-away discard never fires on its own buttons), so without this the player is
       left looking at a Cancel button for an order that no longer exists until they click the gate
       again. ShipTooltip.update() with no arguments re-runs every condition and rebuilds the row;
       passing no selectedShip is deliberate, since a gate signal routinely has none (section 2.1).

       currentInfo is set by hand because the pointer does not MOVE across the swap, so no mouseover
       fires on the replacement button and the info line would otherwise still read "Cancel Gate
       Signal" underneath a Signal button. It is only claimed when the signal button is actually
       going to be there - canSignalGate can have gone false in the meantime. */
    function cancelJumpGateSignal() {
        weaponManager.removeGateSignalOrder(this.targetedShip);

        if (this.shipTooltip) {
            this.currentInfo = canSignalGate.call(this) ? "Signal Jump Gate" : "";
            this.shipTooltip.update();
        }
    }

    function isSelf() {
        return this.selectedShip === this.targetedShip;
    }

    function notSelf() {
        return this.selectedShip !== this.targetedShip;
    }

    function isEnemy() {
        return this.selectedShip && !gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isEnemyEW() {
        /* ⚠️ THE NULL GUARD IS THE WHOLE OF USER REPORT 2026-08-29, AND IT COST THE PLAYER THE
           ENTIRE TOOLTIP. With nothing selected, hasSpecialAbility(null) walks null.systems and
           throws - and this runs inside ShipTooltipMenu.renderTo's condition filter, which runs
           inside the ShipTooltip constructor BEFORE show(). So one TypeError in a condition that
           was never going to add a button took down the whole panel: no Signal Gate buttons, no
           Signal Gate for Arrival, and no Open Ship Details either, on every click on every unit.
           It only ever stayed hidden because Initial Orders auto-selects a ship on activate - a
           fleet held entirely in hyperspace has none to select, which is what reinforcements made
           reachable. An EW order with no source is meaningless, so the answer is a plain false,
           BEFORE the friendly-fire shortcut (which would otherwise say yes with no shooter). */
        if (!this.selectedShip) return false;
        if (gamedata.rules && gamedata.rules.friendlyFire === 1) return true;
        if (shipManager.hasSpecialAbility(this.selectedShip, "alliedEW")) return true;

        var hexWeaponSelected = gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });

        if (hexWeaponSelected) return true;

        return this.selectedShip && !gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isFriendly() {
        return gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isElint() {
        return this.selectedShip && shipManager.isElint(this.selectedShip);
    }

    function notFlight() {
        return (!this.selectedShip || !this.selectedShip.flight) && (!this.targetedShip || !this.targetedShip.flight);
    }

    function notMine() {
        return (!this.selectedShip || !this.selectedShip.mine) && (!this.targetedShip || !this.targetedShip.mine);
    }

    function sourceNotFlight() {
        return (!this.selectedShip || !this.selectedShip.flight);
    }

    function targetNotFlight() {
        return (!this.targetedShip || !this.targetedShip.flight);
    }

    function targetIsRemoteControl() {
        return !!(this.targetedShip && this.targetedShip.remoteControl);
    }

    function isInElintDistance(distance) {
        return function () {
            return ew.checkInELINTDistance(this.selectedShip, this.targetedShip, distance);
        };
    }

    function doesNotHaveBDEW() {
        return ew.getEWByType("BDEW", this.selectedShip) === 0;
    }

    function enemyStealth() {
        return gamedata.isStealthPresent;
    } 
    
    function enemyMines() {
        return gamedata.areMinesPresent;
    }     

    function doesNotHaveOtherElintEWThanBDEW() {
        return ew.getEWByType("SDEW", this.selectedShip) === 0 && ew.getEWByType("DIST", this.selectedShip) === 0 && ew.getEWByType("SOEW", this.selectedShip) === 0 && ew.getEWByType("Detect Stealth", this.selectedShip) === 0;
    }

    function hasOEW() { return ew.getEWByType("OEW", this.selectedShip, this.targetedShip) > 0; }
    function hasCCEW() { return ew.getEWByType("CCEW", this.selectedShip) > 0; }
    function hasSDEW() { return ew.getEWByType("SDEW", this.selectedShip, this.targetedShip) > 0; }
    function hasSOEW() { return ew.getEWByType("SOEW", this.selectedShip, this.targetedShip) > 0; }
    function hasBDEW() { return ew.getEWByType("BDEW", this.selectedShip) > 0; }
    function hasDIST() { return ew.getEWByType("DIST", this.selectedShip, this.targetedShip) > 0; }
    function hasJAM() { return ew.getEWByType("JAM", this.selectedShip, this.targetedShip) > 0; }
    function hasDSEW() { return ew.getEWByType("Detect Stealth", this.selectedShip) > 0; }

    function advSensorsCheck() { /*check whether source ship has Advanced Sensors OR target ship does NOT have Advanced Sensors*/
        return (shipManager.hasSpecialAbility(this.selectedShip, "AdvancedSensors") || (!(shipManager.hasSpecialAbility(this.targetedShip, "AdvancedSensors"))))
    }

    return ShipTooltipInitialOrdersMenu;
}();
