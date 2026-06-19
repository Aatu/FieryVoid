"use strict";

window.ReplayPhaseStrategy = function () {

    function ReplayPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);
        this.webglScene = null;
        this.currentTurn = null;
        this.currentPhase = null;
        this.replayTurn = null;
        this.replayPhase = null;
        this.allBallistics = null;  //New variable added so that ballisticLineIcons can be rendered during Replay - DK 12/24

        this.loading = false;

        // Replay playback speed, shared by both Play and Rewind. Adjusted by the
        // −/+ Speed buttons (clamped to the REPLAY_SPEEDS range); persists across
        // play/pause/direction changes so the user's chosen speed sticks for the
        // whole replay.
        this.replaySpeed = 1;

        // Direction the spacebar resumes in. AnimationStrategy.pause() discards
        // goingBack, so we remember the last running direction here: spacebar
        // pause records it, spacebar play replays in it (defaults to forward).
        this.lastDirectionForward = true;
    }

    ReplayPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    ReplayPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ewIconContainer.hide();
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.webglScene = webglScene;
        this.inactive = false;
        this.currentTurn = gamedata.turn;
        this.currentPhase = gamedata.gamephase;
        this.setSelectShip(null);
        this.shipIconContainer.setAllSelected(false);
        this.replayTurn = getInitialReplayTurn.call(this);

        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.ewIconContainer.consumeGamedata(this.gamedata);
        this.shipWindowManager = shipWindowManager;
        gamedata.replay = true;
        this.createReplayUI();

        startReplayOrRequestGamedata.call(this);

        // Previous phase strategy's deactivate() hid every ballistic launch/target sprite.
        // For in-turn replays, consumeGamedata above re-uses the existing icons via
        // updateBallisticIcon and so never re-shows them — re-show them explicitly here.
        this.ballisticIconContainer.show();

        activatePause.call(this);

        this.setPhaseHeader(false);
        this.showAppropriateHighlight();
        this.showAppropriateEW();

        // No informPhase() here: the phase banner reports the live game phase, not
        // the replayed one, and overlaps the ReplayUI. informPhase() is guarded
        // against gamedata.replay anyway, but skip it outright on replay entry.
        // Observers are permanently in replay (see PhaseDirector.resolvePhaseStrategy),
        // so hiding the combat-log print buttons here would deny them the log entirely.
        // Hide them only for participants, who get them back on deactivate.
        var combatLogContainer = document.getElementById('combatLogContainer');
        if (combatLogContainer) {
            combatLogContainer.style.display = gamedata.isPlayerInGame() ? 'none' : 'block';
        }
        return this;
    };

    ReplayPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        gamedata.replay = false;
        var combatLogContainer = document.getElementById('combatLogContainer');
        if (combatLogContainer) combatLogContainer.style.display = 'block'; //Show print Log buttons
        return this;
    };

    ReplayPhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.ewIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);

        startReplayOrRequestGamedata.call(this);
    };

    ReplayPhaseStrategy.prototype.done = function () { };

    ReplayPhaseStrategy.prototype.onHexClicked = function (payload) {
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
    };

    ReplayPhaseStrategy.prototype.selectShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    ReplayPhaseStrategy.prototype.setSelectShip = function (ship, payload) {
    };

    ReplayPhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    ReplayPhaseStrategy.prototype.onMouseOverShip = function (ship, payload) {
        if (this.animationStrategy.isPaused()) {
            PhaseStrategy.prototype.onMouseOverShip.call(this, ship, payload);
        }
    };

    ReplayPhaseStrategy.prototype.createReplayUI = function (gamedata) {
        this.replayUI = new ReplayUI(true, {
            playPause: toggleDirection.bind(this, true),
            back: toggleDirection.bind(this, false),
            slower: adjustSpeed.bind(this, -1),
            faster: adjustSpeed.bind(this, 1),
            turnForward: turnForward.bind(this),
            turnBack: turnBack.bind(this),
            endReplay: requestPlayableGamedata.bind(this),
            toMovementPhase: toMovementPhase.bind(this),
            toFiringPhase: toFiringPhase.bind(this)
        }).activate();
        this.replayUI.setSpeed(this.replaySpeed);
    };

    ReplayPhaseStrategy.prototype.render = function (coordinateConverter, scene, zoom) {
        PhaseStrategy.prototype.render.call(this, coordinateConverter, scene, zoom);

        if (this.animationStrategy && this.animationStrategy.isDone && this.animationStrategy.isDone()) {
            activatePause.call(this);
        }
    };

    ReplayPhaseStrategy.prototype.showAppropriateEW = function () {
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

    ReplayPhaseStrategy.prototype.showAppropriateHighlight = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.showSideSprite(false);
            icon.setHighlighted(false);
        })
    }

    function toMovementPhase() {
        this.animationStrategy.toMovementPhase();
        resetAudio.call(this);
        window.combatLog.critsShown = {}; //Empty crits shown array
        window.combatLog.critAnimations = {}; //Empty crit animations shown array
        this.animationStrategy.pause();
        this.lastDirectionForward = true; // Seeked to a phase start: resume forward.
        syncPlayPause.call(this);
        // Idle render-loop gating (perf #2): goToTime only sets the target time; the
        // ships aren't repositioned until render() replays the animations to it. We then
        // pause(), so isAnimating() is false and the loop won't paint until Play. Kick
        // the render budget so the seek-to-phase shows immediately. A paused render draws
        // the icons at the new time without advancing the clock.
        if (window.webglScene && window.webglScene.requestRender) {
            window.webglScene.requestRender();
        }
    }

    function toFiringPhase() {
        this.animationStrategy.toFiringPhase();
        resetAudio.call(this);
        window.combatLog.critsShown = {}; //Empty crits shown array
        window.combatLog.critAnimations = {}; //Empty crit animations shown array
        this.animationStrategy.pause();
        this.lastDirectionForward = true; // Seeked to a phase start: resume forward.
        syncPlayPause.call(this);
        // See toMovementPhase: kick the render budget so the seek-to-firing-phase paints
        // immediately instead of waiting for Play to restart the animation loop.
        if (window.webglScene && window.webglScene.requestRender) {
            window.webglScene.requestRender();
        }
    }

    //To reset audio when player clicks on Movement or Firing buttons in Replay, otherwise the sound only plays once.
    function resetAudio(value = false) {
        for (const animContainer of this.animationStrategy.animations) {

            // --- 🏃 0. Skip pure movement animations (no audio at all) ---
            const isMovementAnimation =
                animContainer?.shipIcon &&
                animContainer?.turnCurve &&
                !animContainer?.emitters &&
                !animContainer?.particleEmitterContainer &&
                !animContainer?.movementAnimations;

            if (isMovementAnimation) continue;

            // --- 1. Handle emitter-based containers (Bolt/Missile/Torpedo) ---
            if (animContainer.emitters?.length) {
                const firstEmitter = animContainer.emitters[0];
                if (firstEmitter?.reservations?.length) {
                    for (const reservation of firstEmitter.reservations) {
                        const anim = reservation.animation;
                        if (
                            anim instanceof BoltEffect ||
                            anim instanceof MissileEffect ||
                            anim instanceof TorpedoEffect ||
                            anim instanceof BlinkEffect
                        ) {
                            anim.playedLaunchSound = value;
                            anim.playedImpactSound = value;
                        }
                    }
                }
                continue;
            }

            if (animContainer instanceof ShipDestroyedAnimation || animContainer instanceof ShipJumpAnimation) {
                animContainer.explosionTriggered = value;
                animContainer.soundTriggered = value; // 🔊 Fix: Reset sound flag too!
                continue;
            }

            // --- 2. Handle AllWeaponFireAgainstShipAnimation containers ---
            const isAllWeaponFire =
                animContainer?.movementAnimations &&
                animContainer?.shipIconContainer &&
                animContainer?.particleEmitterContainer &&
                animContainer?.logAnimation &&
                !animContainer?.emitters;

            if (isAllWeaponFire) {
                for (const effect of animContainer.animations) {
                    if (effect instanceof LaserEffect) {
                        effect.playedSound = value;
                    }

                    // Handle anonymous Gravitic Mine pull effects if they expose the flag
                    if (effect.playedPullSound !== undefined) {
                        effect.playedPullSound = value;
                    }

                    // Handle anonymous mine explosion effects
                    if (effect.playedImpactSound !== undefined) {
                        effect.playedImpactSound = value;
                    }
                }
            }
        }
    }


    // Spacebar toggles replay play/pause (see Settings.TogglePlayPause). Only the
    // ReplayPhaseStrategy handles it, so the key is inert outside replay.
    ReplayPhaseStrategy.prototype.onTogglePlayPause = function (payload) {
        if (payload.up) return; // Fire once on keydown, not on key hold / keyup.
        togglePlayPause.call(this);
    };

    ReplayPhaseStrategy.prototype.onToggleSound = function (payload) {
        if (payload.up) return; // Prevent repeats

        const now = Date.now();

        if (gamedata.playAudio) {
            // 🔇 Turn sound OFF            
            gamedata.playAudio = false;
        } else {
            // 🔊 Turn sound ON            
            gamedata.playAudio = true;

        }

        window.dispatchEvent(new CustomEvent("soundToggled"));
    };

    function startReplayOrRequestGamedata() {
        if (this.replayTurn === this.gamedata.turn) {
            //Refresh ballistic icons with replay flag so weapons suppressed in live phase 3
            //(e.g. GraviticMine, see BallisticIconContainer createBallisticIcon) appear in the replay
            //without needing the user to tab away and back.
            this.allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(this.gamedata.turn, 'ballistic');
            this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer, this.allBallistics);
            this.changeAnimationStrategy(new ReplayAnimationStrategy(this.gamedata, this.shipIconContainer, webglScene.scene));
            this.replayUI.setTurn(this.replayTurn);
        } else {
            if (!this.animationStrategy) {
                this.changeAnimationStrategy(new IdleAnimationStrategy(this.shipIconContainer, this.gamedata.turn));
                this.animationStrategy.update(this.gamedata);
            }

            requestReplayGamedata.call(this);
        }
    }

    function activatePause() {
        this.animationStrategy.pause();
        // syncPlayPause repaints both transport buttons to their paused glyphs.
        syncPlayPause.call(this);
    }

    // Mirror the transport buttons onto the live animation state: Pause (❚❚)
    // appears in place of whichever direction is running (rewind/forward), or
    // both show their normal glyphs when paused/stopped. Call this after every
    // transition that can start or stop playback.
    function syncPlayPause() {
        if (!this.replayUI || !this.animationStrategy) {
            return;
        }
        var state = "paused";
        if (!this.animationStrategy.isPaused()) {
            state = this.animationStrategy.goingBack ? "rewind" : "forward";
        }
        this.replayUI.setTransportState(state);
    }

    // Selectable replay speeds, adjusted by the −/+ Speed buttons. Ordered
    // slowest→fastest; 1× is the default. Both Play and Rewind use the chosen
    // rate (multiplier <1 slows playback, >1 speeds it up).
    var REPLAY_SPEEDS = [0.25, 0.5, 1, 2, 4, 8];

    // Play (forward=true) and Rewind (forward=false). Both run at the current
    // replaySpeed; speedMultiplier 1 is identical to the old play()/back().
    function playDirection(forward) {
        if (this.loading) {
            return;
        }

        // Resuming forward motion clears any printed combat log so the live
        // replay log isn't drawn over the static print (matches old play()).
        if (forward) {
            window.combatLog.showCurrent();
        }

        // Remember the direction so the spacebar resumes the way it was last
        // moving (AnimationStrategy.pause() forgets it — see lastDirectionForward).
        this.lastDirectionForward = forward;
        this.animationStrategy.fastSeek(this.replaySpeed, forward);
        syncPlayPause.call(this); // Repaints the active direction button as Pause.
    }

    // Per-button toggle: Pause appears in place of the active direction, so the
    // Play button pauses while playing forward and the Rewind button pauses
    // while rewinding. Pressing a direction button that isn't the active one
    // starts playback that way.
    function toggleDirection(forward) {
        if (this.loading) {
            return;
        }

        var running = this.animationStrategy && !this.animationStrategy.isPaused();
        var thisDirection = running && this.animationStrategy.goingBack === !forward;

        if (thisDirection) {
            activatePause.call(this);
        } else {
            playDirection.call(this, forward);
        }
    }

    // Spacebar toggle: pause if anything is running (either direction),
    // otherwise resume playback in the last direction it was moving (forward by
    // default), so pausing a rewind and hitting space again keeps rewinding.
    function togglePlayPause() {
        if (this.loading) {
            return;
        }

        if (this.animationStrategy && !this.animationStrategy.isPaused()) {
            activatePause.call(this);
        } else {
            playDirection.call(this, this.lastDirectionForward);
        }
    }

    // Step the speed one notch slower (direction -1) or faster (+1), clamped to
    // the REPLAY_SPEEDS range. The chosen speed sticks; if motion is already
    // running it's re-applied live in the current direction, otherwise it just
    // takes effect on the next Play/Rewind.
    function adjustSpeed(direction) {
        if (this.loading) {
            return;
        }

        var idx = REPLAY_SPEEDS.indexOf(this.replaySpeed);
        var next = idx + direction;
        if (next < 0 || next >= REPLAY_SPEEDS.length) {
            return; // Already at the slowest / fastest setting.
        }

        this.replaySpeed = REPLAY_SPEEDS[next];
        this.replayUI.setSpeed(this.replaySpeed);

        // Apply immediately to in-progress playback (paused playback picks the
        // new speed up when Play/Rewind is next pressed).
        if (this.animationStrategy && !this.animationStrategy.isPaused()) {
            this.animationStrategy.fastSeek(this.replaySpeed, !this.animationStrategy.goingBack);
        }
    }

    function getInitialReplayTurn() {

        if (this.currentTurn === 1 && this.currentPhase <= 1) {
            return 0;
        }

        if (this.currentPhase === 1) {
            return this.currentTurn - 1;
        }

        return this.currentTurn;
    }

    function turnBack() {
        if (this.replayTurn == 1 || this.loading) {
            return;
        }

        clearCombatLogs();
        this.replayTurn--;
        this.lastDirectionForward = true; // Jumped to a new turn: resume forward.
        activatePause.call(this);
        requestReplayGamedata.call(this);
    }

    function turnForward() {
        if (this.replayTurn === getInitialReplayTurn.call(this) || this.loading) {
            return;
        }

        clearCombatLogs();
        this.replayTurn++;
        this.lastDirectionForward = true; // Jumped to a new turn: resume forward.
        activatePause.call(this);
        requestReplayGamedata.call(this);
    }

    // Changing the replayed turn must wipe both combat-log surfaces: the printed
    // log (#LogActual via showCurrent) and the live replay messages (.logentry in
    // #log via onTurnStart). Otherwise the previous turn's entries linger and the
    // new turn's replay log renders over the top of them.
    function clearCombatLogs() {
        window.combatLog.showCurrent();
        window.combatLog.onTurnStart();
    }

    /*
    function requestReplayGamedata() {
        startLoading.call(this);

        jQuery.ajax({
            type: 'GET',
            url: 'replay.php',
            dataType: 'json',
            data: {
                turn: this.replayTurn,
                gameid: this.gamedata.gameid,
                time: new Date().getTime()
            },
            success: function (data) {
                gamedata.parseServerData(data); 
                //New section called at this point so that ballisticIcons can be rendered during Replay - DK 10/24  
                this.allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');		
                this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer, this.allBallistics);         		
                stopLoading.call(this);
            }.bind(this),
            error: ajaxInterface.errorAjax
        });
    } 

    function requestPlayableGamedata() {
        startLoading.call(this);

        jQuery.ajax({
            type: 'GET',
            url: 'gamedata.php',
            dataType: 'json',
            data: {
                turn: -1,
                phase: 0,
                activeship: -1,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer || -1,
                time: new Date().getTime(),
                force: true
            },
            success: function (data) {
                gamedata.replay = false;
                stopLoading.call(this);
                gamedata.parseServerData(data);
            }.bind(this),
            error: ajaxInterface.errorAjax
        });
    }    
    
*/
    //New versions to use ajaxWithRetry()
    function requestReplayGamedata() {
        startLoading.call(this);

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'replay.php',
            dataType: 'json',
            data: {
                turn: this.replayTurn,
                gameid: this.gamedata.gameid,
                time: new Date().getTime()
            },
            success: function (data) {
                gamedata.parseServerData(data);
                gamedata.replay = true;
                // New section so ballisticIcons render during Replay
                this.allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(
                    gamedata.turn,
                    'ballistic'
                );

                this.ballisticIconContainer.consumeGamedata(
                    this.gamedata,
                    this.shipIconContainer,
                    this.allBallistics
                );
            }.bind(this),
            error: ajaxInterface.errorAjax,
            complete: function () {
                stopLoading.call(this);
            }.bind(this)
        });
    }

    function requestPlayableGamedata() {
        startLoading.call(this);

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'gamedata.php',
            dataType: 'json',
            data: {
                turn: -1,
                phase: 0,
                activeship: -1,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer || -1,
                time: new Date().getTime(),
                force: true
            },
            success: function (data) {
                gamedata.replay = false;
                gamedata.parseServerData(data);
            }.bind(this),
            error: ajaxInterface.errorAjax,
            complete: function () {
                stopLoading.call(this);
            }.bind(this)
        });
    }


    function startLoading() {
        this.loading = true;
        this.replayUI.startLoading();
    }

    function stopLoading() {
        this.loading = false;
        this.replayUI.stopLoading();
    }

    return ReplayPhaseStrategy;
}();