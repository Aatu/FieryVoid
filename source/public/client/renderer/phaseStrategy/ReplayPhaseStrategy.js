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

    ReplayPhaseStrategy.prototype.deselectShip = function(ship) {};

    ReplayPhaseStrategy.prototype.targetShip = function(ship) {};

    ReplayPhaseStrategy.prototype.onMouseOverShip = function(ship, payload) {
        var icon = this.shipIconContainer.getById(ship.id);
        this.showShipTooltip(ship, payload);
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
            }
        ).activate();
    };

    function startReplayOrRequestGamedata() {
        if (this.replayTurn === this.gamedata.turn) {
            this.changeAnimationStrategy(new ReplayAnimationStrategy(null, this.gamedata, this.shipIconContainer, webglScene.scene));
            this.replayUI.setTurn(this.replayTurn);
        } else {
            if (! this.animationStrategy) {
                this.changeAnimationStrategy(new IdleAnimationStrategy(this.shipIconContainer, this.gamedata.turn));
            }

            requestReplayGamedata.call(this);
        }
    }

    function activateButton(action, event) {

        this.replayUI.activateButton(event.target);

        this.animationStrategy[action]();
    }

    function activateStop() {
        this.replayUI.activateButton("#stop");
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
        if (this.replayTurn === 1) {
            return;
        }

        this.replayTurn--;
        activateStop.call(this);
        requestReplayGamedata.call(this);
    }

    function turnForward() {
        if (this.replayTurn === getInitialReplayTurn.call(this)) {
            return;
        }

        this.replayTurn++;
        activateStop.call(this);
        requestReplayGamedata.call(this);
    }

    function requestReplayGamedata() {
        console.log("request replay gamedata", this.replayTurn);
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