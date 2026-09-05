"use strict";

window.shipManager = {
    /*
        shipImages: Array(),
        initiated: 0,
        
        initShips: function initShips() {
            if (window.webglScene) {
                return;
            }
    
            shipManager.initiated = 1;
            for (var i in gamedata.ships) {
                shipManager.createHexShipDiv(gamedata.ships[i]);
            }
            shipManager.initiated = 2;
            shipManager.drawShips();
        },
    
        createHexShipDiv: function createHexShipDiv(ship) {
    
            if (ship.htmlContainer) return;
    
            var e = $("#pagecontainer #hexship_" + ship.id + ".hexship");
    
            if (!e.length) {
    
                e = $("#templatecontainer .hexship");
                e.attr("id", "hexship_" + ship.id);
                var s = shipManager.getShipCanvasSize(ship);
                var w = s;
                var h = s;
                $("canvas.hexshipcanvas", e).attr("id", "shipcanvas_" + ship.id).attr("width", w).attr("height", h);
                var n = e.clone(true).appendTo("#pagecontainer");
                n.data("ship", ship.id);
                ship.htmlContainer = $("#pagecontainer #hexship_" + ship.id);
                ship.shipclickableContainer = $('<div oncontextmenu="shipManager.onShipContextMenu(this);return false;" class="shipclickable ship_' + ship.id + '"></div>').appendTo("#pagecontainer");
                ship.shipclickableContainer.data("id", ship.id);
                ship.shipclickableContainer.on("dblclick", shipManager.onShipDblClick);
                ship.shipclickableContainer.on("click", shipManager.onShipClick);
                ship.shipclickableContainer.on("mouseover", shipClickable.shipclickableMouseOver);
                ship.shipclickableContainer.on("mouseout", shipClickable.shipclickableMouseOut);
                if (ship.flight) {
                    ship.shipStatusWindow = flightWindowManager.createShipWindow(ship);
                } else {
                    ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
                }

                shipWindowManager.setData(ship);
                $("canvas.hexshipcanvas", e).attr("id", "shipcanvas_");
                e.attr("id", "hexship_");
                var img = new Image();
                img.src = window.AssetManager.getSmartImagePath(ship.imagePath);
                shipManager.shipImages[ship.id] = {
                    orginal: img,
                    modified: null,
                    rolled: null,
                    drawData: Array()
                };
                $(shipManager.shipImages[ship.id].orginal).on("load", function () {
                    shipManager.shipImages[ship.id].orginal.loaded = true;
                });
            } else {
                ship.htmlContainer = e;
                ship.shipclickableContainer = $(".shipclickable.ship_" + ship.id);
                ship.shipStatusWindow = $(".shipwindow.ship_" + ship.id);
                shipWindowManager.setData(ship);
            }
    
            if (shipManager.isDestroyed(ship)) ship.dontDraw = true;
        },
    
        drawShips: function drawShips() {
    
            if (shipManager.initiated == 0) {
                shipManager.initShips();
                return;
            }
    
            if (shipManager.initiated == 1) return;
    
            for (var i in gamedata.ships) {
                shipManager.drawShip(gamedata.ships[i]);
            }
        },
    
        /*
        drawShip: function(ship){
             if (shipManager.initiated == 0){
                shipManager.initShips();
                return;
            }
             if (shipManager.initiated == 1)
                return;
             if (gamedata.gamephase != -1){
                if (ship.dontDraw || ship.unavailable){
                    ship.shipclickableContainer.css("z-index", "1");
                    ship.htmlContainer.hide();
                    return;
                }   
            }
            //graphics.clearCanvas("shipcanvas_" + ship.id);
            var canvas = window.graphics.getCanvas("shipcanvas_" + ship.id);
             canvas.fillStyle    = hexgrid.hexlinecolor;;
            canvas.font         = 'italic 12px sans-serif';
            canvas.textBaseline = 'top';
             var pos = shipManager.getShipPositionForDrawing(ship);
               var s = shipManager.getShipCanvasSize(ship);
            var h = Math.round(s/2)
            var hexShipZ = 1000; //+ship.id;
            var scZ = 4500;//+ship.id;
            if (gamedata.mouseOverShipId == ship.id){
                hexShipZ+=500;
                scZ+=500;
            }
             if (gamedata.activeship == ship.id || gamedata.isSelected(ship)){
                hexShipZ+=250;
                scZ+=250;
            }
            ship.htmlContainer.css("top", pos.y -h + "px").css("left", pos.x -h + "px").css("z-index", hexShipZ);
            ship.htmlContainer.show();
             var img = damageDrawer.getShipImage(ship);
             var sc = ship.shipclickableContainer;
            scSize = s*0.15*gamedata.zoom;
            sc.css("width", scSize+"px");
            sc.css("height", scSize+"px");
            sc.css("left", ((pos.x) - (scSize*0.5))+"px");
            sc.css("top", ((pos.y) - (scSize*0.5))+"px");
            sc.css("z-index", scZ);
               //console.log("gamedata.gamephase: " + gamedata.gamephase + " gamedata.activeship: " + gamedata.activeship + " ship.id: " + ship.id);
            if (gamedata.gamephase == 2 && gamedata.activeship == ship.id && gamedata.animating == false && gamedata.waiting == false && gamedata.isMyShip(ship))
                UI.shipMovement.drawShipMovementUI(ship);
             if (gamedata.gamephase == -1 && gamedata.isMyShip(ship) && gamedata.isSelected(ship))
                UI.shipMovement.drawShipMovementUI(ship);
             if (gamedata.gamephase == 3 && ship.flight && gamedata.isSelected(ship))
                UI.shipMovement.drawShipMovementUI(ship);
             if (img.loaded){
                shipManager.doDrawShip(canvas, s, ship, img);
            }else{
                $(img).on("load", function(){
                    img = damageDrawer.getShipImage(ship);
                    if (img.loaded){
                        shipManager.doDrawShip(canvas, s, ship, img);
                    }else{
                        $(img).on("load", function(){
                            img = damageDrawer.getShipImage(ship);
                            shipManager.doDrawShip(canvas, s, ship, img);
                        });
                    }
                });
            }
         },
         doDrawShip: function(canvas, s, ship, img){
            var dew = ew.getDefensiveEW(ship);
            if (ship.flight)
        dew = shipManager.movement.getJinking(ship);
             var ccew = ew.getCCEW(ship);
             var shipdrawangle = shipManager.getShipHeadingAngleForDrawing(ship);
            var selected = gamedata.isSelected(ship);
            var mouseover = (gamedata.mouseOverShipId == ship.id);
             if (ship.drawn && shipdrawangle == ship.shipdrawangle && ship.drawnzoom == gamedata.zoom
        && ship.drawmouseover == mouseover && ship.drawselected == selected && ship.drawDamage == false
        && ship.drawDEW == dew && ship.drawCCEW == ccew)
        {
        return;
            }
              var myship = gamedata.isMyShip(ship);
            //console.log("draw");
            canvas.clearRect(0, 0, s, s);
        if ((selected && myship && gamedata.gamephase == 1) || (mouseover && gamedata.gamephase > 1) || (mouseover && myship)){
        if (gamedata.zoom > 0){
            if (dew > 0){
            dew = Math.ceil(( dew )*gamedata.zoom*0.5);
            canvas.strokeStyle = "rgba(144,185,208,0.40)";
            graphics.drawCircle(canvas, s/2, s/2, s*0.18*gamedata.zoom, dew);
        }
            if (ccew > 0){
            ccew = Math.ceil(( ccew )*gamedata.zoom*0.5);
                        if (myship)
                        {
                            canvas.strokeStyle = "rgba(20,80,128,0.50)";
                        }
                        else
                        {
                            canvas.strokeStyle = "rgba(179,65,25,0.50)";
                        }
                graphics.drawCircle(canvas, s/2, s/2, ((s*0.18*gamedata.zoom)+(dew*0.5) + (ccew*0.5) + 2), ccew);
        }
        }
        }
        if (selected && !mouseover && !(gamedata.gamephase == 2 && ship.id == gamedata.activeship)) {
        canvas.strokeStyle = "rgba(144,185,208,0.40)";
        canvas.fillStyle = "rgba(255,255,255,0.18)";
        graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);
        }else if ( mouseover ){
        if (gamedata.isMyShip(ship)){
        canvas.strokeStyle = "rgba(86,200,45,0.60)";
        canvas.fillStyle = "rgba(50,122,24,0.50)";
        }else{
        canvas.strokeStyle = "rgba(229,87,38,0.60)";
        canvas.fillStyle = "rgba(179,65,25,0.50)";
        }
        graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);
        }
        if (gamedata.isTargeted(ship)) {
        canvas.strokeStyle = "rgba(144,185,208,0.40)";
        canvas.fillStyle = "rgba(255,255,255,0.18)";
        graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);
        }
             var rolled = shipManager.movement.isRolled(ship);
        graphics.drawAndRotate(canvas, s, s, s*gamedata.zoom, s*gamedata.zoom, shipdrawangle, img, rolled);
             if (mouseover
                && (!gamedata.isMyShip(ship) || gamedata.gamephase != 2 || gamedata.activeship != ship.id)){
                 canvas.strokeStyle = "rgba(86,200,45,0.90)";
                canvas.fillStyle = "rgba(50,122,24,0.70)";
                 var c = Math.floor(s/2);
                var a = shipManager.getShipDoMAngle(ship);
                var r = s*0.18*gamedata.zoom;
                var p = mathlib.getPointInDirection(r, a , c, c);
                //graphics.drawCircleAndFill(canvas, p.x, p.y, 5*gamedata.zoom, 2);
                graphics.drawArrow(canvas, p.x, p.y, a, 30, 1);
            }
        ship.shipdrawangle = shipdrawangle;
        ship.drawn = true;
        ship.drawnzoom = gamedata.zoom;
        ship.drawselected = selected;
        ship.drawmouseover = mouseover;
        ship.drawDamage = false;
        ship.drawDEW = dew;
        ship.drawCCEW = ccew;
          },
        
        getShipCanvasSize: function getShipCanvasSize(ship) {
            return ship.canvasSize;
        },
    
        hasAnimationsDone: function hasAnimationsDone(ship) {
    
            for (var i in ship.movement) {
                movement = ship.movement[i];
                if (movement.animated == false || movement.commit == false) {
                    return false;
                }
            }
    
            return true;
        },
    */
    getShipDoMAngle: function getShipDoMAngle(ship) {
        var d = shipManager.movement.getLastCommitedMove(ship).heading;
        if (d == 0) {
            return 0;
        }
        if (d == 1) {
            return 60;
        }
        if (d == 2) {
            return 120;
        }
        if (d == 3) {
            return 180;
        }
        if (d == 4) {
            return 240;
        }
        if (d == 5) {
            return 300;
        }
    },

    getShipHeadingAngle: function getShipHeadingAngle(ship) {

        var d = shipManager.movement.getLastCommitedMove(ship).facing;
        if (d == 0) {
            return 0;
        }
        if (d == 1) {
            return 60;
        }
        if (d == 2) {
            return 120;
        }
        if (d == 3) {
            return 180;
        }
        if (d == 4) {
            return 240;
        }
        if (d == 5) {
            return 300;
        }
    },

    /*
    getShipHeadingAngleForDrawing: function(ship){
         var movement = null;
        for (var i in ship.movement){
            movement = ship.movement[i];
            if (movement.animated == true)
                continue;
             if (movement.type=="turnleft" || movement.type=="turnright"){
                var last = ship.movement[i-1];
                if (!last)
                    return shipManager.getShipHeadingAngle(ship);
                 var lastheading = mathlib.hexFacingToAngle(last.facing);
                var destination = mathlib.hexFacingToAngle(movement.facing);
                var perc = movement.animationtics / animation.turningspeed;
                 var right = (movement.type=="turnright");
                 return mathlib.getFacingBetween(lastheading, destination, perc, right);
             }
             if (movement.type=="pivotleft" || movement.type=="pivotright"){
                var last = ship.movement[i-1];
                if (!last)
                    return shipManager.getShipHeadingAngle(ship);
                 var lastheading = mathlib.hexFacingToAngle(last.facing);
                var destination = mathlib.hexFacingToAngle(movement.facing);
                var perc = movement.animationtics / animation.turningspeed;
                 var right = (movement.type=="pivotright");
                 return mathlib.getFacingBetween(lastheading, destination, perc, right);
             }
             break;
         }
          return shipManager.getShipHeadingAngle(ship);
     },
    */

    /*
    getShipPositionInTurn: function getShipPositionInTurn(ship, turn) {

        if (turn <= 0) turn = 1;

        var movement = null;

        for (var i in ship.movement) {
            if (ship.movement[i].turn === turn) {
                movement = ship.movement[i];
            }
        }

        if (movement === null && ship.movement.length > 0) {
            movement = ship.movement[ship.movement.length - 1];
        }

        var x = movement.x;
        var y = movement.y;
        var xO = movement.xOffset;
        var yO = movement.yOffset;
        return { x: x, y: y, xO: xO, yO: yO };
    },
    */

    getShipPosition: function getShipPosition(ship) {
        var movement = shipManager.movement.getLastCommitedMove(ship);
        return new hexagon.Offset(movement.position);
    },

    getShipPositionForDrawing: function getShipPositionForDrawing(ship) {
        var movement = null;
        for (var i in ship.movement) {
            if (ship.movement[i].commit == false) break;

            movement = ship.movement[i];

            if (movement.animated == true) continue;

            if (movement.type == "move" || movement.type == "slipright" || movement.type == "slipleft") {
                var last = ship.movement[i - 1];

                if (!last) {
                    break;
                }
                var lastpos = hexgrid.hexCoToPixel(last.x, last.y);
                lastpos.x = lastpos.x + last.xOffset * gamedata.zoom;
                lastpos.y = lastpos.y + last.yOffset * gamedata.zoom;
                var destination = hexgrid.hexCoToPixel(movement.x, movement.y);
                destination.x = destination.x + movement.xOffset * gamedata.zoom;
                destination.y = destination.y + movement.yOffset * gamedata.zoom;
                var perc = movement.animationtics / animation.movementspeed;

                return mathlib.getPointBetween(lastpos, destination, perc);
            }

            break;
        }

        var x = movement.x;
        var y = movement.y;

        var lastpos = hexgrid.hexCoToPixel(x, y);
        lastpos.x = Math.floor(lastpos.x + movement.xOffset * gamedata.zoom);
        lastpos.y = Math.floor(lastpos.y + movement.yOffset * gamedata.zoom);
        return lastpos;
    },
    /*
    onShipContextMenu: function onShipContextMenu(e) {
        var id = $(e).data("id");
        var ship = gamedata.getShip(id);
    // REMOVED 2026-08-20: onShipContextMenu, doShipContextMenu, onShipDblClick,
    // onShipClick and doShipClick. All five called window.shipSelectList, which was
    // deleted along with source/public/client/UI/shipSelect.js in commit 7ea324de3
    // ("delete unused files") — so every one of them would have thrown a
    // ReferenceError on its first line.
    //
    // They never fired. Their ONLY binding site was createHexShipDiv (the oncontextmenu
    // attribute and the .on("click") / .on("dblclick") handlers on .shipclickable), and
    // that whole function sits inside the big commented-out block at the top of this
    // file, which is why it is absent from the built bundle entirely. The WebGL
    // renderer's map clicks go to PhaseStrategy.onShipClicked instead — a different
    // function with a confusingly similar name, and the live path.
    //
    // Found via a colour audit: the .shipSelectList rules in tactical.css (removed in
    // the same change) held the last two allegiance literals in the codebase sitting on
    // no brightness tier, which is what led back here. See [[arch_team_colour_logic]].

        if (shipSelectList.haveToShowList(ship, e)) {
            shipSelectList.showList(ship);
        } else {
            shipManager.doShipContextMenu(ship);
        }
    },

    doShipContextMenu: function doShipContextMenu(ship) {

        shipSelectList.remove();

        if (shipManager.isDestroyed(ship)) return;

        if (ship.userid == gamedata.thisplayer && (gamedata.gamephase == 1 || gamedata.gamephase > 2)) {
            gamedata.selectShip(ship, false);
            gamedata.shipStatusChanged(ship);
            drawEntities();
        } else {
        }
        return false;
    },

    onShipDblClick: function onShipDblClick(e) { },

    onShipClick: function onShipClick(e) {
        //console.log("click on ship");

        if (!e || e.which !== 1) return;

        e.stopPropagation();
        var id = $(this).data("id");
        var ship = gamedata.getShip(id);

        if (shipSelectList.haveToShowList(ship, e)) {
            shipSelectList.showList(ship);
        } else {
            shipManager.doShipClick(ship);
        }
    },

    doShipClick: function doShipClick(ship) {

        shipSelectList.remove();

        if (ship == null) {
            return;
        }

        if (gamedata.thisplayer == -1) return;

        if (shipManager.isDestroyed(ship)) return;

        if (gamedata.gamephase == 2) return;

        if (gamedata.waiting) return;

        if (ship.userid == gamedata.thisplayer) {
            gamedata.selectShip(ship, false);
        }

        if (ship.userid != gamedata.thisplayer && gamedata.gamephase == 3) {
            weaponManager.targetShip(ship, false);
        }

        if (gamedata.gamephase == 1 && ship.userid != gamedata.thisplayer) {
            if (gamedata.selectedSystems.length > 0) {
                weaponManager.targetShip(ship, false);
            } else if (!ship.flight) {
                ew.AssignOEW(ship);
            }
        }
        gamedata.shipStatusChanged(ship);
        drawEntities();
        //scrolling.scrollToShip(ship);
    },
    */


    getPrimaryCnC: function getPrimaryCnC(ship) {
        var cncs = [];

        for (var system in ship.systems) {
            if (ship.systems[system].displayName == "C&C") {
                cncs.push(ship.systems[system]);
            }
        }

        cncs.sort(function (a, b) {
            if (shipManager.systems.getRemainingHealth(a) > shipManager.systems.getRemainingHealth(b)) {
                return 1;
            } else {
                return -1;
            }
        });

        var primary = cncs[0];

        return primary;
    },

    isDisabled: function isDisabled(ship) {
        if (ship.base) {
            var primary = shipManager.getPrimaryCnC(ship);

            if (!shipManager.criticals.hasCriticalOnTurn(primary, "ShipDisabledOneTurn", gamedata.turn - 1) || !shipManager.criticals.hasCriticalOnTurn(primary, "ShipDisabled", gamedata.turn - 1)) {
                return false;
            }
        } else {
            for (var i = 0; i < ship.systems.length; i++) {
                if (ship.systems[i].displayName == "C&C") {
                    if (shipManager.criticals.hasCriticalOnTurn(ship.systems[i], "ShipDisabledOneTurn", gamedata.turn - 1) || shipManager.criticals.hasCriticalOnTurn(ship.systems[i], "ShipDisabled", gamedata.turn - 1)) {
                        return true;
                    }
                }
            }
        }
        return false;
    },

    /* ⭐ JUMP_POINTS_PLAN.md Stage 6 - "THIS UNIT IS ON ITS WAY OUT", as a client-only marker.
     *
     * A PLANNED departure had nothing to show for itself. Under the retired boost-to-jump rules
     * the ship window lit up the moment the Jump Engine was boosted; the vortex rules replaced
     * that with two separate gestures a turn or more apart, and neither said anything about the
     * unit making them (user request 2026-08-22). Both are answered here:
     *
     *   1. THE DECLARATION - this turn's Jump Engine order at a hex (firing modes 1-6, the vortex
     *      facings; mode 7 is Maintain, which is about keeping a jump point open rather than
     *      going through one). Removing the fire order removes the marker, because the order IS
     *      the marker - there is no flag anywhere to keep in step.
     *   2. THE PLOTTED JUMP-OUT - a 'jumpout' movement order this unit has drawn but not yet
     *      committed. Once it IS committed the fleet list, the map sprite and the ship row all
     *      change together (hasCommittedJumpOut), so the banner has done its job and stands down.
     *
     * Purely presentational, and deliberately NOT own-ship-only: a declaration is public from the
     * moment Initial Orders close (TacGamedata::hideSystemFireOrders masks it during phase 1 only)
     * and the yellow "Jump Point Forming" hex is on everyone's map, so this discloses nothing that
     * is not already drawn.
     *
     * Guarded on weaponManager because ships.js is loaded by the LOBBY as well, where it does not
     * exist and no ship has orders of any kind.
     */
    isJumpingToHyperspace: function isJumpingToHyperspace(ship) {
        if (!ship || typeof gamedata === 'undefined') return false;
        if (gamedata.gamephase === -2) return false;   //lobby

        if (shipManager.movement.hasJumpedOut(ship) && !shipManager.movement.hasCommittedJumpOut(ship)) return true;
        /*
        if (typeof weaponManager === 'undefined') return false;

        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (!system || system.name !== 'jumpEngine') continue;

            for (var f in system.fireOrders) {
                var fire = system.fireOrders[f];
                if (fire.turn != gamedata.turn) continue;
                var mode = parseInt(fire.firingMode, 10);
                if (mode >= 1 && mode <= 6) return true;   //an OPENING declaration, not Maintain (7)
            }
        }
        */
        return false;
    },

    //Used by RelayAnimationStrategy/fleetList to check if ship has jumped, if so different destroyed sprite/entry
    hasJumpedNotDestroyed: function (ship) {
        /* JUMP_POINTS_PLAN.md Stage 6 - A FLIGHT HAS NO PRIMARY STRUCTURE, so the hull test below
           cannot answer for one. Movement::applyJumpOut takes a flight off the board by destroying
           every CRAFT with a HyperspaceJump entry instead, so that is what is asked here: some
           craft carries a jump entry, and the damage that is NOT jump damage left at least one
           craft alive - i.e. the flight was still flying when it went. Mirror of
           FighterFlight::hasJumpedToHyperspace, which is authoritative.

           This is the flight jumping under its OWN power. A DOCKED flight carried out inside a
           jumping carrier is a different question with a different answer - jumpedWithCarrier, or
           fleetListManager.getJumpedDockedFlightIds - because nothing on that flight is damaged
           at all. */
        if (ship.flight) {
            var anyJumped = false;
            var anySurvivor = false;

            for (var f in ship.systems) {
                var craft = ship.systems[f];
                if (!craft || !craft.damage) continue;

                var craftJumped = false;
                var craftNonJump = 0;
                for (var d in craft.damage) {
                    var craftDamage = craft.damage[d];
                    if (craftDamage.damageclass === 'HyperspaceJump') {
                        craftJumped = true;
                        continue;
                    }
                    craftNonJump += Math.max(0, craftDamage.damage - craftDamage.armour);
                }

                if (craftJumped) {
                    anyJumped = true;
                    if (craftNonJump < craft.maxhealth) anySurvivor = true;
                }
            }

            return anyJumped && anySurvivor;
        }

        // Check if the ship has a jump engine
        const jumpEngine = shipManager.systems.getSystemByName(ship, "jumpEngine");

        // Check if the jump engine is boosted
        //var boostedJump = shipManager.power.isBoosted(ship, jumpEngine);
        //if(!boostedJump) return false; //Hasn't boosted jump engine, it cannot have jumped.

        //Check damage entries, and remove Hyperspace jump entry, to see if ship was 'destroyed' by jumping not actual damage.	    
        var struct = shipManager.systems.getStructureSystem(ship, 0);
        if (!struct) return false;
        var maxHealth = struct.maxhealth;
        var totalDamage = 0;
        var thisDamage = null;
        var jumpEntry = false;
        for (var i in struct.damage) {
            thisDamage = struct.damage[i];
            if (thisDamage.damageclass === 'HyperspaceJump') {
                jumpEntry = true;
                continue; //Only count non-jump damage, as jumping destroys ship anyway.
            }
            totalDamage += Math.max(0, thisDamage.damage - thisDamage.armour);
        }

        /* A unit with NO jump engine can still leave through somebody else's open jump vortex
           (JUMP_POINTS_PLAN.md Stage 4, section 2.5), so the engine can no longer be the gate.
           It is replaced by a stricter test rather than simply dropped: without either, any
           destroyed unit whose primary structure was not what killed it - a collision, a captured
           hull - would read as having jumped. Mirrors BaseShip::hasJumpedToHyperspace. */
        if (!jumpEngine && !jumpEntry) return false;

        if (totalDamage < maxHealth) return true; //The other damage sustained has not destroyed this ship, jumping has.

        return false;
    },


    isDestroyed: function isDestroyed(ship) {

        if (ship == null) {
            return;
        }

        // Hangar Ops Stage 5: a docked flight has $removed=true; treat as
        // destroyed for filtering purposes (icon hide, target list exclusion,
        // hex-occupancy). The destruction explosion is keyed off
        // damageManager.getTurnDestroyed (turn-of-damage record), which we
        // never write for docked flights — so no explosion fires.
        if (ship.removed) return true;

        return shipManager.isDestroyedByDamage(ship);
    },

    /* The same question asked of the DAMAGE ALONE, without the docked-in-a-hangar
       shortcut above. A docked flight or a rail-parked LCV is not a wreck — it is a
       live unit parked out of sight — so the handful of callers that have to tell
       "gone" from "stowed" apart (Save Fleet, ajaxInterface.isSaveableFleetShip) ask
       this instead. Everything else wants isDestroyed and its Stage 5 shortcut.
       Mirrors BaseShip::isDestroyed minus its own $removed line. */
    isDestroyedByDamage: function isDestroyedByDamage(ship) {

        if (ship == null) {
            return;
        }

        if (ship.destroyed) return true; // Early exit if server-side status is already set - DK 04/26

        if (ship.flight) {
            for (var i in ship.systems) {
                var fighter = ship.systems[i];
                if (!shipManager.systems.isDestroyed(ship, fighter)) {
                    return false;
                }
            }
            return true;
        } else {
            if (!ship.base) {
                var stru = shipManager.systems.getStructureSystem(ship, 0);
                if (stru && shipManager.systems.isDestroyed(ship, stru)) {
                    return true;
                }
                if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !ship.mine) {
                    var react = shipManager.systems.getSystemByName(ship, "reactor");
                    if (shipManager.systems.isDestroyed(ship, react)) {
                        return true;
                    }
                }
            } else {
                var stru = shipManager.systems.getStructureSystem(ship, 0);
                if (stru && shipManager.systems.isDestroyed(ship, stru)) {
                    return true;
                }
                if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !ship.mine) {
                    var mainReactor = shipManager.systems.getSystemByNameInLoc(ship, "reactor", 0);
                    if (shipManager.systems.isDestroyed(ship, mainReactor)) {
                        return true;
                    }
                }
            }
        }

        return false;
    },

    getStructuresDestroyedThisTurn: function getStructuresDestroyedThisTurn(ship) {

        var array = [];

        for (var j = 0; j < ship.systems.length; j++) {
            system = ship.systems[j];
            if (system.displayName == "Structure" && system.location != 0) {
                if (system.destroyed) {
                    for (var k = 0; k < system.damage.length; k++) {
                        var dmg = system.damage[k];
                        if (dmg.destroyed) {
                            if (gamedata.turn == dmg.turn) {
                                array.push(system);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (array.length > 0) {
            return array;
        } else return null;
    },

    getOuterReactorDestroyedThisTurn: function getOuterReactorDestroyedThisTurn(ship) {

        var array = [];

        for (var j = 0; j < ship.systems.length; j++) {
            system = ship.systems[j];
            if (system.displayName == "Reactor" && system.location != 0) {
                if (system.destroyed) {
                    for (var k = 0; k < system.damage.length; k++) {
                        var dmg = system.damage[k];
                        if (dmg.destroyed) {
                            if (gamedata.turn == dmg.turn) {
                                array.push(system);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (array.length > 0) {
            return array;
        } else return null;
    },

    isAdrift: function isAdrift(ship) {
        if (ship.flight || ship.osat || ship.base) return false;

        if (shipManager.criticals.hasCriticalInAnySystem(ship, "ShipDisabledOneTurn") || shipManager.criticals.hasCriticalInAnySystem(ship, "ShipDisabled")) return true;

        if (shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByName(ship, "cnC"))) {
            return true;
        }

        /*ship without power (power deficit or Reactor shutdown critical) is adrift as well*/
        /*...after consulting rulebook - it isn't!*/
        /*
            //isPowerless already checks for appropriate critical, actually
            if (shipManager.power.isPowerless(ship)) return true;
            */

        return false;
    },

    isEngineless: function isEngineless(ship) {
        var engines = [];
        for (var sys in ship.systems) {
            if (ship.systems[sys].displayName == "Engine") {
                engines.push(ship.systems[sys]);
            }
        }

        for (var i = 0; i < engines.length; i++) {
            if (engines[i].destroyed == false) {
                return false;
            }
        }

        return true;
    },

    getTurnDestroyed: function getTurnDestroyed(ship) {
        var turn = null;
        if (!shipManager.isDestroyed(ship)) return null; //if ship is not destroyed then it's not destroyed :)
        if (ship.flight) {

            var fightersSurviving = ship.systems.some(function (fighter) {
                return damageManager.getTurnDestroyed(ship, fighter) === null;
            });

            if (fightersSurviving) {
                return null;
            }

            ship.systems.forEach(function (fighter) {
                var dturn = damageManager.getTurnDestroyed(ship, fighter);
                if (dturn > turn) turn = dturn;
            });
        } else {

            var react = shipManager.systems.getSystemByName(ship, "reactor");
            var rturn = damageManager.getTurnDestroyed(ship, react);
            var stru = shipManager.systems.getStructureSystem(ship, 0);
            var sturn = damageManager.getTurnDestroyed(ship, stru);

            if (rturn != null && (rturn < sturn || sturn == null)) turn = rturn; else turn = sturn;
        }

        return turn;
    },
    /*
        getIniativeOrder: function getIniativeOrder(ship) {
            var previousInitiative = -100000; //same Ini move together now!
            var order = 0;  
    
            for (var i in gamedata.ships) {
                if (shipManager.isDestroyed(gamedata.ships[i])) continue;
                if (gamedata.ships[i].iniative > previousInitiative){ //new Ini higher than previous!         
                    order++;
                    previousInitiative = gamedata.ships[i].iniative;
                }
                if (gamedata.ships[i].id == ship.id) return order;
            }
    
            return 0; //should not happen
        },
    */

    //New getInitiativeOrder function to accommodate terrain units like asteroids.
    getIniativeOrder: function getIniativeOrder(ship) {
        var previousInitiative = -100000;
        var order = 0;

        // Filter and SORT by initiative before calculating order - DK 04/26
        var validShips = gamedata.ships.filter(function (s) {
            return !shipManager.isDestroyed(s) && !gamedata.isTerrain(s.shipSizeClass, s.userid) && !s.mine && !(shipManager.getTurnDeployed(s) > gamedata.turn);
        }).sort(function (a, b) {
            if (a.iniative > b.iniative) return 1;
            if (a.iniative < b.iniative) return -1;
            return a.id - b.id; // Stability
        });

        for (var i in validShips) {
            if (validShips[i].iniative > previousInitiative) {
                order++;
                previousInitiative = validShips[i].iniative;
            }
            if (validShips[i].id === ship.id) return order;
        }

        return 0; // should not happen
    },


    hasBetterInitive: function hasBetterInitive(a, b) {
        //console.log(a.name);
        if (a.iniative > b.iniative) return true;

        if (a.iniative < b.iniative) return false;

        /*
                if (a.unmodifiedIniative != null && b.unmodifiedIniative != null) {
                    if (a.unmodifiedIniative > b.unmodifiedIniative)
                        return 1;
                
                    if (a.unmodifiedIniative < b.unmodifiedIniative)
                        return -1;
                }
        */
        //if (a.iniative == b.iniative) {
        if (a.iniativebonus > b.iniativebonus) return true;

        if (b.iniativebonus > a.iniativebonus) return false;

        return (a.id > b.id); //lower ID wins, if all else fails

        /*
            for (var i in gamedata.ships) {
                if (gamedata.ships[i] == a) return false;

                if (gamedata.ships[i] == b) return true;
            }
        */
        //}

        return 0; //shouldn't get here
    },

    hasWorseInitiveSort: function hasWorseInitiveSort(a, b) {
        var hasBetterIni = shipManager.hasBetterInitive(a, b);
        if (hasBetterIni) return -1; //reverse
        return 1;
    },

    //Only used for Deployment checks to prevent ships deploying on same hex (or now allow for later Deployments) - DK
    getShipsInSameHex: function getShipsInSameHex(ship, pos1) {

        if (!pos1) var pos1 = shipManager.getShipPosition(ship);

        /* REINFORCEMENTS_PLAN.md Stage 7 - DO NOT SHOUT AT A UNIT WHOSE DESTINATION *IS* TERRAIN
           (user report 2026-08-28, game 4318). A reinforcement arrives inside its jump point
           exit, which is a Terrain unit, so both error calls below fired on every single unit
           of every wave - and said nothing true: the arrival bypasses the terrain block entirely
           (DeploymentPhaseStrategy.onHexClicked), so the placement went through and the message was
           pure noise. Only the TOAST is suppressed; the vortex still goes into the returned list,
           because callers use that list for their own occupancy decisions.
           A null ship is legal here - window.validateMineDeploymentHex passes one - and
           isArrivingReinforcement answers false for it. */
        var arrivingReinforcement = shipManager.isArrivingReinforcement(ship);

        var shipsInHex = Array();
        for (var i in gamedata.ships) {
            var ship2 = gamedata.ships[i];

            if (shipManager.isDestroyed(ship2)) continue;
            //Stage 7 (Hangar Ops): a flight queued for deployment-phase dock is
            //logically inside a carrier's hangar, not on the board. Skip it for
            //hex occupancy so the carrier can move back to the original dock
            //hex without the (invisible) flight blocking placement.
            if (ship2.pendingDeployDock) continue;
            //LCV Rails: an LCV queued to deploy-dock is likewise off the board.
            if (ship2.pendingLcvDeployDock) continue;

            //Let's allow ships that deploy on later turns to deploy on same hex as existing units - DK
            // NO LONGER REQUIRED - Overridden by explicit isBlocked rules in Deployment Phase.
            //var depTurn = shipManager.getTurnDeployed(ship2);
            //if (depTurn !== gamedata.turn && !ship2.Enormous) continue;

            var pos2 = shipManager.getShipPosition(ship2);

            //But never let ships Deployment on unoccupied parts of Huge terrain.
            var collides = false;

            // Check for custom hex offsets (non-circular terrain)
            if (ship2.hexOffsets && ship2.hexOffsets.length > 0) {
                var lastMove = shipManager.movement.getLastCommitedMove(ship2);
                var facing = lastMove ? lastMove.facing : 0;

                for (var j in ship2.hexOffsets) {
                    var offset = ship2.hexOffsets[j];

                    // Use getRotatedHex for accurate positioning
                    var newHex = mathlib.getRotatedHex(pos2, offset, facing);

                    // Check if pos1 matches the offset position
                    if (pos1.q === newHex.q && pos1.r === newHex.r) {
                        collides = true;
                        break;
                    }
                }
            } else if (ship2.Huge > 0 && ship2.Huge <= 3) { //Between 1 and 3, Moons basically - DK
                //var s2pos = shipManager.getShipPosition(ship2);
                var distance = pos1.distanceTo(pos2);
                if (distance > 0 && distance <= ship2.Huge) {
                    collides = true;
                }
            }

            if (collides) {
                shipsInHex.push(ship2);
                if (!arrivingReinforcement) confirm.error("You cannot deploy on terrain.");
                continue; // Collision found, skip center check for this ship
            }

            //if (ship.id = ship2.d)
            //  continue;

            if (pos1.equals(pos2)) {
                if (gamedata.isTerrain(ship2.shipSizeClass, ship2.userid)) {
                    shipsInHex.push(ship2);
                    if (!arrivingReinforcement) confirm.error("You cannot deploy on terrain.");
                } else {
                    shipsInHex.push(ship2);
                }
            }
        }

        return shipsInHex;
    },

    getFighterPosition: function getFighterPosition(pos, angle, zoom) {

        var dir = 0;
        if (pos == 0) {
            dir = mathlib.addToDirection(0, angle);
            return mathlib.getPointInDirection(19 * zoom, dir, 0, 0);
        } else if (pos == 1) {
            dir = mathlib.addToDirection(300, angle);
            return mathlib.getPointInDirection(13 * zoom, dir, 0, 0);
        } else if (pos == 2) {
            dir = mathlib.addToDirection(60, angle);
            return mathlib.getPointInDirection(13 * zoom, dir, 0, 0);
        } else if (pos == 3) {
            dir = mathlib.addToDirection(180, angle);
            return mathlib.getPointInDirection(12 * zoom, dir, 0, 0);
        } else if (pos == 4) {
            dir = mathlib.addToDirection(250, angle);
            return mathlib.getPointInDirection(21 * zoom, dir, 0, 0);
        } else if (pos == 5) {
            dir = mathlib.addToDirection(110, angle);
            return mathlib.getPointInDirection(21 * zoom, dir, 0, 0);
        } else if (pos == 6) {
            dir = mathlib.addToDirection(180, angle);
            return mathlib.getPointInDirection(29 * zoom, dir, 0, 0);
        } else if (pos == 7) {
            dir = mathlib.addToDirection(230, angle);
            return mathlib.getPointInDirection(32 * zoom, dir, 0, 0);
        } else if (pos == 8) {
            dir = mathlib.addToDirection(130, angle);
            return mathlib.getPointInDirection(32 * zoom, dir, 0, 0);
        } else if (pos == 9) {
            dir = mathlib.addToDirection(0, angle);
            return mathlib.getPointInDirection(35 * zoom, dir, 0, 0);
        } else if (pos == 10) {
            dir = mathlib.addToDirection(295, angle);
            return mathlib.getPointInDirection(28 * zoom, dir, 0, 0);
        } else if (pos == 11) {
            dir = mathlib.addToDirection(65, angle);
            return mathlib.getPointInDirection(28 * zoom, dir, 0, 0);
        }

        return { x: 0, y: 0 };
    },

    getSpecialAbilitySystem: function getSpecialAbilitySystem(ship, ability) {
        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (shipManager.systems.isDestroyed(ship, system)) continue;

            if (shipManager.power.isOffline(ship, system)) continue;

            for (var a in system.specialAbilities) {
                if (system.specialAbilities[a] == ability) return system;
            }
        }

        return false;
    },

    hasSpecialAbility: function hasSpecialAbility(ship, ability) {
        if (shipManager.getSpecialAbilitySystem(ship, ability)) return true;

        return false;
    },

    isElint: function isElint(ship) {
        if (shipManager.hasSpecialAbility(ship, "ELINT")) {
            return true;
        }

        return false;
    },


    isEscorting: function isEscorting(ship, target) {
        if (!ship.flight) return false;
        //var ships = shipManager.getShipsInSameHex(ship);
        //for (var i in ships) {
        //var othership = ships[i];

        //if (gamedata.turn == 1) return true; //on turn 1 all friendly ships can be protected! NO LONGER REQUIRED - DK Mar 2026

        for (var i in gamedata.ships) { //doesn't need to be on the same hex NOW... only at the start and end of move :)
            var othership = gamedata.ships[i];
            if (shipManager.isDestroyed(othership)) continue; //no need to list ships already destroyed
            if (othership.flight === true) continue; //can escort only ships
            if (othership.id == ship.id) continue;
            if (gamedata.isTerrain(othership.shipSizeClass, othership.userid)) continue;            

            if (gamedata.isEnemy(ship, othership)) continue;

            var oPos = shipManager.movement.getPositionAtStartOfTurn(othership);
            var tPos = shipManager.movement.getPositionAtStartOfTurn(ship);

            if (!target || target.id == othership.id) {
                if (oPos.equals(tPos)) return true;
            }
        }
        return false;
    },

    /*list of names of escorted ships*/
    listEscorting: function listEscorting(ship) {
        var resultTxt = '';
        if (!ship.flight) return resultTxt;

        //if (gamedata.turn == 1) return 'All'; //turn 1: all ships can be escorted. NO LONGER REQUIRED - DK Mar 2026

        for (var i in gamedata.ships) {
            var othership = gamedata.ships[i];

            if (shipManager.isEscorting(ship, othership)) {
                if (resultTxt != '') resultTxt += ', ';
                resultTxt += othership.name;
            }
        }

        return resultTxt;
    },

    //Called in various places to identify a ship as having ability to be invisible to enemy.
    /*
    isStealthShip: function (ship) {
        return ship.trueStealth;
    },   
    */

    //Generic function called from various front end functions.  Checks if ships should be shown/interactable or not.
    shouldBeHidden: function (ship) {
        if (!gamedata.replay && shipManager.isDestroyed(ship)) return true; //Prevents lots of things from happening when a ship collides and dies to Terrain.
        /* A unit that has COMMITTED a jump-out has left the battle (JUMP_POINTS_PLAN.md section 2.5).
           The server does not remove it until the end of the Movement phase, but movement is
           sequential - so without this it sits in the vortex as a ghost while everyone else takes
           their turn, and the players plotting after it cannot tell it has already gone.
           Live play only: in REPLAY the unit has to be seen flying INTO the vortex before it
           vanishes, and the destroyed check above takes over there. */
        if (!gamedata.replay && shipManager.movement.hasCommittedJumpOut(ship)) return true;
        if (shipManager.getTurnDeployed(ship) > gamedata.turn) {
            /* Not deployed yet - with ONE exception. Reinforcements pick their entry hex during
               the Deployment phase of the turn BEFORE they arrive (shipManager.getTurnPlaced), so
               for that one phase the owner has to be able to see, select and place them. To
               everyone else, and in every later phase of that same turn, they remain off-board:
               the only thing anyone sees is the blue "Jump Point" ballistic marker at the hex
               they committed to. Own-slot only - a teammate can't place your ships either. */
            var placingNow = gamedata.gamephase == -1
                && gamedata.isMyShip(ship)
                && shipManager.getTurnPlaced(ship) == gamedata.turn;
            if (!placingNow) return true;
        }
        if (ship.spawned !== -1 && ship.spawned > gamedata.turn) return true; //Not spawned yet.
        /* REINFORCEMENTS_PLAN.md STAGE 9 - A PHASING HULL'S ARRIVAL POINT IS NEVER DRAWN. Shadow
           and other legacy-drive factions leave nothing behind when they go and put nothing on the
           board when they come back (user ruling 2026-08-29), so the doorway exists server-side -
           where every arrival, closure and berth rule is anchored on it - and is invisible here.
           ⭐ THIS ONE LINE IS THE WHOLE SUPPRESSION. shouldBeHidden is what the icon, the facing
           arrow, the click/hover sweep, the hex ship-list and the replay animation all consult, so
           there is no second place to remember. What the players see instead is the blue ballistic
           hex the declaration already draws, labelled REINFORCEMENTS. */
        if (shipManager.movement.isPhaseInVortex(ship)) return true;
        //Hangar Ops: a partial-dock fragment ("- Split") is born removed=true with
        //spawned == removedTurn == the dock turn — it never existed on the board as
        //its own flight (its craft are shown firing as part of the SOURCE flight).
        //So it must stay hidden EVERYWHERE on/after its dock turn, including replay
        //(where ordinary removed flights are deliberately still shown so they appear
        //in earlier turns). This mirrors ReplayAnimationStrategy's bornAndRemovedSameTurn
        //board-hide so the hex ship-list / target popups don't list a phantom split.
        if (ship.removed && ship.spawned !== undefined && ship.spawned !== -1 &&
            ship.removedTurn != null && ship.spawned >= ship.removedTurn &&
            gamedata.turn >= ship.removedTurn) return true;
        if (!gamedata.isMyorMyTeamShip(ship) && ship.trueStealth && !shipManager.isDetected(ship)) return true; //Enemy, stealth ship and not currently detected
        //Stage 7 (Hangar Ops): a flight queued for deployment-phase dock isn't on the
        //board — its icon should be hidden until either the dock is cancelled or the
        //next reload (which sets ship.removed via the persisted hangarUsage snapshot).
        if (ship.pendingDeployDock) return true;
        //LCV Rails: an LCV queued to deploy-dock onto a rail likewise isn't on the board.
        if (ship.pendingLcvDeployDock) return true;
        return false;
    },

    getTurnDeployed: function getTurnDeployed(ship) {

        if (ship.osat || ship.base || gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            return 1; //Bases and OSATs never 'jump in', returns Turn 1.
        } else if (ship.spawned !== undefined && ship.spawned !== -1) {
            return ship.spawned; //Spawned units enter the game on ship.spawned turn.
        } else {
            //return Math.max(ship.deploysOnTurn, slot.depavailable);
            var slot = playerManager.getSlotById(ship.slot);
            var depTurn = slot.depavailable;

            /* REINFORCEMENTS_PLAN.md §3.2 — mirror of BaseShip::getTurnDeployed. A reinforcement's
               arrival turn is decided IN PLAY by the jump point exit it rides through, not by
               its slot, so depavailable says nothing about it. A null/undefined arrivalTurn means
               it is still in HYPERSPACE, which reads here as 999 = "not on the board" — the same
               sentinel a surrendered fleet gets, because it means exactly the same thing. That one
               line is what keeps a hyperspace unit out of shouldBeHidden, the fleet list, the
               Deployment phase and every firing/EW gate without touching any of them.
               BEFORE the surrender check, exactly as on the server: a surrendered slot still wins
               and takes its reinforcements with it. */
            if (ship.reinforcement) {
                depTurn = (ship.arrivalTurn === null || ship.arrivalTurn === undefined) ? 999 : ship.arrivalTurn;
            }

            if (slot.surrendered !== null) {
                /* 999 is the "not on the board" sentinel — shouldBeHidden() and every
                   deployed-yet check key off it, so a surrendered fleet vanishes from the game. - DK

                   The cut-off differs between live play and replay. Live, it is immediate: the
                   fleet is out the moment it concedes, which is what the server enforces
                   (BaseShip::getTurnDeployed) and what Firing::withdrawSurrenderedFireOrders
                   backs up by withdrawing whatever it still had in the air.

                   Replay pushes it out by a turn. The turn a fleet surrendered ON still happened
                   — its ships moved, fired and were fired at right up to the moment it left — so
                   erasing it there replayed that turn onto a half-empty board (user report
                   2026-08-03). Same idea as the isDestroyed line at the top of shouldBeHidden:
                   replay deliberately shows what live play has already removed. */
                var goneFromTurn = gamedata.replay ? gamedata.turn : gamedata.turn + 1;
                if (slot.surrendered < goneFromTurn) {
                    depTurn = 999;
                }
            }
            return depTurn;
        }
    },


    /*The turn this unit picks its ENTRY HEX, as opposed to the turn it is physically on the
      board (getTurnDeployed above). Mirrors BaseShip::getTurnPlaced on the server.

      LATE-SLOT arrivals place a turn EARLY: the player commits entry hexes during the Deployment
      phase of turn depTurn-1, and those hexes show to everyone as blue "Jump Point" markers for
      the whole of that turn, so an arriving fleet no longer materialises without warning.

      ONLY code answering "is this unit being placed right now?" may use this - the Deployment
      phase strategy, its commit warnings, the deploy-start dock dialog. Everything asking "is
      this unit on the board?" (firing, EW, power, movement, the OOB "[Deploys on Turn N]"
      header) must keep reading getTurnDeployed.

      Bases/OSATs/Terrain place and arrive on turn 1, and spawned units (mid-game mines, launched
      flights) never see a Deployment phase at all, so both keep their deploy turn untouched.*/
    getTurnPlaced: function getTurnPlaced(ship) {
        if (ship.osat || ship.base || gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return 1;
        if (ship.spawned !== undefined && ship.spawned !== -1) return ship.spawned;

        /* REINFORCEMENTS_PLAN.md §3.2 / trap 2 — a REINFORCEMENT (the jump point exit kind,
           not a late slot) places and arrives on the SAME turn: its early warning is the blue jump
           point that formed last turn, not an early placement. Subtracting one here would give it
           a Deployment phase a turn before its vortex exists, with nowhere legal to stand. */
        if (ship.reinforcement) return shipManager.getTurnDeployed(ship);

        var depTurn = shipManager.getTurnDeployed(ship); //carries the 999 surrender sentinel
        return (depTurn > 1) ? depTurn - 1 : depTurn;
    },


    /* REINFORCEMENTS_PLAN.md STAGE 7 - is this unit coming out of hyperspace THIS turn? The mirror
       of JumpEngine::isArrivingReinforcement, and the predicate every Stage 7 branch on this side
       keys off: the one legal hex, the stacking bypass, the forced facing, the optional placement.

       Note what it is NOT the complement of. `reinforcement && arrivalTurn == null` is HYPERSPACE
       (the server's isReinforcement); this is the single turn between that and being an ordinary
       deployed ship. A unit that arrived on an earlier turn must answer false, or it would be
       offered for re-placement every Deployment phase for the rest of the game. */
    isArrivingReinforcement: function isArrivingReinforcement(ship) {
        if (!ship || ship.reinforcement !== true) return false;
        if (ship.arrivalTurn === null || ship.arrivalTurn === undefined) return false;

        return (parseInt(ship.arrivalTurn, 10) === gamedata.turn);
    },

    /* ⭐ REINFORCEMENTS_PLAN.md STAGE 9 - HOW MUCH INITIATIVE THIS UNIT IS LOSING FOR COMING OUT
       OF HYPERSPACE OFF COURSE, as a negative number, or 0.

       -5 per hex of scatter and -10 per 60 degrees of facing shift, on the ARRIVAL TURN ONLY. The
       number is not computed here: the server sends the figure its own initiative generators
       applied (BaseShip::getReinforcementArrivalIniModifier, attached in
       TacGamedata::stripForJson), so the two can never quote different penalties for the same
       ship - which on a d100 roll is exactly the kind of disagreement nobody would ever notice
       and everybody would argue about.

       ⚠️ THE KEY'S PRESENCE IS THE WHOLE TEST. It is emitted only when the penalty is non-zero,
       which already means "a reinforcement, arriving this turn, through a doorway that scattered" -
       so there is no arrivalTurn or rule test to repeat here. A gate's doorway never deviates and
       therefore never sends one.

       The single reader for the tooltip line and the ship-window banner both, so the two cannot
       drift apart. */
    getArrivalIniPenalty: function getArrivalIniPenalty(ship) {
        if (!ship || ship.arrivalIniPenalty === undefined || ship.arrivalIniPenalty === null) return 0;

        return parseInt(ship.arrivalIniPenalty, 10) || 0;
    },


    //True if the player still has at least one ship to PLACE this turn — which for a late slot
    //is the turn before it arrives. Anything placing later is excluded; those don't keep the
    //Deployment phase active.
    hasShipsToDeployThisTurn: function hasShipsToDeployThisTurn(playerid) {
        for (const ship of gamedata.ships) {
            if (ship.userid !== playerid) continue;
            if (shipManager.getTurnPlaced(ship) === gamedata.turn) return true;
        }
        return false;
    },

    //Need abridged version of this to prevent false positive returns from main function when a system is offline e.g. cloaking devices
    getSpecialAbilityStealth: function getSpecialAbilityStealth(ship, ability) {
        for (var i in ship.systems) {
            var system = ship.systems[i];

            for (var a in system.specialAbilities) {
                if (system.specialAbilities[a] == ability) return system;
            }
        }

        return false;
    },

    /*Pre-Turn/Deployment forecast for the stealth systems the player can still toggle
      (Torvalus Shading Field, Trek Cloaking Device). The server only re-runs its detection
      test when the phase ADVANCES (checkStealthNextPhase, called from Deployment->advance),
      so the detected/detectedNew values the client holds during gamephase -1 describe the
      PREVIOUS check and never move while the player switches the system on and off. This
      re-runs the server's rule against the current toggle so the tooltip and ship window can
      answer "would I be detected if I commit this?" - the same live feedback a Hyach stealth
      ship already gets when it is assigned non-DEW EW (Stealth.isDetectedStealth reads the
      EW live, and ShipEwChanged repaints).

      Returns true (would be detected) / false (would stay hidden), or NULL when no forecast
      applies - callers must then fall back to the stored server state untouched.
      Deliberately own-team only: an enemy's pending toggle is not ours to see, and every
      enemy-facing caller (shouldBeHidden, ballistic icons, replay) must keep reading the
      committed values.*/
    getStealthToggleForecast: function getStealthToggleForecast(ship) {
        //Ordered cheapest-first: isDetected() runs this for every ship it is asked about, so the
        //two plain property reads must reject the whole fleet before any function call happens.
        if (!ship.trueStealth) return null; //nothing to forecast - the only ships that can hide
        if (ship.mine) return null; //mines settle their own stealth, no Active toggle
        if (gamedata.gamephase != -1) return null; //only while Deployment/Pre-Turn orders are open
        if (gamedata.turn == 1) return null; //turn 1 Deployment never hides anyone (both isDetected* agree)
        if (gamedata.replay) return null;
        if (!gamedata.isMyorMyTeamShip(ship)) return null;
        if (shipManager.isDestroyed(ship)) return null;
        if (shipManager.getTurnDeployed(ship) > gamedata.turn) return null; //not on the board yet

        if (ship.faction == "Torvalus Speculators") {
            var shadingField = shipManager.systems.getSystemByName(ship, "ShadingField");
            if (shadingField) return shadingField.forecastDetectionTorvalus(ship);
            return null;
        }

        if (shipManager.getSpecialAbilityStealth(ship, "Cloaking")) {
            var cloakingDevice = shipManager.systems.getSystemByName(ship, "CloakingDevice");
            if (cloakingDevice) return cloakingDevice.forecastDetectionTrek(ship);
            return null;
        }

        return null;
    },

    /*Shared enemy sweep for the stealth forecasts above: walks every live enemy unit and asks
      getDetectionRange(otherShip) how far THAT unit can see, returning true as soon as one is
      in range with line of sight. Mirrors the server's own loops (ShadingField::isDetected in
      baseSystems.php, CloakingDevice::isDetected in customTrek.php) - same skips, same LoS
      test - so the forecast matches what the phase advance will actually record.
      Units that have not deployed yet are skipped on top of the server's skips: they are not
      on the board, and their client-side position is whatever their last movement says.*/
    isDetectedByAnyEnemy: function isDetectedByAnyEnemy(ship, getDetectionRange) {
        var blockedHexes = gamedata.blockedHexes;
        var shipPos = shipManager.getShipPosition(ship);

        for (var i in gamedata.ships) {
            var otherShip = gamedata.ships[i];
            if (otherShip.team == ship.team) continue; //loose: team ids arrive as JSON and drift string/int
            if (gamedata.isTerrain(otherShip.shipSizeClass, otherShip.userid)) continue;
            if (shipManager.isDestroyed(otherShip)) continue;
            if (shipManager.getTurnDeployed(otherShip) > gamedata.turn) continue;

            var range = getDetectionRange(otherShip);
            if (range <= 0) continue;

            var distance = parseFloat(mathlib.getDistanceBetweenShipsInHex(ship, otherShip));
            if (distance > range) continue;

            //LoS is only worth testing on a map that actually has blocking hexes (as the server does)
            if (blockedHexes && blockedHexes.length > 0
                && mathlib.isLoSBlocked(shipPos, shipManager.getShipPosition(otherShip), blockedHexes)) continue;

            return true;
        }

        return false;
    },

    //Main Front End check on whether a stealth ship is detected or not, called in various places and diverts to appropriate systems.
    isDetected: function (ship) {
        //A Shading Field / Cloaking Device the player can still toggle this phase is answered
        //live rather than from the last committed check - see getStealthToggleForecast.
        //trueStealth first so non-stealth callers never pay for the lookup.
        if (ship.trueStealth) {
            var forecast = shipManager.getStealthToggleForecast(ship);
            if (forecast !== null) return forecast;
        }

        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem) {
                return stealthSystem.isDetectedMine(ship);
            } else {
                return true; //No stealth system, is detected I guess.
            }
        }

        if (ship.faction == "Torvalus Speculators") {
            //getSystemByName descends into fighters, so it resolves a flight's Shading Field too.
            //Which copy it returns doesn't matter: isDetectedTorvalus re-resolves a flight to the
            //canonical (first-fighter) field internally before reading detection state.
            var shadingField = shipManager.systems.getSystemByName(ship, "ShadingField");
            if (shadingField) {
                return shadingField.isDetectedTorvalus(ship, 15);
            } else {
                return true; //Torvalus with no Shading field, is detected I guess.
            }
        }
        if (shipManager.getSpecialAbilityStealth(ship, "Cloaking")) {
            var cloakingDevice = shipManager.systems.getSystemByName(ship, "CloakingDevice");
            if (cloakingDevice) {
                return cloakingDevice.isDetectedTrek(ship);
            } else {
                return true; //No cloak, is detected I guess.
            }
        }

        if (shipManager.getSpecialAbilityStealth(ship, "Stealth")) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "stealth");
            if (stealthSystem) {
                return stealthSystem.isDetectedStealth(ship);
            } else {
                return true; //No stealth system, is detected I guess.
            }
        }

        return true; //No one had any stealth systems, shouldn't reach here but just in case.
    },


};
