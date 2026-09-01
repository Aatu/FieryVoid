'use strict';

window.PhaseStrategy = function () {

    function PhaseStrategy(coordinateConverter) {
        this.inactive = true;
        this.gamedata = null;
        this.shipIconContainer = null;
        this.ewIconContainer = null;
        this.ballisticIconContainer = null;
        this.shipWindowManager = null;
        this.coordinateConverter = coordinateConverter;
        this.currentlyMouseOveredIds = null;

        this.onMouseOutCallbacks = [];
        this.onZoomCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this), this.repositionSelectFromShips.bind(this), this.positionVortexFacingUI.bind(this), this.positionGateSignalUI.bind(this)];
        this.onScrollCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this), this.repositionSelectFromShips.bind(this), this.positionVortexFacingUI.bind(this), this.positionGateSignalUI.bind(this)];
        this.onClickCallbacks = [this.hideSystemInfo.bind(this, true)];

        this.selectedShip = null;
        this.targetedShip = null;
        this.animationStrategy = null;
        this.replayUI = null;

        this.shipTooltip = null;
        this.selectFromShips = null;
        this.movementUI = null;

        this.onDoneCallback = null;

        this.systemInfoState = null;
        this._lastHoveredHex = null;
        this._startHexRuler = null;

        this.uiManager = new window.UIManager($("body")[0]);
    }

    PhaseStrategy.prototype.onOpenShipWindowFor = function (payload) {
        this.shipWindowManager.open(payload.ship);
    }

    PhaseStrategy.prototype.onCloseShipWindow = function (payload) {
        this.shipWindowManager.close(payload.ship);
        //a window closed under the cursor never fires mouse-out on its health bar - sweep any
        //structure wedge it left behind
        this.onStructureMouseOut();
    }

    PhaseStrategy.prototype.onCloseSystemInfo = function () {
        this.hideSystemInfo(true);
    }

    PhaseStrategy.prototype.hideSystemInfo = function (force) {
        if (!this.systemInfoState) {
            return true;
        }

        if (!this.systemInfoState.menu || force) {
            this.uiManager.hideSystemInfo();
            this.systemInfoState = null;
        }

        this.hoveredArcSystem = null;
        this.refreshSystemArcs();

        return true;
    }

    /* ⭐ JUMP_POINTS_PLAN.md Stage 6 - WHOSE ARC IS ON SCREEN, decided in one place.
     *
     * Arcs used to be a pure hover display: three call sites each cleared every icon's arcs, and
     * onSystemMouseOver drew the one under the pointer. That lost the arc at the exact moment a
     * HEX-TARGETED system needs it - the player clicks the icon to SELECT the system, the pointer
     * leaves, mouse-out fires, and the overlay showing where they may aim disappears just as they
     * turn to the map to aim (user request 2026-08-22).
     *
     * So the answer is now "the hovered system, plus anything selected that asks to stay up", and
     * every site that used to clear arcs calls this instead. The hovered system is remembered
     * (this.hoveredArcSystem) rather than passed, because the sweep also has to run from
     * onSystemDataChanged - selecting and unselecting both land there - and that must not tear
     * down an arc the pointer is still sitting on.
     *
     * Deliberately narrow: shipManager.systems.showsArcWhenSelected holds the short list and says
     * why. Every gun's arc left standing would paint the map solid on a selected broadside. */
    PhaseStrategy.prototype.refreshSystemArcs = function () {
        if (!this.shipIconContainer) return;

        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideWeaponArcs();
        });

        var drawn = [];
        var container = this.shipIconContainer;

        var draw = function (ship, system) {
            if (!ship || !system) return;
            if (drawn.indexOf(system) !== -1) return;  //never twice - two fills would alpha-compound
            var icon = container.getByShip(ship);
            if (!icon) return;
            icon.showWeaponArc(ship, system);
            drawn.push(system);
        };

        if (this.hoveredArcSystem) draw(this.hoveredArcSystem.ship, this.hoveredArcSystem.system);

        if (!gamedata.selectedSystems) return;
        gamedata.selectedSystems.forEach(function (system) {
            if (!shipManager.systems.showsArcWhenSelected(system)) return;
            draw(system.ship, system);
        });
    };

    PhaseStrategy.prototype.showSystemInfo = function (ship, system, element, menu) {
        if (this.systemInfoState && this.systemInfoState.menu && !menu) {
            return;
        }

        var boundingBox = element.getBoundingClientRect ? element.getBoundingClientRect() : element.get(0).getBoundingClientRect();

        if (menu) {
            if (!this.uiManager.canShowSystemInfoMenu(ship, system)) {
                this.hideSystemInfo(true);
                return;
            }
            this.uiManager.showSystemInfoMenu({ ship: ship, selectedShip: this.selectedShip, system: system, boundingBox: boundingBox });
        } else {
            this.uiManager.showSystemInfo({ ship: ship, selectedShip: this.selectedShip, system: system, boundingBox: boundingBox });
        }
        this.systemInfoState = { menu: menu, element: element, system: system }
    }

    PhaseStrategy.prototype.consumeGamedata = function () {
        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.animationStrategy.update(this.gamedata);
        this.ewIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.redrawMovementUI();
    };

    PhaseStrategy.prototype.render = function (coordinateConverter, scene, zoom) {
        this.animationStrategy.render(coordinateConverter, scene, zoom);
    };

    // Idle render-loop gating: delegates to the animation strategy so
    // webglScene can keep rendering full frames only while animations play.
    PhaseStrategy.prototype.isAnimating = function () {
        return Boolean(this.animationStrategy && this.animationStrategy.isAnimating());
    };

    PhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.consumeGamedata();
        //this.refreshModifiedShips(); //Fix was actually server side, so comment this out for now in case sueful later - DK.
        this.ewIconContainer.hide();
        this.ballisticIconContainer.show();
        this.syncAllDeclaredAreas(); //a poll rebuilds every ship, so re-read what each weapon has declared
    };

    /*// A same-phase poll (this.update, as opposed to a phase-change activate()) refreshes ship
    // icons/EW/ballistics/movement via consumeGamedata, but NOT a ship's movement glyphs unless it
    // is selected, nor the predicted hit-chance in the weapon list / system-info tooltip. When the
    // server sends a ship flagged isModified - a buff applied at LOAD to ANOTHER ship (Gravitic
    // Augmenter Mode 2 gives a Warrior +OB/+thrust and 3 forced jink; stripForJson emits the buffed
    // stats + the transient forced jink), those changes silently fail to show until the phase flips
    // or the user clicks the ship / refreshes. Poke the existing re-render hooks for each modified
    // ship so its jink/thrust display and everyone's hit-chance against it update in place. Cheap:
    // isModified is set on a handful of buffed flights only, so the loop is a no-op almost always.
    PhaseStrategy.prototype.refreshModifiedShips = function () {
        for (var i in this.gamedata.ships) {
            var ship = this.gamedata.ships[i];
            if (!ship || !ship.isModified) continue;

            this.onShipMovementChanged({ ship: ship });   // jink/thrust glyphs + ballistic lines
            PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship }); // predicted hit chance
        }
    };*/

    PhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager, doneCallback) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.inactive = false;
        this.consumeGamedata();
        this.shipIconContainer.setAllSelected(false);
        this.ballisticIconContainer.show();
        this.onDoneCallback = doneCallback;
        this.shipWindowManager = shipWindowManager;
        this.createReplayUI(gamedata);
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        this.syncAllDeclaredAreas();
        return this;
    };

    PhaseStrategy.prototype.deactivate = function () {
        this.inactive = true;
        this.animationStrategy.deactivate();
        this.replayUI && this.replayUI.deactivate();

        if (this.ballisticIconContainer) {
            this.ballisticIconContainer.hide();
        }

        if (this.ewIconContainer) {
            this.ewIconContainer.hide();
        }

        if (this.shipTooltip) {
            this.shipTooltip.destroy();
        }

        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }

        if (this.selectFromShips) { //To clear selectFromShips correctly if player clicks Commit before clicking anywhere else - DK 10/24
            this.hideSelectFromShips(this.selectFromShips);
        }

        //Same reason: a vortex declaration left mid-transaction when the phase ends is a discard,
        //and its preview sprites must not outlive the phase that owns them.
        UI.vortexFacing.close();

        //And the same again for a fixed jump gate's duration panel (JUMP_GATES_PLAN.md Stage 3).
        //Nothing is declared until SIGNAL is pressed, so an open panel at phase end is simply
        //abandoned - but the div is outside the WebGL canvas and would otherwise stay on screen.
        UI.gateSignal.close();

        this.currentlyMouseOveredIds = null;

        this.uiManager.hideWeaponList();
        this.hideSystemInfo(true);
        this.shipWindowManager.closeAll();

        this.shipIconContainer.getArray(icon => {
            icon.showSideSprite(false);
            icon.setHighlighted(false);
        })

        return this;
    };

    PhaseStrategy.prototype.onEvent = function (name, payload) {
        var target = this['on' + name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    /* Declared-area overlays (Weapon.getDeclaredArea / ShipIcon.showDeclaredArea) put in step with
       what this ship's weapons have actually declared. State-driven rather than event-driven on
       purpose: a weapon says what it wants shown and this decides when, so a weapon never has to
       raise or tear down anything, and every route into and out of an order is covered by the one
       mechanism - the activation menu, doDeactivate, the generic remove-fire-order button, a
       server-side change arriving on a poll, and a page reload mid-phase (which rebuilds the weapon
       with its order already in place, so its doActivate never runs again).

       showDeclaredArea itself no-ops when what is already drawn is still correct, so calling this as
       often as we do costs a walk of the ship's systems and nothing else.

       OWN SIDE ONLY. A declared area says what a ship is ABOUT to do, which is precisely the thing
       the per-viewer masking exists to keep from the other side (see the Planet-Cracker Beam's
       hideFireOrdersFromEnemies - an enemy client is not sent the order at all, so its
       getDeclaredArea would return null anyway). The gate is here rather than left to each weapon so
       that a future opt-in whose orders AREN'T masked cannot quietly become an info leak.

       On the base strategy so it holds in every phase. */
    PhaseStrategy.prototype.syncDeclaredAreas = function (ship) {
        if (!ship || !ship.systems) return;
        if (!gamedata.isMyorMyTeamShip(ship)) return;

        var icon = this.shipIconContainer.getByShip(ship);
        if (!icon) return;

        ship.systems.forEach(function (system) {
            if (typeof system.getDeclaredArea !== 'function') return;

            var spec = system.getDeclaredArea();

            if (spec) icon.showDeclaredArea(system, spec);
            else icon.removeDeclaredArea(system);
        });
    };

    PhaseStrategy.prototype.syncAllDeclaredAreas = function () {
        this.shipIconContainer.getArray().forEach(function (icon) {
            this.syncDeclaredAreas(icon.ship);
        }, this);
    };

    PhaseStrategy.prototype.onScrollToShip = function (payload) {
        var icon = this.shipIconContainer.getById(payload.shipId)
        //A ship with no icon has no position to scroll to (docked flight, undeployed,
        //stale id). Callers guard for this, but a stray id must be a no-op, not a throw.
        if (!icon) {
            return;
        }
        if (shipManager.shouldBeHidden(icon.ship)) {
            return;
        }

        window.webglScene.moveCameraTo(icon.getPosition())

        //Opt-in via payload.select, for callers where clicking a ship's name means "take me
        //to it so I can do something about it" - the commit-dialog ship links. setSelectedShip
        //is the primitive every selection path funnels through (selectShip, onShipRightClicked,
        //selectShipInDeploymentPhase), so the previous ship is deselected, the weapon list and
        //EW display follow, and each phase strategy stays consistent.
        //
        //canSelectShip carries each phase's own rule (own ships; in movement, only ships
        //active in the current step), so a link click can neither declare a fire order nor
        //jump the movement sequence. Ships that shouldBeHidden already returned above, and a
        //ship with no icon never gets here - setSelectedShip would throw on
        //getByShip(...).setSelected for both.
        if (payload.select && this.canSelectShip(icon.ship)) {
            this.setSelectedShip(icon.ship);
        }
    }

    PhaseStrategy.prototype.onScrollEvent = function (payload) {
        this.onScrollCallbacks = this.onScrollCallbacks.filter(function (callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onZoomEvent = function (payload) {
        this.onZoomCallbacks = this.onZoomCallbacks.filter(function (callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onClickEvent = function (payload) {
        var icons = getInterestingStuffInPosition.call(this, payload, this.gamedata.turn);

        this.onClickCallbacks = this.onClickCallbacks.filter(function (callback) {
            return callback();
        });

        if (icons.length > 1) {
            this.onShipsClicked(icons.map(function (icon) {
                return this.gamedata.getShip(icon.shipId);
            }, this), payload);
        } else if (icons.length === 1) {
            if (payload.button !== 0 && payload.button !== undefined) {
                this.onShipRightClicked(this.gamedata.getShip(icons[0].shipId), payload);
            } else {
                this.onShipClicked(this.gamedata.getShip(icons[0].shipId), payload);
            }
        } else {
            this.onHexClicked(payload);
        }
    };

    PhaseStrategy.prototype.onMouseDownEvent = function (payload) {
        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }
    };

    PhaseStrategy.prototype.onHexClicked = function (payload) {
        if (gamedata.showLoS) {
            if (payload.button == 2) { //Right click, just clear and reset to this.selectedShip
                this._startHexRuler = null; //reset.
                mathlib.clearLosSprite();
            } else {
                this._startHexRuler = null; //reset start point on any other type of click
                mathlib.clearLosSprite();
                this._startHexRuler = payload.hex;
            }
        }
    };

    PhaseStrategy.prototype.onShipsClicked = function (ships, payload) {

        // Filter out ships that are not yours or your team's + are stealth ships + not detected + not deployed yet.
        const filteredShips = ships.filter(ship =>
            !(shipManager.shouldBeHidden(ship))
        );

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }

        /* ⭐⭐ NOTHING VISIBLE LEFT IN THE HEX MEANS THE CLICK BELONGS TO THE HEX (bug report
           2026-08-29, game 4321). Every unit here filtered out as hidden, so as far as the player
           can see they clicked empty space - and until this branch existed the click was simply
           swallowed and NOTHING happened, with no message.

           ⚠️ THE RULE IS onShipClicked's, VERBATIM, and the bug was that only IT had it. A hex with
           exactly ONE hidden unit in it routes through onShipClicked, whose first branch already
           says "hidden + a hex weapon selected -> target the hex". A hex with TWO or more arrived
           here instead, where filteredShips.length === 0 fell into the else below and was refused
           by its own `length > 0` guard. One hidden ship: works. Two: silently does nothing.

           A JUMP POINT IS WHERE THAT RELIABLY BITES, which is how it surfaced: units that fly into
           a vortex are removed at the END of the Movement phase but stay in the icon container at
           the vortex hex for the rest of the turn (destroyed, and hidden by shouldBeHidden), so by
           the Firing phase a used jump point is a stack of two, three or four invisible wrecks -
           and a Vortex Disruptor shot at that hex could never be declared. Arriving reinforcements
           stack on a blue exit for the same reason. It is NOT a vortex-specific fix: any hex weapon
           aimed at any hex holding several undetected stealth ships had the same hole.

           Left-click only, and no fall-through to onHexClicked: a RIGHT click on a hidden stack
           still does nothing (it opens a ship window, and there is no ship to open), and routing
           the whole click to onHexClicked would hand the Deployment and Movement phases a hex the
           player cannot see the contents of. Mirroring the one-ship rule changes nothing except the
           case that was broken. */
        if (filteredShips.length === 0) {
            if (payload.button !== 2 && this.selectedShip && weaponManager.hasHexWeaponsSelected()) {
                weaponManager.targetHex(this.selectedShip, payload.hex);
            }
            return;
        }

        if (filteredShips.length === 1) { //only one ship, we have to pretend the stealth ship(s) aren't on same hex!
            var ship = filteredShips[0];
            if (payload.button === 2) {
                this.onShipRightClicked(ship);
            } else {
                this.onShipClicked(ship, payload);
            }
        } else {
            if (filteredShips.length > 0 && !gamedata.showLoS) this.showSelectFromShips(filteredShips, payload); //More than 1, but not 0.  Prevents graphic from appearing and indicating where hidden ships are.
        }
    };

    PhaseStrategy.prototype.onShipRightClicked = function (ship) {

        if (shipManager.shouldBeHidden(ship)) return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025

        if (this.gamedata.isMyShip(ship)) {
            this.setSelectedShip(ship);
        }
        //Needs to have a separate method here, since this count as a hex clicked apparently.
        if (gamedata.showLoS) {
            this._startHexRuler = null; //reset start point on right-clicking ship
            mathlib.clearLosSprite();
        }

        this.shipWindowManager.open(ship);
    };

    PhaseStrategy.prototype.onShipClicked = function (ship, payload) {//30 June 2024 - DK - Added for Ally targeting.
        if (shipManager.shouldBeHidden(ship)){
            if(weaponManager.hasHexWeaponsSelected()){
                weaponManager.targetHex(this.selectedShip, payload.hex);
                return;
            }else{
                return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025
            }    
        }

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }

        if (gamedata.rules && gamedata.rules.friendlyFire === 1) {
            if (this.gamedata.isMyShip(ship)) {
                this.selectShip(ship, payload);
            } else {
                this.targetShip(ship, payload);
            }
        } else {
            if (this.gamedata.isMyShip(ship) && (!this.gamedata.canTargetAlly(ship))) {
                this.selectShip(ship, payload);
            } else {
                this.targetShip(ship, payload);
            }
        }
    };

    PhaseStrategy.prototype.selectShip = function (ship, payload) {
        this.setSelectedShip(ship);
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        if (!gamedata.showLoS) {
            var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn); //Don't show tooltip if ruler is on, as it blocks vision
            this.showShipTooltip(ship, payload, menu, false);
        }
    };

    //Whether `ship` may become the selected ship right now. The base rule is the one
    //onShipClicked applies to a map click: your own ships select, anything else routes to
    //targetShip instead. Phases with a tighter rule override this (MovementPhaseStrategy).
    //Exists so programmatic selection - onScrollToShip, which has no map click to push
    //through onShipClicked - obeys the same rule the player would hit on the board.
    PhaseStrategy.prototype.canSelectShip = function (ship) {
        return this.gamedata.isMyShip(ship);
    };

    PhaseStrategy.prototype.setSelectedShip = function (ship) {
        if ($(".confirm").length > 0) return;

        if (this.selectedShip) {
            //RE-selecting the ship that is already selected keeps its weapon selection. Clicking
            //your own ship is how you open its tooltip, and the tear-down/rebuild below would
            //otherwise unselect every weapon on the way through - so the INCOMING list opened with
            //nothing selected and manual interception could not be declared without picking the
            //weapons again (user report 2026-08-19). Only the weapon-unselect is skipped; the icon,
            //weapon list, movement UI and EW all still tear down and rebuild exactly as before.
            this.deselectShip(this.selectedShip, this.selectedShip === ship);
        }

        this.selectedShip = ship;
        this.shipIconContainer.getByShip(ship).setSelected(true, true);
        this.showAppropriateEW();

        if (this.shipTooltip) {
            this.shipTooltip.update(ship, this.selectedShip);
        }

        this.uiManager.showWeaponList({ ship: ship, gamePhase: gamedata.gamephase });

        //Mark, flash and scroll to this ship's row in the INFO tab's fleet list
        //(LOG_PANEL_REDESIGN_PLAN.md Stage 3). Guarded because the fleet list is a
        //game.php-only surface and this file is shared; it no-ops when the tab is hidden.
        if (window.fleetListManager) fleetListManager.revealShipRow(ship);
    };

    /* keepWeapons: leave gamedata.selectedSystems alone. Passed ONLY by setSelectedShip when the
       ship being selected is the one already selected - see the note there. Every other caller
       omits it and gets the original clear-everything behaviour. */
    PhaseStrategy.prototype.deselectShip = function (ship, keepWeapons) {
        this.shipIconContainer.getById(ship.id).setSelected(false);

        if (!keepWeapons) {
            gamedata.selectedSystems.slice(0).forEach(function (selected) {
                weaponManager.unSelectWeapon(this.selectedShip, selected);
            }, this);
        }

        this.selectedShip = null;
        this.uiManager.hideWeaponList();
        if (gamedata.showLoS) mathlib.clearLosSprite();
    };

    PhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    /*
    PhaseStrategy.prototype.targetShip = function (ship) {
        if (this.targetedShip) {
            this.untargetShip(this.targetedShip);
        }
        this.targetedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.untargetShip = function (ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);
        this.targetedShip = null;
    };
    */

    PhaseStrategy.prototype.onMouseMoveEvent = function (payload) {
        var icons = getInterestingStuffInPosition.call(this, payload, this.gamedata.turn);

        // Initialize _lastHoveredHex if null
        if (!this._lastHoveredHex) this._lastHoveredHex = null;

        if (gamedata.showLoS) {
            // Only update _lastHoveredHex & showLoS if no icons (empty hex hover)
            if (icons.length === 0) {
                // Check if hex changed since last hover
                if (
                    !this._lastHoveredHex ||
                    this._lastHoveredHex.q !== payload.hex.q ||
                    this._lastHoveredHex.r !== payload.hex.r
                ) {
                    // Update with a copy of hex coords (avoid referencing the same object)
                    this._lastHoveredHex = { q: payload.hex.q, r: payload.hex.r };

                    mathlib.showLoS(this._startHexRuler, payload.hex);

                }
            } else {
                // If hovering a ship, reset _lastHoveredHex so next hex hover triggers showLoS
                this._lastHoveredHex = null;
            }
        }

        function doMouseOut() {
            if (this.currentlyMouseOveredIds) {
                this.currentlyMouseOveredIds = null;
            }

            this.onMouseOutCallbacks = this.onMouseOutCallbacks.filter(function (callback) {
                callback();
                return false;
            });

            this.onMouseOutShips(gamedata.ships, payload);

            // Reset hovered hex to force rerun on next move
            this._lastHoveredHex = null;
        }

        if (icons.length === 0 && this.currentlyMouseOveredIds !== null) {
            doMouseOut.call(this);
            return;
        } else if (icons.length === 0) {
            return;
        }

        var mouseOverIds = icons.reduce(function (value, icon) {
            return value + icon.shipId;
        }, '');

        if (mouseOverIds === this.currentlyMouseOveredIds) {
            return;
        }

        doMouseOut.call(this);

        this.currentlyMouseOveredIds = mouseOverIds;

        var ships = icons.map(function (icon) {
            return this.gamedata.getShip(icon.shipId);
        }, this);
        if (ships.length > 1) {
            this.onMouseOverShips(ships, payload);
        } else {
            this.onMouseOverShip(ships[0], payload);
        }
    };

    PhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        this.showAppropriateHighlight();
        this.showAppropriateEW();

        if (window.LosSprite) mathlib.clearLosSprite();

        //Both of these mutate the scene (mesh z, facing sprites, EW lines) outside the
        //animation list, so the render-loop invariant applies. A hover that ORIGINATES ON
        //THE CANVAS is masked: webglScene.mouseMove already requested a render for the same
        //event. A hover driven from the DOM — the hex picker's rows — produces no canvas
        //event at all, so without this the icon never actually redraws and the raise and
        //the heading/facing sprites simply never appear.
        if (window.webglScene) window.webglScene.requestRender();
    };

    PhaseStrategy.prototype.onMouseOverShips = function (ships, payload) {
        // Filter out ships that are not visible or shouldn't show tooltips
        if (gamedata.showLoS) mathlib.showLoS(this._startHexRuler, payload.hex)

        const visibleShips = ships.filter(ship => {
            if (shipManager.shouldBeHidden(ship)) return false;  //Enemy, stealth equipped and undetected, or not deployed yet - DK May 2025
            return true;
        });

        if (visibleShips.length === 0) return;

        if (this.shipTooltip && this.shipTooltip.isForAnyOf(visibleShips)) {
            return;
        }

        if (this.shipTooltip && this.shipTooltip.menu) {
            return;
        }

        if (!gamedata.showLoS) this.showShipTooltip(visibleShips, payload, null, true);
    };

    PhaseStrategy.prototype.onMouseOverShip = function (ship, payload) {

        if (gamedata.showLoS) mathlib.showLoS(this._startHexRuler, payload.hex);

        if (shipManager.shouldBeHidden(ship)) return;  //Enemy, stealth equipped and undetected, or not deployed yet - DK May 2025

        this.showAppropriateHighlight();
        this.showAppropriateEW();

        //trying to allow hex targeting more easily where there are friendly units located.
        var menu = null;
        var hasHex = weaponManager.hasHexWeaponsSelected()
        if (hasHex && this.gamedata.isMyorMyTeamShip(ship)) {
            if (this.gamedata.gamephase == 3) {
                menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
            }
            if (this.gamedata.gamephase == 1) {
                var position = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());
                menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position);
            }
        }

        var icon = this.shipIconContainer.getById(ship.id);
        if (!this.shipTooltip || !this.shipTooltip.menu) {
            //this.showShipTooltip(ship, payload, null, true);            
            if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !gamedata.showLoS) this.showShipTooltip(ship, payload, menu, true);
        }

        if (this.shipTooltip && this.shipTooltip.ships.includes(ship) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(ship, this.selectedShip);
        }



        this.showShipEW(ship);
        icon.showSideSprite(true);
        icon.showBDEW();
        icon.showMDEW();
        icon.setHighlighted(true);

        //See the note in onMouseOutShips: setHighlighted raises the icon out of the pile
        //and shows its prow/movement sprites, which is a scene mutation and therefore has
        //to ask for a frame. Canvas-driven hovers get one for free from
        //webglScene.mouseMove; DOM-driven ones (the hex picker) do not.
        if (window.webglScene) window.webglScene.requestRender();
    };

    PhaseStrategy.prototype.showShipEW = function (ship) {
        this.shipIconContainer.getByShip(ship).showEW();
        this.ewIconContainer.showForShip(ship);
    };

    PhaseStrategy.prototype.hideShipEW = function (ship) {
        this.shipIconContainer.getByShip(ship).hideEW();
        this.ewIconContainer.hide();
    };

    PhaseStrategy.prototype.showShipTooltip = function (ships, payload, menu, hide, ballisticsMenu) {

        // Suppress hover tooltip while the SelectFromShips picker is open — they show overlapping info.
        // Click-driven tooltips (hide=false) must still appear; the picker routes ship clicks to onShipClicked,
        // which needs the persistent targeting tooltip.
        /*if (this.selectFromShips && hide) {
            return;
        }
        */

        if (this.shipTooltip) {
            this.hideShipTooltip(this.shipTooltip)
        }

        ships = [].concat(ships);

        var position = payload.hex;
        if (ships.length === 1) {
            position = this.shipIconContainer.getByShip(ships[0]).getPosition();
        }

        if (!ballisticsMenu) {
            ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, false);
        }

        var shipTooltip = new window.ShipTooltip(this.selectedShip, ships, position, shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip), menu, payload.hex, ballisticsMenu);

        this.shipTooltip = shipTooltip;
        //What the tooltip's own X presses. It has to route back through hideShipTooltip rather
        //than the tooltip's destroy(), or this.shipTooltip would keep pointing at a tooltip that
        //is no longer in the DOM and onMouseOverShip's `!this.shipTooltip.menu` guard would then
        //block every later tooltip. Same bound call the click / mouse-out callbacks below use, so
        //dismissing by X and dismissing by clicking the map are literally the same code path.
        shipTooltip.onClose = this.hideShipTooltip.bind(this, shipTooltip);
        this.onClickCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));

        if (hide) {
            this.onMouseOutCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));
        }

        // Mobile/Tablet specific logic: Highlight ships (shows direction sprites) when tooltip is shown
        // Only if not 'hide' (i.e. persistent tooltip from click/tap), to avoid highlighting on spurious touches/scrolling
        if (!hide && window.matchMedia("(pointer: coarse)").matches) {
            ships.forEach(ship => {
                this.shipIconContainer.getByShip(ship).setHighlighted(true);
            });
        }
    };

    PhaseStrategy.prototype.showSelectFromShips = function (ships, payload) {
        var selectFromShips = new window.SelectFromShips(this.selectedShip, ships, payload, this)
        this.selectFromShips = selectFromShips;
        this.onClickCallbacks.push(this.hideSelectFromShips.bind(this, selectFromShips));
    };

    PhaseStrategy.prototype.hideShipTooltip = function (shipTooltip) {
        if (this.shipTooltip && this.shipTooltip === shipTooltip) {
            this.shipTooltip.destroy();
            this.shipTooltip = null;

            // Mobile/Tablet specific logic: Remove highlights when tooltip is hidden
            if (window.matchMedia("(pointer: coarse)").matches) {
                this.showAppropriateHighlight();
            }
        }
    };

    PhaseStrategy.prototype.hideSelectFromShips = function (selectFromShips) {
        if (this.selectFromShips && this.selectFromShips === selectFromShips) {
            this.selectFromShips.destroy();
            this.selectFromShips = null;
        }
    };

    PhaseStrategy.prototype.repositionSelectFromShips = function () {
        if (this.selectFromShips) {
            this.selectFromShips.reposition();
        }

        return true;
    };


    PhaseStrategy.prototype.repositionTooltip = function () {
        if (this.shipTooltip) {
            this.shipTooltip.reposition();
        }

        return true;
    };

    PhaseStrategy.prototype.positionMovementUI = function () {
        if (!this.movementUI) {
            return true;
        }

        var pos = this.coordinateConverter.fromGameToViewPort(this.movementUI.icon.getPosition());
        var heading = mathlib.hexFacingToAngle(this.movementUI.icon.getLastMovement().heading);

        UI.shipMovement.reposition(pos, heading);

        return true;
    };

    /* JUMP_POINTS_PLAN.md STAGE 2b - the vortex facing control.

       Raised by weaponManager.queueJumpPointOrder when a Jump Engine is aimed at a hex. Nothing is
       committed yet: this shows the control, anchors it to the target hex, and registers the
       click-away discard. The OK button calls the payload's own onConfirm, which is what actually
       builds the FireOrder.

       The one-shot discard rides onClickCallbacks - the same list showShipTooltip and
       showSelectFromShips use. Note the ORDER inside onClickEvent: the callback list is filtered
       and run BEFORE the click is dispatched to onHexClicked, so a click that opens a NEW
       declaration first discards the pending one, and the callback pushed here lands on the fresh
       array and survives to the next click. */
    PhaseStrategy.prototype.onVortexFacingRequested = function (payload) {
        /* CLOSE THE TOOLTIP THAT LAUNCHED THIS, exactly as onGateSignalRequested does below and for
           the same reason (user request 2026-08-24). The declaration can be started from the "Target
           selected weapons on hexagon" button in a ship tooltip, and that tooltip is anchored to a
           unit standing on or beside the very hex the facing ring lays itself out around - so it sits
           on top of the turn arrows and the OK button and the ring cannot be worked. The tooltip
           swallows its own mousedown/mouseup, so the click-away discard never fires for it here. */
        this.hideShipTooltip(this.shipTooltip);

        UI.vortexFacing.open(payload);
        this.positionVortexFacingUI();
        this.onClickCallbacks.push(this.hideVortexFacingUI.bind(this, payload));
    };

    //Token-matched (like hideShipTooltip): by the time this fires the transaction may already have
    //been closed by OK, or replaced by a newer one that must not be torn down by the old click.
    //Returns undefined so onClickCallbacks filters it out - it is a one-shot.
    PhaseStrategy.prototype.hideVortexFacingUI = function (pending) {
        if (UI.vortexFacing.isOpenFor(pending)) {
            UI.vortexFacing.close();
        }
    };

    PhaseStrategy.prototype.positionVortexFacingUI = function () {
        if (!UI.vortexFacing.isOpen()) {
            return true;
        }

        UI.vortexFacing.reposition(this.coordinateConverter.fromGameToViewPort(UI.vortexFacing.getPosition()));

        return true;
    };

    /* JUMP_GATES_PLAN.md STAGE 3 - the fixed jump gate signal panel.

       The same three-part wiring the facing control above uses, and for the same reasons: show the
       panel, anchor it to the gate's hex, and register a ONE-SHOT click-away discard on
       onClickCallbacks (filtered and run BEFORE the click reaches onHexClicked, so a click that
       opens a new panel first discards the pending one).

       The tooltip that launched it is closed here rather than left standing: the button lives in
       the gate's own Initial Orders menu, which is drawn on the very hex this panel anchors to, so
       leaving it up would put two overlapping controls on one spot. */
    PhaseStrategy.prototype.onGateSignalRequested = function (payload) {
        this.hideShipTooltip(this.shipTooltip);

        UI.gateSignal.open(payload);
        this.positionGateSignalUI();
        this.onClickCallbacks.push(this.hideGateSignalUI.bind(this, payload));
    };

    //Token-matched, exactly as hideVortexFacingUI is: by the time this fires the transaction may
    //have been closed by SIGNAL or replaced by a newer one that must not be torn down by the old
    //click. Returns undefined so onClickCallbacks filters it out - it is a one-shot.
    PhaseStrategy.prototype.hideGateSignalUI = function (pending) {
        if (UI.gateSignal.isOpenFor(pending)) {
            UI.gateSignal.close();
        }
    };

    PhaseStrategy.prototype.positionGateSignalUI = function () {
        if (!UI.gateSignal.isOpen()) {
            return true;
        }

        UI.gateSignal.reposition(this.coordinateConverter.fromGameToViewPort(UI.gateSignal.getPosition()));

        return true;
    };

    PhaseStrategy.prototype.redrawMovementUI = function () {

        if (gamedata.waiting) return;

        if (!this.selectedShip) {
            return;
        }

        if (this.movementUI && this.movementUI.ship.movement.some(function (movement) {
            return !movement.commit;
        })) {
            this.hideMovementUI();
            return;
        }

        this.drawMovementUI(this.selectedShip);
    };

    PhaseStrategy.prototype.drawMovementUI = function (ship) {
        if (gamedata.waiting) return;

        var drawn = UI.shipMovement.drawShipMovementUI(ship, new ShipMovementCallbacks(ship, this.onShipMovementChanged.bind(this)));

        if (drawn === false) {
            this.movementUI = null;
            return;
        }

        this.movementUI = {
            element: UI.shipMovement.uiElement,
            ship: ship,
            icon: this.shipIconContainer.getByShip(ship),
            position: null
        };

        UI.shipMovement.show();
        this.positionMovementUI();
    };

    PhaseStrategy.prototype.hideMovementUI = function () {
        UI.shipMovement.hide();
        this.movementUI = null;
    };

    PhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function () {
        var ship = gamedata.getFirstFriendlyShip();
        //TODO: what about active ship?
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    PhaseStrategy.prototype.done = function () {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };

    PhaseStrategy.prototype.onSystemMouseOver = function (payload) {
        var ship = payload.ship;
        var system = payload.system;
        var element = payload.element;
        var showInfo = payload.showInfo !== false;

        if (showInfo) {
            this.showSystemInfo(ship, system, element, false);
        } else {
            this.hideSystemInfo();
        }

        //Ship (the hex-stack hover pseudo-system) has no arc of its own, but it still ENDS the
        //previous hover - so record the miss and let refreshSystemArcs redraw whatever is selected.
        this.hoveredArcSystem = (system instanceof Ship) ? null : { ship: ship, system: system };
        this.refreshSystemArcs();
    };

    PhaseStrategy.prototype.onSystemMouseOut = function () {
        this.hoveredArcSystem = null;
        this.refreshSystemArcs();

        this.hideSystemInfo();
    };

    /* Structure arc indicator (STRUCTURE_ARCS_PLAN.md): the ship window's section health bars
       raise their OWN event rather than SystemMouseOver, so hovering a bar draws the section's
       facing wedge without also opening the system info tooltip (a structure has nothing useful
       to say there) and without disturbing the weapon-arc show/hide. */
    PhaseStrategy.prototype.onStructureMouseOver = function (payload) {
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideStructureArcs();
        });

        var icon = this.shipIconContainer.getByShip(payload.ship);
        if (!icon) return;

        icon.showStructureArc(payload.ship, payload.structure);
    };

    PhaseStrategy.prototype.onStructureMouseOut = function () {
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideStructureArcs();
        });
    };

    /* Ship-window EW panel hover (2026-07-30): hovering the BDEW or Detect Mines row raises that
       ship's blanket / mine-detection area, the same overlay the map's own ship hover draws.

       Deliberately only sweeps what it raised itself: if the overlay was already up - the pointer
       came off the ship on the map, or a show-EW key is held - mouse-out leaves it alone instead of
       clearing a display this hover never created. showBDEW/showMDEW are no-ops when the ship has
       no rating (or, for mines, when none are on the board), hence testing the sprite afterwards
       rather than assuming one appeared. */
    PhaseStrategy.prototype.onEwRangeHover = function (payload) {
        var icon = this.shipIconContainer.getById(payload.shipId);
        if (!icon) return;

        var mines = payload.type === 'MDEW';
        var sprite = mines ? 'MDEWSprite' : 'BDEWSprite';

        if (payload.active) {
            if (icon[sprite]) return; //already displayed by something else - not ours to sweep
            if (shipManager.shouldBeHidden(icon.ship)) return; //never draw an area at a hidden ship's position

            if (mines) {
                icon.showMDEW();
            } else {
                icon.showBDEW();
            }

            this.ewRangeHover = icon[sprite] ? { icon: icon, type: payload.type } : null;
            return;
        }

        if (!this.ewRangeHover || this.ewRangeHover.icon !== icon || this.ewRangeHover.type !== payload.type) return;
        this.ewRangeHover = null;

        if (mines) {
            icon.hideMDEW();
        } else {
            icon.hideBDEW();
        }
    };

    PhaseStrategy.prototype.createReplayUI = function (gamedata) {
        this.replayUI = new ReplayUI().activate();
    };

    PhaseStrategy.prototype.changeAnimationStrategy = function (newAnimationStartegy) {
        this.animationStrategy && this.animationStrategy.deactivate();
        this.animationStrategy = newAnimationStartegy;
        this.animationStrategy.activate();
    };

    function getInterestingStuffInPosition(payload, turn) {
        return this.shipIconContainer.getIconsInProximity(payload).filter(function (icon) {
            var turnDestroyed = shipManager.getTurnDestroyed(icon.ship);
            if (turnDestroyed !== null && turnDestroyed < turn) return false;
            //Stage 7 (Hangar Ops): a flight queued for deployment-phase dock has
            //its icon hidden but the icon object remains in the container with
            //its old position. Without filtering it here, clicks on the vacated
            //hex resolve to the hidden flight and get dropped by
            //shouldBeHidden()-checks downstream — preventing other ships from
            //being deployed to the same hex.
            if (icon.ship && icon.ship.pendingDeployDock) return false;
            //LCV Rails: same for an LCV queued to deploy-dock onto a rail.
            if (icon.ship && icon.ship.pendingLcvDeployDock) return false;
            /* A JUMP VORTEX IS A MARKER, NOT A UNIT (JUMP_POINTS_PLAN.md Stage 4 feedback).
               It is unselectable terrain with nothing to target and nothing to open, but while it
               sat in this sweep it shared a hex with whatever flew into it and broke both halves
               of that hex's interaction: a click became a two-icon stack, so selecting your own
               ship in a vortex hex went through the hex picker with the vortex listed in it; and a
               hover flipped between the two icons (getIconsInProximity returns only the CLOSEST
               when zoomed past ~0.33), and every flip back to the ship ran ShipTooltip.update,
               which empties .buttons and rebuilds the menu - so the Jump Out button was torn down
               and recreated under the cursor. Dropping the vortex here fixes both at once: the
               hex reads as holding exactly the units that are really in it. */
            /* EITHER KIND (REINFORCEMENTS_PLAN.md §4 Stage 3). This is the strongest case on the
               list for matching an exit too: arriving reinforcements deliberately STACK in
               the exit hex, so if it stayed in this sweep every arrival-hex click would go
               through the stack picker with the vortex listed in it, and every hover would tear
               down and rebuild the tooltip - exactly the two symptoms described above, on the
               one hex where several units are guaranteed to be sharing space. */
            if (icon.ship && shipManager.movement.isAnyJumpVortex(icon.ship)) return false;
            return true;
        });
    }

    PhaseStrategy.prototype.setPhaseHeader = function (name, shipName) {

        if (name === false) {
            jQuery("#phaseheader").hide();
            return;
        }

        if (!shipName) {
            shipName = "";
        }

        $("#phaseheader .turn.value").html("TURN: " + this.gamedata.turn + ",");
        $("#phaseheader .phase.value").html(name);
        $("#phaseheader .activeship.value").html(shipName);
        $("#phaseheader").show();
    };

    PhaseStrategy.prototype.onShipEwChanged = function (payload) {
        var ship = payload.ship;

        if (this.shipTooltip) {
            this.shipTooltip.update(ship, this.selectedShip);
        }

        this.shipIconContainer.getByShip(ship).consumeEW(ship);
        this.ewIconContainer.updateForShip(ship);
        this.shipWindowManager.update();
    };

    PhaseStrategy.prototype.onShipMovementChanged = function (payload) {
        var ship = payload.ship;
        this.shipIconContainer.getByShip(ship).consumeMovement(ship.movement);
        if (this.animationStrategy) {
            this.animationStrategy.shipMovementChanged(ship);
        }
        this.ballisticIconContainer.updateLinesForShip(ship, this.shipIconContainer);
        this.redrawMovementUI(ship);

        // Mirror movement to attached units (e.g. pods) - DK 04/26
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            for (var attachedId in ship.hasAttached) {
                var location = ship.hasAttached[attachedId];
                var attachedShip = gamedata.getShip(attachedId);

                if (!attachedShip || attachedShip.detached || shipManager.isDestroyed(attachedShip)) continue;

                var newMovements = [];
                // 1. Maintain the pod's movement history for previous turns
                for (var m = 0; m < attachedShip.movement.length; m++) {
                    if (attachedShip.movement[m].turn < gamedata.turn) {
                        newMovements.push(attachedShip.movement[m]);
                    }
                }

                // Prefer the precise entry-side offset recorded at attach time; fall back to
                // the location-derived offset for in-progress games attached before this change.
                var locOffset = (ship.hasAttachedFacing && ship.hasAttachedFacing[attachedId] !== undefined)
                    ? ship.hasAttachedFacing[attachedId]
                    : shipManager.movement.getAttachedFacingOffset(location);
                var facingAdjustment = shipManager.movement.isRolled(ship) ? 3 : 0;

                // 2. Clone parent movements for the CURRENT turn
                for (var i = 0; i < ship.movement.length; i++) {
                    var move = ship.movement[i];
                    if (move.turn != gamedata.turn) continue;

                    var attachedMove = JSON.parse(JSON.stringify(move));
                    attachedMove.id = -1;
                    attachedMove.type = "attached";
                    attachedMove.facing = mathlib.addToHexFacing(move.facing, locOffset + facingAdjustment);
                    newMovements.push(attachedMove);
                }

                attachedShip.movement = newMovements;

                // Refresh the pod's visual state
                this.shipIconContainer.getByShip(attachedShip).consumeMovement(attachedShip.movement);
                if (this.animationStrategy) {
                    this.animationStrategy.shipMovementChanged(attachedShip);
                }
                this.ballisticIconContainer.updateLinesForShip(attachedShip, this.shipIconContainer);
            }
        }

        // Idle render-loop gating (perf #2): consumeMovement above mutates the icon's
        // facing/position in the THREE scene outside the animation list, so we must kick
        // the render budget or it won't paint until the next input. Surfaced by combat
        // pivots in the Fire phase, where the icon didn't reface until the mouse moved.
        if (window.webglScene && window.webglScene.requestRender) {
            window.webglScene.requestRender();
        }
    };

    PhaseStrategy.prototype.onShowAllEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships, payload);
    };

    PhaseStrategy.prototype.onShowFriendlyEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowEnemyEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.showAppropriateEW = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.hideEW();
            icon.hideBDEW();
            icon.hideMDEW();
        });

        this.ewIconContainer.hide();
        if (this.selectedShip) {
            this.showShipEW(this.selectedShip);
        }
    }

    PhaseStrategy.prototype.showAppropriateHighlight = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.showSideSprite(false);
            icon.setHighlighted(false);
        })

        if (this.selectedShip) {
            this.shipIconContainer.getByShip(this.selectedShip).setSelected(true, true);
        }
    }

    function showGlobalEW(ships, payload) {
        if (payload.up) {
            this.showAppropriateEW();
        } else {
            ships.forEach(function (ship) {
                var icon = this.shipIconContainer.getById(ship.id);
                this.ewIconContainer.showByShip(ship);
                icon.showEW();
                icon.showBDEW();
                icon.showMDEW();
            }, this);
        }
    }

    PhaseStrategy.prototype.onSystemDataChanged = function (payload) {
        var ship = payload.ship;
        var system = payload.system;

        //Declaring or withdrawing an order is a SystemDataChanged, so this is where a declared-area
        //overlay appears and disappears in response to the player - see syncDeclaredAreas.
        this.syncDeclaredAreas(ship);

        if (this.selectedShip === ship) {
            this.uiManager.showWeaponList({ ship: ship, gamePhase: gamedata.gamephase })
        }

        if (this.systemInfoState) {
            this.showSystemInfo(ship, this.systemInfoState.system, this.systemInfoState.element, this.systemInfoState.menu);
        }

        //Keep an open tooltip in step, the way onShipEwChanged does: toggling a Shading Field /
        //Cloaking Device in the Pre-Turn phase moves the tooltip's Detected/Undetected line
        //(shipManager.getStealthToggleForecast) and it would otherwise hold the old answer until
        //the pointer left the ship and came back. Deliberately narrow - a forecast only exists for
        //an own stealth ship during gamephase -1 - so the many other SystemDataChanged callers
        //(weapon selection, power) don't start rebuilding a hovered tooltip out from under
        //whatever the player is clicking in it.
        if (this.shipTooltip && ship && ship.trueStealth
            && this.shipTooltip.ships.length === 1 && this.shipTooltip.ships.includes(ship)
            && shipManager.getStealthToggleForecast(ship) !== null) {
            this.shipTooltip.update(ship, this.selectedShip);
        }

        //Manual interception: every clickable hit chance in the INCOMING list is computed against
        //the CURRENT weapon selection, so selecting or unselecting an interceptor has to re-render
        //them - otherwise the row goes on answering with the selection it was BUILT with, and
        //reports "No interceptor selected" at a weapon the player can plainly see is selected
        //(user report 2026-08-19). Both paths land here, and both now arrive AFTER
        //gamedata.selectedSystems has actually changed: selectWeapon and unSelectWeapon each fire
        //SystemDataChanged once their push/splice is done. (They did not always - see the ordering
        //note in weaponManager.selectWeapon for why the WeaponSelected event cannot carry this.)
        //
        //The tooltip's other two selection-dependent halves are refreshed the same way (user
        //report 2026-08-24). The TARGETING list is the selection's own arcs and hit chances, and
        //the button row's conditions (hasWeaponsSelected, hasHexWeaponsSelected, FFWeaponSelected,
        //hasSplitWeaponFiringOrder) each ask what is selected - so emptying the selection has to
        //take "Target Weapons" and "Remove a Firing Order" away with it, and refilling it has to
        //bring them back, rather than leaving buttons that do nothing when clicked.
        //
        //None of this goes through ShipTooltip.update(), which would rebuild the name and re-run
        //the whole single-ship body; these three redraw only what actually depends on the
        //selection. Weapon selection is driven from the weapon list and the ship window, never
        //from inside the tooltip, so nothing here moves under the pointer that put it there - and
        //a button that redraws itself from its OWN click is already the norm (see the note on
        //ShipTooltip.refreshButtons).
        //
        //Both are unphased on purpose - they render whatever createForSingleShip would render
        //right now, and hide themselves when nothing is selected - whereas the INCOMING rebuild
        //below stays pinned to the Firing phase, where interception may be declared.
        if (this.shipTooltip && typeof this.shipTooltip.refreshTargeting === 'function') {
            this.shipTooltip.refreshTargeting();
        }

        if (this.shipTooltip && typeof this.shipTooltip.refreshButtons === 'function') {
            this.shipTooltip.refreshButtons();
        }

        if (gamedata.gamephase === 3 && this.shipTooltip && this.shipTooltip.ballisticsMenu
            && typeof this.shipTooltip.ballisticsMenu.refresh === 'function') {
            this.shipTooltip.ballisticsMenu.refresh();
        }

        if (system
            && (system.ballistic
                || system.hextarget //same for direct fire hextarget weapons - they use ballistic highlight...
                || system.canSplitShots //same for weapon that split shots, ballistic icons used to track these.
            )
        ) {
            this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        }

        //Selecting AND unselecting a weapon both land here - selectWeapon and unSelectWeapon each
        //fire SystemDataChanged once the selection array has changed - so this is the seam that
        //puts a selected system's arc up and takes it down again. See refreshSystemArcs; it
        //keeps the hovered arc, so running here while the pointer is still on the icon that was
        //just clicked changes nothing.
        this.refreshSystemArcs();

        this.shipWindowManager.update();
    }

    PhaseStrategy.prototype.onSystemClicked = function (payload) {
        var ship = payload.ship;
        var system = payload.system;
        var element = payload.element;

        if (this.systemInfoState && this.systemInfoState.system === system && this.systemInfoState.menu) {
            this.hideSystemInfo(true);
            return;
        }

        if (shipManager.getTurnDeployed(ship) > gamedata.turn) return;

        this.showSystemInfo(ship, system, element, true);
        PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship, system: system });
    };

    PhaseStrategy.prototype.onHexTargeted = function (payload) {
        this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.shipWindowManager.update();

        if (this.selectedShip === payload.shooter) {
            this.uiManager.showWeaponList({ ship: payload.shooter, gamePhase: gamedata.gamephase })
        }
    };

    PhaseStrategy.prototype.onShipTargeted = function (payload) {
        /*        if (payload.weapons.some(function(weapon) {return weapon.ballistic})) {
                    this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
                }
        */

        if (payload.weapons.some(function (weapon) {
            return weapon.ballistic || weapon.canSplitShots;
        })) {
            this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        }

        if (this.selectedShip === payload.shooter) {
            this.uiManager.showWeaponList({ ship: payload.shooter, gamePhase: gamedata.gamephase })
        }

        if (this.shipTooltip && this.shipTooltip.ships.includes(payload.target) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(payload.target, this.selectedShip);
        }

        this.shipWindowManager.update();
    };

    PhaseStrategy.prototype.onSplitOrderRemoved = function (payload) {

        if (this.shipTooltip && this.shipTooltip.ships.includes(payload.target) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(payload.target, this.selectedShip);
        }

        this.shipWindowManager.update();
    };

    PhaseStrategy.prototype.onToggleFriendlyBallisticLines = function (payload) {
        toggleBallisticLines.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onToggleEnemyBallisticLines = function (payload) {
        toggleBallisticLines.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowAllBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships, payload);
    };

    PhaseStrategy.prototype.onShowFriendlyBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowEnemyBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onToggleLoS = function (payload) {
        if (payload.up) return;

        if (!gamedata.showLoS) {
            gamedata.showLoS = true;
            const hex = this._lastHoveredHex || { q: 0, r: 0 };
            mathlib.showLoS(this._startHexRuler, hex);
        } else {
            gamedata.showLoS = false;
            this._startHexRuler = null; //reset.            
            mathlib.clearLosSprite();
        }

        window.dispatchEvent(new CustomEvent("LoSToggled"));
    };

    PhaseStrategy.prototype.onToggleHexNumbers = function (payload) {

        if (payload.up) return; // Prevent repeating on key hold or keyup

        var scene = webglScene.scene;
        this.ballisticIconContainer.createHexNumbers(scene);
        window.dispatchEvent(new CustomEvent("HexNumbersToggled"));
    };

    PhaseStrategy.prototype.onToggleBackground = function (payload) {
        if (payload.up) return;
        window.dispatchEvent(new CustomEvent("BackgroundToggled"));
    };

    function toggleBallisticLines(ships, payload) {
        this.ballisticIconContainer.toggleBallisticLines(ships);
        if (!this.gamedata.replay) this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
    };

    function showAllBallisticLines(ships, payload) {
        if (payload.up) {
            this.ballisticIconContainer.hideLines(ships);
        } else {
            this.ballisticIconContainer.showLines(ships);
        }
        if (!this.gamedata.replay) this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
    }


    return PhaseStrategy;
}();