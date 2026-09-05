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
    identityReloadPending: false, //Chameleon Sensor Suite (D14) - a reveal has forced a page reload

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
        /* ⭐ SOMETHING THAT STILL NEEDS PLACING, IN PREFERENCE TO ANYTHING ELSE
           (REINFORCEMENTS_PLAN.md Stage 7). On turn 1 every unit places, so this pass returns
           exactly what the loop below would have; on any LATER Deployment phase the two differ
           sharply. A player whose only business this turn is a reinforcement arriving through a
           jump point would otherwise be handed the first ship of their turn-1 fleet - already on
           the board, nothing to do with it - and would have to hunt the arrival's icon down at the
           off-map 'start' marker its slot gave it before they could place anything at all.
           Falls through to the original loop when nothing is placing, so a Pre-Turn phase with no
           placements still selects a ship to work with. */
        for (var p in gamedata.ships) {
            var placing = gamedata.ships[p];
            if (shipManager.getTurnPlaced(placing) != gamedata.turn) continue;
            if (placing.pendingDeployDock || placing.pendingLcvDeployDock) continue;
            if (!gamedata.isMyShip(placing) || placing.mine) continue;
            if (shipManager.isDestroyed(placing)) continue;

            return placing;
        }

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            //PLACEMENT turn: a reinforcement is selectable in the Deployment phase of the turn
            //before it arrives, which is when the player picks its entry hex.
            if (shipManager.getTurnPlaced(ship) > gamedata.turn) continue;
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
        //Uncontrolled (HK-jammed) remote-controlled flights are moved by the server (drift),
        //so exclude them here the way mines/terrain are excluded from getActiveShips: the player
        //must never get the movement UI / become the active ship for one. Gated on remoteControl.
        return gamedata.getActiveShips().filter(ship => gamedata.isMyShip(ship) && !ship.mine && !shipManager.movement.isUncontrolled(ship));
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

    /* JUMP_GATES_PLAN.md - IS THIS UNIT A FIXED JUMP GATE?

       ONE PLACE holds the class name, mirroring shipManager.movement.isJumpVortex, because three
       different sweeps ask: the submit path in ajaxInterface (a gate is the one unit a player may
       order without owning it), the Initial Orders tooltip, and the signal panel.

       phpclass reaches the client on the STATIC blueprint (model/ship.js merges by faction +
       phpclass), so it is always present.

       ⚠️ JumpgateCapital ONLY. jumpgateNew (terrain) and the civilian Jumpgate also mount a Jump
       Engine and are obsolete and out of scope (user ruling 2026-08-23, plan trap 12) - they keep
       their Phase 1 behaviour and must never match here.

       ⚠️ This does NOT loosen isMyShip, and must not be made to. A gate stays terrain for the fleet
       list, the active-ship sweep, the movement UI and the commit checks; the ONE thing that
       changes is that a signal order on its Jump Engine rides the POST. */
    isJumpGate: function isJumpGate(unit) {
        return !!unit && unit.phpclass === "JumpgateCapital";
    },

    /* JUMP_GATES_PLAN.md Stage 3 - THIS GATE'S JUMP ENGINE, or null.

       Keyed on the system NAME, because markGate() deliberately leaves $name 'jumpEngine' so the
       existing client JumpEngine class is reused with no new JS (plan section 3.2). The client
       tells a gate engine from a ship engine by the SHIP - isJumpGate above - never by the system,
       which is why this asks isJumpGate first rather than looking for a flag on the system. */
    getGateJumpEngine: function getGateJumpEngine(gate) {
        if (!gamedata.isJumpGate(gate) || !gate.systems) return null;

        for (var i in gate.systems) {
            var system = gate.systems[i];
            if (system && system.name === 'jumpEngine') return system;
        }

        return null;
    },

    /* ⭐ MY NEAREST UNIT THAT LETS ME SIGNAL THIS GATE, or null when I have none.

       ⭐ WHICH UNIT IS NEVER CHOSEN BY THE PLAYER, and never matters (user ruling 2026-08-23, plan
       section 2.1). The rule is "you have a live unit within the gate's signal range", not "this
       ship signals" - so no ship needs to be selected to signal, and the gate is clicked directly.
       The NEAREST is returned rather than the first found because the distance is what settles a
       contested claim, and it is what the order's targetid records.

       ⭐ NO LINE-OF-SIGHT TEST, DELIBERATELY (user ruling 2026-08-23). Signalling is a transmission,
       not an aimed effect - unlike a ship projecting its own vortex, which runs mathlib.isLoSBlocked
       against gamedata.blockedHexes in weaponManager.targetHex. Its absence is the RULE; do not add
       one here to "match" the ship path.

       ⭐ AND IT NEVER REVEALS THE UNIT IT PICKS. A stealthed, shaded or cloaked ship may be the
       signaller and keeps its concealment - the opposite of the rule for a ship opening its own
       vortex - so there is no isHidden guard here either.

       The server re-derives all of this from the DB in Firing::getGateSignalBlock and overwrites
       the order's targetid with its own answer, so this is a UX predicate and a hint, never an
       authority (plan section 3.3 and trap 4).

       Mirrors JumpEngine::getNearestGateSignaller. Keep the two lists in step. */
    getGateSignalSource: function getGateSignalSource(gate) {
        var engine = gamedata.getGateJumpEngine(gate);
        if (!engine) return null;

        var gateHex = shipManager.getShipPosition(gate);
        var best = null;
        var bestDistance = null;

        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!unit || unit.userid !== gamedata.thisplayer) continue;
            if (unit.removed) continue;
            if (shipManager.isDestroyed(unit)) continue;
            if (gamedata.isTerrain(unit.shipSizeClass, unit.userid)) continue;   //a gate cannot signal itself
            if (shipManager.getTurnDeployed(unit) > gamedata.turn) continue;     //not on the board yet

            var distance = gateHex.distanceTo(shipManager.getShipPosition(unit));
            if (distance > engine.range) continue;

            if (bestDistance === null || distance < bestDistance) {
                bestDistance = distance;
                best = unit;
            }
        }

        return best;
    },

    /* MAY I SIGNAL THIS GATE RIGHT NOW? The condition on the Initial Orders tooltip button, and the
       client mirror of Firing::getGateSignalBlock's list - minus the two rules only the server can
       judge (the one-claim-per-player test, which is a property of the submission, and the targetid
       re-derivation).

       Deliberately NOT a test of ownership: ANY player may signal ANY gate, including one the enemy
       bought. That is the whole point of the contested-claim rule (plan section 2.4), and it is why
       the gate had to be let through the POST at all (plan section 3.1). */
    canSignalJumpGate: function canSignalJumpGate(gate) {
        if (gamedata.gamephase !== 1) return false;      //declared in Initial Orders and nowhere else
        if (gamedata.waiting) return false;

        var engine = gamedata.getGateJumpEngine(gate);
        if (!engine) return false;

        if (shipManager.isDestroyed(gate)) return false;
        if (shipManager.systems.isDestroyed(gate, engine)) return false;
        if (shipManager.power.isOffline(gate, engine)) return false;

        /* A gate holds ONE jump point at a time. While one stands the engine's charge reads 0, so
           the load test below covers it too - but say it, because the two are different rules and
           the reasons the player is shown differ.

           ⚠️ BOTH KINDS OF VORTEX (Stage 8). getVortexHeldBy is ENTRANCE-ONLY by design - see
           isJumpVortex, whose callers do not agree on the verdict - so on its own it is blind to a
           gate holding an EXIT, and this test would answer "free to signal" for a gate that
           demonstrably is not. The server refuses such a claim outright
           (Firing::getGateSignalBlock asks hasOpenVortex, which knows nothing of flavour), so the
           blindness would show up as a button that is offered and then silently rejected at commit
           - the exact "worst of both" the charge note above exists to avoid. */
        if (shipManager.movement.getVortexHeldBy(gate)) return false;
        if (shipManager.movement.getExitHeldBy(gate.id)) return false;

        //THE 20-TURN RECHARGE. turnsloaded / loadingtime are sent per instance by
        //JumpEngine::stripForJson off getVortexRechargeLoad, so this is the ordinary weapon load
        //test and needs no gate-specific arithmetic.
        if (!weaponManager.isLoaded(engine)) return false;

        return !!gamedata.getGateSignalSource(gate);
    },

    /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - MAY I SIGNAL THIS GATE FOR AN ARRIVAL? The condition on
       the second Initial Orders tooltip button, and the client mirror of the one extra rule
       Firing::getGateSignalBlock applies to a 'gateexit' claim.

       Everything a departure claim needs, plus the reinforcements rule being on in this game.

       ⭐⭐ AND NOTHING ABOUT WHAT IS IN HYPERSPACE (user ruling 2026-09-02). Until now this also
       demanded a unit of the player's OWN still waiting, on the reasoning that an arrival doorway
       with nobody behind it spends the gate's whole charge on a door nobody can use. That test was
       too narrow in two directions at once: a gate exit stands for the whole of its programmed hold
       and ANY unit of ANY side may ride it (JUMP_GATES_PLAN.md section 2.6), so it barred a player
       from opening a doorway their TEAMMATE's reinforcements would come through, and barred opening
       one this turn for a wave only ready to ride it on a later one. Whether a door is worth the
       charge is the player's call to make, not this predicate's.

       ⚠️ THE RULE GATE STAYS, AND IT IS NOT DECORATION. The server still refuses an arrival claim
       in a game without allowReinforcements (Firing::getGateSignalBlock, "this game has no
       reinforcements rule"), so without this line the button would be offered and the order would be
       dropped at commit with nothing said. It used to be implicit - myHyperspaceUnits() returns []
       when the rule is off - and it has to be stated now that the count is gone. */
    canSignalJumpGateForArrival: function canSignalJumpGateForArrival(gate) {
        if (!gamedata.canSignalJumpGate(gate)) return false;

        return gamedata.reinforcementsAllowed();
    },

    /* ⭐ REINFORCEMENTS_PLAN.md STAGE 9 - IS THE REINFORCEMENTS RULE ON IN THIS GAME?
     *
     * The efficiency gate for every reinforcements sweep on this side (user request 2026-08-29).
     * Nothing in the feature can be true in a game without the rule - BuyingGamePhase never sets
     * the flag - so a game that does not use it should pay one property read rather than a pass
     * over every ship, on every UI refresh, for the whole battle. The lobby has had the identical
     * helper since Stage 1b (gamelobby.js reinforcementsAllowed); this is its game.php twin.
     *
     * ⚠️ IT IS AN EFFICIENCY GATE AND NEVER A SECURITY ONE. Every rule it guards is also enforced
     * server-side, where the same test is asked again against the real GameRules object; a client
     * that lied about it would gain nothing but a button that fails at commit. Do not move a rule
     * BEHIND this that is not also checked on the server.
     *
     * `in` rather than truthiness, matching declarations.js: the rule is stored as the KEY
     * gamedata.rules.allowReinforcements and its value is not part of the contract. */
    reinforcementsAllowed: function reinforcementsAllowed() {
        return !!(gamedata.rules && ('allowReinforcements' in gamedata.rules));
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

    // Base team colours (sRGB 0-255), team 1..8, used when the game has
    // EXACTLY two distinct teams (participant or observer) — see
    // teamBaseColorsMultiTeam below for 3+ teams. Team 1 uses CSS limegreen
    // (== combat-log FIRE: "mine" green); note the participant "mine"
    // ship-icon overlay in getShipOverlayColor is intentionally a lighter
    // green ([160,250,100]) and is NOT kept in sync. Teams 1-2 match the
    // mine/enemy colours used by the 2-team relative scheme elsewhere in
    // this file, so an observer's absolute view lines up with what a
    // participant sees. Team 3 is the SAME blue as the participant "ally"
    // (user preference — a deeper/steel blue was tried but read too dark).
    teamBaseColors: [
        [50, 205, 50],   // 1 Green  (== "mine")
        [255, 80, 80],   // 2 Red    (== "enemy")
        [51, 173, 255],  // 3 Blue   (== "ally")
        [255, 150, 40],  // 4 Orange
        [40, 230, 230],  // 5 Cyan
        [230, 40, 230],  // 6 Magenta
        [240, 230, 60],  // 7 Yellow
        [170, 90, 230]   // 8 Purple
    ],

    // Same 8 hues, reordered for games with 3+ distinct teams (participant
    // or observer both see the absolute palette once there's no single
    // unambiguous "ally"/"enemy" — see getShipOverlayColor). Team 1 stays
    // green ("mine" if you're on it), but red is pushed to the LAST slot
    // instead of team 2: with only two teams "team 2 = red" reads as
    // "enemy" by genre convention, but in a 3+-team game team 2 is just
    // another team, not necessarily hostile. Red now only shows up once a
    // game actually has all 8 teams in play.
    teamBaseColorsMultiTeam: [
        [50, 205, 50],   // 1 Green
        [255, 150, 40],  // 2 Orange
        [40, 230, 230],  // 3 Cyan
        [170, 90, 230],  // 4 Purple
        [240, 230, 60],  // 5 Yellow
        [51, 173, 255],  // 6 Blue
        [230, 40, 230],  // 7 Magenta
        [255, 80, 80]    // 8 Red
    ],

    // Raw sRGB [r,g,b] (0-255) team colour keyed on ship.team, for an observer.
    // Teams beyond 8 reuse the palette but lightened one step per full cycle.
    // Use this for canvas 2D (combat log, selection circles); getTeamColor()
    // wraps it for sprite overlays (linear space). Uses teamBaseColors for an
    // exactly-2-team game (so it matches the 2-team relative mine/ally/enemy
    // scheme elsewhere in this file) and teamBaseColorsMultiTeam otherwise.
    getTeamColorRGB: function getTeamColorRGB(team) {
        var palette = gamedata.getDistinctTeamCount() === 2
            ? gamedata.teamBaseColors
            : gamedata.teamBaseColorsMultiTeam;
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

    // TONE-MAPPED team colour, for TEXT that sits beside a full-chroma mark of the same
    // team. The hex picker (SELECT_FROM_SHIPS_PLAN.md §2.5) puts the raw palette on a 3px
    // allegiance bar — where a strong colour reads as a signal precisely because there is
    // so little of it — and this on the ship NAME, so an arbitrary team colour lands in the
    // same brightness band as the four fixed allegiance tints instead of red shouting and
    // blue sinking.
    //
    // Same shape and same reasoning as getIniTeamColorRGB above, which darkens the palette
    // for the Order of Battle: a per-SURFACE transform of one shared palette, declared once
    // rather than inlined at each call site. Saturation is CLAMPED (a colour that is
    // already muted stays where it is) but lightness is NORMALISED to a fixed value — the
    // whole point is that every team ends up equally bright. Returns integer sRGB [r,g,b].
    MUTED_TEAM_SAT: 0.40,
    MUTED_TEAM_LIGHT: 0.68,
    getMutedTeamColorRGB: function getMutedTeamColorRGB(team) {
        var rgb = gamedata.getTeamColorRGB(team);
        var r = rgb[0] / 255, g = rgb[1] / 255, b = rgb[2] / 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h = 0, s = 0, l = (max + min) / 2;

        if (max !== min) {
            var d = max - min;
            s = (l > 0.5) ? d / (2 - max - min) : d / (max + min);
            if (max === r) {
                h = (g - b) / d + (g < b ? 6 : 0);
            } else if (max === g) {
                h = (b - r) / d + 2;
            } else {
                h = (r - g) / d + 4;
            }
            h /= 6;
        }

        s = Math.min(s, gamedata.MUTED_TEAM_SAT);
        l = gamedata.MUTED_TEAM_LIGHT;

        if (s === 0) {
            var grey = Math.round(l * 255);
            return [grey, grey, grey];
        }

        var q = (l < 0.5) ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;

        function hueToChannel(t) {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        }

        return [
            Math.round(hueToChannel(h + 1 / 3) * 255),
            Math.round(hueToChannel(h) * 255),
            Math.round(hueToChannel(h - 1 / 3) * 255)
        ];
    },

    // MID-TONE team colour — the runtime twin of the --fv-*-mid tokens in tokens.css,
    // for allegiance text that sits in a DENSE list: the SelectFromShips picker's row
    // names and the ShipTooltip stack-grid cells. Sits deliberately between
    // getMutedTeamColorRGB (too faint to read as a signal at all) and the raw palette
    // (a column of twenty full-chroma rows stops being a list and starts being a
    // warning). Added 2026-08-20 alongside the ShipTooltip/fleetList brightening.
    //
    // A straight sRGB blend from the tone-mapped value toward the raw one, rather than
    // a third set of HSL constants, and for a specific reason: the tone-map NORMALISES
    // lightness to a fixed value, so re-deriving a "brighter" tier from HSL pushes an
    // already-light hue (green) PAST the raw palette instead of toward it. Interpolating
    // between the two endpoints cannot overshoot either of them, whatever the hue.
    //
    // ⚠️ MID_TEAM_MIX must stay in step with the --fv-*-mid literals: the CSS classes
    // paint the fixed mine/ally/enemy tints for 2-team participants while THIS paints
    // arbitrary teams for observers and 3+-team games, and the two arms sit in the same
    // list. Change one, recompute the other (blend --fv-own/-ally/-enemy toward
    // --fv-own-bright/-ally-bright/-enemy-bright by the same factor).
    MID_TEAM_MIX: 0.55,
    getMidTeamColorRGB: function getMidTeamColorRGB(team) {
        var toned = gamedata.getMutedTeamColorRGB(team);
        var raw = gamedata.getTeamColorRGB(team);
        var m = gamedata.MID_TEAM_MIX;
        return [
            Math.round(toned[0] + (raw[0] - toned[0]) * m),
            Math.round(toned[1] + (raw[1] - toned[1]) * m),
            Math.round(toned[2] + (raw[2] - toned[2]) * m)
        ];
    },

    // Inline style for an "active mover" IniGUI box, derived from the ship's team
    // colour. Mirrors the .iniActive* CSS (border + translucent fill + glow) but
    // keyed on team instead of mine/ally/enemy.
    //
    // Pass moved=true for the .iniActiveMoved equivalent: the ship is still the
    // active mover but has already committed its movement, so the FILL is dropped
    // and only the border + glow remain.
    //
    // Border and glow use the IniGUI-darkened colour; the FILL must be derived from
    // the FULL-strength palette instead. Taking the fill off the already-darkened
    // colour compounded the two factors (0.65 * 0.22 = 0.14 of full strength) and
    // produced a fill within a few points of the #iniTable background (#04161C) —
    // visually no fill at all, just a border. The .iniActive* classes sit at roughly
    // 0.30 of their border colour, so match that.
    INI_ACTIVE_FILL: 0.30,
    getIniActiveTeamStyle: function getIniActiveTeamStyle(team, moved) {
        var rgb = gamedata.getIniTeamColorRGB(team);
        var r = rgb[0];
        var g = rgb[1];
        var b = rgb[2];

        if (moved) {
            // .iniActiveMoved equivalent: border + a slightly stronger glow, no fill.
            return "border:1px solid rgb(" + r + "," + g + "," + b + ") !important;"
                + "box-shadow:0px 0px 4px rgb(" + r + "," + g + "," + b + ");";
        }

        var full = gamedata.getTeamColorRGB(team);
        var f = gamedata.INI_ACTIVE_FILL;
        var fillR = Math.round(full[0] * f);
        var fillG = Math.round(full[1] * f);
        var fillB = Math.round(full[2] * f);

        return "border:1px solid rgb(" + r + "," + g + "," + b + ") !important;"
            + "background-color:rgba(" + fillR + "," + fillG + "," + fillB + ",0.9) !important;"
            + "box-shadow:0px 0px 3px rgb(" + r + "," + g + "," + b + ");";
    },

    // Linear-space THREE.Color version of getTeamColorRGB, ready for sprite overlays.
    getTeamColor: function getTeamColor(team) {
        var rgb = gamedata.getTeamColorRGB(team);
        return new THREE.Color(rgb[0] / 255, rgb[1] / 255, rgb[2] / 255).convertSRGBToLinear();
    },

    // Overlay colour for a ship icon. Mirrors the fleetList / combat-log scheme:
    // 2-team participants see the familiar relative mine/ally/enemy scheme;
    // observers AND 3+-team participants see a distinct colour per team instead
    // (a single "ally" colour is ambiguous once there are several teams).
    getShipOverlayColor: function getShipOverlayColor(ship, mine, ally, terrain) {
        if (terrain) {
            /* JUMP_POINTS_PLAN.md Stage 6 - a jump point is terrain, but it is not scenery. Zoomed
               out, every icon collapses to its overlay colour, and an off-white blob in the middle
               of a battle reads as an asteroid - which is the one thing a player must not mistake
               it for, because flying into it is how you leave the game. Yellow, the same --fv-warn
               the "Jump Point Forming" hex marker, the facing arrow and the Jump Engine's map arc
               all use (user request 2026-08-22).
               isJumpVortex holds the class name once, so this and the two movement sweeps that ask
               the same question cannot drift apart.

               REINFORCEMENTS_PLAN.md §3.7 - AN EXIT TAKES THE SAME TREATMENT IN THE OTHER
               COLOUR: #00b8e6, FV's "not here yet" cyan, the same value as the blue Jump Point
               marker and the fleet list's hyperspace rows. Leaving it unmatched would be worse than
               either colour - it would fall through to the off-white below and read as an asteroid,
               which is the exact confusion the yellow was introduced to prevent. */
            if (shipManager.movement && shipManager.movement.isJumpVortexExit(ship)) {
                return new THREE.Color(0x00 / 255, 0xB8 / 255, 0xE6 / 255).convertSRGBToLinear(); // hexBlue
            }
            if (shipManager.movement && shipManager.movement.isJumpVortex(ship)) {
                return new THREE.Color(0xE1 / 255, 0xB0 / 255, 0x00 / 255).convertSRGBToLinear(); // --fv-warn
            }
            return new THREE.Color(0xBE / 255, 0xBE / 255, 0xBE / 255).convertSRGBToLinear(); // Off-white
        }

        if (!gamedata.isPlayerInGame() || gamedata.getDistinctTeamCount() !== 2) {
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

    // CSS "color:rgb(...)" string for a FLEET-LIST header (team number + player
    // name), keyed on a player SLOT. Mirrors the combat-log colour scheme:
    //   - Observer (not a player in this game): absolute per-team palette, so
    //     every distinct team is identifiable (no "ally" — nobody is your ally).
    //   - Participant in a 2-team game: relative mine=green / ally=blue /
    //     enemy=red, matching the FIRE-header scheme so your own fleet reads
    //     green even if you're team 2.
    //   - Participant in a 3+-team game: fall back to the absolute per-team
    //     palette (same as observer) — with several teams a single "ally" blue
    //     is ambiguous, so each team gets its own colour instead.
    // Returns a raw "rgb(r,g,b)" string (no "color:" prefix) so callers can drop
    // it straight into a style attribute.
    //
    // REVERTED to full chroma 2026-08-20, both arms, on user request. The Stage 6 pass
    // above had put this label in the muted band to sit level with the hex picker; in
    // practice the picker is a dense twenty-row list and this is ONE header per fleet,
    // and at that size the tone-mapped tints read as washed out. It now runs the raw
    // palette on BOTH arms — the --fv-*-bright tokens as literals for the 2-team
    // relative case, getTeamColorRGB for observers and 3+-team games — so a 2-team
    // game and a 4-team game agree about how bright a fleet header is, which brightening
    // only the relative arm would have broken. Now matches getShipLogColorCss exactly;
    // the two are no longer allowed to diverge.
    getFleetHeaderColorRGB: function getFleetHeaderColorRGB(slot) {
        var rgb;
        if (gamedata.isPlayerInGame() && gamedata.getDistinctTeamCount() === 2) {
            if (parseInt(slot.playerid, 10) === parseInt(gamedata.thisplayer, 10)) {
                rgb = [50, 205, 50];    // --fv-own-bright   #32cd32 (mine)
            } else if (parseInt(slot.team, 10) === parseInt(gamedata.getPlayerTeam(), 10)) {
                rgb = [51, 173, 255];   // --fv-ally-bright  #33adff (ally)
            } else {
                rgb = [255, 80, 80];    // --fv-enemy-bright #ff5050 (enemy)
            }
        } else {
            // Observer, or 3+-team participant: absolute per-team palette at full
            // strength, the same values the 2-team arm hard-codes for teams 1-3.
            rgb = gamedata.getTeamColorRGB(slot.team);
        }
        return "rgb(" + Math.round(rgb[0]) + "," + Math.round(rgb[1]) + "," + Math.round(rgb[2]) + ")";
    },

    // Colour for a SHIP NAME / header in the combat log, keyed on the ship itself.
    // Implements the same observer / 2-team / 3+-team rule as everything else:
    // terrain is neutral white; observers and 3+-team participants get the absolute
    // per-team palette (a single "ally" colour can't tell several teams apart);
    // 2-team participants get relative mine=green / ally=blue / enemy=red, so your
    // own fleet reads green even if you're team 2. Used for BOTH the "FIRE:" header
    // (shooter) and the attacked ship's name (target) so one log line stays
    // self-consistent. NOTE: unlike getFleetHeaderColorRGB this returns a COMPLETE
    // declaration ("color:rgb(...);") ready to drop into a style attribute.
    //
    // Kept its bright literals through the Stage 6 muting pass (user decision
    // 2026-08-14) because the combat log is a dense scrolling wall of text where the
    // stronger colours still earn their place. As of 2026-08-20 its sibling
    // getFleetHeaderColorRGB ABOVE has been brought BACK to those same values on both
    // arms, so the two functions no longer disagree: they are now the same scheme in
    // two output shapes. Keep them in step.
    getShipLogColorCss: function getShipLogColorCss(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            // ⚠️ DELIBERATELY pure white, NOT --fv-neutral (#b4c2cf) as the ship tooltip
            // and the hex picker use for terrain. Ruled intentional by the user
            // 2026-08-20 when the audit flagged it: the log is a scrolling wall of ship
            // names, and terrain needs to stand out AGAINST the firing ship's name
            // rather than sit level with it. Elsewhere terrain is a quiet default; here
            // it is a distinction. Do not "unify" this.
            return "color:#ffffff;";
        }
        if (!gamedata.isPlayerInGame() || gamedata.getDistinctTeamCount() !== 2) {
            var rgb = gamedata.getTeamColorRGB(ship.team); // guards bad team values
            return "color:rgb(" + Math.round(rgb[0]) + "," + Math.round(rgb[1]) + "," + Math.round(rgb[2]) + ");";
        }
        if (gamedata.isMyShip(ship)) return "color:rgb(50,205,50);";        // green (mine)
        if (gamedata.isMyorMyTeamShip(ship)) return "color:rgb(51,173,255);"; // blue (ally)
        return "color:rgb(255,80,80);";                                     // red (enemy)
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
        var shuttleClasses = { "Shuttle": 1, "MinesweepingShuttle": 1, "CargoShuttle": 1, "Flyer": 1, "FlyerProtectorate": 1, "Lifeboat":1, "MedicalShuttle": 1, "PresidentialShuttle": 1, "EmperorsYacht": 1 };
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

    //The systems a canPreOrder ship switches on and off during the Deployment/Pre-Turn phase
    //(gamephase -1), mapped system name -> the label the commit warning shows. Currently the
    //two stealth toggles; a future pre-order system only has to be added here to be covered.
    preOrderToggleSystems: {
        "ShadingField": "Shading Field",
        "CloakingDevice": "Cloaking Device"
    },

    //Deployment commit warning: labels of the pre-order toggles this ship could still switch
    //ON but hasn't. The systems' own canActivate() already encodes the whole rule (gamephase
    //-1, not already active, not offline this turn), so this stays correct if those conditions
    //change; destroyed is tested separately because canActivate() doesn't look at it.
    //One system answers for the whole unit, flights included: a flight's fields are toggled
    //collectively, so getSystemByName's first match is enough and it stops there rather than
    //walking every fighter. It must be a LIVE fighter's copy though, which is what that call
    //returns - doActivate/doDeactivate skip destroyed fighters, so a dead one's field keeps
    //whatever it held when it died and would read as unshaded on a flight that IS shaded.
    getInactivePreOrderSystems: function getInactivePreOrderSystems(ship) {
        var labels = [];
        if (!ship || !ship.systems) return labels;
        for (var name in gamedata.preOrderToggleSystems) {
            var sys = shipManager.systems.getSystemByName(ship, name);
            if (!sys) continue;
            if (shipManager.systems.isDestroyed(ship, sys)) continue;
            if (typeof sys.canActivate !== 'function' || !sys.canActivate()) continue;
            labels.push(gamedata.preOrderToggleSystems[name]);
        }
        return labels;
    },

    //Renders one ship name for a confirm/error dialog. `.ship-name` is the existing
    //styling hook (confirm.css); adding `.clickable` + data-shipid on top of it opts the
    //span into the delegated scroll-to-ship handler in UI/fleetList.js, exactly the way a
    //fleet list row does. Marking is deliberately conditional: a span with no data-shipid
    //stays inert, so a caller with only a name string (or a ship class, or a hidden ship)
    //still renders normally instead of producing a click that leaks a position or throws.
    //`label` overrides the default "Name (Class)" text for the "- Name" list style.
    shipNameSpan: function shipNameSpan(ship, label) {
        if (!ship || !ship.id) {
            return '<span class="ship-name">' + (label || ship || '') + '</span>';
        }
        var text = (label !== undefined && label !== null) ? label : (ship.name + ' (' + ship.shipClass + ')');
        //Same two gates doScrollToShip applies, in the same order, so a dialog name behaves
        //exactly like a fleet list row: a docked flight has no board position but opens its
        //ship window, and shouldBeHidden (enemy/stealthed/not-yet-deployed) is never jumped to.
        if (!(ship.removed && ship.flight) && shipManager.shouldBeHidden(ship)) {
            return '<span class="ship-name">' + text + '</span>';
        }
        return '<span class="ship-name clickable" data-shipid="' + ship.id + '">' + text + '</span>';
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

            // Mine ranges can only be set on the turn the mine is PLACED (which for a late slot is
            // the turn before it arrives); once placed on an earlier turn the ranges are locked
            // in. Only warn about mines being placed THIS turn - otherwise a later (delayed)
            // deployment phase re-lists already-placed mines whose transient mineSet flag was
            // reset on page reload.
            var playerHasMines = gamedata.ships.some(function (ship) {
                return ship.mine &&
                    ship.userid == gamedata.thisplayer &&
                    !shipManager.isDestroyed(ship) &&
                    shipManager.getTurnPlaced(ship) == gamedata.turn;
            });
            if (playerHasMines) {
                for (var i in gamedata.ships) {
                    var ship = gamedata.ships[i];
                    if (ship.userid == gamedata.thisplayer) {
                        if (ship.mine && shipManager.getTurnPlaced(ship) == gamedata.turn) {
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

            // CHECK for un-activated Pre-Turn systems (Shading Field / Cloaking Device)
            // Deployment turn only. From the ship's second Pre-Turn phase onwards the player is
            // looking at a unit already on the board, and leaving its field/cloak down is a real
            // choice we shouldn't nag about every turn; the turn a ship ARRIVES it is easy to
            // place it and commit without ever opening its ship window. Warning only, never a block.
            var inactivePreOrder = {}; //label -> ships, so a mixed fleet gets one list per system
            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];
                //canPreOrder first: a plain property read rejects the whole fleet bar a few units
                //(Torvalus and Klingon hulls plus Stiletto flights), so nothing below runs for a
                //normal ship. Mines carry the marker for their range settings, which the block
                //above already warns about.
                if (!ship.canPreOrder || ship.mine) continue;
                if (ship.userid != gamedata.thisplayer) continue;
                //ARRIVAL turn, not placement turn: a field/cloak is only meaningful once the unit
                //is on the board, and a late slot gets its own Pre-Turn phase on the turn it
                //arrives (FireGamePhase's $doDeployment branch) - that is where the nag belongs.
                if (shipManager.getTurnDeployed(ship) != gamedata.turn) continue;
                if (shipManager.isDestroyed(ship)) continue;

                var labels = gamedata.getInactivePreOrderSystems(ship);
                for (var l = 0; l < labels.length; l++) {
                    if (!inactivePreOrder[labels[l]]) inactivePreOrder[labels[l]] = [];
                    inactivePreOrder[labels[l]].push(ship);
                }
            }

            var preOrderLabels = Object.keys(inactivePreOrder);
            if (preOrderLabels.length > 0 && html !== '') html += "<br>"; //blank line off the mine warning above
            for (var p = 0; p < preOrderLabels.length; p++) {
                var preOrderShips = inactivePreOrder[preOrderLabels[p]];
                html += "You have not activated the " + preOrderLabels[p] + " on: ";
                html += "<br>";
                for (var s = 0; s < preOrderShips.length; s++) {
                    html += gamedata.shipNameSpan(preOrderShips[s]);
                    html += "<br>";
                }
                html += "<br>";
            }

            /* ⭐ REINFORCEMENTS_PLAN.md STAGE 7 - REINFORCEMENTS LEFT IN HYPERSPACE.

               Since arrivals place themselves (DeploymentPhaseStrategy.autoPlaceArrivingReinforcements,
               user request 2026-08-28) this is no longer the ordinary "you chose to hold one back"
               case - it is the FAILURE case, and that makes it more worth saying, not less: the
               only way to reach it now is a unit whose doorway could not be resolved, which is
               something the player has no other way of noticing before their jump point closes at
               the end of this turn and the berth goes with it.

               Silent unless something is actually being left behind, and it names the units rather
               than counting them. Never a block - the phase still has to be committable. */
            var leftInHyperspace = [];
            for (var i in gamedata.ships) {
                var arrival = gamedata.ships[i];
                if (arrival.userid != gamedata.thisplayer) continue;
                if (!shipManager.isArrivingReinforcement(arrival)) continue;
                if (shipManager.isDestroyed(arrival)) continue;
                //The two hangar routes are arrivals too, into a hold rather than onto a hex.
                if (arrival.pendingDeployDock || arrival.pendingLcvDeployDock) continue;
                if (arrival.deploymove) continue;

                leftInHyperspace.push(arrival);
            }

            if (leftInHyperspace.length > 0) {
                if (html !== '') html += "<br>";
                html += "These reinforcements could not be brought out of hyperspace &mdash; no open "
                    + "jump point was found for them: ";
                html += "<br>";
                for (var h = 0; h < leftInHyperspace.length; h++) {
                    html += gamedata.shipNameSpan(leftInHyperspace[h]);
                    html += "<br>";
                }
                html += "They stay where they are with nothing spent, and will need another "
                    + "exit opened for them.<br>";
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
            var vortexClosing = [];  //JUMP_POINTS_PLAN.md Stage 6 - see the block that fills it
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

                    /* ⭐ JUMP_POINTS_PLAN.md Stage 6 - THE JUMP POINT IS ABOUT TO CLOSE.
                       A vortex closes at the end of every turn its holder does not declare
                       Maintain (plan section 2.3), and until now nothing said so: the player
                       committed Initial Orders and found out a phase later, by which time the
                       decision could not be taken back. This is the last moment it can be.

                       Asked of the VORTEX, not of the toggle, so it also covers the cases where
                       maintaining was never on offer - the holder is out of range, or the vortex
                       has reached its four-turn cap - which are precisely the ones a player has
                       no other way of seeing coming. getVortexHeldBy returns null on the turn a
                       vortex was declared (it has not formed yet), so a fresh declaration never
                       warns about itself. */
                    var heldVortex = shipManager.movement.getVortexHeldBy(myShips[ship]);
                    if (heldVortex) {
                        var jumpEngine = shipManager.systems.getSystemByName(myShips[ship], "jumpEngine");
                        var maintaining = jumpEngine && typeof jumpEngine.isMaintainingVortex === 'function'
                            && jumpEngine.isMaintainingVortex();
                        if (!maintaining) vortexClosing.push(myShips[ship]);
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
                        /* The Jump Engine is a BALLISTIC hex-target weapon (JUMP_POINTS_PLAN.md
                           section 3.1) but it is not a launcher, and "you have not assigned any
                           ballistic launch" is not advice about it - every jump-capable hull in
                           the game would carry that line every turn it was charged. Its own
                           warning is the jump-point one further down. */
                        if (currWeapon.name === 'jumpEngine') continue;
                        if (currWeapon.ballistic) { //only ballistic weapons are of interest now
                            if (currWeapon.fireOrders.length > 0) {
                                fired = 1;
                                break;
                            }
                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant - GTS
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
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant
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
                    html += gamedata.shipNameSpan(selfDestructing[ship]);
                    html += "<br>";
                }
                html += "<br>";
            }
            if (jumping.length > 0) {
                html += "You have ordering following ships to JUMP TO HYPERSPACE: ";
                html += "<br>";
                for (var ship in jumping) {
                    //html += jumping[ship].name + " (" + jumping[ship].shipClass + ")";
                    html += gamedata.shipNameSpan(jumping[ship]);
                    html += "<br>";
                }
                html += "<br>";
            }
            if (vortexClosing.length > 0) {
                html += "The JUMP POINTS held by the following ships will CLOSE at the end of this turn: ";
                html += "<br>";
                for (var ship in vortexClosing) {
                    html += gamedata.shipNameSpan(vortexClosing[ship]);
                    html += "<br>";
                }
                html += "<br>";
            }

            /* ⭐ REINFORCEMENTS_PLAN.md - REINFORCEMENTS ABOUT TO BE STRANDED FOR GOOD (user
               report 2026-08-28). The exact sibling of the vortexClosing warning above, and it
               exists for the same reason: the decision is irreversible one phase later, and
               nothing said so.

               A reinforcement with no jump drive of its own only ever arrives as a passenger. If
               the unit that opened this turn's doorway leaves it off the manifest and jumps in
               alone, nobody able to open the next one is left in hyperspace and those units are
               unusable for the rest of the battle. ReinforcementEntry.strandedByCommit owns the
               whole test (and stays silent unless it is really true - see the note on it there);
               this only renders it.

               ⚠️ Read from the module, NOT from myShips: that list drops everything whose
               getTurnDeployed is later than this turn, and a unit in hyperspace answers 999.
               Guarded on the module existing at all, exactly as drawIniGUI's button is. */
            var strandedReinforcements = window.ReinforcementEntry
                ? ReinforcementEntry.strandedByCommit() : [];
            if (strandedReinforcements.length > 0) {
                html += "These REINFORCEMENTS are not riding any jump point, and no unit able to "
                    + "open another one will be left in hyperspace &mdash; they can NEVER be "
                    + "called in: ";
                html += "<br>";
                for (var s = 0; s < strandedReinforcements.length; s++) {
                    html += gamedata.shipNameSpan(strandedReinforcements[s]);
                    html += "<br>";
                }
                html += "<br>";
            }
            if (hasNoEW.length > 0) {
                // New check to see if Scanner exists / has positive output before giving warning - DK 01/25
                for (var i = hasNoEW.length - 1; i >= 0; i--) {
                    var ship = hasNoEW[i];
                    const scanners = shipManager.systems.getScannerList(ship, true);

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
                        html += gamedata.shipNameSpan(hasNoEW[ship]);
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
                    html += gamedata.shipNameSpan(notLaunching[ship]);
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetAA.length > 0) {
                html += "You have not assigned available AA points for the following units: ";
                html += "<br>";
                for (var ship in notSetAA) {
                    //html += notSetAA[ship].name + " (" + notSetAA[ship].shipClass + ")";
                    html += gamedata.shipNameSpan(notSetAA[ship]);
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetFC.length > 0) {
                html += "You have not assigned available BFCP points for the following units: ";
                html += "<br>";
                for (var ship in notSetFC) {
                    //html += notSetFC[ship].name + " (" + notSetFC[ship].shipClass + ")";
                    html += gamedata.shipNameSpan(notSetFC[ship]);
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
                    html += gamedata.shipNameSpan(powerSurplus[ship], powerSurplus[ship].name + ' (' + powerSurplus[ship].shipClass + '): <b>&#60;' + surplusVal + '&#62;</b>');
                    html += "<br>";
                }
                html += "<br>";
            }
            confirm.confirm(html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR INITIAL ORDERS?</span>', gamedata.doCommit);
        }

        else if (gamedata.gamephase == 2) {
            var zeroSpeedShips = [];
            var leavingBattle = [];
            var activeShips = gamedata.getActiveShips();
            var html = '';

            for (var i in activeShips) {
                var ship = activeShips[i];
                if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
                    if (shipManager.movement.canChangeSpeed(ship, true) && ship.userid == gamedata.thisplayer) {
                        zeroSpeedShips.push(ship);
                    }
                    /* JUMP_POINTS_PLAN.md Stage 6 - "this ship will leave the battle" was Stage 4's
                       second reported gap: a jump-out committed with no confirmation at all, and it
                       is the most irreversible order in the game. Committed is committed - the
                       server removes the unit at the end of this phase. */
                    if (ship.userid == gamedata.thisplayer && shipManager.movement.hasJumpedOut(ship)) {
                        leavingBattle.push(ship);
                    }
                }
            }

            if (leavingBattle.length > 0) {
                html += "<br>";
                html += "The following units will LEAVE THE BATTLE through a jump point: <br>";

                for (var k in leavingBattle) {
                    html += gamedata.shipNameSpan(leavingBattle[k], leavingBattle[k].name) + '<br>';
                }
            }

            if (zeroSpeedShips.length > 0) {
                html += "<br>";
                html += "The following ships can still move: <br>";

                for (var j in zeroSpeedShips) {
                    var movingShip = zeroSpeedShips[j];
                    html += gamedata.shipNameSpan(movingShip, movingShip.name) + '<br>';
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
                                    && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant - GTS
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
                                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant											
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
                confirm.confirm('<span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR PRE-FIRE ORDERS?</span>', gamedata.doCommit);
            } else {
                var html = '';
                if (hasNoFO.length > 0) {
                    html += "You have not assigned any pre-fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        //html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += gamedata.shipNameSpan(hasNoFO[ship]);
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += gamedata.shipNameSpan(hasSplitFO[ship]);
                        html += "<br>";
                    }
                }
                confirm.confirm(html + '<br><span class="commit-confirm-q">Are you sure you wish to COMMIT YOUR PRE-FIRE ORDERS?</span>', gamedata.doCommit);
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
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant - GTS
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
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon, true)) //check for ammo (if relevant											
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
                        html += gamedata.shipNameSpan(hasNoFO[ship]);
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += gamedata.shipNameSpan(hasSplitFO[ship]);
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
                        html += gamedata.shipNameSpan(notLaunchedFighters[ship]);
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
            var specialistShips = shipManager.systems.getUnusedSpecialists();

            if (specialistShips.length > 0) {
                var specialistsError = "The following ships have not selected Specialists:<br>";

                for (var i in specialistShips) {
                    var specialistShip = specialistShips[i];
                    specialistsError += gamedata.shipNameSpan(specialistShip, '- ' + specialistShip.name) + '<br>';
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
                //Placement turn: a reinforcement flight is queued for its hangar during the
                //Deployment phase of the turn before it arrives.
                if (shipManager.getTurnPlaced(flight) != gamedata.turn) continue;
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
                    if (shipManager.getTurnPlaced(carrier) != gamedata.turn) continue;
                    myDeployingCarriers.push(carrier);
                }

                var mustDockFlights = [];
                for (var hi = 0; hi < hangarDeployCandidates.length; hi++) {
                    var hdFlight = hangarDeployCandidates[hi];
                    for (var ci = 0; ci < myDeployingCarriers.length; ci++) {
                        //Only a carrier arriving on the SAME turn can hold it - being placed in the
                        //same phase is not enough (turn-1 and turn-2 units are both placed on turn 1).
                        //Otherwise this would block the commit over a berth the flight can't use.
                        if (shipManager.getTurnDeployed(myDeployingCarriers[ci]) !== shipManager.getTurnDeployed(hdFlight)) continue;
                        if (window.DeploymentDock.eligibleHangarsForFlight(myDeployingCarriers[ci], hdFlight).length > 0) {
                            mustDockFlights.push(hdFlight);
                            break;
                        }
                    }
                }

                if (mustDockFlights.length > 0) {
                    var hangarDeployError = "The following flights must be deployed inside a Hangar:<br>";
                    for (var mi = 0; mi < mustDockFlights.length; mi++) {
                        hangarDeployError += gamedata.shipNameSpan(mustDockFlights[mi], '- ' + mustDockFlights[mi].name) + '<br>';
                    }
                    hangarDeployError += "<br>Dock them into a carrier's hangar before committing your orders.";
                    window.confirm.error(hangarDeployError, function () { });
                    return false;
                }
            }

            ajaxInterface.submitGamedata();

            //INITIAL ORDERS    
        } else if (gamedata.gamephase == 1) {
            //        	ajaxInterface.fastpolling=true;
            var noPowerShips = shipManager.power.getShipsNegativePower();

            if (noPowerShips.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";

                for (var index in noPowerShips) {
                    var noPowerShip = noPowerShips[index];
                    negPowerError += gamedata.shipNameSpan(noPowerShip, '- ' + noPowerShip.name) + '<br>';
                }
                negPowerError += "<br>You need to turn off systems before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }

            //We have one thrust-boosted weapon in Initial Orders Phase, let's put in a check for it and future - DK 26.11.24
            var lowThrustShips = shipManager.movement.getShipsNegativeThrust();

            if (lowThrustShips.length > 0) {
                var negThrustError = "The following ships have insufficient Engine Thrust:<br>";

                for (var index in lowThrustShips) {
                    var lowThrustShip = lowThrustShips[index];
                    negThrustError += gamedata.shipNameSpan(lowThrustShip, '- ' + lowThrustShip.name) + '<br>';
                }
                negThrustError += "<br>You need to lower channelled thrust before you can commit the turn.";
                window.confirm.error(negThrustError, function () { });
                return false;
            }

            var gravShieldShips = shipManager.power.getShipsGraviticShield();

            if (gravShieldShips.length > 0) {
                var tooManyShieldsError = "The following ships have too many active shields:<br>";

                for (var i in gravShieldShips) {
                    var gravShieldShip = gravShieldShips[i];
                    tooManyShieldsError += gamedata.shipNameSpan(gravShieldShip, '- ' + gravShieldShip.name) + '<br>';
                }
                tooManyShieldsError += "<br>You need to turn off shields or boost your shield generator before you can commit the turn.";
                window.confirm.error(tooManyShieldsError, function () { });
                return false;
            }

            var negBFCPShips = shipManager.systems.getNegativeBFCP();

            if (negBFCPShips.length > 0) {
                var tooManyBFCPError = "The following ships have too many Bonus Fire Control Points (BFCP) set:<br>";

                for (var i in negBFCPShips) {
                    var negBFCPShip = negBFCPShips[i];
                    tooManyBFCPError += gamedata.shipNameSpan(negBFCPShip, '- ' + negBFCPShip.name) + '<br>';
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
            var shieldGenShips = shipManager.systems.checkShieldGenValue();

            if (shieldGenShips.length > 0) {
                var shieldCapacityError = "The following ships have directed too much or too little power to their shields:<br>";

                for (var i in shieldGenShips) {
                    var shieldGenShip = shieldGenShips[i];
                    shieldCapacityError += gamedata.shipNameSpan(shieldGenShip, '- ' + shieldGenShip.name) + '<br>';
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
                    errorText += gamedata.shipNameSpan(EWIncorrect[shipID]);
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWRestrictedIncorrect.length > 0) {
                errorText += "The following ships have too many EW points set:<br>";
                for (var shipID in EWRestrictedIncorrect) {
                    //errorText += EWRestrictedIncorrect[shipID].name + " (" + EWRestrictedIncorrect[shipID].shipClass + ")";
                    errorText += gamedata.shipNameSpan(EWRestrictedIncorrect[shipID]);
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWLCVIncorrect.length > 0) {
                errorText += "The following LCVs have too many EW points set on non-OEW:<br>";
                for (var shipID in EWLCVIncorrect) {
                    //errorText += EWLCVIncorrect[shipID].name + " (" + EWLCVIncorrect[shipID].shipClass + ")";
                    errorText += gamedata.shipNameSpan(EWLCVIncorrect[shipID]);
                    errorText += "<br>";
                }
                errorText += "<br>";
            }


            if (ammoMagazineError.length > 0) {
                errorText += "The following units are trying to launch more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //errorText += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    errorText += gamedata.shipNameSpan(ammoMagazineError[shipID]);
                    errorText += "<br>";
                }
                errorText += "<br>";
            }

            if (derelictFiring.length > 0) {
                errorText += "The following units are derelict and should be considered shut down - cancel all firing orders:<br>";
                for (var shipID in derelictFiring) {
                    //errorText += derelictFiring[shipID].name + " (" + derelictFiring[shipID].shipClass + ")";
                    errorText += gamedata.shipNameSpan(derelictFiring[shipID]);
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
                        mustPivotError += gamedata.shipNameSpan(pShip, '- ' + pShip.name) + '<br>';
                    }
                }

                var tShip = active[i];

                //Limited thrust check to Hyach Specialist now for efficiency, but we can expand it as needed - DK
                if (shipManager.hasSpecialAbility(tShip, "HyachSpecialists") && shipManager.movement.hasNegativeThrust(tShip)) {
                    foundTShip = true;
                    negThrustError += gamedata.shipNameSpan(tShip, '- ' + tShip.name) + '<br>';
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
                    ammoMagError += gamedata.shipNameSpan(ammoMagazineError[shipID]);
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
            var capacitorShips = shipManager.power.getCapacitorShipsNegativePower();
            if (capacitorShips.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";
                for (var index in capacitorShips) {
                    var capacitorShip = capacitorShips[index];
                    negPowerError += gamedata.shipNameSpan(capacitorShip, '- ' + capacitorShip.name) + '<br>';
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
                    var batteryShip = batteryShips[index];
                    negPowerError += gamedata.shipNameSpan(batteryShip, '- ' + batteryShip.name) + '<br>';
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
                    ammoMagError += gamedata.shipNameSpan(ammoMagazineError[shipID]);
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

    // Number of distinct teams in the game (two = a classic two-sided match,
    // including 2v2 etc). Used to decide whether a relative mine/enemy colour
    // scheme is unambiguous (only meaningful with exactly two sides).
    getDistinctTeamCount: function getDistinctTeamCount() {
        var teams = {};
        for (var i in gamedata.slots) {
            teams[gamedata.slots[i].team] = true;
        }
        return Object.keys(teams).length;
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

        if (gamedata.gamephase == 2) return "MOVEMENT:";

        if (gamedata.gamephase == 5) return "PRE-FIRING";

        if (gamedata.gamephase == 3) return "FIRE ORDERS";

        if (gamedata.gamephase == 4) return "FINAL ORDERS";

        if (gamedata.gamephase == -1) {
            if (shipManager.hasShipsToDeployThisTurn(gamedata.thisplayer)) {
                return "DEPLOYMENT";
            } else {
                return "PRE-TURN ACTIONS";
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

        /* The combat log's turn stepper and its "TURN 7 · FIRING" readout
           (LOG_PANEL_REDESIGN_PLAN.md Stage 2b). Here rather than in a $(window).on("load")
           of its own because the page bootstrap calls parseServerData from INSIDE a
           setTimeout, so a load handler registered by the bundle runs while gamedata.turn
           is still unset. initPhase runs on every phase change and on the first one, which
           is exactly when the readout has something new to say. */
        if (window.combatLog) combatLog.updateTurnControls();


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

            // Colour the initiative number by team (per-team palette) instead of
            // the relative mine/ally/enemy scheme for observers AND 3+-team
            // participants, matching the fleetList / combat-log / ship-icon rule
            // (a single "ally" colour is ambiguous once there are several teams).
            // When teamColorCss is set, the active-mover box below also switches to
            // the per-team style, so the whole row follows one scheme.
            //
            // FULL-STRENGTH palette since 2026-08-20 (user request), not the ×0.65
            // getIniTeamColorRGB this used to call. Two reasons that is not a
            // regression of the original "don't out-shout the muted panel" rule:
            // the NUMBER is a two-character glyph, which is the small-mark case
            // where chroma reads as a signal, and the CSS arm beside it
            // (.iniMyShip / .iniAllyShip / .iniEnemyShip) moved to --fv-*-bright in
            // the same change, so both arms of the gate agree. Darkening only the
            // inline arm was the actual bug: a 2-team participant saw pure #ff0000
            // where an observer saw #a63434.
            //
            // ⚠️ getIniTeamColorRGB still exists and is still correct — it is the
            // ACTIVE-MOVER BOX's colour (via getIniActiveTeamStyle), where the
            // colour becomes a border, a fill and a glow all at once and genuinely
            // does need holding back. Do not "tidy" the two back together.
            var teamColorCss = "";
            if (!gamedata.isPlayerInGame() || gamedata.getDistinctTeamCount() !== 2) {
                var iniRgb = gamedata.getTeamColorRGB(ships[i].team);
                teamColorCss = "color:rgb(" + Math.round(iniRgb[0]) + "," + Math.round(iniRgb[1]) + "," + Math.round(iniRgb[2]) + ");";
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
                    // Observers / 3+-team participants: style the active-mover box from
                    // the ship's team colour instead of the mine/ally/enemy iniActive*
                    // classes. This branch short-circuits the .iniActiveMoved test below,
                    // so it has to make the same "already moved" call itself — otherwise
                    // the box keeps its fill for the whole phase in 3+-team games while
                    // 2-team games correctly drop it once movement is committed. Gated on
                    // isMyShip exactly like .iniActiveMoved, so it never reveals whether
                    // another team's ship has moved yet.
                    var teamMoved = gamedata.isMyShip(ships[i])
                        && shipManager.movement.isMovementReady(ships[i])
                        && shipManager.movement.hasDeletableMovements(ships[i]);
                    td.style.cssText += gamedata.getIniActiveTeamStyle(ships[i].team, teamMoved);
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

        /* REINFORCEMENTS_PLAN.md Stage 4 - MANAGE REINFORCEMENTS. Same shape as the mine button
           above and for the same reason: a bespoke map mode needs somewhere to be switched on, and
           #iniGui is where this game puts that.

           The whole state machine lives in the module (ReinforcementEntry.buttonLabel /
           onButtonClicked), so the label and the click cannot disagree - the button opens the
           menu, or cancels the armed hex mode, and this loop only draws it. drawIniGUI is re-run
           by the module after every state change, which is what repaints the label.
           ⚠️ It no longer flips to "Withdraw Jump Point" when a declaration stands (user request
           2026-08-28) - that state locked the player out of declaring a SECOND exit with a
           second jump-capable hull. Withdrawing is a row in the menu now.

           Guarded on the module existing at all: game.php loads it `defer` alongside every other
           phase-strategy file, but this method is also reached from the lobby's shared code paths
           in some flows, where it is not loaded. */
        var existingReinfBtn = document.getElementById('reinforcementEntryBtn');
        if (existingReinfBtn) existingReinfBtn.parentNode.removeChild(existingReinfBtn);

        if (window.ReinforcementEntry && ReinforcementEntry.isOffered()) {
            var reinfBtn = document.createElement('button');
            reinfBtn.id = 'reinforcementEntryBtn';
            reinfBtn.textContent = ReinforcementEntry.buttonLabel();
            if (ReinforcementEntry.isActive()) reinfBtn.classList.add('active');

            reinfBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                ReinforcementEntry.onButtonClicked();
            });

            ini_gui.appendChild(reinfBtn);
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

    /* showSurrenderButton/hideSurrenderButton are GONE (2026-08-03). Surrender is no longer a
       cell in the Initial Orders phase header that a phase strategy shows and hides — it is a
       permanent top-right HUD button (reactJs/surrender/Surrender.js) that decides its own
       visibility. Note that the old helpers bound on the bare `.surrender` selector, which also
       matched the surrender div inside confirm.confirmOrSurrender's dialog. */

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

        //Chameleon Sensor Suite (D14): window.staticShips is fixed at page load and carries only the
        //blueprints of the ships this player could see THEN. When a disguise breaks mid-session the
        //server starts sending the ship's true phpclass, which this page has no blueprint for - Ship()
        //would build it from the JSON alone, with no armour, no arcs and no maxhealth. Reload instead,
        //before anything on the page has been touched; reveals happen at most once per ship per game,
        //at a turn boundary. Skipped in replay, where stepping across the reveal turn is SUPPOSED to
        //change identity and a reload would throw the viewer out of the replay.
        if (!gamedata.replay && gamedata.hasShipIdentityChanged(serverdata.ships)) return;

        /* REINFORCEMENTS_PLAN.md Stage 7 - a hull arriving mid-session that this page has no
           blueprint for. Same failure as the Chameleon reveal above and a much commoner one; it
           fetches the missing faction instead of reloading. Returns true when it has deferred this
           update - nothing below has run yet, so re-entering with the same payload is clean. */
        if (gamedata.ensureBlueprintsFor(serverdata)) return;

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

        /* A surrendered/finished game is frozen — nothing on the server can change again, so
           there is nothing left to poll for. Not merely an optimisation: the poll would
           otherwise run to its 300-request cap, because pollGamedata's `waiting == false` exit
           can never fire once the game ends. PhaseDirector puts everyone into
           ReplayPhaseStrategy at that point, which sets gamedata.replay, and the block just
           above deliberately stops refreshing gamedata.waiting while replay is on — so
           `waiting` stays pinned at the true that goToWaiting() set on submit.

           Worse than the wasted requests: each live response rewinds gamedata.turn to the LIVE
           turn while ReplayPhaseStrategy is showing an earlier one, and its update() answers by
           re-fetching that turn from replay.php, which sets gamedata.turn back again — the two
           fetches then ping-pong for as long as the poll keeps feeding them.

           Keyed on the SERVER's status, deliberately, and placed here rather than in
           pollGamedata: doSurrender() sets gamedata.status optimistically before the POST (it
           has to — construcGamedata reads it into the payload), and in a 3+ team game one team
           folding does NOT end the match. Trusting the local value would strand that player's
           client on a stale board. */
        if (gamedata.status === "SURRENDERED" || gamedata.status === "FINISHED") {
            ajaxInterface.stopPolling();
        }

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

        /* SAVE FLEET tab (PREBATTLE_DAMAGE_PLAN.md §7.3). Its own bootstrap runs at
           DOM-ready, when gamedata.ships is still empty - so "the viewer has ships in this
           game" was false, the tab hid itself, and the only other refresh trigger was the
           panel's own "onshow", which cannot fire while the tab is hidden. Refresh it here
           instead: this is the one place ships actually arrive, and it also keeps the
           "N units will be saved" line honest as units die during the battle. */
        if (window.savedFleets) savedFleets.refreshSavePanel();

        gamedata.checkGameStatus();
    },

    /* Has a ship already on this page come back wearing a different hull? Triggers the reload and
       returns true. Positional lookup first - the server sends ships in a stable order, so the
       id-scan fallback is only reached when something was spawned or removed. */
    hasShipIdentityChanged: function hasShipIdentityChanged(jsonShips) {
        if (gamedata.identityReloadPending) return true;
        if (!jsonShips) return false;

        for (var i in jsonShips) {
            var json = jsonShips[i];
            var existing = (gamedata.ships[i] && gamedata.ships[i].id == json.id)
                ? gamedata.ships[i]
                : gamedata.getShip(json.id);

            if (!existing || !existing.phpclass) continue;
            if (existing.phpclass === json.phpclass && existing.faction === json.faction) continue;

            gamedata.identityReloadPending = true;
            window.location.reload();
            return true;
        }

        return false;
    },

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 7 - A HULL THIS PAGE HAS NO BLUEPRINT FOR
       (user report 2026-08-28, game 4318).

       window.staticShips is built ONCE, by game.php, from the ships in THIS VIEWER'S payload at the
       moment the page loaded - and a reinforcement waiting in hyperspace is not in an opponent's
       payload at all (§3.6 removes it outright). So when the wave arrives, the opponent's client is
       handed ship JSON for classes it holds no blueprint for: Ship() builds them from the live JSON
       alone, with no armour, no arcs, no maxhealth and no systems - "no ship information", exactly
       as reported. The one class that DID render was the Primus, because a front-line Primus was
       already on the board and had brought its blueprint with it.

       ⭐ THIS IS THE SAME BUG AS hasShipIdentityChanged ABOVE, AND IT IS ANSWERED DIFFERENTLY.
       That one reloads the page, which is right for it: a Chameleon reveal changes the identity of
       a ship the page has ALREADY built - its icon, its ship window and every cached reference are
       made of the old hull, so nothing short of a rebuild is honest. Here the ships do not exist
       yet. Only the blueprint is missing, so fetching it is enough, and a mid-turn reload is a
       cost the player should not have to pay for their opponent's reinforcements arriving.

       ⚠️ IT DEFERS THE WHOLE UPDATE RATHER THAN PATCHING AFTERWARDS. Returning true from here
       leaves parseServerData having touched NOTHING, so the re-entry on completion is an ordinary
       first pass over the same payload - no half-applied turn, no ships built from a blueprint that
       had not landed yet. The payload is a moment staler by then, which is the same staleness the
       reload path accepts.

       ⚠️ EVERY FACTION IS ASKED FOR AT MOST ONCE. A class that is missing even after the fetch (a
       spawnable that never made it into the static generator, say) must not put the client into a
       fetch-defer-fetch loop, or worse freeze it out of a turn change - the poll stops once it is
       this player's move, so a permanently deferred update is a dead screen, not a slow one. After
       one attempt the payload is applied whatever happened, which is exactly today's behaviour.

       gamelobbyloader.php rather than a new endpoint: it already serves per-faction blueprints with
       gzip and ETag revalidation, and its output is the SAME static/json/<faction>.json the
       generator writes. Diffed against BlueprintCache's own output for Primus and Sentri: identical
       but for the blueprint's ship-level `id`/`flightid` (overwritten from the live JSON by Ship()
       anyway), one tooltip `data` value that is a string on one side and a number on the other, and
       the lobby-only `systemEnhancementOffers`. Nothing the game screen reads. */
    blueprintFetchAttempted: {},
    blueprintFetchPending: false,

    ensureBlueprintsFor: function ensureBlueprintsFor(serverdata) {
        //A fetch is already in flight for an earlier payload - defer this one too, and let that
        //fetch's own re-entry apply what it captured.
        if (gamedata.blueprintFetchPending) return true;
        if (!serverdata || !serverdata.ships || !window.staticShips) return false;

        var wanted = {};
        for (var i in serverdata.ships) {
            var json = serverdata.ships[i];
            if (!json || !json.faction || !json.phpclass) continue;
            if (gamedata.blueprintFetchAttempted[json.faction]) continue;

            var faction = window.staticShips[json.faction];
            if (faction && faction[json.phpclass]) continue;

            wanted[json.faction] = true;
        }

        var factions = Object.keys(wanted);
        if (factions.length === 0) return false;

        factions.forEach(function (faction) { gamedata.blueprintFetchAttempted[faction] = true; });
        gamedata.blueprintFetchPending = true;

        //Settle on the LAST response, success or failure alike - .always, so a 401/timeout on one
        //faction cannot strand the update behind it.
        var remaining = factions.length;
        var settle = function () {
            remaining--;
            if (remaining > 0) return;
            gamedata.blueprintFetchPending = false;
            gamedata.parseServerData(serverdata);
        };

        factions.forEach(function (faction) {
            $.ajax({
                type: 'POST',
                url: 'gamelobbyloader.php',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({ faction: String(faction) }),
                timeout: 30000
            }).done(function (data) {
                gamedata.mergeStaticShips(data);
            }).always(settle);
        });

        return true;
    },

    /* Fold a faction blueprint file into window.staticShips. Shape is
       { "<faction>": { "<phpclass>": {...} } }, which is what staticShips already is.

       ⚠️ ADDS ONLY, NEVER OVERWRITES. The classes already present came from BlueprintCache and are
       what every Ship on the page was built from; the loader's copies are the lobby's, equivalent
       but not byte-identical (see ensureBlueprintsFor). Replacing them would swap the blueprint out
       from under units that are already on the board, to fix a problem those units do not have. */
    mergeStaticShips: function mergeStaticShips(data) {
        if (!data || typeof data !== 'object' || data.error) return;
        if (!window.staticShips) window.staticShips = {};

        for (var faction in data) {
            var incoming = data[faction];
            if (!incoming || typeof incoming !== 'object') continue;
            if (!window.staticShips[faction]) window.staticShips[faction] = {};

            for (var phpclass in incoming) {
                if (window.staticShips[faction][phpclass]) continue;
                window.staticShips[faction][phpclass] = incoming[phpclass];
            }
        }
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
