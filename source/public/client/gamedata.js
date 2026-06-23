"use strict";

window.gamedata = {

    gamewidth: 1600,
    gameheight: 1000,
    zoom: 0.6,
    zoomincrement: 0.1,
    scroll: { x: 0, y: 0 },
    scrollOffset: { x: 0, y: 0 },
    animating: false,
    ships: Array(),
    ballistics: Array(),
    thisplayer: -1,
    waiting: false,
    selectedShips: Array(),
    targetedShips: Array(),
    selectedSystems: Array(),
    effectsDrawing: false,
    finished: false,
    gamephase: 0,
    subphase: 0,
    selectedSlot: null,
    gamespace: null,
    replay: false,
    playAudio: true, //To allow toggling of audio during Replay.    
    showLoS: false,
    blockedHexes: Array(),
    isStealthPresent: false,
    areMinesPresent: false, //Marks that ENEMY mines are present.

    mouseOverShipId: -1,

    /*
    selectShip: function(ship, add){
        if (!add){
            for (var i in gamedata.selectedShips){
                var s2 = gamedata.selectedShips[i];
                gamedata.unSelectShip(s2);
            }
            gamedata.selectedShips = Array();
            
        }
            
        
        
        if (!gamedata.isSelected(ship)){   
            gamedata.selectedShips.push(ship);
            
            gamedata.shipStatusChanged(ship);
            shipWindowManager.checkIfAnyStatusOpen(ship);
            gamedata.selectedSystems = Array();
           
        } 
        
        
    },
     
    targetShip: function(ship, add){
        if (!add){
            for (var i in gamedata.targetedShips){
                var s2 = gamedata.targetedShips[i];
                gamedata.unTargetShip(s2);
            }
            gamedata.targetedShips = Array();
            
        }
            
        
        
        if (!gamedata.isTargeted(ship)){   
            gamedata.targetedShips.push(ship);
            
                
            shipWindowManager.checkShipWindow(ship);
        } 
        
    },
    */
    elintShips: Array(),

    getElintShips: function getElintShips() {
        if (gamedata.elintShips.length === 0) {
            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];
                if (shipManager.isElint(ship)) gamedata.elintShips.push(ship);
            }
        }
        return gamedata.elintShips;
    },
    /*
    unTargetShip: function(ship){
        
    },
    
    unSelectShip: function(ship){
        if (gamedata.gamephase == 3)
            UI.shipMovement.hide();
        gamedata.selectedSystems = Array();
    },
    isTargeted: function(ship){
        if ($.inArray(ship, gamedata.targetedShips) >= 0)
            return true;
            
        return false;
    },
    
    isSelected: function(ship){
        if ($.inArray(ship, gamedata.selectedShips) >= 0)
            return true;
            
        return false;
    },
     */
    getSelectedShip: function getSelectedShip() {

        throw new Error("This won't work anymore. Get ship from phase strategy");

        for (var i in gamedata.selectedShips) {
            return gamedata.selectedShips[i];
        }

        return false;
    },

    getTargetedShip: function getTargetedShip() {
        for (var i in gamedata.targetedShips) {
            return gamedata.targetedShips[i];
        }
    },

    getFirstFriendlyShip: function getFirstFriendlyShip() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isMyShip(ship) && !ship.mine) {
                return ship;
            }
        }
    },

    getFirstFriendlyShipDeployment: function getFirstFriendlyShipDeployment() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (shipManager.getTurnDeployed(ship) > gamedata.turn) continue;
            //Stage 7 (Hangar Ops): skip flights queued for deployment-phase dock —
            //they're going into a hangar, not onto the map, so auto-selecting them
            //in deployment would be misleading.
            if (ship.pendingDeployDock) continue;
            //LCV Rails: skip LCVs queued to deploy-dock onto a rail.
            if (ship.pendingLcvDeployDock) continue;

            if (gamedata.isMyShip(ship) && !ship.mine) {
                return ship;
            }
        }
    },


    getFirstEnemyShip: function getFirstEnemyShip() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (!gamedata.isMyShip(ship)) {
                return ship;
            }
        }
    },

    /*Marcin Sawicki: re-created so there are no dumps during replay...*/
    //TODO: remove this function AND ALL CALLS TO IT (delete or replace by new approach, as appropriate)
    /*commenting out...
    getActiveShip: function getActiveShip() {
        return null;
    },
    */

    getActiveShips: function getActiveShips() {
        if (Array.isArray(gamedata.activeship)) {
            return gamedata.activeship.map(function (id) {
                return gamedata.getShip(id);
            }).filter(function (ship) {
                return ship && !ship.mine && !gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !(shipManager.getTurnDeployed(ship) > gamedata.turn);
            });
        } else {
            return [gamedata.getShip(gamedata.activeship)].filter(function (ship) {
                return ship && !ship.mine && !gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !(shipManager.getTurnDeployed(ship) > gamedata.turn);
            });
        }
    },

    getMyActiveShips: function getMyActiveShips() {
        return gamedata.getActiveShips().filter(ship => gamedata.isMyShip(ship) && !ship.mine);
    },

    getShip: function getShip(id) {
        for (var i in gamedata.ships) {
            if (gamedata.ships[i].id == id) {
                return gamedata.ships[i];
            }
        }

        return null;
    },

    isMyShip: function isMyShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid) && (gamedata.gamephase !== -1)) return false; //Players can purchase Terrain, and will need to select to deploy it.
        //if (ship.mine && (gamedata.gamephase !== -1)) return false;           
        return ship.userid === gamedata.thisplayer;
    },

    isMyorMyTeamShip: function isMyorMyTeamShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid) && (gamedata.gamephase !== -1)) return false; //Players can purchase Terrain, and will need to select to deploy it. 
        //if (ship.mine && (gamedata.gamephase !== -1)) return false;        
        if (ship.userid === gamedata.thisplayer) return true;
        if (ship.team === gamedata.getPlayerTeam()) return true;

        return false;
    },

    isEnemy: function isEnemy(target, shooter) {
        if (!shooter) {
            throw new Error("You need to give shooter for this one");
        }

        if (gamedata.isTerrain(target.shipSizeClass, target.userid)) {
            return true; // Always treat Terrain as enemies
        }

        return target.team !== shooter.team;
    },

    isTerrain: function isTerrain(shipSizeClass, userid) {
        if (shipSizeClass == 5 || userid == -5) return true;
        return false;

    },

    isMyOrTeamOneShip: function isMyOrTeamOneShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            return false; // Ensure terrain units are never considered friendly
        }

        if (gamedata.isPlayerInGame()) {
            return ship.team === gamedata.getPlayerTeam();
        } else {
            return ship.team === 1;
        }
    },


    canTargetAlly: function canTargetAlly(ship) {//30 June 2024 - DK - Added for Ally targeting.
        for (var i in gamedata.selectedSystems) {
            if (gamedata.selectedSystems[i].canTargetAllies || gamedata.selectedSystems[i].canTargetAll) return true;
        }
    },

    // Base team colours (sRGB 0-255), team 1..8. Teams 2-3 match the enemy/ally
    // colours used for participants so the two views stay consistent. Team 1 uses
    // CSS limegreen (== combat-log FIRE: "mine" green); note the participant
    // "mine" ship-icon overlay in getShipOverlayColor is intentionally a lighter
    // green ([160,250,100]) and is NOT kept in sync with this.
    teamBaseColors: [
        [50, 205, 50],   // 1 Green  (CSS limegreen)
        [255, 80, 80],   // 2 Red    (== "enemy")
        [51, 173, 255],  // 3 Blue   (== "ally")
        [255, 150, 40],  // 4 Orange
        [40, 230, 230],  // 5 Cyan
        [230, 40, 230],  // 6 Magenta
        [240, 230, 60],  // 7 Yellow
        [170, 90, 230]   // 8 Purple
    ],

    // Raw sRGB [r,g,b] (0-255) team colour keyed on ship.team, for an observer.
    // Teams beyond 8 reuse the palette but lightened one step per full cycle.
    // Use this for canvas 2D (combat log, selection circles); getTeamColor()
    // wraps it for sprite overlays (linear space).
    getTeamColorRGB: function getTeamColorRGB(team) {
        var palette = gamedata.teamBaseColors;
        var count = palette.length;

        // Teams are 1-indexed; guard against missing/0 values.
        var index = (parseInt(team, 10) || 1) - 1;
        if (index < 0) index = 0;

        var cycle = Math.floor(index / count); // 0 for teams 1-8, 1 for 9-16, ...
        var base = palette[index % count];

        // Each extra cycle blends 35% further toward white (capped so it never washes out).
        var lighten = Math.min(cycle * 0.35, 0.85);

        return [
            base[0] + (255 - base[0]) * lighten,
            base[1] + (255 - base[1]) * lighten,
            base[2] + (255 - base[2]) * lighten
        ];
    },

    // Raw team colours are tuned for the 3D sprite overlays and read as too
    // bright/neon against the dark IniGUI panel — noticeably richer than the
    // muted CSS participant colours (green/#6091d2/red, #2ea86b/#6d95c5/#c65d4a).
    // Darken them toward black for IniGUI use only, leaving sprites/combat log
    // on the full-strength palette. Returns integer sRGB [r,g,b].
    INI_TEAM_DARKEN: 0.65,
    getIniTeamColorRGB: function getIniTeamColorRGB(team) {
        var rgb = gamedata.getTeamColorRGB(team);
        var f = gamedata.INI_TEAM_DARKEN;
        return [
            Math.round(rgb[0] * f),
            Math.round(rgb[1] * f),
            Math.round(rgb[2] * f)
        ];
    },

    // Inline style for an observer's "active mover" IniGUI box, derived from the
    // ship's team colour. Mirrors the .iniActive* CSS (border + translucent fill +
    // glow) but keyed on team instead of mine/ally/enemy.
    getIniActiveTeamStyle: function getIniActiveTeamStyle(team) {
        var rgb = gamedata.getIniTeamColorRGB(team);
        var r = rgb[0];
        var g = rgb[1];
        var b = rgb[2];
        // Dark, desaturated fill (~22% of the team colour) so the team-coloured
        // text stays readable, matching the dim backgrounds the class versions use.
        var fillR = Math.round(r * 0.22);
        var fillG = Math.round(g * 0.22);
        var fillB = Math.round(b * 0.22);
        return "border:1px solid rgb(" + r + "," + g + "," + b + ") !important;"
            + "background-color:rgba(" + fillR + "," + fillG + "," + fillB + ",0.9) !important;"
            + "box-shadow:0px 0px 3px rgb(" + r + "," + g + "," + b + ");";
    },

    // Linear-space THREE.Color version of getTeamColorRGB, ready for sprite overlays.
    getTeamColor: function getTeamColor(team) {
        var rgb = gamedata.getTeamColorRGB(team);
        return new THREE.Color(rgb[0] / 255, rgb[1] / 255, rgb[2] / 255).convertSRGBToLinear();
    },

    // Overlay colour for a ship icon. Participants see the familiar mine/ally/enemy
    // scheme; observers (not in the game) see a distinct colour per team instead of
    // everything being red.
    getShipOverlayColor: function getShipOverlayColor(ship, mine, ally, terrain) {
        if (terrain) {
            return new THREE.Color(0xBE / 255, 0xBE / 255, 0xBE / 255).convertSRGBToLinear(); // Off-white
        }

        if (!gamedata.isPlayerInGame()) {
            return gamedata.getTeamColor(ship.team);
        }

        if (mine) {
            return new THREE.Color(160 / 255, 250 / 255, 100 / 255).convertSRGBToLinear(); // Light green
        }
        if (ally) {
            return new THREE.Color(51 / 255, 173 / 255, 255 / 255).convertSRGBToLinear(); // Light blue
        }
        return new THREE.Color(255 / 255, 40 / 255, 40 / 255).convertSRGBToLinear(); // Red
    },

    isPlayerInGame: function isPlayerInGame() {
        if (gamedata.thisplayer === null || gamedata.thisplayer === -1) {
            return false;
        }

        var slot = Object.keys(gamedata.slots).find(function (key) {
            var slot = gamedata.slots[key];
            return slot.playerid === gamedata.thisplayer;
        })
        return Boolean(slot);
    },

    shipStatusChanged: function shipStatusChanged(ship) {
        shipWindowManager.setData(ship);
        gamedata.checkGameStatus();
        window.webglScene.receiveGamedata(this);
    },

    //True if a docked hangarUsage entry is a faction-default shuttle (Shuttle
    //subclass / Flyer / MinesweepingShuttle) rather than a real fighter. Mirrors
    //HangarOps::isDefaultShuttleClass + the shuttle-pool hangarTypes the server
    //seats default shuttles under (populateInitialHangarUsage). The full PHP
    //subclass hierarchy isn't visible client-side, so match the known default
    //shuttle phpclasses by name (same set as systems.js excludesDefaultShuttles)
    //and the default-shuttle pool hangarTypes.
    isDefaultShuttleEntry: function isDefaultShuttleEntry(entry) {
        if (!entry) return false;
        var shuttleClasses = { "Shuttle": 1, "MinesweepingShuttle": 1, "CargoShuttle": 1, "MedicalShuttle": 1, "Flyer": 1, "FlyerProtectorate": 1, "lifeboats":1 };
        if (entry.phpclass && shuttleClasses[entry.phpclass]) return true;
        var t = String(entry.hangarType || "").toLowerCase().trim();
        if (t === "shuttles" || t === "minesweeping shuttles") return true;
        return false;
    },

    //Firing-phase commit warning: true if this carrier still has launchable
    //(non-default-shuttle) fighters sitting in a hangar that has launch capacity
    //left this turn AND no launch already ordered on that bay. Mirrors the launch
    //gate in shipTooltipFireMenu.js hasLaunchableHangar (catapult/rail/ShadowHangar
    //handling, cannotLaunch wrecks, output budget) but additionally excludes
    //default shuttles and bays the player has already queued a launch on.
    shipHasUnlaunchedFighters: function shipHasUnlaunchedFighters(ship) {
        if (!ship || !ship.systems) return false;
        for (var i in ship.systems) {
            var sys = ship.systems[i];
            if (!sys) continue;
            var isCat = !!(sys.isCatapult || sys.name === 'catapult');
            if (sys.name !== 'hangar' && sys.name !== 'fighterRail' && !isCat) continue;
            //ShadowHangars launch only via the Fighter Bomb weapon — not the launch dialog.
            if (sys.isShadowHangar) continue;
            if (shipManager.systems.isDestroyed(ship, sys)) continue;
            if (!Array.isArray(sys.hangarUsage) || sys.hangarUsage.length === 0) continue;

            //Any launchable craft that ISN'T a default shuttle?
            var hasRealFighter = sys.hangarUsage.some(function (e) {
                return e && !e.cannotLaunch && !gamedata.isDefaultShuttleEntry(e);
            });
            if (!hasRealFighter) continue;

            //A launch already ordered on this bay means the player IS launching here.
            if (Array.isArray(sys.pendingLaunchOrders) && sys.pendingLaunchOrders.length > 0) continue;

            //Catapults launch regardless of output budget; ordinary hangars/rails
            //need remaining launch+land budget this turn.
            if (isCat) return true;
            var output = parseInt(sys.output || 0, 10);
            var used = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            if (used >= output) continue;
            return true;
        }
        return false;
    },

    onCommitClicked: function onCommitClicked(e) {

        if (gamedata.waiting == true) return;

        if (gamedata.status == "FINISHED") return;

        // CHECK for Base Rotation
        if (gamedata.gamephase == -1 && gamedata.turn == 1) {
            var bases = [];

            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];
                if (ship.userid == gamedata.thisplayer) {
                    if (ship.base) {
                        bases.push(ship);
                    }
                }
            }
            if (bases) {
                for (var i = 0; i < bases.length; i++) {
                    if ((bases[i].movement[1].value == 0) && (!bases[i].nonRotating)) {
                        confirm.error("Please setup the rotation of your starbase.", function () { });
                        return false;
                    }
                }
            }
        }

        // CHECK for Mine settings
        if (gamedata.gamephase == -1) {
            var mines = [];
            var html = '';

            var playerHasMines = gamedata.ships.some(function (ship) {
                return ship.mine &&
                    ship.userid == gamedata.thisplayer &&
                    !shipManager.isDestroyed(ship) &&
                    shipManager.getTurnDeployed(ship) <= gamedata.turn;
            });
            if (playerHasMines) {
                for (var i in gamedata.ships) {
                    var ship = gamedata.ships[i];
                    if (ship.userid == gamedata.thisplayer) {
                        if (ship.mine) {
                            mines.push(ship);
                        }
                    }
                }

                if (mines && mines.length > 0) {
                    var unsetClasses = {};

                    for (var i = 0; i < mines.length; i++) {
                        var mine = mines[i];
                        var hasUnset = false;
                        for (var j in mine.systems) {
                            var sys = mine.systems[j];
                            if (sys.name == "CaptorMine" || sys.name == "ProximityMine" || sys.name == "MineControllerDEW") {
                                if (!sys.mineSet) {
                                    hasUnset = true;
                                    break;
                                }
                            }
                        }

                        if (hasUnset) {
                            unsetClasses[mine.shipClass] = true;
                        }
                    }

                    var classList = Object.keys(unsetClasses);
                    if (classList.length > 0) {
                        html += "You have not set ranges for the following types of mine:";
                        for (var c = 0; c < classList.length; c++) {
                            html += "<br><span class='ship-name'>" + classList[c] + "</span>";
                        }
                        html += "<br>They will default to their maximum range.<br>";
                    }
                }
            }
            confirm.confirm(html + '<br><span class="commit-confirm-q">Are you sure you wish to commit your orders?</span>', gamedata.doCommit);


            // CHECK for NO EW
        } else if (gamedata.gamephase == 1) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if ((!gamedata.ships[ship].mine || gamedata.ships[ship].commandControl) &&
                        !shipManager.isDestroyed(gamedata.ships[ship]) &&
                        !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {

                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoEW = [];
            var selfDestructing = [];
            var jumping = [];
            var notLaunching = [];
            var notSetAA = [];//available Adaptive Armor points remaining!
            var notSetFC = [];//available BFCP points remaining for Hyach!
            var powerSurplus = [];//power surplus

            for (var ship in myShips) {

                if (!myShips[ship].flight) {

                    //loop at systems looking for overloading reactor(s)
                    for (var syst in myShips[ship].systems) {
                        if (myShips[ship].systems[syst].name == "reactor") {
                            for (var pow in myShips[ship].systems[syst].power) {
                                if (myShips[ship].systems[syst].power[pow].turn == gamedata.turn && myShips[ship].systems[syst].power[pow].type == 2) {
                                    selfDestructing.push(myShips[ship]);
                                }
                            }
                        } else if (myShips[ship].systems[syst].name == "jumpEngine") {
                            for (var pow in myShips[ship].systems[syst].power) {
                                if (myShips[ship].systems[syst].power[pow].turn == gamedata.turn && myShips[ship].systems[syst].power[pow].type == 2) {
                                    jumping.push(myShips[ship]);
                                }
                            }
                        } else if (myShips[ship].systems[syst].name == "adaptiveArmorController") {
                            if (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[syst])
                                && myShips[ship].systems[syst].canIncreaseAnything(myShips[ship])) {
                                notSetAA.push(myShips[ship]);
                            }
                        } else if (myShips[ship].systems[syst].name == "hyachComputer") {
                            if (myShips[ship].systems[syst].canIncreaseAnything()) {
                                notSetFC.push(myShips[ship]);
                            }
                        }
                    }

                    if (shipManager.isDisabled(myShips[ship])) {
                        continue;
                    }

                    //checking for power surplus
                    if (shipManager.power.getReactorPower(myShips[ship], shipManager.systems.getSystemByName(myShips[ship], "reactor")) > 0) {
                        powerSurplus.push(myShips[ship]);
                    }

                    if (gamedata.turn == 1) {
                        if (myShips[ship].EW.length == 0) {
                            hasNoEW.push(myShips[ship]);
                        }
                    } else if (gamedata.turn > 1) {
                        var hasEW = 0;
                        for (var entry in myShips[ship].EW) {
                            var ew = myShips[ship].EW[entry];
                            if (ew.turn == gamedata.turn && ew.type != "DEW") {
                                hasEW = 1;
                                break;
                            }
                        }
                        if (hasEW == 0) {
                            hasNoEW.push(myShips[ship]);
                        };
                    }

                    //check for ballistic launch
                    var fired = 0;
                    var hasReadyLaunchers = false;
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.ballistic) { //only ballistic weapons are of interest now
                            if (currWeapon.fireOrders.length > 0) {
                                fired = 1;
                                break;
                            }
                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                            ) { //non-ballistic weapon ready to fire
                                hasReadyLaunchers = true;
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyLaunchers) { //no missile launch was declared, and there are ready launchers
                        notLaunching.push(myShips[ship]);
                    }
                } else { //fighter flight
                    //check for ballistic launch
                    //and Adaptive Armor
                    var fired = 0;
                    var didNotSetAA = false;
                    var hasReadyLaunchers = false;
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if ((fired == 0) && currWeapon.ballistic) { //only ballistic weapons are of interest now
                                        if (currWeapon.fireOrders.length > 0) {
                                            fired = 1;
                                            //break;
                                        }
                                        /*
                                        if (weaponManager.isLoaded(currWeapon) ){ //ballistic weapon ready to fire
                                            hasReadyLaunchers = true;
                                        }*/
                                        if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant
                                        ) { //non-ballistic weapon ready to fire
                                            hasReadyLaunchers = true;
                                        }
                                    } else if (currWeapon.name == "adaptiveArmorController") {
                                        //skip destroyed fighters - their unlocked AA can never be allocated
                                        if (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i])
                                            && currWeapon.canIncreaseAnything(myShips[ship])) {
                                            didNotSetAA = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyLaunchers) { //no missile launch was declared, and there are ready launchers
                        notLaunching.push(myShips[ship]);
                    }
                    if (didNotSetAA) { //available Adaptive Armor has not been set
                        notSetAA.push(myShips[ship]);
                    }
                }
            }

            /*
                      if (hasNoEW.length == 0){
                          confirm.confirm("Are you sure you wish to COMMIT YOUR INITIAL ORDERS?", gamedata.doCommit);
                      }
                      else {
                          var html = "You have not assigned any EW for the following ships: ";
                              html += "<br>";
                          for (var ship in hasNoEW){
                              html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                              html += "<br>";
                          }
                          confirm.confirm((html + "<br>Are you sure you wish to COMMIT YOUR INITIAL ORDERS?"), gamedata.doCommit);
                      }
               */
            var html = '';
            if (selfDestructing.length > 0) {
                html += "You have ordering following ships to SELF DESTRUCT: ";
                html += "<br>";
                for (var ship in selfDestructing) {
                    //html += selfDestructing[ship].name + " (" + selfDestructing[ship].shipClass + ")";
                    html += '<span class="ship-name">' + selfDestructing[ship].name + ' (' + selfDestructing[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (jumping.length > 0) {
                html += "You have ordering following ships to JUMP TO HYPERSPACE: ";
                html += "<br>";
                for (var ship in jumping) {
                    //html += jumping[ship].name + " (" + jumping[ship].shipClass + ")";
                    html += '<span class="ship-name">' + jumping[ship].name + ' (' + jumping[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (hasNoEW.length > 0) {
                // New check to see if Scanner exists / has positive output before giving warning - DK 01/25
                for (var i = hasNoEW.length - 1; i >= 0; i--) {
                    var ship = hasNoEW[i];
                    const standardScanners = shipManager.systems.getSystemListByName(ship, "scanner");
                    const elintScanners = shipManager.systems.getSystemListByName(ship, "elintScanner");
                    const scanners = [...standardScanners, ...elintScanners];

                    // Check if all scanners for this ship are either destroyed or have output <= 0
                    var allScannersDisabled = scanners.every(function (scanner) {
                        return shipManager.systems.isDestroyed(ship, scanner) ||
                            shipManager.systems.getOutput(ship, scanner) <= 0;
                    });

                    // If all scanners are disabled, remove the ship from hasNoEW
                    if (allScannersDisabled) {
                        hasNoEW.splice(i, 1);
                    }
                }

                //Now check again and give message if hasNoEW length still over 0.            
                if (hasNoEW.length > 0) {
                    html += "You have not assigned any EW for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoEW) {
                        //html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoEW[ship].name + ' (' + hasNoEW[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                    html += "<br>";
                }
            }
            if (notLaunching.length > 0) {
                html += "You have not assigned any ballistic launch for the following ships: ";
                html += "<br>";
                for (var ship in notLaunching) {
                    //html += notLaunching[ship].name + " (" + notLaunching[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notLaunching[ship].name + ' (' + notLaunching[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetAA.length > 0) {
                html += "You have not assigned available AA points for the following units: ";
                html += "<br>";
                for (var ship in notSetAA) {
                    //html += notSetAA[ship].name + " (" + notSetAA[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notSetAA[ship].name + ' (' + notSetAA[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetFC.length > 0) {
                html += "You have not assigned available BFCP points for the following units: ";
                html += "<br>";
                for (var ship in notSetFC) {
                    //html += notSetFC[ship].name + " (" + notSetFC[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notSetFC[ship].name + ' (' + notSetFC[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (powerSurplus.length > 0) {
                html += "The following ships have unassigned Power reserves: ";
                html += "<br>";
                for (var ship in powerSurplus) {
                    //show actual surplus, too - like: Surplusser (PowerShip) - <10>
                    var surplusVal = shipManager.power.getReactorPower(powerSurplus[ship], shipManager.systems.getSystemByName(powerSurplus[ship], "reactor"));
                    //html += powerSurplus[ship].name + " (" + powerSurplus[ship].shipClass + "): <b>&#60;" + surplusVal + '&#62;</b>';
                    html += '<span class="ship-name">' + powerSurplus[ship].name + ' (' + powerSurplus[ship].shipClass + '): <b>&#60;' + surplusVal + '&#62;</b></span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            confirm.confirm(html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR INITIAL ORDERS?</span>', gamedata.doCommit);
        }

        else if (gamedata.gamephase == 2) {
            var zeroSpeedShips = [];
            var activeShips = gamedata.getActiveShips();
            var html = '';

            for (var i in activeShips) {
                var ship = activeShips[i];
                if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
                    if (shipManager.movement.canChangeSpeed(ship, true) && ship.userid == gamedata.thisplayer) {
                        zeroSpeedShips.push(ship);
                    }
                }
            }

            if (zeroSpeedShips.length > 0) {
                html += "<br>";
                html += "The following ships can still move: <br>";

                for (var j in zeroSpeedShips) {
                    var movingShip = zeroSpeedShips[j];
                    html += '<span class="ship-name">' + movingShip.name + '</span><br>';
                }
            }

            UI.shipMovement.hide();

            confirm.confirm(
                html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR MOVEMENT ORDERS?</span>',
                gamedata.doCommit,
                function () {
                    UI.shipMovement.show();
                }
            );

            //CHECK for NO PRE FIRE            
        } else if (gamedata.gamephase == 5) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoFO = [];
            var hasSplitFO = [];

            for (var ship in myShips) {
                var fired = 0;
                var hasReadyGuns = false;
                var hasShotsLeft = false; //For split shot weapons that might not have used al their shots.
                if (!myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.preFires) {
                            if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                if (currWeapon.fireOrders.length > 0) {
                                    fired = 1;
                                    if (currWeapon.canSplitShots && currWeapon.fireOrders.length < currWeapon.guns) {
                                        hasShotsLeft = true;
                                    }
                                    break;
                                }
                                if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                    && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                                ) { //non-ballistic weapon ready to fire
                                    hasReadyGuns = true;
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    if (hasShotsLeft) { //Some shots used, but not all.
                        hasSplitFO.push(myShips[ship]);
                    }
                } else if (myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if (currWeapon.preFires) {
                                        if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                            if (currWeapon.fireOrders.length > 0) {
                                                fired = 1;
                                                break;
                                            }
                                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant											
                                            ) { //non-ballistic weapon ready to fire
                                                hasReadyGuns = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                }
            }

            if (hasNoFO.length == 0 && hasSplitFO.length == 0) { //Has no ships with no fireOrders at all.
                confirm.confirm('<span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR FIRE ORDERS?</span>', gamedata.doCommit);
            } else {
                var html = '';
                if (hasNoFO.length > 0) {
                    html += "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        //html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoFO[ship].name + ' (' + hasNoFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasSplitFO[ship].name + ' (' + hasSplitFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                confirm.confirm(html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR FIRE ORDERS?</span>', gamedata.doCommit);
            }
        } else if (gamedata.gamephase == 3) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if ((!gamedata.ships[ship].mine || gamedata.ships[ship].commandControl) &&
                        !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid) &&
                        !shipManager.isDestroyed(gamedata.ships[ship])) {

                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoFO = [];
            var hasSplitFO = [];
            var notLaunchedFighters = []; //carriers with unlaunched non-shuttle fighters + launch capacity

            for (var ship in myShips) {
                var fired = 0;
                var hasReadyGuns = false;
                var hasShotsLeft = false; //For split shot weapons that might not have used al their shots.

                //Warn if this carrier still has launchable fighters (NOT default
                //shuttles) sitting in a hangar with launch capacity left this turn.
                if (gamedata.shipHasUnlaunchedFighters(myShips[ship])) {
                    notLaunchedFighters.push(myShips[ship]);
                }

                if (!myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.preFires) continue;
                        if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                            if (currWeapon.fireOrders.length > 0) {
                                fired = 1;
                                if (currWeapon.canSplitShots && (currWeapon.fireOrders.length < currWeapon.guns || (currWeapon.checkForWastedShots()))) {
                                    hasShotsLeft = true;
                                }
                                break;
                            }
                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                            ) { //non-ballistic weapon ready to fire
                                hasReadyGuns = true;
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    if (hasShotsLeft) { //Some shots used, but not all.
                        hasSplitFO.push(myShips[ship]);
                    }
                } else if (myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                        if (currWeapon.fireOrders.length > 0) {
                                            fired = 1;
                                            break;
                                        }
                                        if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant											
                                        ) { //non-ballistic weapon ready to fire
                                            hasReadyGuns = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    UI.shipMovement.hide();  //To hide combat pivot UI again on commit clicked                  
                }
            }

            if (hasNoFO.length == 0 && hasSplitFO.length == 0 && notLaunchedFighters.length == 0) { //No warnings at all.
                confirm.confirm('<span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR FIRE ORDERS?</span>', gamedata.doCommit);
            } else {
                var html = '';
                if (hasNoFO.length > 0) {
                    html += "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        //html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoFO[ship].name + ' (' + hasNoFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasSplitFO[ship].name + ' (' + hasSplitFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                if (notLaunchedFighters.length > 0) {
                    if (html != '') html += "<br>";
                    //Cobalt-blue header (matches the launch/dock confirm windows' #58c7e6)
                    //so this warning reads as part of the hangar-operations family.
                    html += '<span style="color:#58c7e6; ">';
                    html += "The following ships have not launched fighters:";
                    html += '</span><br>';
                    for (var ship in notLaunchedFighters) {
                        html += '<span class="ship-name">' + notLaunchedFighters[ship].name + ' (' + notLaunchedFighters[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                //confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
                confirm.confirm(
                    html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR FIRING ORDERS?</span>',
                    gamedata.doCommit,
                    function () {
                        UI.shipMovement.show(); //To show combat pivot UI again on Cancel
                    }
                );
            }
        } else if (gamedata.gamephase != 4) {
            confirm.confirm('<span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR TURN?</span>', gamedata.doCommit);
            //            if (window.helper.autocomm!=true) {
            //	            confirm.confirm("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit);
            //            } else {
            //            	gamedata.doCommit();
            //            }	
        } else {
            confirm.confirmOrSurrender('<span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR TURN?</span>', gamedata.doCommit, gamedata.onSurrenderClicked);
            //            if (window.helper.autocomm!=true) {
            //	            confirm.confirmOrSurrender("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit, gamedata.onSurrenderClicked);
            //            } else {
            //	            confirm.askSurrender("Do you wish to SURRENDER?", gamedata.doCommit, gamedata.onSurrenderClicked);
            //            }	
        }
    },

    onSurrenderClicked: function onSurrenderClicked(e) {
        confirm.confirm("Are you sure you wish to SURRENDER THIS MATCH?", gamedata.doSurrender);
    },

    doSurrender: function doSurrender() {
        UI.shipMovement.hide();

        gamedata.status = "SURRENDERED";
        ajaxInterface.submitGamedata();
    },

    doCommit: function doCommit() {
        UI.shipMovement.hide();

        //DEPLOYMENT PHASE
        if (gamedata.gamephase == -1) {
            shipNames = shipManager.systems.getUnusedSpecialists();

            if (shipNames.length > 0) {
                var specialistsError = "The following ships have not selected Specialists:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //specialistsError += "- " + shipName + "<br>";
                    specialistsError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                specialistsError += "<br>You need to choose Specialists for these ships.";
                window.confirm.error(specialistsError, function () { });
                return false;
            }

            //Flights flagged $deploysInHangar (e.g. Orieni HKs) MUST start docked
            //IF the owning fleet still has any hangar that can hold them. If no
            //carrier in this turn's deployment has a fitting slot, we let it pass
            //rather than bricking a poorly-built fleet. The trait is rare, so
            //gather candidate flights first and skip the carrier scan entirely
            //if none of mine have it pending.
            var hangarDeployCandidates = [];
            for (var fk in gamedata.ships) {
                var flight = gamedata.ships[fk];
                if (!flight || !flight.flight) continue;
                if (!flight.deploysInHangar) continue;
                if (flight.pendingDeployDock) continue;
                if (!gamedata.isMyShip(flight)) continue;
                if (shipManager.getTurnDeployed(flight) != gamedata.turn) continue;
                hangarDeployCandidates.push(flight);
            }

            if (hangarDeployCandidates.length > 0
                && window.DeploymentDock
                && typeof window.DeploymentDock.eligibleHangarsForFlight === 'function') {

                var myDeployingCarriers = [];
                for (var ck in gamedata.ships) {
                    var carrier = gamedata.ships[ck];
                    if (!carrier || !gamedata.isMyShip(carrier)) continue;
                    if (carrier.flight) continue;
                    if (shipManager.getTurnDeployed(carrier) != gamedata.turn) continue;
                    myDeployingCarriers.push(carrier);
                }

                var mustDockNames = [];
                for (var hi = 0; hi < hangarDeployCandidates.length; hi++) {
                    var hdFlight = hangarDeployCandidates[hi];
                    for (var ci = 0; ci < myDeployingCarriers.length; ci++) {
                        if (window.DeploymentDock.eligibleHangarsForFlight(myDeployingCarriers[ci], hdFlight).length > 0) {
                            mustDockNames.push(hdFlight.name);
                            break;
                        }
                    }
                }

                if (mustDockNames.length > 0) {
                    var hangarDeployError = "The following flights must be deployed inside a Hangar:<br>";
                    for (var mi = 0; mi < mustDockNames.length; mi++) {
                        hangarDeployError += '<span class="ship-name">- ' + mustDockNames[mi] + '</span><br>';
                    }
                    hangarDeployError += "<br>Dock them into a carrier's hangar before committing yor orders.";
                    window.confirm.error(hangarDeployError, function () { });
                    return false;
                }
            }

            ajaxInterface.submitGamedata();

            //INITIAL ORDERS    
        } else if (gamedata.gamephase == 1) {
            //        	ajaxInterface.fastpolling=true;
            var shipNames = shipManager.power.getShipsNegativePower();

            if (shipNames.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";

                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "<br>You need to turn off systems before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }

            //We have one thrust-boosted weapon in Initial Orders Phase, let's put in a check for it and future - DK 26.11.24
            shipNames = shipManager.movement.getShipsNegativeThrust();

            if (shipNames.length > 0) {
                var negThrustError = "The following ships have insufficient Engine Thrust:<br>";

                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negThrustError += "- " + name + "<br>";
                    negThrustError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negThrustError += "<br>You need to lower channelled thrust before you can commit the turn.";
                window.confirm.error(negThrustError, function () { });
                return false;
            }

            shipNames = shipManager.power.getShipsGraviticShield();

            if (shipNames.length > 0) {
                var tooManyShieldsError = "The following ships have too many active shields:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //tooManyShieldsError += "- " + shipName + "<br>";
                    tooManyShieldsError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                tooManyShieldsError += "<br>You need to turn off shields or boost your shield generator before you can commit the turn.";
                window.confirm.error(tooManyShieldsError, function () { });
                return false;
            }

            shipNames = shipManager.systems.getNegativeBFCP();

            if (shipNames.length > 0) {
                var tooManyBFCPError = "The following ships have too many Bonus Fire Control Points (BFCP) set:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //tooManyBFCPError += "- " + shipName + "<br>";
                    tooManyBFCPError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                tooManyBFCPError += "<br>You need to decrease the number of allocated BFCPs.";
                window.confirm.error(tooManyBFCPError, function () { });
                return false;
            }
            /*
            shipNames = shipManager.systems.getUnusedSpecialists();        	

            if (shipNames.length > 0) {
                var specialistsError = "The following ships have not selected Specialists:<br>";
                
                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //specialistsError += "- " + shipName + "<br>";
                    specialistsError += '<span class="ship-name">- ' + shipName + '</span><br>'; 
                }
                specialistsError += "<br>You need to choose Specialists for these ships.";
                window.confirm.error(specialistsError, function () {});
                return false;                
            }		
            */
            shipNames = shipManager.systems.checkShieldGenValue();

            if (shipNames.length > 0) {
                var shieldCapacityError = "The following ships have directed too much or too little power to their shields:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //shieldCapacityError += "- " + shipName + "<br>";
                    shieldCapacityError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                shieldCapacityError += "<br>You need to change their allocation of shield power.";
                window.confirm.error(shieldCapacityError, function () { });
                return false;
            }

            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            //ammo usage check - AmmoMagazine equipped units
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                if (!currShip.flight) { //actual ship - check for every magazine on board!			
                    for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                        var currMagazine = currShip.systems[i];
                        var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                        if (!checkResult) ammoMagazineError.push(currShip);
                    }
                } else { //fighter flight - check for every fighter separately!
                    var flightCheckResult = true;
                    for (var j in currShip.systems) for (var i in currShip.systems[j].systems) if (currShip.systems[j].systems[i].name == 'ammoMagazine') {
                        var currMagazine = currShip.systems[j].systems[i];
                        var checkResult = currMagazine.doVerifyAmmoUsageFighter(currShip.systems[j]);
                        if (!checkResult) flightCheckResult = false;
                    }
                    if (!flightCheckResult) ammoMagazineError.push(currShip); //at least one fighter uses nonexisting ammo
                }
            }


            //EW correctness check
            var EWIncorrect = []; //too many EW points set
            var EWRestrictedIncorrect = [];//RestrictedEW critical circumvented
            var EWLCVIncorrect = [];//LCV set too many EW to tasks other than OEW
            for (var shipID in myShips) {
                if (!myShips[shipID].flight) {
                    if (ew.convertUnusedToDEW(myShips[shipID]) != true) {
                        EWIncorrect.push(myShips[shipID]);
                    }
                    if (ew.checkRestrictedEW(myShips[shipID]) != true) {
                        EWRestrictedIncorrect.push(myShips[shipID]);
                    }
                    if (ew.checkLCVSensors(myShips[shipID]) != true) {
                        EWLCVIncorrect.push(myShips[shipID]);
                    }
                }
            }


            //Derelict ship firing check (for Initial phase - ballistics only - assuming direct fire weapons all require power...
            var derelictFiring = []; //too many EW points set
            for (var shipID in myShips) {
                if (!myShips[shipID].flight) if (shipManager.power.isPowerless(myShips[shipID])) if (weaponManager.shipHasFiringOrder(myShips[shipID])) {
                    derelictFiring.push(myShips[shipID]);
                }
            }


            var errorText = '';
            if (EWIncorrect.length > 0) {
                errorText += "The following ships have too many EW points set:<br>";
                for (var shipID in EWIncorrect) {
                    //errorText += EWIncorrect[shipID].name + " (" + EWIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWIncorrect[shipID].name + ' (' + EWIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWRestrictedIncorrect.length > 0) {
                errorText += "The following ships have too many EW points set:<br>";
                for (var shipID in EWRestrictedIncorrect) {
                    //errorText += EWRestrictedIncorrect[shipID].name + " (" + EWRestrictedIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWRestrictedIncorrect[shipID].name + ' (' + EWRestrictedIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWLCVIncorrect.length > 0) {
                errorText += "The following LCVs have too many EW points set on non-OEW:<br>";
                for (var shipID in EWLCVIncorrect) {
                    //errorText += EWLCVIncorrect[shipID].name + " (" + EWLCVIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWLCVIncorrect[shipID].name + ' (' + EWLCVIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }


            if (ammoMagazineError.length > 0) {
                errorText += "The following units are trying to launch more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //errorText += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }

            if (derelictFiring.length > 0) {
                errorText += "The following units are derelict and should be considered shut down - cancel all firing orders:<br>";
                for (var shipID in derelictFiring) {
                    //errorText += derelictFiring[shipID].name + " (" + derelictFiring[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + derelictFiring[shipID].name + ' (' + derelictFiring[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }


            if (errorText != '') {
                window.confirm.error(errorText, function () { });
                return false;
            }



            ajaxInterface.submitGamedata();

            //MOVEMENT PHASE    
        } else if (gamedata.gamephase == 2) {

            var mustPivotError = "The following ships must pivot during their movement<br>";
            var foundPShip = false; //Toggle to show error or not
            //Hyach Specialist can actually reduce Thurst below zero through toggling - DK
            var negThrustError = "The following ships have insufficient engine thrust:<br>";
            var foundTShip = false; //Toggle to show error or not

            var active = gamedata.getActiveShips();

            for (var i in active) {
                var pShip = active[i];

                if (pShip.mustPivot) {
                    if (pShip.unavailable) continue;
                    if (pShip.userid != gamedata.thisplayer) continue;
                    if (shipManager.isDestroyed(pShip)) continue;
                    var deployTurn = shipManager.getTurnDeployed(pShip);
                    if (deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

                    var pivoted = shipManager.movement.hasPivoted(pShip)
                    if (!pivoted.left && !pivoted.right) {
                        foundPShip = true;
                        mustPivotError += '<span class="ship-name">- ' + pShip.name + '</span><br>';
                    }
                }

                var tShip = active[i];

                //Limited thrust check to Hyach Specialist now for efficiency, but we can expand it as needed - DK
                if (shipManager.hasSpecialAbility(tShip, "HyachSpecialists") && shipManager.movement.hasNegativeThrust(tShip)) {
                    foundTShip = true;
                    negThrustError += '<span class="ship-name">- ' + tShip.name + '</span><br>';
                }

            }

            if (foundPShip) {
                mustPivotError += "<br>You need to order them to pivot.";
                window.confirm.error(mustPivotError, function () { });
                return false;
            }

            if (foundTShip) {
                negThrustError += "<br>You need to lower channelled thrust before you can commit the turn.";
                window.confirm.error(negThrustError, function () { });
                return false;
            }

            ajaxInterface.submitGamedata();

            /* //Old version of mustPivot check.  Remove if no issues - DK - Dec 2025
            var pivotShips = shipManager.checkConstantPivot();        	

            if (pivotShips.length > 0) {
            	
                // Get the active ships array
                //var active = gamedata.getActiveShips();            	
                var mustPivotError = "The following ships must pivot during their movement<br>";

                // Check if any of the ship ids exist in the active array
                var foundActiveShip = false;
                for (var i in pivotShips) {
                    var ship = pivotShips[i];
                    
                    if (active.some(activeShip => activeShip.id == ship.id)) {
                        foundActiveShip = true;
                        //mustPivotError += "- " + ship.name + "<br>";
                        mustPivotError += '<span class="ship-name">- ' + ship.name + '</span><br>';
                    }
                }

                if (foundActiveShip) {
                    mustPivotError += "<br>You need to order them to pivot.";
                    window.confirm.error(mustPivotError, function () {});
                    return false;
                }
            }	        	        	
            */

            //PRE FIRING PHASE        
        } else if (gamedata.gamephase == 5) {

            //check ammo magazine, there miiight be ammo weapons in Pre-Firing?		
            //ammo usage check - AmmoMagazine equipped units
            var myShips = [];
            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                //check for every magazine on board!
                for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                    var currMagazine = currShip.systems[i];
                    var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                    if (!checkResult) ammoMagazineError.push(currShip);
                }
            }
            if (ammoMagazineError.length > 0) {
                var ammoMagError = "The following units are trying to fire more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //ammoMagError += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    ammoMagError += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    ammoMagError += "<br>";
                }
                ammoMagError += "You need to reduce number of shots (or change mode) before you can commit the turn.";
                window.confirm.error(ammoMagError, function () { });
                return false;
            }


            ajaxInterface.submitGamedata();

            //FIRING PHASE
        } else if (gamedata.gamephase == 3) {

            //prevent Vorlons from borrowing future power for firing 
            //Capacitor-equipped ships cannot commit firing with negative power balance (they actively use power in this phase, AND they don't have any legal option of achieving negative balance by other means)
            var shipNames = shipManager.power.getCapacitorShipsNegativePower();
            if (shipNames.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";
                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "You need to reduce your firing declarations before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }

            //Likewise, Plasma Battery-equipped ships cannot commit firing with negative power balance (they actively use power in this phase for Plasma Webs, AND they don't have any legal option of achieving negative balance by other means)
            var batteryShips = shipManager.power.getPlasmaBatteryShipsNegativePower();
            if (batteryShips.length > 0) {
                var negPowerError = "The following ships have insufficient plasma battery power:<br>";
                for (var index in batteryShips) {
                    var name = batteryShips[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "You need to reduce the number of unboosted Plasma Webs firing in Offensive Mode before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }


            //check ammo magazine		
            //ammo usage check - AmmoMagazine equipped units
            var myShips = [];
            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                //check for every magazine on board!
                for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                    var currMagazine = currShip.systems[i];
                    var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                    if (!checkResult) ammoMagazineError.push(currShip);
                }
            }
            if (ammoMagazineError.length > 0) {
                var ammoMagError = "The following units are trying to fire more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //ammoMagError += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    ammoMagError += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    ammoMagError += "<br>";
                }
                ammoMagError += "You need to reduce number of shots (or change mode) before you can commit the turn.";
                window.confirm.error(ammoMagError, function () { });
                return false;
            }


            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == 4) {
            ajaxInterface.submitGamedata();
        } //else if (gamedata.gamephase == -1) {
        //ajaxInterface.submitGamedata();
        //}
    },


    autoCommitOnMovement: function autoCommitOnMovement(ship) {
        //if (ship.base) {
        //combatLog.logMoves(ship);
        //shipManager.movement.RemoveMovementIndicators();
        //ajaxInterface.submitGamedata();
        //}
    },

    onCancelClicked: function onCancelClicked(e) {
        /* no longer valid
        if (gamedata.gamephase == 2) {
            var ship = gamedata.getActiveShip();
            shipManager.movement.deleteMove(ship);
        }
    */

        if (gamedata.gamephase == 3) {
            var ship = gamedata.getSelectedShip();
            shipManager.movement.deleteMove(ship);
        }
    },

    /*no longer valid
getActiveShipName: function getActiveShipName() {
    var ship = gamedata.getActiveShip();
    if (ship) return ship.name;
    return "";
},
*/

    getPlayerTeam: function getPlayerTeam() {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == gamedata.thisplayer) return slot.team;
        }
    },

    getPlayerSlot: function getPlayerSlot() {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == gamedata.thisplayer) return slot.slot;
        }
    },

    hasSlotSurrendered: function hasSlotSurrendered(slotid) {
        var slot = playerManager.getSlotById(slotid);

        if (slot.surrendered !== null) {
            if (slot.surrendered <= gamedata.turn) { //Surrendered on this turn or before.
                return true;
            }
        }

        return false;
    },

    getPlayerNameById: function getPlayerNameById(id) {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == id) {
                return slot.playername;
            }
        }
    },

    getPhasename: function getPhasename() {
        if (gamedata.gamephase == 1) return "INITIAL ORDERS";

        if (gamedata.gamephase == 2) return "MOVEMENT ORDERS:";

        if (gamedata.gamephase == 5) return "PRE-FIRING ORDERS";

        if (gamedata.gamephase == 3) return "FIRE ORDERS";

        if (gamedata.gamephase == 4) return "FINAL ORDERS";

        if (gamedata.gamephase == -1) {
            if (shipManager.hasShipsToDeployThisTurn(gamedata.thisplayer)) {
                return "DEPLOYMENT";
            } else {
                return "PRE-TURN ORDERS";
            }
        }

        return "ERROR";
    },

    setPhaseClass: function setPhaseClass() {

        var b = $("body");

        b.removeClass("phase1");
        b.removeClass("phase2");
        b.removeClass("phase3");
        b.removeClass("phase4");
        b.removeClass("phase-1");

        b.addClass("phase" + gamedata.gamephase);
    },

    initPhase: function initPhase() {
        gamedata.subphase = 0;
        //shipManager.initShips();
        UI.shipMovement.hide();
        if (gamedata.gamephase == 1) {
            //To recalculate fleet list values in Info Tab without refreshing page
            fleetListManager.reset();
            fleetListManager.displayFleetLists();
        }


        gamedata.setPhaseClass();
        //		window.helper.doUpdateHelpContent(gamedata.gamephase,0);        

    },

    drawIniGUI: function drawIniGUI() {

        var ini_gui = document.getElementById("iniGui");
        ini_gui.innerHTML = "";

        var topicDiv = document.createElement("div");
        topicDiv.className = "topicDiv";

        var span = document.createElement("span");
        span.id = "iniTopic";
        span.innerHTML = "Order of Battle";

        topicDiv.appendChild(span);

        ini_gui.appendChild(topicDiv);

        //var allShips = gamedata.ships;
        var ships = gamedata.ships.filter(function (ship) {
            return !shipManager.isDestroyed(ship)
                && !gamedata.isTerrain(ship.shipSizeClass, ship.userid)
                && !ship.mine
                && !gamedata.hasSlotSurrendered(ship.slot)
                && shipManager.getTurnDeployed(ship) <= gamedata.turn;
        });


        //ships.sort(shipManager.hasBetterInitive);
        var table = document.createElement("table");
        table.id = "iniTable";

        for (var i = 0; i < ships.length; i++) {

            var tr = document.createElement("tr");
            tr.className = "iniTr";
            tr.id = ships[i].id;

            jQuery(tr).addClass('button').on('click', function () {
                window.webglScene.customEvent('ScrollToShip', { shipId: this.id });
            })

            //var categoryIndex = window.SimultaneousMovementRule.getShipCategoryIndex(ships[i]);

            // Observers (not in the game) colour the initiative number by team
            // instead of the mine/ally/enemy scheme. Use the IniGUI-darkened
            // palette so it isn't brighter than the muted CSS participant colours.
            var teamColorCss = "";
            if (!gamedata.isPlayerInGame()) {
                var iniRgb = gamedata.getIniTeamColorRGB(ships[i].team);
                teamColorCss = "color:rgb(" + iniRgb[0] + "," + iniRgb[1] + "," + iniRgb[2] + ");";
            }

            var td = document.createElement("td");
            td.className = "iniOrder";
            td.innerHTML = shipManager.getIniativeOrder(ships[i]);

            if (teamColorCss) {
                td.style.cssText += teamColorCss;
            } else if (gamedata.isMyShip(ships[i])) {
                td.classList.add("iniMyShip");
            } else if (gamedata.isMyorMyTeamShip(ships[i])) {
                td.classList.add("iniAllyShip");
            } else {
                td.classList.add("iniEnemyShip");
            }

            tr.appendChild(td);

            var td = document.createElement("td");
            td.className = "iniInfo";

            var span = document.createElement("span");
            span.innerHTML += "<p class='iniName'>" + ships[i].name + "</p>";
            span.innerHTML += "<p class='iniClass'>" + ships[i].shipClass + "</p>";

            var active = window.SimultaneousMovementRule.isActiveMovementShip(ships[i]);
            if (active !== null) {
                if (active === true && teamColorCss) {
                    // Observers: style the active-mover box from the ship's team colour
                    // instead of the mine/ally/enemy iniActive* classes.
                    td.style.cssText += gamedata.getIniActiveTeamStyle(ships[i].team);
                } else if (active === true && gamedata.isMyShip(ships[i]) && shipManager.movement.isMovementReady(ships[i]) && shipManager.movement.hasDeletableMovements(ships[i])) {
                    td.classList.add("iniActiveMoved");
                } else if (active === true && gamedata.isMyShip(ships[i])) {
                    td.classList.add("iniActive");
                } else if (active === true && gamedata.isMyorMyTeamShip(ships[i])) {
                    td.classList.add("iniActiveAlly");
                } else if (active === true && !gamedata.isMyShip(ships[i])) {
                    td.classList.add("iniActiveEnemy");
                }
            } else {
                if (gamedata.getActiveShips().includes(ships[i])) {
                    if (teamColorCss) {
                        td.style.cssText += gamedata.getIniActiveTeamStyle(ships[i].team);
                    } else {
                        td.classList.add(gamedata.isMyShip(ships[i]) ? "iniActive" : "iniActiveEnemy");
                    }
                }
            }


            td.appendChild(span);

            tr.appendChild(td);

            var td = document.createElement("td");
            td.className = "iniImage";

            var img = document.createElement("img");
            img.src = window.AssetManager.getSmartImagePath(ships[i].imagePath);

            if (ships[i].flight) {
                td.classList.add("flight");
            }

            td.appendChild(img);
            tr.appendChild(td);

            table.appendChild(tr);
        }

        ini_gui.appendChild(table);

        var backDiv = document.getElementById("backDiv");

        // Preserve state
        var isOpen = $(backDiv).data("on");
        if (isOpen === undefined) isOpen = 1; // Default to open

        backDiv.innerHTML = "";


        // $(backDiv).removeData(); // Don't remove!

        var img = new Image();
        img.id = "iniSlider";

        if (isOpen == 0) {
            img.src = "img/pullOut.png";
            $(ini_gui).addClass("closed");
            $(backDiv).addClass("closed");
        } else {
            img.src = "img/pullIn.png";
            $(ini_gui).removeClass("closed");
            $(backDiv).removeClass("closed");
        }

        backDiv.appendChild(img);
        $(backDiv).data("on", isOpen);

        backDiv.addEventListener("click", gamedata.sliderToggle);

        // ── Deploy Mines button ───────────────────────────────────────────────────
        // Only show during deployment phase when the player has un-destroyed mines.
        var existingMineBtn = document.getElementById('mineDeployBtn');
        if (existingMineBtn) existingMineBtn.parentNode.removeChild(existingMineBtn);

        if (gamedata.gamephase === -1 && gamedata.turn == 1) {
            var playerHasMines = gamedata.ships.some(function (ship) {
                return ship.mine &&
                    ship.userid == gamedata.thisplayer &&
                    !shipManager.isDestroyed(ship) &&
                    ship.spawned == -1 &&
                    shipManager.getTurnDeployed(ship) <= gamedata.turn;
            });

            if (playerHasMines) {
                var mineBtn = document.createElement('button');
                mineBtn.id = 'mineDeployBtn';
                //mineBtn.textContent = '💣  Deploy Minefield  💣';
                mineBtn.textContent = 'Deploy Minefield';
                if (window.MineDeployment && window.MineDeployment.isActive()) {
                    mineBtn.classList.add('active');
                }
                mineBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (window.MineDeployment) window.MineDeployment.toggle();
                    if (window.MineDeployment && window.MineDeployment.isActive()) {
                        mineBtn.classList.add('active');
                    } else {
                        mineBtn.classList.remove('active');
                    }
                });
                // Append inside #iniGui so it sits naturally at the bottom of the panel
                ini_gui.appendChild(mineBtn);
            }
        }
    },


    sliderToggle: function sliderToggle() {
        var backDiv = document.getElementById("backDiv");
        var iniGui = document.getElementById("iniGui");

        if ($(backDiv).data("on") == 1) { // If open, close it
            $(iniGui).addClass("closed");
            $(backDiv).addClass("closed");
            $(backDiv).data("on", 0);
            document.getElementById("iniSlider").src = "img/pullOut.png";
        } else { // If closed, open it
            $(iniGui).removeClass("closed");
            $(backDiv).removeClass("closed");
            $(backDiv).data("on", 1);
            document.getElementById("iniSlider").src = "img/pullIn.png";
        }
    },

    showCommitButton: function showCommitButton() {
        $(".committurn").show();
    },

    hideCommitButton: function hideCommitButton() {
        $(".committurn").hide();
    },

    showSurrenderButton: function showSurrenderButton() {
        $(".surrender").on('click', gamedata.onSurrenderClicked).show();
    },

    hideSurrenderButton: function hideSurrenderButton() {
        $(".surrender").off('click', gamedata.onSurrenderClicked).hide();
    },


    checkGameStatus: function checkGameStatus() {

        //TODO: to phase strategy


    },

    goToWaiting: function goToWaiting() {
        if (gamedata.waiting == false) {
            gamedata.waiting = true;
            ajaxInterface.startPollingGamedata();
            gamedata.checkGameStatus();
            webglScene.receiveGamedata(this);
        }
    },

    parseServerData: function parseServerData(serverdata) {
        if (serverdata == null) return;

        // APCu Optimization: Always update timestamp if present
        if (serverdata.last_update) {
            gamedata.lastUpdateTimestamp = serverdata.last_update;
        }

        if (!serverdata.id) return;

        gamedata.turn = serverdata.turn;
        gamedata.gamephase = serverdata.phase;
        gamedata.activeship = serverdata.activeship;
        gamedata.gameid = serverdata.id;
        gamedata.slots = serverdata.slots;
        gamedata.rules = serverdata.rules;
        gamedata.name = serverdata.name;
        gamedata.description = serverdata.description;

        if (!gamedata.replay) {
            gamedata.thisplayer = serverdata.forPlayer;
            gamedata.waiting = serverdata.waiting;
        }
        gamedata.status = serverdata.status;
        gamedata.elintShips = Array();
        gamedata.gamespace = serverdata.gamespace;
        gamedata.blockedHexes = serverdata.blockedHexes;
        gamedata.isStealthPresent = serverdata.isStealthPresent;
        gamedata.areMinesPresent = serverdata.areMinesPresent;

        shipManager.initiated = 0;

        gamedata.setShipsFromJson(serverdata.ships);

        gamedata.initPhase();
        gamedata.drawIniGUI();
        window.webglScene.receiveGamedata(this);

        // ✅ Update Info Tab (Waiting Status) with new data
        if (window.fleetListManager) {
            fleetListManager.refreshed = false;
            fleetListManager.displayFleetLists();
        }

        gamedata.checkGameStatus();
    },

    setShipsFromJson: function setShipsFromJson(jsonShips) {
        //gamedata.ships = Array();

        for (var i in jsonShips) {
            var ship = jsonShips[i];
            gamedata.ships[i] = new Ship(ship);
        }
    },

    checkPlayerHasDeployedShips: function checkPlayerHasDeployedShips() {
        return gamedata.ships.some(ship =>
            shipManager.getTurnDeployed(ship) <= gamedata.turn && gamedata.isMyShip(ship) && !gamedata.isTerrain(ship.shipSizeClass, ship.userid)
        );
    },

};
