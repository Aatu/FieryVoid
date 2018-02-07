window.ReplayPhaseStrategy = (function(){

    function ReplayPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.webglScene = null;
        this.currentTurn = null;
        this.currentPhase = null;
        this.replayTurn = null;
        this.replayPhase = null;

        this.loading = false;
    }

    ReplayPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    ReplayPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ewIconContainer.hide();
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.webglScene = webglScene;
        this.inactive = false;
        this.currentTurn = gamedata.turn;
        this.currentPhase = gamedata.gamephase;
        this.shipIconContainer.setAllSelected(false);
        this.replayTurn = getInitialReplayTurn.call(this);

        this.shipIconContainer.consumeGamedata(this.gamedata);

        this.createReplayUI();

        startReplayOrRequestGamedata.call(this);

        activateStop.call(this);

        console.log("activate replay phase");

        return this;
    };

    ReplayPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);

        return this;
    };

    ReplayPhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.shipIconContainer.consumeGamedata(this.gamedata);

        console.log("update replay phase");

        startReplayOrRequestGamedata.call(this);
    };


    ReplayPhaseStrategy.prototype.done = function () {};

    ReplayPhaseStrategy.prototype.onHexClicked = function(payload) {};

    ReplayPhaseStrategy.prototype.selectShip = function(ship) {};

    ReplayPhaseStrategy.prototype.deselectShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    ReplayPhaseStrategy.prototype.targetShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    ReplayPhaseStrategy.prototype.onMouseOverShip = function(ship, payload) {
        var icon = this.shipIconContainer.getById(ship.id);
        this.showShipTooltip(ship, payload, null, true);
        icon.showSideSprite(true);
    };

    ReplayPhaseStrategy.prototype.createReplayUI = function(gamedata) {
        this.replayUI = new ReplayUI(
            true,
            {
                play: activateButton.bind(this, "play"),
                pause: activateButton.bind(this, "pause"),
                stop: activateButton.bind(this, "stop"),
                turnForward: turnForward.bind(this),
                turnBack: turnBack.bind(this),
                endReplay: requestPlayableGamedata.bind(this)
            }
        ).activate();
    };

    ReplayPhaseStrategy.prototype.render = function(coordinateConverter, scene, zoom){
        PhaseStrategy.prototype.render.call(this, coordinateConverter, scene, zoom);

        if (this.animationStrategy && this.animationStrategy.isDone && this.animationStrategy.isDone()) {
            activateStop.call(this);
        }
    };

    function startReplayOrRequestGamedata() {
        if (this.replayTurn === this.gamedata.turn) {
            this.changeAnimationStrategy(new ReplayAnimationStrategy(this.gamedata, this.shipIconContainer, webglScene.scene));
            this.replayUI.setTurn(this.replayTurn);
        } else {
            if (! this.animationStrategy) {
                this.changeAnimationStrategy(new IdleAnimationStrategy(this.shipIconContainer, this.gamedata.turn));
                this.animationStrategy.update(this.gamedata);
            }

            requestReplayGamedata.call(this);
        }
    }

    function activateButton(action, event) {

        if (this.loading) {
            return
        }

        this.replayUI.activateButton(event.target);

        this.animationStrategy[action]();
    }

    function activateStop() {
        this.replayUI.activateButton("#pause");
        this.animationStrategy.pause();
    }

    function getInitialReplayTurn() {

        if (this.currentTurn  === 1 && this.currentPhase <= 1) {
            throw new Error("Activating replay too early");
        }

        if (this.currentPhase === 1) {
            return this.currentTurn  - 1;
        }

        return this.currentTurn;
    }

    function turnBack() {
        if (this.replayTurn === 1 || this.loading) {
            return;
        }

        this.replayTurn--;
        activateStop.call(this);
        requestReplayGamedata.call(this);
    }

    function turnForward() {
        if (this.replayTurn === getInitialReplayTurn.call(this) || this.loading) {
            return;
        }

        this.replayTurn++;
        activateStop.call(this);
        requestReplayGamedata.call(this);
    }

    function requestReplayGamedata() {
        startLoading.call(this);

        jQuery.ajax({
            type : 'GET',
            url : 'replay.php',
            dataType : 'json',
            data: {
                turn: this.replayTurn,
                gameid: this.gamedata.gameid,
                time: new Date().getTime()
            },
            success : function(data) {
                gamedata.parseServerData(data);
                stopLoading.call(this)
            }.bind(this),
            error : ajaxInterface.errorAjax
        });
    }

    function requestPlayableGamedata() {
        startLoading.call(this);
        console.log("request playable", gamedata.thisplayer);

        jQuery.ajax({
            type : 'GET',
            url : 'gamedata.php',
            dataType : 'json',
            data: {
                turn: -1,
                phase: 0,
                activeship: -1,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer || -1,
                time: new Date().getTime(),
                force: true
            },
            success: function(data) {
                console.log("new replay", data);
                gamedata.replay = false;
                stopLoading.call(this);
                gamedata.parseServerData(data);
            }.bind(this),
            error: ajaxInterface.errorAjax
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
})();